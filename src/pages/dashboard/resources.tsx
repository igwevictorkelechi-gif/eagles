import ResourceManager, { useResource } from "@/components/ResourceManager";
import { Badge, fmtMoney } from "@/components/ui";

export function StudentsPage() {
  const classes = useResource("/admin/classes");
  return (
    <ResourceManager
      title="Students" subtitle="Manage student records and enrollment." addLabel="Add student"
      endpoint="/admin/students"
      columns={[
        { key: "admission_no", label: "Adm. No" },
        { key: "name", label: "Name", render: (r) => `${r.first_name} ${r.last_name}` },
        { key: "gender", label: "Gender" },
        { key: "class", label: "Class", render: (r) => classes.find((c: any) => c.id === r.class_id)?.name || "—" },
        { key: "guardian_phone", label: "Guardian phone" },
        { key: "status", label: "Status", render: (r) => <Badge status={r.status} /> },
      ]}
      fields={[
        { name: "admission_no", label: "Admission No" },
        { name: "first_name", label: "First name", required: true },
        { name: "last_name", label: "Last name", required: true },
        { name: "gender", label: "Gender", type: "select", options: [{ value: "M", label: "Male" }, { value: "F", label: "Female" }] },
        { name: "date_of_birth", label: "Date of birth", type: "date" },
        { name: "class_id", label: "Class", type: "select", options: classes.map((c: any) => ({ value: c.id, label: c.name })) },
        { name: "guardian_name", label: "Guardian name" },
        { name: "guardian_phone", label: "Guardian phone" },
        { name: "status", label: "Status", type: "select", options: [{ value: "active", label: "Active" }, { value: "inactive", label: "Inactive" }] },
      ]}
    />
  );
}

export function TeachersPage() {
  return (
    <ResourceManager
      title="Teachers" subtitle="Manage teaching staff." addLabel="Add teacher"
      endpoint="/admin/teachers"
      columns={[
        { key: "name", label: "Name", render: (r) => `${r.first_name} ${r.last_name}` },
        { key: "email", label: "Email" },
        { key: "subject", label: "Subject" },
        { key: "employee_no", label: "Employee No" },
        { key: "status", label: "Status", render: (r) => <Badge status={r.status} /> },
      ]}
      fields={[
        { name: "first_name", label: "First name", required: true },
        { name: "last_name", label: "Last name", required: true },
        { name: "email", label: "Email" },
        { name: "phone", label: "Phone" },
        { name: "subject", label: "Subject" },
        { name: "employee_no", label: "Employee No" },
        { name: "status", label: "Status", type: "select", options: [{ value: "active", label: "Active" }, { value: "inactive", label: "Inactive" }] },
      ]}
    />
  );
}

export function ClassesPage() {
  const teachers = useResource("/admin/teachers");
  return (
    <ResourceManager
      title="Classes" subtitle="Set up classes and assign form teachers." addLabel="Add class"
      endpoint="/admin/classes"
      columns={[
        { key: "name", label: "Class" },
        { key: "level", label: "Level" },
        { key: "teacher", label: "Form teacher", render: (r) => { const t = teachers.find((x: any) => x.user_id === r.teacher_id || x.id === r.teacher_id); return t ? `${t.first_name} ${t.last_name}` : "—"; } },
        { key: "capacity", label: "Capacity" },
      ]}
      fields={[
        { name: "name", label: "Class name", required: true, placeholder: "JSS 1A" },
        { name: "level", label: "Level", placeholder: "Junior / Senior" },
        { name: "capacity", label: "Capacity", type: "number" },
      ]}
    />
  );
}

export function SubjectsPage() {
  return (
    <ResourceManager
      title="Subjects" subtitle="Manage the subjects offered." addLabel="Add subject"
      endpoint="/admin/subjects"
      columns={[{ key: "name", label: "Subject" }, { key: "code", label: "Code" }]}
      fields={[
        { name: "name", label: "Subject name", required: true },
        { name: "code", label: "Code", placeholder: "MTH" },
      ]}
    />
  );
}

export function AnnouncementsPage() {
  return (
    <ResourceManager
      title="Announcements" subtitle="Broadcast news to your school community." addLabel="New announcement"
      endpoint="/admin/announcements"
      columns={[
        { key: "title", label: "Title" },
        { key: "audience", label: "Audience" },
        { key: "created_at", label: "Date", render: (r) => new Date(r.created_at).toLocaleDateString() },
      ]}
      fields={[
        { name: "title", label: "Title", required: true },
        { name: "body", label: "Message", type: "textarea", required: true },
        { name: "audience", label: "Audience", type: "select", options: [
          { value: "all", label: "Everyone" }, { value: "students", label: "Students" },
          { value: "teachers", label: "Teachers" }, { value: "parents", label: "Parents" }, { value: "staff", label: "Staff" },
        ] },
      ]}
    />
  );
}

export function FeesPage() {
  return (
    <ResourceManager
      title="Fee Structures" subtitle="Define tuition and other fees." addLabel="Add fee"
      endpoint="/admin/fee-structures"
      columns={[
        { key: "name", label: "Fee" },
        { key: "category", label: "Category" },
        { key: "amount", label: "Amount", render: (r) => fmtMoney(r.amount) },
      ]}
      fields={[
        { name: "name", label: "Fee name", required: true },
        { name: "category", label: "Category", type: "select", options: [
          "tuition", "registration", "examination", "transport", "boarding", "uniform", "miscellaneous",
        ].map((v) => ({ value: v, label: v[0].toUpperCase() + v.slice(1) })) },
        { name: "amount", label: "Amount (₦)", type: "number", required: true },
      ]}
    />
  );
}

export function InventoryPage() {
  return (
    <ResourceManager
      title="Products & Inventory" subtitle="Manage sellable materials and stock." addLabel="Add product"
      endpoint="/admin/products"
      columns={[
        { key: "name", label: "Product" },
        { key: "category", label: "Category" },
        { key: "selling_price", label: "Price", render: (r) => fmtMoney(r.selling_price) },
        { key: "stock_qty", label: "Stock", render: (r) => (
          <span className={r.stock_qty <= r.low_stock ? "font-semibold text-red-600" : ""}>{r.stock_qty}{r.stock_qty <= r.low_stock ? " ⚠" : ""}</span>
        ) },
      ]}
      fields={[
        { name: "name", label: "Product name", required: true },
        { name: "sku", label: "SKU" },
        { name: "category", label: "Category" },
        { name: "purchase_price", label: "Purchase price (₦)", type: "number" },
        { name: "selling_price", label: "Selling price (₦)", type: "number", required: true },
        { name: "stock_qty", label: "Stock quantity", type: "number" },
        { name: "low_stock", label: "Low-stock threshold", type: "number" },
      ]}
    />
  );
}

export function ExamsPage() {
  const subjects = useResource("/admin/subjects");
  const classes = useResource("/admin/classes");
  return (
    <ResourceManager
      title="Examinations" subtitle="Create CBT and other examinations." addLabel="Create exam"
      endpoint="/admin/exams"
      columns={[
        { key: "title", label: "Title" },
        { key: "type", label: "Type" },
        { key: "duration_mins", label: "Duration", render: (r) => `${r.duration_mins} min` },
        { key: "pass_mark", label: "Pass mark" },
        { key: "status", label: "Status", render: (r) => <Badge status={r.status} /> },
      ]}
      fields={[
        { name: "title", label: "Exam title", required: true },
        { name: "type", label: "Type", type: "select", options: [
          "cbt", "class_test", "mock", "practice", "internal", "entrance",
        ].map((v) => ({ value: v, label: v.replace("_", " ") })) },
        { name: "subject_id", label: "Subject", type: "select", options: subjects.map((s: any) => ({ value: s.id, label: s.name })) },
        { name: "class_id", label: "Class", type: "select", options: classes.map((c: any) => ({ value: c.id, label: c.name })) },
        { name: "duration_mins", label: "Duration (minutes)", type: "number" },
        { name: "question_count", label: "Question count", type: "number" },
        { name: "pass_mark", label: "Pass mark (%)", type: "number" },
        { name: "attempts", label: "Allowed attempts", type: "number" },
        { name: "instructions", label: "Instructions", type: "textarea" },
        { name: "status", label: "Status", type: "select", options: [
          { value: "draft", label: "Draft" }, { value: "published", label: "Published" }, { value: "closed", label: "Closed" },
        ] },
      ]}
    />
  );
}

export function ExpensesPage() {
  return (
    <ResourceManager
      title="Accounting" subtitle="Record income and expenses." addLabel="Add transaction"
      endpoint="/admin/expenses"
      columns={[
        { key: "type", label: "Type", render: (r) => <Badge status={r.type === "income" ? "approved" : "unpaid"} /> },
        { key: "category", label: "Category" },
        { key: "description", label: "Description" },
        { key: "amount", label: "Amount", render: (r) => fmtMoney(r.amount) },
        { key: "date", label: "Date" },
      ]}
      fields={[
        { name: "type", label: "Type", type: "select", required: true, options: [
          { value: "income", label: "Income" }, { value: "expense", label: "Expense" },
        ] },
        { name: "category", label: "Category", placeholder: "salaries / utilities / supplies" },
        { name: "description", label: "Description" },
        { name: "amount", label: "Amount (₦)", type: "number", required: true },
        { name: "date", label: "Date", type: "date" },
      ]}
    />
  );
}
