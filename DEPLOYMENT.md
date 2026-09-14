# Deploying SAS (Laravel) to WhoGoHost

WhoGoHost is cPanel shared hosting (PHP + MySQL). This Laravel app is built to
run there with no Node.js and no build step on the server: the Tailwind stylesheet
is precompiled into `public/css/app.css` and all views are server-rendered Blade.

## Requirements on the host
- PHP **8.2+** (set in cPanel → *MultiPHP Manager*; this app targets 8.2–8.4).
- A **MySQL** database (cPanel → *MySQL Databases*).
- Composer is usually **not** available on shared hosting, so we upload `vendor/`.

## Step 1 — Build locally
On your machine (needs PHP + Composer):

```bash
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate            # writes APP_KEY into .env
```

This produces the `vendor/` directory you will upload.

## Step 2 — Create the database in cPanel
1. cPanel → **MySQL Databases** → create a database, e.g. `cpaneluser_sas`.
2. Create a MySQL user and **add it to the database** with *All Privileges*.
3. Note the DB name, user, and password.

## Step 3 — Upload the files
Zip the whole project (including `vendor/`) and upload via cPanel → **File Manager**,
or use FTP. Two layouts work on WhoGoHost:

**A. Subdomain/addon domain with an adjustable document root (preferred)**
- Put the project in `~/sas` (outside `public_html`).
- Point the domain's **Document Root** to `~/sas/public`.

**B. Main domain where document root is fixed to `public_html`**
- Upload the project to `~/sas` (outside `public_html`).
- Move the **contents** of `~/sas/public` into `public_html`.
- Edit `public_html/index.php` and fix the two require paths to point at `~/sas`:
  ```php
  require __DIR__.'/../sas/vendor/autoload.php';
  $app = require_once __DIR__.'/../sas/bootstrap/app.php';
  ```

## Step 4 — Run the setup wizard (in your browser)

Open your domain. On the first visit SAS redirects to a **setup wizard** that
does the rest — no file editing, no terminal:

1. **Requirements** — it checks PHP version, extensions and folder permissions.
2. **Database** — enter the MySQL database, user and password you created in
   Step 2. It tests the connection live before saving.
3. **Email (SMTP)** — optional; enter a cPanel email account's SMTP details, or
   skip it for now. There's a "send test email" button.
4. It then **creates all the tables and demo data automatically** and locks the
   installer.

That's it — you're taken to a finished screen with the demo logins.

> Prefer the command line? If cPanel has **Terminal/SSH** you can instead run
> `cd ~/sas && php artisan migrate --force && php artisan db:seed --force` after
> filling `.env`, and the wizard will detect it's installed.

## Step 5 — Permissions (set before the wizard)
Ensure these are writable by PHP (File Manager → Permissions, `755`/`775`):
```
storage/                      (and all subfolders)
bootstrap/cache/
```

## Step 6 — Done
Visit your domain. The marketing site is public; sign in at `/login`.

### Demo accounts (password `Password123!`)
| Role | Email |
|------|-------|
| Super Admin | super@sas.app |
| School Admin | admin@greenfield.edu |
| Teacher | teacher@greenfield.edu |
| Student | student@greenfield.edu |
| Sales Staff | sales@greenfield.edu |

## Step 7 — Payment gateways (Paystack & CheqPay)
Schools pay for subscriptions from **App → Billing**. Add whichever gateway keys
you have to `.env` (or re-run the wizard's finished screen); a gateway only shows
on the billing page once its secret key is present:

```
PAYSTACK_PUBLIC_KEY=pk_live_xxx
PAYSTACK_SECRET_KEY=sk_live_xxx

CHEQPAY_PUBLIC_KEY=xxx
CHEQPAY_SECRET_KEY=xxx
# Optional — override only if CheqPay gives you a different API base/paths:
CHEQPAY_BASE_URL=https://api.cheqpay.com/v1
```

Set each gateway's **webhook URL** in its dashboard so subscriptions activate even
if the buyer closes the tab on the payment page:
- Paystack: `https://your-domain/pay/webhook/paystack`
- CheqPay:  `https://your-domain/pay/webhook/cheqpay`

Webhooks are verified by signature (HMAC) and are exempt from CSRF; no extra
config is needed.

## Step 8 — Per-school subdomains (optional)
Each school can get its own address, e.g. `greenfield.your-domain`. Leave this off
by keeping `APP_DOMAIN` blank (the app then runs on a single domain). To enable it:

1. In `.env` set the shared parent domain:
   ```
   APP_DOMAIN=your-domain.com
   ```
2. In cPanel create a **wildcard subdomain**: *Domains → Create* a subdomain named
   `*` on your domain, with its **Document Root** pointed at the same `public/`
   folder as the main site. (Some WhoGoHost plans require asking support to enable
   wildcard subdomains.)
3. Add a wildcard **DNS A record** `*.your-domain.com` pointing at the same server
   IP as the main domain, then re-run AutoSSL (a wildcard SSL cert covers
   `*.your-domain.com`).

Once live, a school's login and app live at `{slug}.your-domain.com`; the slug is
shown next to each school in **Super Admin → Schools**. The bare domain and `www`
stay the public marketing/platform site. Logging in on a subdomain is scoped to
that school.

## Notes
- **HTTPS**: enable AutoSSL in cPanel; set `APP_URL` to `https://…`.
- **PWA**: SAS is installable (Add to Home Screen / Install app) and works offline
  for static assets. No configuration needed — the manifest and service worker
  ship in `public/`.
- **Emails/queues**: not required for the core app; the shipped `.env.example`
  uses `SESSION_DRIVER=file` and `CACHE_STORE=file` so nothing extra is needed.
- To reset everything: `php artisan migrate:fresh --seed --force`.
