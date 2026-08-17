import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "@/context/AuthContext";
import { Logo } from "@/components/MarketingLayout";
import { Field } from "@/components/ui";
import { roleHome } from "@/lib/roles";

export default function Login() {
  const { login } = useAuth();
  const nav = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [err, setErr] = useState("");
  const [busy, setBusy] = useState(false);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setErr(""); setBusy(true);
    try {
      const user = await login(email, password);
      nav(roleHome(user.role));
    } catch (e: any) {
      setErr(e.message || "Login failed");
    } finally {
      setBusy(false);
    }
  }

  function fill(em: string) { setEmail(em); setPassword("Password123!"); }

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-50 to-slate-100 p-4">
      <div className="w-full max-w-md">
        <Link to="/" className="mb-6 flex justify-center"><Logo /></Link>
        <form onSubmit={submit} className="card space-y-4 p-8">
          <div>
            <h1 className="text-2xl font-bold text-slate-900">Welcome back</h1>
            <p className="mt-1 text-sm text-slate-500">Sign in to your SAS account.</p>
          </div>
          {err && <div className="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{err}</div>}
          <Field label="Email">
            <input className="input" type="email" value={email} onChange={(e) => setEmail(e.target.value)} required autoFocus />
          </Field>
          <Field label="Password">
            <input className="input" type="password" value={password} onChange={(e) => setPassword(e.target.value)} required />
          </Field>
          <button className="btn-primary w-full" disabled={busy}>{busy ? "Signing in…" : "Sign in"}</button>
          <p className="text-center text-sm text-slate-500">
            No account? <Link to="/start" className="font-semibold text-brand-700">Start a free trial</Link>
          </p>
        </form>

        <div className="card mt-4 p-4 text-sm">
          <p className="mb-2 font-semibold text-slate-700">Demo accounts <span className="font-normal text-slate-400">(password: Password123!)</span></p>
          <div className="grid grid-cols-2 gap-2">
            {[
              ["Super Admin", "super@sas.app"],
              ["School Admin", "admin@greenfield.edu"],
              ["Teacher", "teacher@greenfield.edu"],
              ["Sales Staff", "sales@greenfield.edu"],
            ].map(([label, em]) => (
              <button key={em} type="button" onClick={() => fill(em)} className="btn-outline justify-start !py-1.5 text-xs">
                {label}
              </button>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
