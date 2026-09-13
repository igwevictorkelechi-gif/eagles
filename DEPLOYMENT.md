# Deploying SAS (Laravel) to WhoGoHost

WhoGoHost is cPanel shared hosting (PHP + MySQL). This Laravel app is built to
run there with no Node.js and no build step on the server (Tailwind is loaded
via CDN, and all views are server-rendered Blade).

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

## Step 4 — Configure `.env`
Edit `.env` (File Manager) with your production values:

```
APP_NAME=SAS
APP_ENV=production
APP_KEY=base64:...          # from step 1
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpaneluser_sas
DB_USERNAME=cpaneluser_sasuser
DB_PASSWORD=your-db-password

SESSION_DRIVER=database
CACHE_STORE=database
```

## Step 5 — Create tables + demo data
If cPanel offers **Terminal** (or SSH):

```bash
cd ~/sas
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
```

If there is **no** terminal, use the one-time web installer route included in this
app: set `APP_INSTALL_TOKEN=some-secret` in `.env`, then visit
`https://yourdomain.com/install?token=some-secret` once — it runs `migrate --seed`.
Afterwards, unset `APP_INSTALL_TOKEN` (the route returns 404 when it is empty).

Alternatively import a SQL dump: run `php artisan schema:dump` locally, or export
your local DB, and import via cPanel → **phpMyAdmin**.

## Step 6 — Permissions
Ensure these are writable by PHP (File Manager → Permissions, `755`/`775`):
```
storage/                      (and all subfolders)
bootstrap/cache/
```

## Step 7 — Done
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
