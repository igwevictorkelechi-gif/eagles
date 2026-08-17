import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { PageHeader, Table, Spinner, Badge, EmptyState, fmtMoney } from "@/components/ui";

export default function Subscriptions() {
  const [rows, setRows] = useState<any[] | null>(null);

  async function load() {
    setRows(null);
    const r = await api.get<{ data: any[] }>("/superadmin/subscriptions");
    setRows(r.data);
  }
  useEffect(() => { load(); }, []);

  async function setStatus(id: string, status: string) {
    await api.put(`/superadmin/subscriptions/${id}`, { status });
    await load();
  }

  return (
    <div>
      <PageHeader title="Subscriptions" subtitle="Manage school subscriptions and billing status." />
      {rows === null ? <Spinner /> : rows.length === 0 ? <EmptyState message="No subscriptions yet." /> : (
        <Table head={["School", "Plan", "Price", "Cycle", "Status", "Actions"]}>
          {rows.map((s) => (
            <tr key={s.id} className="hover:bg-slate-50">
              <td className="px-4 py-3 font-medium">{s.school_name}</td>
              <td className="px-4 py-3">{s.plan_name}</td>
              <td className="px-4 py-3">{fmtMoney(s.price_monthly)}</td>
              <td className="px-4 py-3">{s.billing_cycle}</td>
              <td className="px-4 py-3"><Badge status={s.status} /></td>
              <td className="px-4 py-3">
                <select className="input !py-1 text-xs" value={s.status} onChange={(e) => setStatus(s.id, e.target.value)}>
                  {["trial", "active", "expired", "suspended", "cancelled"].map((st) => <option key={st} value={st}>{st}</option>)}
                </select>
              </td>
            </tr>
          ))}
        </Table>
      )}
    </div>
  );
}
