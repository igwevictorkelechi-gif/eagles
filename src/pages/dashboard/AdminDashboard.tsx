import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import { Stat, Spinner, PageHeader, fmtMoney } from "@/components/ui";

interface Dash {
  counts: { students: number; teachers: number; classes: number; subjects: number; exams: number };
  finance: { income: number; expense: number; profit: number; outstanding: number };
  announcements: { title: string; body: string; created_at: string }[];
}

export default function AdminDashboard() {
  const { user, school } = useAuth();
  const [d, setD] = useState<Dash | null>(null);

  useEffect(() => { api.get<Dash>("/admin/dashboard").then(setD).catch(() => setD(null)); }, []);

  return (
    <div>
      <PageHeader
        title={`Welcome, ${user?.name?.split(" ")[0]} 👋`}
        subtitle={`Here's what's happening at ${school?.name || "your school"} today.`}
      />
      {!d ? <Spinner /> : (
        <div className="space-y-6">
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Stat label="Students" value={d.counts.students} accent="text-brand-600" />
            <Stat label="Teachers" value={d.counts.teachers} accent="text-blue-600" />
            <Stat label="Classes" value={d.counts.classes} accent="text-purple-600" />
            <Stat label="Examinations" value={d.counts.exams} accent="text-amber-600" />
          </div>

          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Stat label="Total Income" value={fmtMoney(d.finance.income)} accent="text-brand-600" />
            <Stat label="Total Expenses" value={fmtMoney(d.finance.expense)} accent="text-red-600" />
            <Stat label="Net Profit" value={fmtMoney(d.finance.profit)} accent={d.finance.profit >= 0 ? "text-brand-600" : "text-red-600"} />
            <Stat label="Outstanding Fees" value={fmtMoney(d.finance.outstanding)} accent="text-amber-600" />
          </div>

          <div className="card p-6">
            <h3 className="mb-4 text-lg font-semibold text-slate-900">Latest announcements</h3>
            {d.announcements.length === 0 ? (
              <p className="text-sm text-slate-400">No announcements yet.</p>
            ) : (
              <ul className="space-y-3">
                {d.announcements.map((a, i) => (
                  <li key={i} className="border-l-2 border-brand-500 pl-3">
                    <p className="font-medium text-slate-800">{a.title}</p>
                    <p className="text-sm text-slate-500">{a.body}</p>
                  </li>
                ))}
              </ul>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
