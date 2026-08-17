import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { PageHeader, Stat, Spinner, fmtMoney } from "@/components/ui";

interface Data {
  counts: { schools: number; active: number; trials: number; users: number; plans: number };
  mrr: number;
}

export default function PlatformDashboard() {
  const [d, setD] = useState<Data | null>(null);
  useEffect(() => { api.get<Data>("/superadmin/dashboard").then(setD).catch(() => setD(null)); }, []);

  return (
    <div>
      <PageHeader title="Platform Overview" subtitle="Monitor the entire SAS platform." />
      {!d ? <Spinner /> : (
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <Stat label="Total Schools" value={d.counts.schools} accent="text-brand-600" />
          <Stat label="Active Subscriptions" value={d.counts.active} accent="text-blue-600" />
          <Stat label="Schools on Trial" value={d.counts.trials} accent="text-amber-600" />
          <Stat label="Total Users" value={d.counts.users} accent="text-purple-600" />
          <Stat label="Active Plans" value={d.counts.plans} accent="text-slate-700" />
          <Stat label="Monthly Recurring Revenue" value={fmtMoney(d.mrr)} accent="text-brand-600" hint="From active subscriptions" />
        </div>
      )}
    </div>
  );
}
