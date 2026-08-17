import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { PageHeader, Stat, Table, Spinner, Badge, fmtMoney } from "@/components/ui";

interface Data {
  student: { name: string; admission_no?: string; class?: string };
  results: { subject_name: string; total_score: number; grade: string; remark: string; status: string }[];
  fees: { amount: number; amount_paid: number; status: string }[];
  announcements: { title: string; body: string; created_at: string }[];
}

export default function StudentDashboard() {
  const [d, setD] = useState<Data | null>(null);
  useEffect(() => { api.get<Data>("/student/dashboard").then(setD).catch(() => setD(null)); }, []);

  if (!d) return <Spinner />;

  const outstanding = d.fees.reduce((s, f) => s + (f.amount - f.amount_paid), 0);
  const avg = d.results.length ? Math.round(d.results.reduce((s, r) => s + r.total_score, 0) / d.results.length) : 0;

  return (
    <div>
      <PageHeader title={`Hello, ${d.student.name.split(" ")[0]} 👋`}
        subtitle={[d.student.admission_no, d.student.class].filter(Boolean).join(" · ")} />

      <div className="mb-6 grid gap-4 sm:grid-cols-3">
        <Stat label="Subjects graded" value={d.results.length} accent="text-brand-600" />
        <Stat label="Average score" value={`${avg}%`} accent="text-blue-600" />
        <Stat label="Outstanding fees" value={fmtMoney(outstanding)} accent={outstanding > 0 ? "text-amber-600" : "text-brand-600"} />
      </div>

      <div className="grid gap-6 lg:grid-cols-2">
        <div>
          <h3 className="mb-3 text-lg font-semibold text-slate-900">My results</h3>
          {d.results.length === 0 ? (
            <div className="card p-6 text-sm text-slate-400">No approved results yet.</div>
          ) : (
            <Table head={["Subject", "Score", "Grade", "Remark"]}>
              {d.results.map((r, i) => (
                <tr key={i}>
                  <td className="px-4 py-3">{r.subject_name}</td>
                  <td className="px-4 py-3 font-semibold">{r.total_score}</td>
                  <td className="px-4 py-3"><span className="font-bold text-brand-700">{r.grade}</span></td>
                  <td className="px-4 py-3 text-slate-500">{r.remark}</td>
                </tr>
              ))}
            </Table>
          )}
        </div>

        <div>
          <h3 className="mb-3 text-lg font-semibold text-slate-900">Announcements</h3>
          <div className="card divide-y divide-slate-100">
            {d.announcements.length === 0 ? (
              <p className="p-6 text-sm text-slate-400">No announcements.</p>
            ) : d.announcements.map((a, i) => (
              <div key={i} className="p-4">
                <p className="font-medium text-slate-800">{a.title}</p>
                <p className="text-sm text-slate-500">{a.body}</p>
              </div>
            ))}
          </div>

          <h3 className="mb-3 mt-6 text-lg font-semibold text-slate-900">My fees</h3>
          {d.fees.length === 0 ? (
            <div className="card p-6 text-sm text-slate-400">No fees assigned.</div>
          ) : (
            <Table head={["Amount", "Paid", "Balance", "Status"]}>
              {d.fees.map((f, i) => (
                <tr key={i}>
                  <td className="px-4 py-3">{fmtMoney(f.amount)}</td>
                  <td className="px-4 py-3">{fmtMoney(f.amount_paid)}</td>
                  <td className="px-4 py-3">{fmtMoney(f.amount - f.amount_paid)}</td>
                  <td className="px-4 py-3"><Badge status={f.status} /></td>
                </tr>
              ))}
            </Table>
          )}
        </div>
      </div>
    </div>
  );
}
