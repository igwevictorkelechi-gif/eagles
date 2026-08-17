import { Hono } from "hono";
import type { Env, Variables } from "../types";

const app = new Hono<{ Bindings: Env; Variables: Variables }>();

// Public pricing plans for the marketing site.
app.get("/plans", async (c) => {
  const rows = await c.env.DB
    .prepare("SELECT id, name, code, price_monthly, currency, max_students, max_teachers, features, is_custom FROM subscription_plans WHERE is_active = 1 ORDER BY sort_order ASC")
    .all();
  return c.json({ data: rows.results });
});

export default app;
