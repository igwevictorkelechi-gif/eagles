import { ReactNode, useState } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useAuth } from "@/context/AuthContext";
import { roleLabel } from "@/lib/roles";

export interface NavLink { to: string; label: string; icon: string; }

export default function DashboardLayout({ nav, children }: { nav: NavLink[]; children: ReactNode }) {
  const { user, school, logout } = useAuth();
  const { pathname } = useLocation();
  const navigate = useNavigate();
  const [open, setOpen] = useState(false);

  function doLogout() { logout(); navigate("/login"); }

  const initials = (user?.name || "?").split(" ").map((s) => s[0]).slice(0, 2).join("").toUpperCase();

  return (
    <div className="flex min-h-screen bg-slate-50">
      {/* Sidebar */}
      <aside className={`fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-200 bg-white transition-transform md:static md:translate-x-0 ${open ? "translate-x-0" : "-translate-x-full"}`}>
        <div className="flex h-16 items-center gap-2 border-b border-slate-100 px-5">
          <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">S</div>
          <div className="min-w-0">
            <p className="truncate text-sm font-bold text-slate-900">{school?.name || "SAS Platform"}</p>
            <p className="text-xs text-slate-400">{user && roleLabel[user.role]}</p>
          </div>
        </div>
        <nav className="flex flex-col gap-0.5 overflow-y-auto p-3" style={{ maxHeight: "calc(100vh - 4rem)" }}>
          {nav.map((n) => {
            const active = pathname === n.to || (n.to !== "/app" && n.to !== "/platform" && pathname.startsWith(n.to));
            return (
              <Link
                key={n.to}
                to={n.to}
                onClick={() => setOpen(false)}
                className={`flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition ${
                  active ? "bg-brand-50 text-brand-700" : "text-slate-600 hover:bg-slate-100"
                }`}
              >
                <span className="w-5 text-center">{n.icon}</span>{n.label}
              </Link>
            );
          })}
        </nav>
      </aside>

      {open && <div className="fixed inset-0 z-30 bg-slate-900/30 md:hidden" onClick={() => setOpen(false)} />}

      {/* Main */}
      <div className="flex min-w-0 flex-1 flex-col">
        <header className="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4">
          <button className="btn-ghost md:hidden" onClick={() => setOpen(true)}>☰</button>
          <div className="hidden md:block" />
          <div className="flex items-center gap-3">
            <div className="text-right">
              <p className="text-sm font-semibold text-slate-800">{user?.name}</p>
              <p className="text-xs text-slate-400">{user && roleLabel[user.role]}</p>
            </div>
            <div className="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{initials}</div>
            <button onClick={doLogout} className="btn-outline !px-3 !py-1.5 text-xs">Logout</button>
          </div>
        </header>
        <main className="flex-1 p-4 md:p-8">{children}</main>
      </div>
    </div>
  );
}
