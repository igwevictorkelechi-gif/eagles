import MarketingLayout from "@/components/MarketingLayout";

const groups = [
  {
    title: "Academic Management",
    items: ["Students, teachers & staff", "Classes, subjects, sessions & terms", "Enrollments & timetables", "Results entry, approval, lock & audit", "Report cards", "Assignments & announcements"],
  },
  {
    title: "CBT / Examinations",
    items: ["CBT, class tests, mocks & practice", "MCQ, true/false, multiple & short answer", "Scheduling, duration & attempts", "Randomization & negative marking", "Countdown timer & autosave", "Automatic grading"],
  },
  {
    title: "Fees & Accounting",
    items: ["Tuition, exam, transport & more", "Fee payments & receipts", "Income & expense tracking", "Outstanding fees reports", "Profit / loss statements", "Transaction history"],
  },
  {
    title: "Materials Sales / POS",
    items: ["Product & inventory management", "Cart, quantities & totals", "Multiple payment methods", "Auto stock deduction", "Receipts & sales history", "Low-stock thresholds"],
  },
  {
    title: "Attendance",
    items: ["Student attendance", "Staff clock-in / clock-out", "Hours-worked tracking", "Late & early-departure stats", "Device & location capture"],
  },
  {
    title: "SaaS & Security",
    items: ["Multi-tenant isolation (school_id)", "Role-based permissions", "Secure auth & password hashing", "Subscriptions, trials & plans", "Per-school branding", "Audit logs"],
  },
];

export default function Features() {
  return (
    <MarketingLayout>
      <div className="mx-auto max-w-6xl px-4 py-16">
        <div className="text-center">
          <h1 className="text-4xl font-extrabold text-slate-900">A complete school operating system</h1>
          <p className="mx-auto mt-3 max-w-2xl text-slate-500">
            Every module you need to run academics, finances and operations — designed to work together.
          </p>
        </div>
        <div className="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {groups.map((g) => (
            <div key={g.title} className="card p-6">
              <h3 className="text-lg font-semibold text-brand-700">{g.title}</h3>
              <ul className="mt-4 space-y-2">
                {g.items.map((it) => (
                  <li key={it} className="flex items-start gap-2 text-sm text-slate-600">
                    <span className="mt-0.5 text-brand-600">✓</span>{it}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </div>
    </MarketingLayout>
  );
}
