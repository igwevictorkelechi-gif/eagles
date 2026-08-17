# Deploying SAS to Cloudflare

SAS runs entirely on Cloudflare: **Pages** serves the React SPA, **Pages Functions** run the Hono API, and **D1** is the database. This guide takes you from zero to a live URL.

## Prerequisites

- A [Cloudflare account](https://dash.cloudflare.com/sign-up) (free tier is enough to start).
- Node.js 18+ and npm.
- Wrangler (bundled as a dev dependency — use `npx wrangler …`).

## 1. Authenticate Wrangler

```bash
npx wrangler login
```

This opens a browser to authorize Wrangler against your Cloudflare account. (In a headless/CI environment, set `CLOUDFLARE_API_TOKEN` instead — create a token with **Account · D1 Edit** and **Pages Edit** permissions.)

## 2. Create the D1 database

```bash
npx wrangler d1 create sas_db
```

Copy the `database_id` it prints and paste it into **`wrangler.toml`**, replacing `REPLACE_WITH_YOUR_D1_DATABASE_ID`:

```toml
[[d1_databases]]
binding = "DB"
database_name = "sas_db"
database_id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"   # <- paste here
migrations_dir = "schema/migrations"
```

## 3. Apply the schema and seed data to the remote DB

```bash
npm run db:migrate:remote     # creates all tables on Cloudflare D1
npm run db:seed:remote        # loads demo school + accounts (optional)
```

> Skip the seed step for a clean production database. You can always register your first school through the UI at `/start`.

## 4. Set the JWT secret (production)

Do **not** ship the placeholder secret in `wrangler.toml`. Set a strong secret as a Pages secret:

```bash
npx wrangler pages secret put JWT_SECRET
# paste a long random string when prompted
```

Generate one with: `openssl rand -base64 48`

## 5. Build and deploy

```bash
npm run deploy
```

`npm run deploy` runs `vite build` then `wrangler pages deploy dist`. The first deploy creates a Pages project named **`sas-school-admin`** and returns a `*.pages.dev` URL. Done — your SaaS is live.

### Deploying from Git (recommended for teams)

Alternatively connect the repo in the Cloudflare dashboard (**Workers & Pages → Create → Pages → Connect to Git**):

- **Build command:** `npm run build`
- **Build output directory:** `dist`
- Add the **D1 binding** `DB → sas_db` under Settings → Functions → D1 database bindings.
- Add the **environment variable / secret** `JWT_SECRET`.

Every push then builds and deploys automatically.

## 6. Custom domain

In the Pages project → **Custom domains**, add your domain (e.g. `app.yourschool.com`). Cloudflare provisions TLS automatically.

## Environment / bindings summary

| Binding | Type | Purpose |
|---------|------|---------|
| `DB` | D1 database | All application data |
| `JWT_SECRET` | Secret | Signs session tokens |

## Local development

```bash
npm run db:migrate:local
npm run db:seed:local
npm run build
npx wrangler pages dev dist --port 8788 --local
```

Local D1 state lives under `.wrangler/` (git-ignored).

## Roadmap (not yet built)

The foundation is structured so these extend cleanly, module by module:

- CBT **student exam-taking runtime** (timer, autosave, auto-submit) — schema (`exams`, `exam_questions`, `exam_attempts`) is already in place.
- **Report-card** generation & PDF export.
- **Payment webhooks** (Flutterwave / Paystack / Stripe) with server-side verification — subscription/plan tables are ready.
- **Staff clock-in/out** UI (the `clock_logs` table and attendance stats exist).
- **Notifications** center and email verification / password reset flows.
- **File uploads** (student/staff photos, logos) via Cloudflare R2 or Supabase Storage.
- **Audit-log** viewer UI (the `audit_logs` table exists).

## Troubleshooting

- **`D1_ERROR: no such table`** — you haven't run `npm run db:migrate:remote` against the remote DB.
- **401 on every request after deploy** — `JWT_SECRET` differs between the value that signed a token and the current one; sign in again after changing it.
- **Blank page / 404 on refresh of a deep link** — Pages serves `index.html` for unknown routes automatically for SPAs; ensure the build output dir is `dist`.
