-- SAS — Postgres schema (for Vercel + Supabase deployment).
-- Mirrors schema/migrations/0001_init.sql (D1/SQLite) in Postgres dialect.
-- All objects live in a dedicated `sas` schema for isolation.

CREATE SCHEMA IF NOT EXISTS sas;
SET search_path TO sas;

CREATE TABLE IF NOT EXISTS subscription_plans (
  id            text PRIMARY KEY,
  name          text NOT NULL,
  code          text NOT NULL UNIQUE,
  price_monthly integer NOT NULL DEFAULT 0,
  currency      text NOT NULL DEFAULT 'NGN',
  max_students  integer,
  max_teachers  integer,
  max_staff     integer,
  storage_mb    integer,
  features      text NOT NULL DEFAULT '[]',
  is_custom     integer NOT NULL DEFAULT 0,
  is_active     integer NOT NULL DEFAULT 1,
  sort_order    integer NOT NULL DEFAULT 0,
  created_at    timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS schools (
  id            text PRIMARY KEY,
  name          text NOT NULL,
  short_name    text,
  slug          text NOT NULL UNIQUE,
  email         text,
  phone         text,
  address       text,
  website       text,
  logo_url      text,
  favicon_url   text,
  primary_color   text NOT NULL DEFAULT '#16a34a',
  secondary_color text NOT NULL DEFAULT '#15803d',
  status        text NOT NULL DEFAULT 'active',
  created_at    timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS subscriptions (
  id            text PRIMARY KEY,
  school_id     text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  plan_id       text NOT NULL REFERENCES subscription_plans(id),
  status        text NOT NULL DEFAULT 'trial',
  billing_cycle text NOT NULL DEFAULT 'monthly',
  trial_ends_at text,
  current_period_end text,
  created_at    timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_subscriptions_school ON subscriptions(school_id);

CREATE TABLE IF NOT EXISTS users (
  id            text PRIMARY KEY,
  school_id     text REFERENCES schools(id) ON DELETE CASCADE,
  role          text NOT NULL,
  first_name    text NOT NULL,
  last_name     text NOT NULL,
  email         text NOT NULL,
  phone         text,
  password_hash text NOT NULL,
  photo_url     text,
  is_active     integer NOT NULL DEFAULT 1,
  email_verified integer NOT NULL DEFAULT 0,
  last_login_at text,
  created_at    timestamptz NOT NULL DEFAULT now(),
  UNIQUE (school_id, email)
);
CREATE INDEX IF NOT EXISTS idx_users_school ON users(school_id);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

CREATE TABLE IF NOT EXISTS sessions_academic (
  id         text PRIMARY KEY,
  school_id  text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name       text NOT NULL,
  is_current integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_sessions_school ON sessions_academic(school_id);

CREATE TABLE IF NOT EXISTS terms (
  id         text PRIMARY KEY,
  school_id  text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  session_id text REFERENCES sessions_academic(id) ON DELETE CASCADE,
  name       text NOT NULL,
  is_current integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_terms_school ON terms(school_id);

CREATE TABLE IF NOT EXISTS classes (
  id         text PRIMARY KEY,
  school_id  text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name       text NOT NULL,
  level      text,
  teacher_id text REFERENCES users(id) ON DELETE SET NULL,
  capacity   integer,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_classes_school ON classes(school_id);

CREATE TABLE IF NOT EXISTS subjects (
  id         text PRIMARY KEY,
  school_id  text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name       text NOT NULL,
  code       text,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_subjects_school ON subjects(school_id);

CREATE TABLE IF NOT EXISTS students (
  id            text PRIMARY KEY,
  school_id     text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  user_id       text REFERENCES users(id) ON DELETE SET NULL,
  admission_no  text,
  first_name    text NOT NULL,
  last_name     text NOT NULL,
  gender        text,
  date_of_birth text,
  class_id      text REFERENCES classes(id) ON DELETE SET NULL,
  guardian_name text,
  guardian_phone text,
  status        text NOT NULL DEFAULT 'active',
  created_at    timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_students_school ON students(school_id);
CREATE INDEX IF NOT EXISTS idx_students_class ON students(class_id);

CREATE TABLE IF NOT EXISTS teachers (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  user_id     text REFERENCES users(id) ON DELETE SET NULL,
  first_name  text NOT NULL,
  last_name   text NOT NULL,
  email       text,
  phone       text,
  subject     text,
  employee_no text,
  status      text NOT NULL DEFAULT 'active',
  created_at  timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_teachers_school ON teachers(school_id);

CREATE TABLE IF NOT EXISTS student_attendance (
  id         text PRIMARY KEY,
  school_id  text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  student_id text NOT NULL REFERENCES students(id) ON DELETE CASCADE,
  class_id   text REFERENCES classes(id) ON DELETE SET NULL,
  date       text NOT NULL,
  status     text NOT NULL DEFAULT 'present',
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_sattend_school ON student_attendance(school_id);

CREATE TABLE IF NOT EXISTS clock_logs (
  id         text PRIMARY KEY,
  school_id  text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  user_id    text NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  clock_in   text,
  clock_out  text,
  hours      real,
  device     text,
  location   text,
  date       text NOT NULL,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_clocklogs_school ON clock_logs(school_id);

CREATE TABLE IF NOT EXISTS results (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  student_id  text NOT NULL REFERENCES students(id) ON DELETE CASCADE,
  subject_id  text REFERENCES subjects(id) ON DELETE SET NULL,
  class_id    text REFERENCES classes(id) ON DELETE SET NULL,
  term_id     text REFERENCES terms(id) ON DELETE SET NULL,
  ca_score    real DEFAULT 0,
  exam_score  real DEFAULT 0,
  total_score real DEFAULT 0,
  grade       text,
  remark      text,
  status      text NOT NULL DEFAULT 'draft',
  entered_by  text REFERENCES users(id) ON DELETE SET NULL,
  created_at  timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_results_school ON results(school_id);

CREATE TABLE IF NOT EXISTS exams (
  id            text PRIMARY KEY,
  school_id     text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  title         text NOT NULL,
  type          text NOT NULL DEFAULT 'cbt',
  subject_id    text REFERENCES subjects(id) ON DELETE SET NULL,
  class_id      text REFERENCES classes(id) ON DELETE SET NULL,
  instructions  text,
  duration_mins integer NOT NULL DEFAULT 30,
  question_count integer NOT NULL DEFAULT 0,
  pass_mark     integer NOT NULL DEFAULT 50,
  attempts      integer NOT NULL DEFAULT 1,
  randomize     integer NOT NULL DEFAULT 0,
  negative_mark real NOT NULL DEFAULT 0,
  starts_at     text,
  ends_at       text,
  status        text NOT NULL DEFAULT 'draft',
  created_by    text REFERENCES users(id) ON DELETE SET NULL,
  created_at    timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_exams_school ON exams(school_id);

CREATE TABLE IF NOT EXISTS exam_questions (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  exam_id     text NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
  type        text NOT NULL DEFAULT 'mcq',
  text        text NOT NULL,
  options     text NOT NULL DEFAULT '[]',
  correct     text NOT NULL DEFAULT '[]',
  marks       integer NOT NULL DEFAULT 1,
  sort_order  integer NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_examq_exam ON exam_questions(exam_id);

CREATE TABLE IF NOT EXISTS exam_attempts (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  exam_id     text NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
  student_id  text NOT NULL REFERENCES students(id) ON DELETE CASCADE,
  answers     text NOT NULL DEFAULT '{}',
  score       real,
  status      text NOT NULL DEFAULT 'in_progress',
  started_at  timestamptz NOT NULL DEFAULT now(),
  submitted_at text
);
CREATE INDEX IF NOT EXISTS idx_attempts_school ON exam_attempts(school_id);

CREATE TABLE IF NOT EXISTS assignments (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  title       text NOT NULL,
  description text,
  class_id    text REFERENCES classes(id) ON DELETE SET NULL,
  subject_id  text REFERENCES subjects(id) ON DELETE SET NULL,
  due_date    text,
  created_by  text REFERENCES users(id) ON DELETE SET NULL,
  created_at  timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_assignments_school ON assignments(school_id);

CREATE TABLE IF NOT EXISTS announcements (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  title       text NOT NULL,
  body        text NOT NULL,
  audience    text NOT NULL DEFAULT 'all',
  created_by  text REFERENCES users(id) ON DELETE SET NULL,
  created_at  timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_announcements_school ON announcements(school_id);

CREATE TABLE IF NOT EXISTS fee_structures (
  id         text PRIMARY KEY,
  school_id  text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name       text NOT NULL,
  category   text NOT NULL DEFAULT 'tuition',
  amount     integer NOT NULL DEFAULT 0,
  class_id   text REFERENCES classes(id) ON DELETE SET NULL,
  term_id    text REFERENCES terms(id) ON DELETE SET NULL,
  created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_fees_school ON fee_structures(school_id);

CREATE TABLE IF NOT EXISTS student_fees (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  student_id  text NOT NULL REFERENCES students(id) ON DELETE CASCADE,
  fee_id      text REFERENCES fee_structures(id) ON DELETE SET NULL,
  amount      integer NOT NULL DEFAULT 0,
  amount_paid integer NOT NULL DEFAULT 0,
  status      text NOT NULL DEFAULT 'unpaid',
  created_at  timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_studentfees_school ON student_fees(school_id);

CREATE TABLE IF NOT EXISTS fee_payments (
  id            text PRIMARY KEY,
  school_id     text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  student_fee_id text REFERENCES student_fees(id) ON DELETE SET NULL,
  student_id    text REFERENCES students(id) ON DELETE SET NULL,
  amount        integer NOT NULL DEFAULT 0,
  method        text NOT NULL DEFAULT 'cash',
  reference     text,
  created_at    timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_feepay_school ON fee_payments(school_id);

CREATE TABLE IF NOT EXISTS transactions (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  type        text NOT NULL,
  category    text,
  description text,
  amount      integer NOT NULL DEFAULT 0,
  date        text NOT NULL DEFAULT to_char(now(), 'YYYY-MM-DD'),
  created_at  timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_transactions_school ON transactions(school_id);

CREATE TABLE IF NOT EXISTS products (
  id            text PRIMARY KEY,
  school_id     text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name          text NOT NULL,
  sku           text,
  category      text,
  purchase_price integer NOT NULL DEFAULT 0,
  selling_price integer NOT NULL DEFAULT 0,
  stock_qty     integer NOT NULL DEFAULT 0,
  low_stock     integer NOT NULL DEFAULT 5,
  created_at    timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_products_school ON products(school_id);

CREATE TABLE IF NOT EXISTS sales (
  id          text PRIMARY KEY,
  school_id   text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  reference   text,
  total       integer NOT NULL DEFAULT 0,
  payment_method text NOT NULL DEFAULT 'cash',
  customer    text,
  sold_by     text REFERENCES users(id) ON DELETE SET NULL,
  created_at  timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_sales_school ON sales(school_id);

CREATE TABLE IF NOT EXISTS sale_items (
  id         text PRIMARY KEY,
  school_id  text NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  sale_id    text NOT NULL REFERENCES sales(id) ON DELETE CASCADE,
  product_id text REFERENCES products(id) ON DELETE SET NULL,
  name       text NOT NULL,
  qty        integer NOT NULL DEFAULT 1,
  price      integer NOT NULL DEFAULT 0,
  subtotal   integer NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_saleitems_sale ON sale_items(sale_id);

CREATE TABLE IF NOT EXISTS audit_logs (
  id          text PRIMARY KEY,
  school_id   text REFERENCES schools(id) ON DELETE CASCADE,
  user_id     text REFERENCES users(id) ON DELETE SET NULL,
  action      text NOT NULL,
  resource    text,
  resource_id text,
  old_value   text,
  new_value   text,
  ip          text,
  device      text,
  created_at  timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX IF NOT EXISTS idx_audit_school ON audit_logs(school_id);
