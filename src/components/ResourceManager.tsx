import { useEffect, useState, ReactNode } from "react";
import { api } from "@/lib/api";
import { PageHeader, Table, Modal, Field, Spinner, EmptyState } from "@/components/ui";

export interface FieldDef {
  name: string;
  label: string;
  type?: "text" | "number" | "date" | "textarea" | "select";
  options?: { value: string; label: string }[];
  required?: boolean;
  placeholder?: string;
}
export interface ColumnDef {
  key: string;
  label: string;
  render?: (row: any) => ReactNode;
}

interface Props {
  title: string;
  subtitle?: string;
  endpoint: string; // e.g. /admin/students
  columns: ColumnDef[];
  fields: FieldDef[];
  addLabel?: string;
  canEdit?: boolean;
  canDelete?: boolean;
}

export default function ResourceManager({ title, subtitle, endpoint, columns, fields, addLabel, canEdit = true, canDelete = true }: Props) {
  const [rows, setRows] = useState<any[] | null>(null);
  const [modal, setModal] = useState(false);
  const [editing, setEditing] = useState<any | null>(null);
  const [form, setForm] = useState<Record<string, any>>({});
  const [err, setErr] = useState("");
  const [busy, setBusy] = useState(false);

  async function load() {
    setRows(null);
    try {
      const r = await api.get<{ data: any[] }>(endpoint);
      setRows(r.data);
    } catch (e: any) {
      setErr(e.message);
      setRows([]);
    }
  }
  useEffect(() => { load(); /* eslint-disable-next-line */ }, [endpoint]);

  function openCreate() {
    setEditing(null);
    setForm({});
    setErr("");
    setModal(true);
  }
  function openEdit(row: any) {
    setEditing(row);
    const f: Record<string, any> = {};
    for (const fd of fields) f[fd.name] = row[fd.name] ?? "";
    setForm(f);
    setErr("");
    setModal(true);
  }

  async function save(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true); setErr("");
    // coerce numbers
    const payload: Record<string, any> = { ...form };
    for (const fd of fields) if (fd.type === "number" && payload[fd.name] !== "" && payload[fd.name] != null) payload[fd.name] = Number(payload[fd.name]);
    try {
      if (editing) await api.put(`${endpoint}/${editing.id}`, payload);
      else await api.post(endpoint, payload);
      setModal(false);
      await load();
    } catch (e: any) {
      setErr(e.message);
    } finally {
      setBusy(false);
    }
  }

  async function remove(row: any) {
    if (!confirm("Delete this record? This cannot be undone.")) return;
    try { await api.del(`${endpoint}/${row.id}`); await load(); }
    catch (e: any) { alert(e.message); }
  }

  const showActions = canEdit || canDelete;

  return (
    <div>
      <PageHeader
        title={title}
        subtitle={subtitle}
        action={<button className="btn-primary" onClick={openCreate}>＋ {addLabel || "Add new"}</button>}
      />

      {rows === null ? <Spinner /> : rows.length === 0 ? (
        <EmptyState message={`No ${title.toLowerCase()} yet.`} cta={<button className="btn-primary" onClick={openCreate}>Add the first one</button>} />
      ) : (
        <Table head={[...columns.map((c) => c.label), ...(showActions ? ["Actions"] : [])]}>
          {rows.map((row) => (
            <tr key={row.id} className="hover:bg-slate-50">
              {columns.map((col) => (
                <td key={col.key} className="whitespace-nowrap px-4 py-3 text-slate-700">
                  {col.render ? col.render(row) : (row[col.key] ?? "—")}
                </td>
              ))}
              {showActions && (
                <td className="whitespace-nowrap px-4 py-3">
                  <div className="flex gap-2">
                    {canEdit && <button className="btn-ghost !px-2 !py-1 text-xs" onClick={() => openEdit(row)}>Edit</button>}
                    {canDelete && <button className="text-xs font-medium text-red-600 hover:underline" onClick={() => remove(row)}>Delete</button>}
                  </div>
                </td>
              )}
            </tr>
          ))}
        </Table>
      )}

      <Modal open={modal} onClose={() => setModal(false)} title={editing ? `Edit ${title}` : `New ${title}`}>
        <form onSubmit={save} className="space-y-3">
          {err && <div className="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{err}</div>}
          <div className="grid gap-3 sm:grid-cols-2">
            {fields.map((fd) => (
              <div key={fd.name} className={fd.type === "textarea" ? "sm:col-span-2" : ""}>
                <Field label={fd.label}>
                  {fd.type === "select" ? (
                    <select className="input" value={form[fd.name] ?? ""} required={fd.required}
                      onChange={(e) => setForm({ ...form, [fd.name]: e.target.value })}>
                      <option value="">Select…</option>
                      {fd.options?.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
                    </select>
                  ) : fd.type === "textarea" ? (
                    <textarea className="input" rows={3} value={form[fd.name] ?? ""} required={fd.required} placeholder={fd.placeholder}
                      onChange={(e) => setForm({ ...form, [fd.name]: e.target.value })} />
                  ) : (
                    <input className="input" type={fd.type === "number" ? "number" : fd.type === "date" ? "date" : "text"}
                      value={form[fd.name] ?? ""} required={fd.required} placeholder={fd.placeholder}
                      onChange={(e) => setForm({ ...form, [fd.name]: e.target.value })} />
                  )}
                </Field>
              </div>
            ))}
          </div>
          <div className="flex justify-end gap-2 pt-2">
            <button type="button" className="btn-outline" onClick={() => setModal(false)}>Cancel</button>
            <button className="btn-primary" disabled={busy}>{busy ? "Saving…" : "Save"}</button>
          </div>
        </form>
      </Modal>
    </div>
  );
}

// Helper: load a resource list once (for populating selects).
export function useResource<T = any>(endpoint: string) {
  const [data, setData] = useState<T[]>([]);
  useEffect(() => {
    api.get<{ data: T[] }>(endpoint).then((r) => setData(r.data)).catch(() => setData([]));
  }, [endpoint]);
  return data;
}
