import { Hono } from "hono";
import { cors } from "hono/cors";
import type { Env, Variables } from "./types";
import authRoutes from "./routes/auth";
import publicRoutes from "./routes/public";
import adminRoutes from "./routes/admin";
import studentRoutes from "./routes/student";
import superadminRoutes from "./routes/superadmin";

const app = new Hono<{ Bindings: Env; Variables: Variables }>().basePath("/api");

app.use("*", cors());

app.get("/health", (c) => c.json({ ok: true, service: "sas", time: new Date().toISOString() }));

app.route("/auth", authRoutes);
app.route("/public", publicRoutes);
app.route("/admin", adminRoutes);
app.route("/student", studentRoutes);
app.route("/superadmin", superadminRoutes);

app.notFound((c) => c.json({ error: "Not found" }, 404));
app.onError((err, c) => {
  console.error(err);
  return c.json({ error: "Server error", detail: String(err?.message || err) }, 500);
});

export default app;
