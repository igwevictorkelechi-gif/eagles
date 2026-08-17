// Vercel serverless entry (Node runtime). Serves the whole /api surface.
// Cloudflare uses functions/api/[[route]].ts instead — this file is Vercel-only.
import { Hono } from "hono";
import { handle } from "@hono/node-server/vercel";
import app from "../functions/_server/app";
import { createPgD1 } from "../functions/_server/pg";

// Reuse one shim across warm invocations.
const db = createPgD1();

// Root app injects a Postgres-backed, D1-compatible `env` (Cloudflare provides
// this via bindings; on Vercel we build it from process.env), then delegates.
const root = new Hono();
root.use("*", async (c, next) => {
  c.env = { DB: db, JWT_SECRET: process.env.JWT_SECRET || "dev-secret" };
  await next();
});
root.route("/", app);

export default handle(root);
