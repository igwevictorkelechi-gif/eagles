// Applies the Postgres schema + seed to DATABASE_URL (Supabase or any Postgres).
// Usage: DATABASE_URL="postgres://..." node scripts/pg-setup.mjs
import postgres from "postgres";
import { readFileSync } from "node:fs";

const url = process.env.DATABASE_URL;
if (!url) {
  console.error("Set DATABASE_URL first.");
  process.exit(1);
}

const sql = postgres(url, { prepare: false, max: 1 });

const schema = readFileSync(new URL("../schema/postgres/0001_init.sql", import.meta.url), "utf8");
let seed = readFileSync(new URL("../schema/seed.sql", import.meta.url), "utf8");

try {
  console.log("Applying schema…");
  await sql.unsafe(schema);

  console.log("Seeding (search_path=sas)…");
  await sql.unsafe("SET search_path TO sas; " + seed);

  const [{ n: schools }] = await sql`select count(*)::int n from sas.schools`;
  const [{ n: users }] = await sql`select count(*)::int n from sas.users`;
  const [{ n: plans }] = await sql`select count(*)::int n from sas.subscription_plans`;
  console.log(`Done. schools=${schools} users=${users} plans=${plans}`);
} catch (e) {
  console.error("Setup failed:", e.message);
  process.exitCode = 1;
} finally {
  await sql.end();
}
