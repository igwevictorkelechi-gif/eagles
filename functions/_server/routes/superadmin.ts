import { Hono } from "hono";
import type { Env, Variables } from "../types";
import { requireAuth, requireRole } from "../auth";
import { uid } from "../util";

const app = new Hono<{ Bindings: Env; Variables: Variables }>();

app.use("*", requireAuth);
app.use("*", requireRole("super_admin"));

// Platform dashboard
app.get("/dashboard", async (c) => {
  const db = c.env.DB;
  const n = async (sql: string) => Number((await db.prepare(sql).first<{ n: number }>())?.n ?? 0);
  const [schools, active, trials, users, plans] = await Promise.all([
    n("SELECT CAST(COUNT(*) AS INTEGER) n FROM schools"),
    n("SELECT CAST(COUNT(*) AS INTEGER) n FROM subscriptions WHERE status = 'active'"),
    n("SELECT CAST(COUNT(*) AS INTEGER) n FROM subscriptions WHERE status = 'trial'"),
    n("SELECT CAST(COUNT(*) AS INTEGER) n FROM users"),
    n("SELECT CAST(COUNT(*) AS INTEGER) n FROM subscription_plans WHERE is_active = 1"),
  ]);
  const mrr = Number((await db
    .prepare(
      `SELECT CAST(COALESCE(SUM(p.price_monthly),0) AS INTEGER) n
       FROM subscriptions s JOIN subscription_plans p ON p.id = s.plan_id
       WHERE s.status = 'active'`,
    )
    .first<{ n: number }>())?.n ?? 0);
  return c.json({ counts: { schools, active, trials, users, plans }, mrr });
});

// Schools list (with plan + subscription status)
app.get("/schools", async (c) => {
  const rows = await c.env.DB
    .prepare(
      `SELECT sc.id, sc.name, sc.slug, sc.email, sc.status, sc.created_at,
              sub.status AS sub_status, sub.trial_ends_at, p.name AS plan_name,
              (SELECT COUNT(*) FROM students st WHERE st.school_id = sc.id) AS students
       FROM schools sc
       LEFT JOIN subscriptions sub ON sub.school_id = sc.id
       LEFT JOIN subscription_plans p ON p.id = sub.plan_id
       ORDER BY sc.created_at DESC`,
    )
    .all();
  return c.json({ data: rows.results });
});

app.put("/schools/:id/status", async (c) => {
  const { status } = (await c.req.json().catch(() => ({}))) as any;
  if (!["active", "suspended"].includes(status)) return c.json({ error: "Invalid status" }, 400);
  await c.env.DB.prepare("UPDATE schools SET status = ? WHERE id = ?").bind(status, c.req.param("id")).run();
  return c.json({ ok: true });
});

// Subscription plans CRUD
app.get("/plans", async (c) => {
  const rows = await c.env.DB.prepare("SELECT * FROM subscription_plans ORDER BY sort_order ASC").all();
  return c.json({ data: rows.results });
});

app.post("/plans", async (c) => {
  const b = (await c.req.json().catch(() => ({}))) as any;
  if (!b.name || !b.code) return c.json({ error: "Name and code required" }, 400);
  const id = uid("plan_");
  await c.env.DB
    .prepare(
      `INSERT INTO subscription_plans (id, name, code, price_monthly, currency, max_students, max_teachers, max_staff, storage_mb, features, is_custom, is_active, sort_order)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
    )
    .bind(
      id, b.name, b.code, Number(b.price_monthly) || 0, b.currency || "NGN",
      b.max_students ?? null, b.max_teachers ?? null, b.max_staff ?? null, b.storage_mb ?? null,
      JSON.stringify(b.features || []), b.is_custom ? 1 : 0, b.is_active === false ? 0 : 1, Number(b.sort_order) || 0,
    )
    .run();
  const row = await c.env.DB.prepare("SELECT * FROM subscription_plans WHERE id = ?").bind(id).first();
  return c.json({ data: row }, 201);
});

app.put("/plans/:id", async (c) => {
  const b = (await c.req.json().catch(() => ({}))) as any;
  const allowed = ["name", "price_monthly", "currency", "max_students", "max_teachers", "max_staff", "storage_mb", "is_custom", "is_active", "sort_order"];
  const sets: string[] = [];
  const vals: unknown[] = [];
  for (const f of allowed) if (f in b) { sets.push(`${f} = ?`); vals.push(typeof b[f] === "boolean" ? (b[f] ? 1 : 0) : b[f]); }
  if ("features" in b) { sets.push("features = ?"); vals.push(JSON.stringify(b.features)); }
  if (!sets.length) return c.json({ error: "No changes" }, 400);
  vals.push(c.req.param("id"));
  await c.env.DB.prepare(`UPDATE subscription_plans SET ${sets.join(", ")} WHERE id = ?`).bind(...vals).run();
  const row = await c.env.DB.prepare("SELECT * FROM subscription_plans WHERE id = ?").bind(c.req.param("id")).first();
  return c.json({ data: row });
});

app.delete("/plans/:id", async (c) => {
  await c.env.DB.prepare("UPDATE subscription_plans SET is_active = 0 WHERE id = ?").bind(c.req.param("id")).run();
  return c.json({ ok: true });
});

// All subscriptions
app.get("/subscriptions", async (c) => {
  const rows = await c.env.DB
    .prepare(
      `SELECT s.*, sc.name AS school_name, p.name AS plan_name, p.price_monthly
       FROM subscriptions s
       JOIN schools sc ON sc.id = s.school_id
       JOIN subscription_plans p ON p.id = s.plan_id
       ORDER BY s.created_at DESC`,
    )
    .all();
  return c.json({ data: rows.results });
});

app.put("/subscriptions/:id", async (c) => {
  const b = (await c.req.json().catch(() => ({}))) as any;
  const allowed = ["status", "plan_id", "billing_cycle", "current_period_end", "trial_ends_at"];
  const sets: string[] = [];
  const vals: unknown[] = [];
  for (const f of allowed) if (f in b) { sets.push(`${f} = ?`); vals.push(b[f]); }
  if (!sets.length) return c.json({ error: "No changes" }, 400);
  vals.push(c.req.param("id"));
  await c.env.DB.prepare(`UPDATE subscriptions SET ${sets.join(", ")} WHERE id = ?`).bind(...vals).run();
  return c.json({ ok: true });
});

export default app;
