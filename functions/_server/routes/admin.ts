import { Hono } from "hono";
import type { Env, Variables } from "../types";
import { requireAuth, requireRole } from "../auth";
import { crudRouter } from "../crud";
import { uid, gradeFor } from "../util";

const app = new Hono<{ Bindings: Env; Variables: Variables }>();

// Everything here requires a school-scoped role.
app.use("*", requireAuth);
app.use("*", requireRole("school_admin", "teacher", "staff", "sales_staff"));

const tenant = (c: any): string => c.get("user").school_id as string;

// ---- Dashboard summary ----
app.get("/dashboard", async (c) => {
  const sid = tenant(c);
  const db = c.env.DB;
  const one = async (sql: string) =>
    ((await db.prepare(sql).bind(sid).first<{ n: number }>())?.n ?? 0);

  const [students, teachers, classes, subjects, exams] = await Promise.all([
    one("SELECT COUNT(*) n FROM students WHERE school_id = ?"),
    one("SELECT COUNT(*) n FROM teachers WHERE school_id = ?"),
    one("SELECT COUNT(*) n FROM classes WHERE school_id = ?"),
    one("SELECT COUNT(*) n FROM subjects WHERE school_id = ?"),
    one("SELECT COUNT(*) n FROM exams WHERE school_id = ?"),
  ]);

  const income = (await db
    .prepare("SELECT COALESCE(SUM(amount),0) n FROM transactions WHERE school_id = ? AND type = 'income'")
    .bind(sid)
    .first<{ n: number }>())?.n ?? 0;
  const expense = (await db
    .prepare("SELECT COALESCE(SUM(amount),0) n FROM transactions WHERE school_id = ? AND type = 'expense'")
    .bind(sid)
    .first<{ n: number }>())?.n ?? 0;
  const outstanding = (await db
    .prepare("SELECT COALESCE(SUM(amount - amount_paid),0) n FROM student_fees WHERE school_id = ?")
    .bind(sid)
    .first<{ n: number }>())?.n ?? 0;

  const recent = await db
    .prepare("SELECT title, body, created_at FROM announcements WHERE school_id = ? ORDER BY created_at DESC LIMIT 5")
    .bind(sid)
    .all();

  return c.json({
    counts: { students, teachers, classes, subjects, exams },
    finance: { income, expense, profit: income - expense, outstanding },
    announcements: recent.results,
  });
});

// ---- Branding / school settings ----
app.get("/school", async (c) => {
  const sid = tenant(c);
  const row = await c.env.DB.prepare("SELECT * FROM schools WHERE id = ?").bind(sid).first();
  return c.json({ data: row });
});

app.put("/school", requireRole("school_admin"), async (c) => {
  const sid = tenant(c);
  const body = (await c.req.json().catch(() => ({}))) as Record<string, unknown>;
  const allowed = [
    "name", "short_name", "email", "phone", "address", "website",
    "logo_url", "favicon_url", "primary_color", "secondary_color",
  ];
  const sets: string[] = [];
  const vals: unknown[] = [];
  for (const f of allowed) if (f in body) { sets.push(`${f} = ?`); vals.push(body[f]); }
  if (!sets.length) return c.json({ error: "No changes" }, 400);
  vals.push(sid);
  await c.env.DB.prepare(`UPDATE schools SET ${sets.join(", ")} WHERE id = ?`).bind(...vals).run();
  const row = await c.env.DB.prepare("SELECT * FROM schools WHERE id = ?").bind(sid).first();
  return c.json({ data: row });
});

// ---- Results (enter + list, with auto-grading) ----
app.get("/results", async (c) => {
  const sid = tenant(c);
  const rows = await c.env.DB
    .prepare(
      `SELECT r.*, s.first_name || ' ' || s.last_name AS student_name, sub.name AS subject_name
       FROM results r
       LEFT JOIN students s ON s.id = r.student_id
       LEFT JOIN subjects sub ON sub.id = r.subject_id
       WHERE r.school_id = ? ORDER BY r.created_at DESC LIMIT 300`,
    )
    .bind(sid)
    .all();
  return c.json({ data: rows.results });
});

app.post("/results", async (c) => {
  const sid = tenant(c);
  const b = (await c.req.json().catch(() => ({}))) as any;
  const ca = Number(b.ca_score) || 0;
  const exam = Number(b.exam_score) || 0;
  const total = ca + exam;
  const { grade, remark } = gradeFor(total);
  const id = uid("res_");
  await c.env.DB
    .prepare(
      `INSERT INTO results (id, school_id, student_id, subject_id, class_id, term_id, ca_score, exam_score, total_score, grade, remark, status, entered_by)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted', ?)`,
    )
    .bind(id, sid, b.student_id, b.subject_id || null, b.class_id || null, b.term_id || null, ca, exam, total, grade, remark, c.get("user").sub)
    .run();
  const row = await c.env.DB.prepare("SELECT * FROM results WHERE id = ?").bind(id).first();
  return c.json({ data: row }, 201);
});

app.put("/results/:id/status", requireRole("school_admin"), async (c) => {
  const sid = tenant(c);
  const { status } = (await c.req.json().catch(() => ({}))) as any;
  const valid = ["draft", "submitted", "approved", "rejected", "locked"];
  if (!valid.includes(status)) return c.json({ error: "Invalid status" }, 400);
  const res = await c.env.DB
    .prepare("UPDATE results SET status = ? WHERE id = ? AND school_id = ?")
    .bind(status, c.req.param("id"), sid)
    .run();
  if (!res.meta.changes) return c.json({ error: "Not found" }, 404);
  return c.json({ ok: true });
});

// ---- POS: record a sale, decrement stock, log income ----
app.post("/sales", requireRole("school_admin", "sales_staff"), async (c) => {
  const sid = tenant(c);
  const b = (await c.req.json().catch(() => ({}))) as any;
  const items: Array<{ product_id?: string; name: string; qty: number; price: number }> = b.items || [];
  if (!items.length) return c.json({ error: "Cart is empty" }, 400);

  const total = items.reduce((s, it) => s + it.qty * it.price, 0);
  const saleId = uid("sale_");
  const ref = "RCP-" + Date.now().toString(36).toUpperCase();

  const stmts = [
    c.env.DB.prepare(
      "INSERT INTO sales (id, school_id, reference, total, payment_method, customer, sold_by) VALUES (?, ?, ?, ?, ?, ?, ?)",
    ).bind(saleId, sid, ref, total, b.payment_method || "cash", b.customer || null, c.get("user").sub),
  ];
  for (const it of items) {
    stmts.push(
      c.env.DB.prepare(
        "INSERT INTO sale_items (id, school_id, sale_id, product_id, name, qty, price, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
      ).bind(uid("si_"), sid, saleId, it.product_id || null, it.name, it.qty, it.price, it.qty * it.price),
    );
    if (it.product_id) {
      stmts.push(
        c.env.DB.prepare(
          "UPDATE products SET stock_qty = stock_qty - ? WHERE id = ? AND school_id = ?",
        ).bind(it.qty, it.product_id, sid),
      );
    }
  }
  stmts.push(
    c.env.DB.prepare(
      "INSERT INTO transactions (id, school_id, type, category, description, amount) VALUES (?, ?, 'income', 'sales', ?, ?)",
    ).bind(uid("txn_"), sid, `POS sale ${ref}`, total),
  );
  await c.env.DB.batch(stmts);

  return c.json({ data: { id: saleId, reference: ref, total } }, 201);
});

app.get("/sales", requireRole("school_admin", "sales_staff"), async (c) => {
  const sid = tenant(c);
  const rows = await c.env.DB
    .prepare("SELECT * FROM sales WHERE school_id = ? ORDER BY created_at DESC LIMIT 100")
    .bind(sid)
    .all();
  return c.json({ data: rows.results });
});

// ---- Fee payment (records payment + income transaction) ----
app.post("/fee-payments", requireRole("school_admin"), async (c) => {
  const sid = tenant(c);
  const b = (await c.req.json().catch(() => ({}))) as any;
  const amount = Number(b.amount) || 0;
  const payId = uid("pay_");
  const stmts = [
    c.env.DB.prepare(
      "INSERT INTO fee_payments (id, school_id, student_fee_id, student_id, amount, method, reference) VALUES (?, ?, ?, ?, ?, ?, ?)",
    ).bind(payId, sid, b.student_fee_id || null, b.student_id || null, amount, b.method || "cash", b.reference || null),
    c.env.DB.prepare(
      "INSERT INTO transactions (id, school_id, type, category, description, amount) VALUES (?, ?, 'income', 'fees', 'Fee payment', ?)",
    ).bind(uid("txn_"), sid, amount),
  ];
  if (b.student_fee_id) {
    stmts.push(
      c.env.DB.prepare(
        "UPDATE student_fees SET amount_paid = amount_paid + ?, status = CASE WHEN amount_paid + ? >= amount THEN 'paid' ELSE 'partial' END WHERE id = ? AND school_id = ?",
      ).bind(amount, amount, b.student_fee_id, sid),
    );
  }
  await c.env.DB.batch(stmts);
  return c.json({ ok: true, id: payId }, 201);
});

// ---- Mount tenant-isolated CRUD resources ----
app.route("/students", crudRouter({
  table: "students", idPrefix: "std_",
  fields: ["admission_no", "first_name", "last_name", "gender", "date_of_birth", "class_id", "guardian_name", "guardian_phone", "status"],
}));
app.route("/teachers", crudRouter({
  table: "teachers", idPrefix: "tch_",
  fields: ["first_name", "last_name", "email", "phone", "subject", "employee_no", "status"],
}));
app.route("/classes", crudRouter({
  table: "classes", idPrefix: "cls_",
  fields: ["name", "level", "teacher_id", "capacity"], orderBy: "name ASC",
}));
app.route("/subjects", crudRouter({
  table: "subjects", idPrefix: "subj_",
  fields: ["name", "code"], orderBy: "name ASC",
}));
app.route("/announcements", crudRouter({
  table: "announcements", idPrefix: "ann_",
  fields: ["title", "body", "audience", "created_by"],
}));
app.route("/fee-structures", crudRouter({
  table: "fee_structures", idPrefix: "fee_",
  fields: ["name", "category", "amount", "class_id", "term_id"],
}));
app.route("/products", crudRouter({
  table: "products", idPrefix: "prod_",
  fields: ["name", "sku", "category", "purchase_price", "selling_price", "stock_qty", "low_stock"], orderBy: "name ASC",
}));
app.route("/exams", crudRouter({
  table: "exams", idPrefix: "exam_",
  fields: ["title", "type", "subject_id", "class_id", "instructions", "duration_mins", "question_count", "pass_mark", "attempts", "randomize", "negative_mark", "starts_at", "ends_at", "status", "created_by"],
}));
app.route("/expenses", crudRouter({
  table: "transactions", idPrefix: "txn_",
  fields: ["type", "category", "description", "amount", "date"],
}));

export default app;
