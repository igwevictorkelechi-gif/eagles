import { Hono } from "hono";
import type { Env, Variables } from "../types";
import { hashPassword, verifyPassword } from "../crypto";
import { issueToken, requireAuth } from "../auth";
import { uid, slugify } from "../util";

const app = new Hono<{ Bindings: Env; Variables: Variables }>();

// POST /api/auth/register  — create a school tenant + its first admin, start a trial.
app.post("/register", async (c) => {
  const body = await c.req.json().catch(() => null);
  if (!body) return c.json({ error: "Invalid body" }, 400);
  const { schoolName, adminFirstName, adminLastName, email, password } = body;
  if (!schoolName || !email || !password || !adminFirstName) {
    return c.json({ error: "Missing required fields" }, 400);
  }
  if (String(password).length < 6) {
    return c.json({ error: "Password must be at least 6 characters" }, 400);
  }

  const db = c.env.DB;
  // unique slug
  let slug = slugify(schoolName) || "school";
  const existing = await db.prepare("SELECT id FROM schools WHERE slug = ?").bind(slug).first();
  if (existing) slug = `${slug}-${Math.random().toString(36).slice(2, 6)}`;

  const schoolIdV = uid("sch_");
  const userId = uid("usr_");
  const now = new Date();
  const trialEnds = new Date(now.getTime() + 14 * 24 * 3600 * 1000).toISOString();

  // Default to the free plan for the trial.
  const plan =
    (await db.prepare("SELECT id FROM subscription_plans WHERE code = 'free'").first<{ id: string }>()) ||
    (await db.prepare("SELECT id FROM subscription_plans ORDER BY sort_order LIMIT 1").first<{ id: string }>());

  const pwHash = await hashPassword(password);

  const stmts = [
    db
      .prepare(
        "INSERT INTO schools (id, name, short_name, slug, email) VALUES (?, ?, ?, ?, ?)",
      )
      .bind(schoolIdV, schoolName, body.shortName || null, slug, email),
    db
      .prepare(
        "INSERT INTO users (id, school_id, role, first_name, last_name, email, password_hash, email_verified) VALUES (?, ?, 'school_admin', ?, ?, ?, ?, 1)",
      )
      .bind(userId, schoolIdV, adminFirstName, adminLastName || "", email, pwHash),
  ];
  if (plan) {
    stmts.push(
      db
        .prepare(
          "INSERT INTO subscriptions (id, school_id, plan_id, status, trial_ends_at, current_period_end) VALUES (?, ?, ?, 'trial', ?, ?)",
        )
        .bind(uid("sub_"), schoolIdV, plan.id, trialEnds, trialEnds),
    );
  }
  await db.batch(stmts);

  const token = await issueToken(c.env.JWT_SECRET, {
    sub: userId,
    role: "school_admin",
    school_id: schoolIdV,
    name: `${adminFirstName} ${adminLastName || ""}`.trim(),
  });

  return c.json({
    token,
    user: { id: userId, role: "school_admin", school_id: schoolIdV, name: adminFirstName },
    school: { id: schoolIdV, name: schoolName, slug },
  });
});

// POST /api/auth/login
app.post("/login", async (c) => {
  const body = await c.req.json().catch(() => null);
  if (!body?.email || !body?.password) return c.json({ error: "Email and password required" }, 400);

  const db = c.env.DB;
  // super admins first (school_id NULL), then any school user by email
  const user = await db
    .prepare("SELECT * FROM users WHERE email = ? AND is_active = 1 ORDER BY (school_id IS NULL) DESC LIMIT 1")
    .bind(body.email)
    .first<any>();

  if (!user || !(await verifyPassword(body.password, user.password_hash))) {
    return c.json({ error: "Invalid email or password" }, 401);
  }

  await db.prepare("UPDATE users SET last_login_at = datetime('now') WHERE id = ?").bind(user.id).run();

  let school = null;
  if (user.school_id) {
    school = await db.prepare("SELECT id, name, slug, primary_color FROM schools WHERE id = ?").bind(user.school_id).first();
  }

  const name = `${user.first_name} ${user.last_name}`.trim();
  const token = await issueToken(c.env.JWT_SECRET, {
    sub: user.id,
    role: user.role,
    school_id: user.school_id,
    name,
  });

  return c.json({
    token,
    user: { id: user.id, role: user.role, school_id: user.school_id, name, email: user.email },
    school,
  });
});

// GET /api/auth/me
app.get("/me", requireAuth, async (c) => {
  const u = c.get("user");
  let school = null;
  if (u.school_id) {
    school = await c.env.DB
      .prepare("SELECT id, name, slug, logo_url, primary_color, secondary_color FROM schools WHERE id = ?")
      .bind(u.school_id)
      .first();
  }
  return c.json({ user: { id: u.sub, role: u.role, school_id: u.school_id, name: u.name }, school });
});

export default app;
