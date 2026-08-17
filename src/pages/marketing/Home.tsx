import { Link } from "react-router-dom";
import MarketingLayout from "@/components/MarketingLayout";

const modules = [
  { icon: "🎓", title: "Academics", desc: "Students, classes, subjects, sessions, terms, enrollments and results." },
  { icon: "🖥️", title: "CBT Examinations", desc: "Computer-based tests, mocks and practice exams with auto-grading." },
  { icon: "🕒", title: "Attendance", desc: "Student attendance plus staff clock-in / clock-out with hours tracking." },
  { icon: "💳", title: "Fees & Accounting", desc: "Fee structures, payments, income, expenses and profit / loss reports." },
  { icon: "🛒", title: "POS & Inventory", desc: "Sell books, uniforms and materials with stock and receipts." },
  { icon: "📊", title: "Reports & Analytics", desc: "Academic, financial, staff and inventory insights at a glance." },
];

export default function Home() {
  return (
    <MarketingLayout>
      {/* Hero */}
      <section className="relative overflow-hidden bg-gradient-to-b from-brand-50 to-white">
        <div className="mx-auto max-w-6xl px-4 py-20 text-center md:py-28">
          <span className="badge bg-brand-100 text-brand-800">Multi-tenant School SaaS</span>
          <h1 className="mx-auto mt-4 max-w-3xl text-4xl font-extrabold leading-tight text-slate-900 md:text-6xl">
            Run your entire school on <span className="text-brand-600">one platform</span>
          </h1>
          <p className="mx-auto mt-5 max-w-2xl text-lg text-slate-600">
            SAS is a complete school operating system — academics, CBT exams, attendance,
            fees, accounting, POS and administration, with isolated data for every school.
          </p>
          <div className="mt-8 flex flex-wrap items-center justify-center gap-3">
            <Link to="/start" className="btn-primary px-6 py-3 text-base">Start Free</Link>
            <Link to="/pricing" className="btn-outline px-6 py-3 text-base">View Pricing</Link>
          </div>
          <p className="mt-4 text-sm text-slate-400">No credit card required · 14-day trial · Green by default 🌿</p>
        </div>
      </section>

      {/* Modules */}
      <section className="mx-auto max-w-6xl px-4 py-16">
        <div className="mb-10 text-center">
          <h2 className="text-3xl font-bold text-slate-900">Everything a modern school needs</h2>
          <p className="mt-2 text-slate-500">One login for admins, teachers, students, parents and sales staff.</p>
        </div>
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {modules.map((m) => (
            <div key={m.title} className="card p-6 transition hover:shadow-md">
              <div className="text-3xl">{m.icon}</div>
              <h3 className="mt-3 text-lg font-semibold text-slate-900">{m.title}</h3>
              <p className="mt-1 text-sm text-slate-500">{m.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Roles band */}
      <section className="bg-slate-900 py-16 text-white">
        <div className="mx-auto max-w-6xl px-4">
          <h2 className="text-center text-3xl font-bold">Built for every role</h2>
          <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {[
              ["Super Admin", "Manage schools, plans, pricing, trials and platform analytics."],
              ["School Admin", "Full control of one school — students, staff, finances and branding."],
              ["Teacher / Staff", "Enter results, run exams, take attendance and clock in / out."],
              ["Student", "Take CBT exams, view results, fees, timetable and announcements."],
              ["Parent", "Follow children's results, attendance, fees and payments."],
              ["Sales Staff", "Operate the POS, manage inventory and print receipts."],
            ].map(([t, d]) => (
              <div key={t} className="rounded-xl border border-white/10 bg-white/5 p-5">
                <h3 className="font-semibold text-brand-300">{t}</h3>
                <p className="mt-1 text-sm text-slate-300">{d}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="mx-auto max-w-4xl px-4 py-20 text-center">
        <h2 className="text-3xl font-bold text-slate-900">Ready to digitize your school?</h2>
        <p className="mt-3 text-slate-500">Set up your school in minutes and invite your team.</p>
        <Link to="/start" className="btn-primary mt-6 px-6 py-3 text-base">Start your free trial</Link>
      </section>
    </MarketingLayout>
  );
}
