-- SAS — School Administration SaaS
-- Multi-tenant schema. Every school-owned record carries school_id.
-- D1 / SQLite dialect.

-- ============================================================
-- Platform: subscription plans (managed by Super Admin)
-- ============================================================
CREATE TABLE IF NOT EXISTS subscription_plans (
  id            TEXT PRIMARY KEY,
  name          TEXT NOT NULL,
  code          TEXT NOT NULL UNIQUE,        -- free | starter | professional | enterprise
  price_monthly INTEGER NOT NULL DEFAULT 0,  -- in minor units / whole currency, configurable
  currency      TEXT NOT NULL DEFAULT 'NGN',
  max_students  INTEGER,                     -- NULL = unlimited
  max_teachers  INTEGER,
  max_staff     INTEGER,
  storage_mb    INTEGER,
  features      TEXT NOT NULL DEFAULT '[]',  -- JSON array of enabled module codes
  is_custom     INTEGER NOT NULL DEFAULT 0,  -- enterprise / custom pricing
  is_active     INTEGER NOT NULL DEFAULT 1,
  sort_order    INTEGER NOT NULL DEFAULT 0,
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);

-- ============================================================
-- Tenants: schools
-- ============================================================
CREATE TABLE IF NOT EXISTS schools (
  id            TEXT PRIMARY KEY,
  name          TEXT NOT NULL,
  short_name    TEXT,
  slug          TEXT NOT NULL UNIQUE,
  email         TEXT,
  phone         TEXT,
  address       TEXT,
  website       TEXT,
  logo_url      TEXT,
  favicon_url   TEXT,
  primary_color   TEXT NOT NULL DEFAULT '#16a34a',
  secondary_color TEXT NOT NULL DEFAULT '#15803d',
  status        TEXT NOT NULL DEFAULT 'active',  -- active | suspended
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);

-- ============================================================
-- Subscriptions (one active per school)
-- ============================================================
CREATE TABLE IF NOT EXISTS subscriptions (
  id            TEXT PRIMARY KEY,
  school_id     TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  plan_id       TEXT NOT NULL REFERENCES subscription_plans(id),
  status        TEXT NOT NULL DEFAULT 'trial',   -- trial | active | expired | suspended | cancelled
  billing_cycle TEXT NOT NULL DEFAULT 'monthly',
  trial_ends_at TEXT,
  current_period_end TEXT,
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_subscriptions_school ON subscriptions(school_id);

-- ============================================================
-- Users (all roles). super_admin has school_id = NULL.
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
  id            TEXT PRIMARY KEY,
  school_id     TEXT REFERENCES schools(id) ON DELETE CASCADE,
  role          TEXT NOT NULL,   -- super_admin | school_admin | teacher | staff | student | parent | sales_staff
  first_name    TEXT NOT NULL,
  last_name     TEXT NOT NULL,
  email         TEXT NOT NULL,
  phone         TEXT,
  password_hash TEXT NOT NULL,
  photo_url     TEXT,
  is_active     INTEGER NOT NULL DEFAULT 1,
  email_verified INTEGER NOT NULL DEFAULT 0,
  last_login_at TEXT,
  created_at    TEXT NOT NULL DEFAULT (datetime('now')),
  UNIQUE (school_id, email)
);
CREATE INDEX IF NOT EXISTS idx_users_school ON users(school_id);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- ============================================================
-- Academic structure
-- ============================================================
CREATE TABLE IF NOT EXISTS sessions_academic (
  id         TEXT PRIMARY KEY,
  school_id  TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name       TEXT NOT NULL,          -- e.g. 2025/2026
  is_current INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_sessions_school ON sessions_academic(school_id);

CREATE TABLE IF NOT EXISTS terms (
  id         TEXT PRIMARY KEY,
  school_id  TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  session_id TEXT REFERENCES sessions_academic(id) ON DELETE CASCADE,
  name       TEXT NOT NULL,          -- First / Second / Third Term
  is_current INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_terms_school ON terms(school_id);

CREATE TABLE IF NOT EXISTS classes (
  id         TEXT PRIMARY KEY,
  school_id  TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name       TEXT NOT NULL,          -- e.g. JSS1A
  level      TEXT,
  teacher_id TEXT REFERENCES users(id) ON DELETE SET NULL,
  capacity   INTEGER,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_classes_school ON classes(school_id);

CREATE TABLE IF NOT EXISTS subjects (
  id         TEXT PRIMARY KEY,
  school_id  TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name       TEXT NOT NULL,
  code       TEXT,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_subjects_school ON subjects(school_id);

-- Students (extends a user of role=student)
CREATE TABLE IF NOT EXISTS students (
  id            TEXT PRIMARY KEY,
  school_id     TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  user_id       TEXT REFERENCES users(id) ON DELETE SET NULL,
  admission_no  TEXT,
  first_name    TEXT NOT NULL,
  last_name     TEXT NOT NULL,
  gender        TEXT,
  date_of_birth TEXT,
  class_id      TEXT REFERENCES classes(id) ON DELETE SET NULL,
  guardian_name TEXT,
  guardian_phone TEXT,
  status        TEXT NOT NULL DEFAULT 'active',
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_students_school ON students(school_id);
CREATE INDEX IF NOT EXISTS idx_students_class ON students(class_id);

-- Teachers/staff extension record
CREATE TABLE IF NOT EXISTS teachers (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  user_id     TEXT REFERENCES users(id) ON DELETE SET NULL,
  first_name  TEXT NOT NULL,
  last_name   TEXT NOT NULL,
  email       TEXT,
  phone       TEXT,
  subject     TEXT,
  employee_no TEXT,
  status      TEXT NOT NULL DEFAULT 'active',
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_teachers_school ON teachers(school_id);

-- ============================================================
-- Attendance & clock logs
-- ============================================================
CREATE TABLE IF NOT EXISTS student_attendance (
  id         TEXT PRIMARY KEY,
  school_id  TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  student_id TEXT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
  class_id   TEXT REFERENCES classes(id) ON DELETE SET NULL,
  date       TEXT NOT NULL,
  status     TEXT NOT NULL DEFAULT 'present', -- present | absent | late
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_sattend_school ON student_attendance(school_id);

CREATE TABLE IF NOT EXISTS clock_logs (
  id         TEXT PRIMARY KEY,
  school_id  TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  user_id    TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  clock_in   TEXT,
  clock_out  TEXT,
  hours      REAL,
  device     TEXT,
  location   TEXT,
  date       TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_clocklogs_school ON clock_logs(school_id);

-- ============================================================
-- Results
-- ============================================================
CREATE TABLE IF NOT EXISTS results (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  student_id  TEXT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
  subject_id  TEXT REFERENCES subjects(id) ON DELETE SET NULL,
  class_id    TEXT REFERENCES classes(id) ON DELETE SET NULL,
  term_id     TEXT REFERENCES terms(id) ON DELETE SET NULL,
  ca_score    REAL DEFAULT 0,
  exam_score  REAL DEFAULT 0,
  total_score REAL DEFAULT 0,
  grade       TEXT,
  remark      TEXT,
  status      TEXT NOT NULL DEFAULT 'draft', -- draft | submitted | approved | rejected | locked
  entered_by  TEXT REFERENCES users(id) ON DELETE SET NULL,
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_results_school ON results(school_id);

-- ============================================================
-- CBT / Examinations
-- ============================================================
CREATE TABLE IF NOT EXISTS exams (
  id            TEXT PRIMARY KEY,
  school_id     TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  title         TEXT NOT NULL,
  type          TEXT NOT NULL DEFAULT 'cbt', -- cbt | class_test | mock | practice | internal | entrance
  subject_id    TEXT REFERENCES subjects(id) ON DELETE SET NULL,
  class_id      TEXT REFERENCES classes(id) ON DELETE SET NULL,
  instructions  TEXT,
  duration_mins INTEGER NOT NULL DEFAULT 30,
  question_count INTEGER NOT NULL DEFAULT 0,
  pass_mark     INTEGER NOT NULL DEFAULT 50,
  attempts      INTEGER NOT NULL DEFAULT 1,
  randomize     INTEGER NOT NULL DEFAULT 0,
  negative_mark REAL NOT NULL DEFAULT 0,
  starts_at     TEXT,
  ends_at       TEXT,
  status        TEXT NOT NULL DEFAULT 'draft', -- draft | published | closed
  created_by    TEXT REFERENCES users(id) ON DELETE SET NULL,
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_exams_school ON exams(school_id);

CREATE TABLE IF NOT EXISTS exam_questions (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  exam_id     TEXT NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
  type        TEXT NOT NULL DEFAULT 'mcq', -- mcq | true_false | multiple | short
  text        TEXT NOT NULL,
  options     TEXT NOT NULL DEFAULT '[]',  -- JSON array of {id,text}
  correct     TEXT NOT NULL DEFAULT '[]',  -- JSON array of correct option ids / answer
  marks       INTEGER NOT NULL DEFAULT 1,
  sort_order  INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_examq_exam ON exam_questions(exam_id);

CREATE TABLE IF NOT EXISTS exam_attempts (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  exam_id     TEXT NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
  student_id  TEXT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
  answers     TEXT NOT NULL DEFAULT '{}',   -- JSON map questionId -> answer
  score       REAL,
  status      TEXT NOT NULL DEFAULT 'in_progress', -- in_progress | submitted | graded
  started_at  TEXT NOT NULL DEFAULT (datetime('now')),
  submitted_at TEXT
);
CREATE INDEX IF NOT EXISTS idx_attempts_school ON exam_attempts(school_id);

-- ============================================================
-- Assignments & announcements
-- ============================================================
CREATE TABLE IF NOT EXISTS assignments (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  title       TEXT NOT NULL,
  description TEXT,
  class_id    TEXT REFERENCES classes(id) ON DELETE SET NULL,
  subject_id  TEXT REFERENCES subjects(id) ON DELETE SET NULL,
  due_date    TEXT,
  created_by  TEXT REFERENCES users(id) ON DELETE SET NULL,
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_assignments_school ON assignments(school_id);

CREATE TABLE IF NOT EXISTS announcements (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  title       TEXT NOT NULL,
  body        TEXT NOT NULL,
  audience    TEXT NOT NULL DEFAULT 'all', -- all | students | teachers | parents | staff
  created_by  TEXT REFERENCES users(id) ON DELETE SET NULL,
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_announcements_school ON announcements(school_id);

-- ============================================================
-- Fees & accounting
-- ============================================================
CREATE TABLE IF NOT EXISTS fee_structures (
  id         TEXT PRIMARY KEY,
  school_id  TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name       TEXT NOT NULL,     -- Tuition, Registration, Exam, Transport...
  category   TEXT NOT NULL DEFAULT 'tuition',
  amount     INTEGER NOT NULL DEFAULT 0,
  class_id   TEXT REFERENCES classes(id) ON DELETE SET NULL,
  term_id    TEXT REFERENCES terms(id) ON DELETE SET NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_fees_school ON fee_structures(school_id);

CREATE TABLE IF NOT EXISTS student_fees (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  student_id  TEXT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
  fee_id      TEXT REFERENCES fee_structures(id) ON DELETE SET NULL,
  amount      INTEGER NOT NULL DEFAULT 0,
  amount_paid INTEGER NOT NULL DEFAULT 0,
  status      TEXT NOT NULL DEFAULT 'unpaid', -- unpaid | partial | paid
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_studentfees_school ON student_fees(school_id);

CREATE TABLE IF NOT EXISTS fee_payments (
  id            TEXT PRIMARY KEY,
  school_id     TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  student_fee_id TEXT REFERENCES student_fees(id) ON DELETE SET NULL,
  student_id    TEXT REFERENCES students(id) ON DELETE SET NULL,
  amount        INTEGER NOT NULL DEFAULT 0,
  method        TEXT NOT NULL DEFAULT 'cash',
  reference     TEXT,
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_feepay_school ON fee_payments(school_id);

CREATE TABLE IF NOT EXISTS transactions (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  type        TEXT NOT NULL,  -- income | expense
  category    TEXT,
  description TEXT,
  amount      INTEGER NOT NULL DEFAULT 0,
  date        TEXT NOT NULL DEFAULT (date('now')),
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_transactions_school ON transactions(school_id);

-- ============================================================
-- Inventory & POS
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
  id            TEXT PRIMARY KEY,
  school_id     TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  name          TEXT NOT NULL,
  sku           TEXT,
  category      TEXT,
  purchase_price INTEGER NOT NULL DEFAULT 0,
  selling_price INTEGER NOT NULL DEFAULT 0,
  stock_qty     INTEGER NOT NULL DEFAULT 0,
  low_stock     INTEGER NOT NULL DEFAULT 5,
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_products_school ON products(school_id);

CREATE TABLE IF NOT EXISTS sales (
  id          TEXT PRIMARY KEY,
  school_id   TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  reference   TEXT,
  total       INTEGER NOT NULL DEFAULT 0,
  payment_method TEXT NOT NULL DEFAULT 'cash',
  customer    TEXT,
  sold_by     TEXT REFERENCES users(id) ON DELETE SET NULL,
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_sales_school ON sales(school_id);

CREATE TABLE IF NOT EXISTS sale_items (
  id         TEXT PRIMARY KEY,
  school_id  TEXT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
  sale_id    TEXT NOT NULL REFERENCES sales(id) ON DELETE CASCADE,
  product_id TEXT REFERENCES products(id) ON DELETE SET NULL,
  name       TEXT NOT NULL,
  qty        INTEGER NOT NULL DEFAULT 1,
  price      INTEGER NOT NULL DEFAULT 0,
  subtotal   INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_saleitems_sale ON sale_items(sale_id);

-- ============================================================
-- Audit logs
-- ============================================================
CREATE TABLE IF NOT EXISTS audit_logs (
  id          TEXT PRIMARY KEY,
  school_id   TEXT REFERENCES schools(id) ON DELETE CASCADE,
  user_id     TEXT REFERENCES users(id) ON DELETE SET NULL,
  action      TEXT NOT NULL,
  resource    TEXT,
  resource_id TEXT,
  old_value   TEXT,
  new_value   TEXT,
  ip          TEXT,
  device      TEXT,
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_audit_school ON audit_logs(school_id);
