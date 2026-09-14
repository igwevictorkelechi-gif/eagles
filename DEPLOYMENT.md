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

## Notes
- **HTTPS**: enable AutoSSL in cPanel; set `APP_URL` to `https://…`.
- **Emails/queues**: not required for the core app; `SESSION_DRIVER=database`
  and `CACHE_STORE=database` avoid needing Redis.
- To reset everything: `php artisan migrate:fresh --seed --force`.
