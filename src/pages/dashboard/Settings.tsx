import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import { PageHeader, Field, Spinner } from "@/components/ui";

export default function Settings() {
  const { refresh } = useAuth();
  const [school, setSchool] = useState<any | null>(null);
  const [saved, setSaved] = useState(false);
  const [busy, setBusy] = useState(false);

  useEffect(() => { api.get<{ data: any }>("/admin/school").then((r) => setSchool(r.data)); }, []);

  function set(k: string, v: any) { setSchool({ ...school, [k]: v }); }

  async function save(e: React.FormEvent) {
    e.preventDefault();
    setBusy(true); setSaved(false);
    try {
      await api.put("/admin/school", {
        name: school.name, short_name: school.short_name, email: school.email, phone: school.phone,
        address: school.address, website: school.website, logo_url: school.logo_url,
        primary_color: school.primary_color, secondary_color: school.secondary_color,
      });
      document.documentElement.style.setProperty("--brand", school.primary_color);
      setSaved(true);
      await refresh();
    } catch (e: any) { alert(e.message); } finally { setBusy(false); }
  }

  if (!school) return <Spinner />;

  return (
    <div className="max-w-3xl">
      <PageHeader title="School Settings & Branding" subtitle="Customize how your school appears across SAS." />
      <form onSubmit={save} className="card space-y-5 p-6">
        {saved && <div className="rounded-lg bg-brand-50 px-3 py-2 text-sm text-brand-700">✓ Settings saved.</div>}
        <div className="grid gap-4 sm:grid-cols-2">
          <Field label="School name"><input className="input" value={school.name || ""} onChange={(e) => set("name", e.target.value)} /></Field>
          <Field label="Short name"><input className="input" value={school.short_name || ""} onChange={(e) => set("short_name", e.target.value)} /></Field>
          <Field label="Email"><input className="input" value={school.email || ""} onChange={(e) => set("email", e.target.value)} /></Field>
          <Field label="Phone"><input className="input" value={school.phone || ""} onChange={(e) => set("phone", e.target.value)} /></Field>
          <Field label="Website"><input className="input" value={school.website || ""} onChange={(e) => set("website", e.target.value)} /></Field>
          <Field label="Logo URL"><input className="input" value={school.logo_url || ""} onChange={(e) => set("logo_url", e.target.value)} placeholder="https://…" /></Field>
        </div>
        <Field label="Address"><input className="input" value={school.address || ""} onChange={(e) => set("address", e.target.value)} /></Field>

        <div className="grid gap-4 sm:grid-cols-2">
          <Field label="Primary color">
            <div className="flex items-center gap-3">
              <input type="color" className="h-10 w-14 rounded border border-slate-300" value={school.primary_color || "#16a34a"} onChange={(e) => set("primary_color", e.target.value)} />
              <input className="input" value={school.primary_color || ""} onChange={(e) => set("primary_color", e.target.value)} />
            </div>
          </Field>
          <Field label="Secondary color">
            <div className="flex items-center gap-3">
              <input type="color" className="h-10 w-14 rounded border border-slate-300" value={school.secondary_color || "#15803d"} onChange={(e) => set("secondary_color", e.target.value)} />
              <input className="input" value={school.secondary_color || ""} onChange={(e) => set("secondary_color", e.target.value)} />
            </div>
          </Field>
        </div>

        <div className="flex justify-end">
          <button className="btn-primary" disabled={busy}>{busy ? "Saving…" : "Save settings"}</button>
        </div>
      </form>
    </div>
  );
}
