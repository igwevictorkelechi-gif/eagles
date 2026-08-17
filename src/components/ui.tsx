import { ReactNode, useEffect } from "react";

export function Spinner() {
  return (
    <div className="flex items-center justify-center py-16">
      <div className="h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-brand-600" />
    </div>
  );
}

export function PageHeader({ title, subtitle, action }: { title: string; subtitle?: string; action?: ReactNode }) {
  return (
    <div className="mb-6 flex flex-wrap items-end justify-between gap-3">
      <div>
        <h1 className="text-2xl font-bold text-slate-900">{title}</h1>
        {subtitle && <p className="mt-1 text-sm text-slate-500">{subtitle}</p>}
      </div>
      {action}
    </div>
  );
}

export function Stat({ label, value, hint, accent }: { label: string; value: ReactNode; hint?: string; accent?: string }) {
  return (
    <div className="card p-5">
      <p className="text-sm font-medium text-slate-500">{label}</p>
      <p className={`mt-2 text-3xl font-bold ${accent || "text-slate-900"}`}>{value}</p>
      {hint && <p className="mt-1 text-xs text-slate-400">{hint}</p>}
    </div>
  );
}

export function Modal({ open, onClose, title, children }: { open: boolean; onClose: () => void; title: string; children: ReactNode }) {
  useEffect(() => {
    function onEsc(e: KeyboardEvent) { if (e.key === "Escape") onClose(); }
    if (open) document.addEventListener("keydown", onEsc);
    return () => document.removeEventListener("keydown", onEsc);
  }, [open, onClose]);
  if (!open) return null;
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" onClick={onClose}>
      <div className="card w-full max-w-lg p-6" onClick={(e) => e.stopPropagation()}>
        <div className="mb-4 flex items-center justify-between">
          <h3 className="text-lg font-semibold text-slate-900">{title}</h3>
          <button onClick={onClose} className="text-slate-400 hover:text-slate-700">✕</button>
        </div>
        {children}
      </div>
    </div>
  );
}

export function Field({ label, children }: { label: string; children: ReactNode }) {
  return (
    <div>
      <label className="label">{label}</label>
      {children}
    </div>
  );
}

export function Badge({ status }: { status: string }) {
  const map: Record<string, string> = {
    active: "bg-brand-100 text-brand-800",
    approved: "bg-brand-100 text-brand-800",
    paid: "bg-brand-100 text-brand-800",
    trial: "bg-blue-100 text-blue-800",
    submitted: "bg-blue-100 text-blue-800",
    partial: "bg-amber-100 text-amber-800",
    draft: "bg-slate-100 text-slate-700",
    pending: "bg-amber-100 text-amber-800",
    unpaid: "bg-red-100 text-red-700",
    rejected: "bg-red-100 text-red-700",
    suspended: "bg-red-100 text-red-700",
    expired: "bg-red-100 text-red-700",
    locked: "bg-purple-100 text-purple-700",
  };
  return <span className={`badge ${map[status] || "bg-slate-100 text-slate-700"}`}>{status}</span>;
}

export function EmptyState({ message, cta }: { message: string; cta?: ReactNode }) {
  return (
    <div className="card flex flex-col items-center gap-3 p-12 text-center">
      <div className="text-4xl">📭</div>
      <p className="text-slate-500">{message}</p>
      {cta}
    </div>
  );
}

export function Table({ head, children }: { head: string[]; children: ReactNode }) {
  return (
    <div className="card overflow-hidden">
      <div className="overflow-x-auto">
        <table className="w-full text-left text-sm">
          <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
            <tr>{head.map((h) => <th key={h} className="whitespace-nowrap px-4 py-3 font-semibold">{h}</th>)}</tr>
          </thead>
          <tbody className="divide-y divide-slate-100">{children}</tbody>
        </table>
      </div>
    </div>
  );
}

export function fmtMoney(n: number, currency = "NGN") {
  const sym = currency === "NGN" ? "₦" : currency === "USD" ? "$" : "";
  return sym + (n ?? 0).toLocaleString();
}
