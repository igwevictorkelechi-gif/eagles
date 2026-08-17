import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { PageHeader, Table, Spinner, Modal, Field, Badge, fmtMoney } from "@/components/ui";

export default function Plans() {
  const [rows, setRows] = useState<any[] | null>(null);
  const [modal, setModal] = useState(false);
  const [editing, setEditing] = useState<any | null>(null);
  const [form, setForm] = useState<any>({});
  const [busy, setBusy] = useState(false);
  const [err, setErr] = useState("");

  async function load() {
    setRows(null);
    const r = await api.get<{ data: any[] }>("/superadmin/plans");
    setRows(r.data);
  }
  useEffect(() => { load(); }, []);

  function openNew() { setEditing(null); setForm({ currency: "NGN", is_active: true }); setErr(""); setModal(true); }
  function openEdit(p: any) { setEditing(p); setForm({ ...p, is_active: !!p.is_active, is_custom: !!p.is_custom }); setErr(""); setModal(true); }

  async function save(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true); setErr("");
    const payload = {
      name: form.name, code: form.code, price_monthly: Number(form.price_monthly) || 0, currency: form.currency || "NGN",
      max_students: form.max_students ? Number(form.max_students) : null,
      max_teachers: form.max_teachers ? Number(form.max_teachers) : null,
      is_custom: !!form.is_custom, is_active: form.is_active !== false, sort_order: Number(form.sort_order) || 0,
    };
    try {
      if (editing) await api.put(`/superadmin/plans/${editing.id}`, payload);
      else await api.post("/superadmin/plans", payload);
      setModal(false);
      await load();
    } catch (e: any) { setErr(e.message); } finally { setBusy(false); }
  }

  async function del(id: string) {
    if (!confirm("Deactivate this plan?")) return;
    await api.del(`/superadmin/plans/${id}`);
    await load();
  }

  return (
    <div>
      <PageHeader title="Subscription Plans" subtitle="Configure pricing, limits and availability." action={
        <button className="btn-primary" onClick={openNew}>＋ New plan</button>
      } />
      {rows === null ? <Spinner /> : (
        <Table head={["Plan", "Code", "Price", "Max students", "Max teachers", "Status", "Actions"]}>
          {rows.map((p) => (
            <tr key={p.id} className="hover:bg-slate-50">
              <td className="px-4 py-3 font-medium">{p.name}</td>
              <td className="px-4 py-3 text-slate-500">{p.code}</td>
              <td className="px-4 py-3">{p.is_custom ? "Custom" : fmtMoney(p.price_monthly, p.currency)}</td>
              <td className="px-4 py-3">{p.max_students ?? "∞"}</td>
              <td className="px-4 py-3">{p.max_teachers ?? "∞"}</td>
              <td className="px-4 py-3"><Badge status={p.is_active ? "active" : "suspended"} /></td>
              <td className="px-4 py-3">
                <div className="flex gap-2">
                  <button className="btn-ghost !px-2 !py-1 text-xs" onClick={() => openEdit(p)}>Edit</button>
                  <button className="text-xs font-medium text-red-600 hover:underline" onClick={() => del(p.id)}>Deactivate</button>
                </div>
              </td>
            </tr>
          ))}
        </Table>
      )}

      <Modal open={modal} onClose={() => setModal(false)} title={editing ? "Edit plan" : "New plan"}>
        <form onSubmit={save} className="space-y-3">
          {err && <div className="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{err}</div>}
          <div className="grid grid-cols-2 gap-3">
            <Field label="Name"><input className="input" value={form.name || ""} required onChange={(e) => setForm({ ...form, name: e.target.value })} /></Field>
            <Field label="Code"><input className="input" value={form.code || ""} required disabled={!!editing} onChange={(e) => setForm({ ...form, code: e.target.value })} /></Field>
            <Field label="Price / month"><input className="input" type="number" value={form.price_monthly ?? ""} onChange={(e) => setForm({ ...form, price_monthly: e.target.value })} /></Field>
            <Field label="Currency"><input className="input" value={form.currency || "NGN"} onChange={(e) => setForm({ ...form, currency: e.target.value })} /></Field>
            <Field label="Max students"><input className="input" type="number" value={form.max_students ?? ""} placeholder="blank = unlimited" onChange={(e) => setForm({ ...form, max_students: e.target.value })} /></Field>
            <Field label="Max teachers"><input className="input" type="number" value={form.max_teachers ?? ""} placeholder="blank = unlimited" onChange={(e) => setForm({ ...form, max_teachers: e.target.value })} /></Field>
            <Field label="Sort order"><input className="input" type="number" value={form.sort_order ?? ""} onChange={(e) => setForm({ ...form, sort_order: e.target.value })} /></Field>
          </div>
          <div className="flex gap-6 pt-1">
            <label className="flex items-center gap-2 text-sm"><input type="checkbox" checked={!!form.is_custom} onChange={(e) => setForm({ ...form, is_custom: e.target.checked })} /> Custom pricing</label>
            <label className="flex items-center gap-2 text-sm"><input type="checkbox" checked={form.is_active !== false} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} /> Active</label>
          </div>
          <div className="flex justify-end gap-2 pt-2">
            <button type="button" className="btn-outline" onClick={() => setModal(false)}>Cancel</button>
            <button className="btn-primary" disabled={busy}>{busy ? "Saving…" : "Save plan"}</button>
          </div>
        </form>
      </Modal>
    </div>
  );
}
