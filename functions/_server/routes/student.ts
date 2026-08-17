import { Hono } from "hono";
import type { Env, Variables } from "../types";
import { requireAuth, requireRole } from "../auth";

const app = new Hono<{ Bindings: Env; Variables: Variables }>();

app.use("*", requireAuth);
app.use("*", requireRole("student", "parent"));

// Resolve the student record linked to the logged-in user.
async function studentFor(c: any) {
  const u = c.get("user");
  return c.env.DB
    .prepare("SELECT * FROM students WHERE school_id = ? AND user_id = ? LIMIT 1")
    .bind(u.school_id, u.sub)
    .first();
}

app.get("/dashboard", async (c) => {
  const u = c.get("user");
  const student = await studentFor(c);

  const announcements = await c.env.DB
    .prepare("SELECT title, body, created_at FROM announcements WHERE school_id = ? AND audience IN ('all','students') ORDER BY created_at DESC LIMIT 10")
    .bind(u.school_id)
    .all();

  let results: any[] = [];
  let fees: any[] = [];
  let cls: any = null;
  if (student) {
    const rr = await c.env.DB
      .prepare(
        `SELECT r.total_score, r.grade, r.remark, r.status, sub.name AS subject_name
         FROM results r LEFT JOIN subjects sub ON sub.id = r.subject_id
         WHERE r.school_id = ? AND r.student_id = ? AND r.status IN ('approved','locked')
         ORDER BY sub.name`,
      )
      .bind(u.school_id, student.id)
      .all();
    results = rr.results as any[];

    const ff = await c.env.DB
      .prepare("SELECT amount, amount_paid, status FROM student_fees WHERE school_id = ? AND student_id = ?")
      .bind(u.school_id, student.id)
      .all();
    fees = ff.results as any[];

    if (student.class_id) {
      cls = await c.env.DB.prepare("SELECT name FROM classes WHERE id = ?").bind(student.class_id).first();
    }
  }

  return c.json({
    student: student
      ? { name: `${student.first_name} ${student.last_name}`, admission_no: student.admission_no, class: cls?.name }
      : { name: u.name },
    results,
    fees,
    announcements: announcements.results,
  });
});

export default app;
