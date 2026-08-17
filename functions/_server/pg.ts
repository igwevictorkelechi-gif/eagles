// A D1-compatible database shim backed by Postgres (postgres.js).
// Lets the same Hono route code (which uses the D1 `.prepare().bind()` API)
// run unchanged on Vercel/Node against Supabase Postgres.
//
// Only loaded on the Node/Vercel path — never bundled into the Cloudflare build.
import postgres from "postgres";

type Sql = ReturnType<typeof postgres>;

let sql: Sql | null = null;

function getSql(): Sql {
  if (sql) return sql;
  const url = process.env.DATABASE_URL;
  if (!url) throw new Error("DATABASE_URL is not set");
  sql = postgres(url, {
    prepare: false, // required for Supabase transaction-mode pooler
    max: 1, // serverless: one connection per invocation
    idle_timeout: 20,
    connection: { search_path: "sas,public" },
  });
  return sql;
}

// Translate D1/SQLite-flavored SQL to Postgres:
//  - `?` positional placeholders -> `$1, $2, ...`
//  - `datetime('now')` -> `now()`, `date('now')` -> CURRENT_DATE
function translate(query: string): string {
  let out = query
    .replace(/datetime\('now'\)/gi, "now()")
    .replace(/date\('now'\)/gi, "CURRENT_DATE");
  let i = 0;
  out = out.replace(/\?/g, () => `$${++i}`);
  return out;
}

async function exec(runner: Sql, query: string, params: unknown[]) {
  const text = translate(query);
  // postgres.js: unsafe(text, params) returns a result array with `.count`
  return runner.unsafe(text, params as any[]);
}

class Statement {
  sql: string;
  params: unknown[] = [];
  constructor(query: string) {
    this.sql = query;
  }
  bind(...args: unknown[]): Statement {
    this.params = args;
    return this;
  }
  async first<T = any>(): Promise<T | null> {
    const rows = await exec(getSql(), this.sql, this.params);
    return (rows[0] as T) ?? null;
  }
  async all<T = any>(): Promise<{ results: T[]; success: true }> {
    const rows = await exec(getSql(), this.sql, this.params);
    return { results: rows as unknown as T[], success: true };
  }
  async run(): Promise<{ success: true; meta: { changes: number } }> {
    const rows = await exec(getSql(), this.sql, this.params);
    return { success: true, meta: { changes: (rows as any).count ?? rows.length ?? 0 } };
  }
}

// D1Database-compatible surface used by the app.
export function createPgD1(): any {
  return {
    prepare(query: string) {
      return new Statement(query);
    },
    async batch(statements: Statement[]) {
      const client = getSql();
      return client.begin(async (tx) => {
        const out: unknown[] = [];
        for (const st of statements) {
          out.push(await exec(tx as unknown as Sql, st.sql, st.params));
        }
        return out;
      });
    },
  };
}
