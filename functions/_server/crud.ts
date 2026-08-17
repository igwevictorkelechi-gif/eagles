import { Hono } from "hono";
import type { Env, Variables } from "./types";
import { requireAuth } from "./auth";
import { uid } from "./util";

interface CrudOptions {
  table: string;
  idPrefix: string;
  fields: string[]; // writable columns (besides id/school_id/created_at)
  orderBy?: string;
}

// Builds a tenant-isolated REST resource. Every query is constrained by the
// authenticated user's school_id, so no school can read or write another's rows.
export function crudRouter(opts: CrudOptions) {
  const app = new Hono<{ Bindings: Env; Variables: Variables }>();
  const order = opts.orderBy || "created_at DESC";

  app.use("*", requireAuth);

  const tenant = (c: any): string => {
    const sid = c.get("user").school_id;
    if (!sid) throw new Error("tenant required");
    return sid;
  };

  // LIST
  app.get("/", async (c) => {
    const sid = tenant(c);
    const rows = await c.env.DB.prepare(
      `SELECT * FROM ${opts.table} WHERE school_id = ? ORDER BY ${order}`,
    )
      .bind(sid)
      .all();
    return c.json({ data: rows.results });
  });

  // GET one
  app.get("/:id", async (c) => {
    const sid = tenant(c);
    const row = await c.env.DB.prepare(
      `SELECT * FROM ${opts.table} WHERE id = ? AND school_id = ?`,
    )
      .bind(c.req.param("id"), sid)
      .first();
    if (!row) return c.json({ error: "Not found" }, 404);
    return c.json({ data: row });
  });

  // CREATE
  app.post("/", async (c) => {
    const sid = tenant(c);
    const body = (await c.req.json().catch(() => ({}))) as Record<string, unknown>;
    const cols = ["id", "school_id"];
    const vals: unknown[] = [uid(opts.idPrefix), sid];
    for (const f of opts.fields) {
      if (f in body) {
        cols.push(f);
        vals.push(body[f] as unknown);
      }
    }
    const placeholders = cols.map(() => "?").join(", ");
    await c.env.DB.prepare(
      `INSERT INTO ${opts.table} (${cols.join(", ")}) VALUES (${placeholders})`,
    )
      .bind(...vals)
      .run();
    const row = await c.env.DB.prepare(`SELECT * FROM ${opts.table} WHERE id = ?`).bind(vals[0]).first();
    return c.json({ data: row }, 201);
  });

  // UPDATE
  app.put("/:id", async (c) => {
    const sid = tenant(c);
    const body = (await c.req.json().catch(() => ({}))) as Record<string, unknown>;
    const sets: string[] = [];
    const vals: unknown[] = [];
    for (const f of opts.fields) {
      if (f in body) {
        sets.push(`${f} = ?`);
        vals.push(body[f] as unknown);
      }
    }
    if (!sets.length) return c.json({ error: "No fields to update" }, 400);
    vals.push(c.req.param("id"), sid);
    const res = await c.env.DB.prepare(
      `UPDATE ${opts.table} SET ${sets.join(", ")} WHERE id = ? AND school_id = ?`,
    )
      .bind(...vals)
      .run();
    if (!res.meta.changes) return c.json({ error: "Not found" }, 404);
    const row = await c.env.DB.prepare(`SELECT * FROM ${opts.table} WHERE id = ?`).bind(c.req.param("id")).first();
    return c.json({ data: row });
  });

  // DELETE
  app.delete("/:id", async (c) => {
    const sid = tenant(c);
    const res = await c.env.DB.prepare(
      `DELETE FROM ${opts.table} WHERE id = ? AND school_id = ?`,
    )
      .bind(c.req.param("id"), sid)
      .run();
    if (!res.meta.changes) return c.json({ error: "Not found" }, 404);
    return c.json({ ok: true });
  });

  return app;
}
