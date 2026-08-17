import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import MarketingLayout from "@/components/MarketingLayout";
import { api } from "@/lib/api";
import { fmtMoney, Spinner } from "@/components/ui";

interface Plan {
  id: string; name: string; code: string; price_monthly: number; currency: string;
  max_students: number | null; max_teachers: number | null; features: string; is_custom: number;
}

export default function Pricing() {
  const [plans, setPlans] = useState<Plan[] | null>(null);

  useEffect(() => {
    api.get<{ data: Plan[] }>("/public/plans").then((r) => setPlans(r.data)).catch(() => setPlans([]));
  }, []);

  return (
    <MarketingLayout>
      <div className="mx-auto max-w-6xl px-4 py-16">
        <div className="text-center">
          <h1 className="text-4xl font-extrabold text-slate-900">Simple, transparent pricing</h1>
          <p className="mt-3 text-slate-500">Start free. Upgrade as your school grows. Prices are configurable per platform.</p>
        </div>

        {!plans ? <Spinner /> : (
          <div className="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            {plans.map((p) => {
              const feats: string[] = (() => { try { return JSON.parse(p.features); } catch { return []; } })();
              const featured = p.code === "professional";
              return (
                <div key={p.id} className={`card flex flex-col p-6 ${featured ? "ring-2 ring-brand-500" : ""}`}>
                  {featured && <span className="badge mb-3 w-fit bg-brand-100 text-brand-800">Most popular</span>}
                  <h3 className="text-lg font-bold text-slate-900">{p.name}</h3>
                  <div className="mt-3">
                    {p.is_custom ? (
                      <span className="text-3xl font-extrabold text-slate-900">Custom</span>
                    ) : (
                      <>
                        <span className="text-3xl font-extrabold text-slate-900">{fmtMoney(p.price_monthly, p.currency)}</span>
                        <span className="text-sm text-slate-400">/month</span>
                      </>
                    )}
                  </div>
                  <ul className="mt-5 flex-1 space-y-2 text-sm text-slate-600">
                    <li className="flex gap-2"><span className="text-brand-600">✓</span>{p.max_students ? `Up to ${p.max_students} students` : "Unlimited students"}</li>
                    <li className="flex gap-2"><span className="text-brand-600">✓</span>{p.max_teachers ? `Up to ${p.max_teachers} teachers` : "Unlimited teachers"}</li>
                    {feats.slice(0, 5).map((f) => (
                      <li key={f} className="flex gap-2 capitalize"><span className="text-brand-600">✓</span>{f.replace(/_/g, " ")}</li>
                    ))}
                  </ul>
                  <Link to="/start" className={`mt-6 ${featured ? "btn-primary" : "btn-outline"}`}>
                    {p.is_custom ? "Contact sales" : "Start free"}
                  </Link>
                </div>
              );
            })}
          </div>
        )}
        <p className="mt-8 text-center text-sm text-slate-400">
          All plans include a configurable 7 / 14 / 30-day trial. Payments via Flutterwave, Paystack or Stripe.
        </p>
      </div>
    </MarketingLayout>
  );
}
