import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { useResource } from "@/components/ResourceManager";
import { PageHeader, Table, Modal, Field, Spinner, EmptyState, Badge } from "@/components/ui";
import { useAuth } from "@/context/AuthContext";

export default function Results() {
  const { user } = useAuth();
  const isAdmin = user?.role === "school_admin";
  const students = useResource("/admin/students");
  const subjects = useResource("/admin/subjects");
  const [rows, setRows] = useState<any[] | null>(null);
  const [modal, setModal] = useState(false);
  const [form, setForm] = useState<any>({ student_id: "", subject_id: "", ca_score: "", exam_score: "" });
  const [busy, setBusy] = useState(false);
  const [err, setErr] = useState("");

  async function load() {
    setRows(null);
    const r = await api.get<{ data: any[] }>("/admin/results");
    setRows(r.data);
  }
  useEffect(() => { load(); }, []);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true); setErr("");
    try {
      await api.post("/admin/results", {
        ...form, ca_score: Number(form.ca_score) || 0, exam_score: Number(form.exam_score) || 0,
      });
      setModal(false);
      setForm({ student_id: "", subject_id: "", ca_score: "", exam_score: "" });
      await load();
    } catch (e: any) { setErr(e.message); } finally { setBusy(false); }
  }

  async function setStatus(id: string, status: string) {
    await api.put(`/admin/results/${id}/status`, { status });
    await load();
  }

  return (
    <div>
      <PageHeader title="Results" subtitle="Enter, approve and lock student results." action={
        <button className="btn-primary" onClick={() => setModal(true)}>＋ Enter result</button>
      } />

      {rows === null ? <Spinner /> : rows.length === 0 ? (
        <EmptyState message="No results recorded yet." />
      ) : (
        <Table head={["Student", "Subject", "CA", "Exam", "Total", "Grade", "Status", ...(isAdmin ? ["Actions"] : [])]}>
          {rows.map((r) => (
            <tr key={r.id} className="hover:bg-slate-50">
              <td className="px-4 py-3">{r.student_name}</td>
              <td className="px-4 py-3">{r.subject_name || "—"}</td>
              <td className="px-4 py-3">{r.ca_score}</td>
              <td className="px-4 py-3">{r.exam_score}</td>
              <td className="px-4 py-3 font-semibold">{r.total_score}</td>
              <td className="px-4 py-3"><span className="font-bold text-brand-700">{r.grade}</span></td>
              <td className="px-4 py-3"><Badge status={r.status} /></td>
              {isAdmin && (
                <td className="px-4 py-3">
                  <div className="flex gap-2">
                    {r.status !== "approved" && <button className="text-xs font-medium text-brand-600 hover:underline" onClick={() => setStatus(r.id, "approved")}>Approve</button>}
                    {r.status !== "rejected" && <button className="text-xs font-medium text-red-600 hover:underline" onClick={() => setStatus(r.id, "rejected")}>Reject</button>}
                    {r.status !== "locked" && <button className="text-xs font-medium text-purple-600 hover:underline" onClick={() => setStatus(r.id, "locked")}>Lock</button>}
                  </div>
                </td>
              )}
            </tr>
          ))}
        </Table>
      )}

      <Modal open={modal} onClose={() => setModal(false)} title="Enter result">
        <form onSubmit={submit} className="space-y-3">
          {err && <div className="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{err}</div>}
          <Field label="Student">
            <select className="input" value={form.student_id} required onChange={(e) => setForm({ ...form, student_id: e.target.value })}>
              <option value="">Select student…</option>
              {students.map((s: any) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name} ({s.admission_no})</option>)}
            </select>
          </Field>
          <Field label="Subject">
            <select className="input" value={form.subject_id} required onChange={(e) => setForm({ ...form, subject_id: e.target.value })}>
              <option value="">Select subject…</option>
              {subjects.map((s: any) => <option key={s.id} value={s.id}>{s.name}</option>)}
            </select>
          </Field>
          <div className="grid grid-cols-2 gap-3">
            <Field label="CA score (max 40)"><input className="input" type="number" value={form.ca_score} onChange={(e) => setForm({ ...form, ca_score: e.target.value })} /></Field>
            <Field label="Exam score (max 60)"><input className="input" type="number" value={form.exam_score} onChange={(e) => setForm({ ...form, exam_score: e.target.value })} /></Field>
          </div>
          <p className="text-xs text-slate-400">Total and grade are calculated automatically on save.</p>
          <div className="flex justify-end gap-2 pt-2">
            <button type="button" className="btn-outline" onClick={() => setModal(false)}>Cancel</button>
            <button className="btn-primary" disabled={busy}>{busy ? "Saving…" : "Save result"}</button>
          </div>
        </form>
      </Modal>
    </div>
  );
}
