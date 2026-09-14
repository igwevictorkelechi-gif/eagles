# SAS — School Administration SaaS (Laravel)

A modern, **multi-tenant School Administration System** built with **Laravel** and
**MySQL**, designed to be hosted on **WhoGoHost** (or any cPanel/PHP shared host).
Multiple schools register and operate independently with fully isolated data,
users, academics, finances and subscriptions. Primary brand color: **green** 🌿

> This is a PHP/Laravel rewrite of the SAS product for shared hosting. Server-rendered
> Blade views, no Node.js and no build step required on the server.

## Tech stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12 (PHP 8.2+) |
| Views | Blade (server-rendered) + Tailwind CSS via CDN |
| Database | MySQL (production) / SQLite (local dev) |
| Auth | Laravel session auth, hashed passwords, role middleware |
| Hosting | WhoGoHost / any cPanel PHP host — see `DEPLOYMENT.md` |

## What's implemented

- **Multi-tenancy & security** — every school-owned row carries `school_id`; a
  `Tenantable` global scope constrains every query to the logged-in user's school,
  so no school can read another's data. Role middleware guards every area.
- **Auth** — school self-registration (creates tenant + admin + trial subscription),
  login, 7 roles (super_admin, school_admin, teacher, staff, student, parent, sales_staff).
- **Marketing website** — Home, Features, Pricing (live from DB), FAQ, Contact, Start Free Trial.
- **School Admin** — dashboard with live stats, Students, Teachers, Classes, Subjects,
  Results (approval workflow + auto-grading), Examinations, Announcements, Fees,
  Accounting, Inventory, POS (checkout → stock deduction → income transaction),
  Settings & per-school branding.
- **Super Admin** — platform dashboard (schools, MRR, trials), Schools
  (suspend/reactivate), Subscription Plans CRUD, Subscriptions.
- **Student/Parent portal** — results, fees, announcements.

## Quick start (local)

```bash
composer install
cp .env.example .env      # or keep the bundled .env (SQLite)
php artisan key:generate
touch database/database.sqlite   # if using SQLite locally
php artisan migrate --seed
php artisan serve         # http://127.0.0.1:8000
```

The bundled `.env` uses SQLite for zero-config local dev. `.env.example` is the
MySQL/WhoGoHost template.

## Demo accounts

All use password **`Password123!`**

| Role | Email |
|------|-------|
| Super Admin | `super@sas.app` |
| School Admin | `admin@greenfield.edu` |
| Teacher | `teacher@greenfield.edu` |
| Student | `student@greenfield.edu` |
| Sales Staff | `sales@greenfield.edu` |

## Project structure

```
app/Models/                 Eloquent models (+ Concerns/Tenantable global scope)
app/Http/Controllers/       Marketing, Auth, Admin/*, SuperAdmin/*, Student/*
app/Http/Middleware/        EnsureRole (role-based access)
database/migrations/        MySQL/SQLite schema (multi-tenant, school_id everywhere)
database/seeders/           Demo school, users, academics, fees, products, results
resources/views/            Blade — layouts, marketing, auth, app (dashboard), platform, student
routes/web.php              All routes + optional one-time /install route
```

## Deployment

See **[`DEPLOYMENT.md`](./DEPLOYMENT.md)** for the full WhoGoHost / cPanel guide
(build locally with `composer install`, upload with `vendor/`, point the document
root at `/public`, create a MySQL database, configure `.env`, run `migrate --seed`).

## Scope note

The SAS PRD is a large product (21 sections, 8 phases). This app delivers a
working, production-shaped **foundation** wired to real database records. Roadmap
items (CBT exam-taking runtime, report-card PDFs, payment webhooks, staff clock-in
UI, notifications, file uploads) have their database tables already in place so
they extend cleanly.
