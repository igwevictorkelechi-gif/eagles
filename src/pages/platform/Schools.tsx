import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { PageHeader, Table, Spinner, Badge, EmptyState } from "@/components/ui";

export default function Schools() {
  const [rows, setRows] = useState<any[] | null>(null);

  async function load() {
    setRows(null);
    const r = await api.get<{ data: any[] }>("/superadmin/schools");
    setRows(r.data);
  }
  useEffect(() => { load(); }, []);

  async function toggle(id: string, current: string) {
    const status = current === "suspended" ? "active" : "suspended";
    await api.put(`/superadmin/schools/${id}/status`, { status });
    await load();
  }

  return (
    <div>
      <PageHeader title="Schools" subtitle="All schools registered on the platform." />
      {rows === null ? <Spinner /> : rows.length === 0 ? <EmptyState message="No schools registered yet." /> : (
        <Table head={["School", "Email", "Plan", "Subscription", "Students", "Status", "Actions"]}>
          {rows.map((s) => (
            <tr key={s.id} className="hover:bg-slate-50">
              <td className="px-4 py-3 font-medium text-slate-800">{s.name}</td>
              <td className="px-4 py-3 text-slate-500">{s.email || "—"}</td>
              <td className="px-4 py-3">{s.plan_name || "—"}</td>
              <td className="px-4 py-3">{s.sub_status ? <Badge status={s.sub_status} /> : "—"}</td>
              <td className="px-4 py-3">{s.students}</td>
              <td className="px-4 py-3"><Badge status={s.status} /></td>
              <td className="px-4 py-3">
                <button className={`text-xs font-medium hover:underline ${s.status === "suspended" ? "text-brand-600" : "text-red-600"}`}
                  onClick={() => toggle(s.id, s.status)}>
                  {s.status === "suspended" ? "Reactivate" : "Suspend"}
                </button>
              </td>
            </tr>
          ))}
        </Table>
      )}
    </div>
  );
}
