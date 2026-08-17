import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "@/context/AuthContext";
import { Logo } from "@/components/MarketingLayout";
import { Field } from "@/components/ui";

export default function StartTrial() {
  const { register } = useAuth();
  const nav = useNavigate();
  const [form, setForm] = useState({
    schoolName: "", adminFirstName: "", adminLastName: "", email: "", password: "",
  });
  const [err, setErr] = useState("");
  const [busy, setBusy] = useState(false);

  const set = (k: string) => (e: React.ChangeEvent<HTMLInputElement>) =>
    setForm({ ...form, [k]: e.target.value });

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr(""); setBusy(true);
    try {
      await register(form);
      nav("/app");
    } catch (e: any) {
      setErr(e.message || "Could not create your school");
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-50 to-slate-100 p-4">
      <div className="w-full max-w-md">
        <Link to="/" className="mb-6 flex justify-center"><Logo /></Link>
        <form onSubmit={submit} className="card space-y-4 p-8">
          <div>
            <h1 className="text-2xl font-bold text-slate-900">Start your free trial</h1>
            <p className="mt-1 text-sm text-slate-500">Create your school and admin account — 14 days free.</p>
          </div>
          {err && <div className="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{err}</div>}
          <Field label="School name">
            <input className="input" value={form.schoolName} onChange={set("schoolName")} required autoFocus placeholder="Greenfield Academy" />
          </Field>
          <div className="grid grid-cols-2 gap-3">
            <Field label="First name">
              <input className="input" value={form.adminFirstName} onChange={set("adminFirstName")} required placeholder="Grace" />
            </Field>
            <Field label="Last name">
              <input className="input" value={form.adminLastName} onChange={set("adminLastName")} placeholder="Adeyemi" />
            </Field>
          </div>
          <Field label="Work email">
            <input className="input" type="email" value={form.email} onChange={set("email")} required placeholder="you@school.edu" />
          </Field>
          <Field label="Password">
            <input className="input" type="password" value={form.password} onChange={set("password")} required minLength={6} placeholder="At least 6 characters" />
          </Field>
          <button className="btn-primary w-full" disabled={busy}>{busy ? "Creating…" : "Create school & start trial"}</button>
          <p className="text-center text-sm text-slate-500">
            Already have an account? <Link to="/login" className="font-semibold text-brand-700">Sign in</Link>
          </p>
        </form>
      </div>
    </div>
  );
}
