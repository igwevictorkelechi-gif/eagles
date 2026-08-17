import { ReactNode } from "react";
import { Link, useLocation } from "react-router-dom";

const navItems = [
  { to: "/", label: "Home" },
  { to: "/features", label: "Features" },
  { to: "/pricing", label: "Pricing" },
  { to: "/faq", label: "FAQ" },
  { to: "/contact", label: "Contact" },
];

export function Logo({ light }: { light?: boolean }) {
  return (
    <div className="flex items-center gap-2">
      <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">S</div>
      <span className={`text-lg font-extrabold ${light ? "text-white" : "text-slate-900"}`}>SAS</span>
    </div>
  );
}

export default function MarketingLayout({ children }: { children: ReactNode }) {
  const { pathname } = useLocation();
  return (
    <div className="min-h-screen bg-white">
      <header className="sticky top-0 z-40 border-b border-slate-100 bg-white/90 backdrop-blur">
        <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
          <Link to="/"><Logo /></Link>
          <nav className="hidden items-center gap-1 md:flex">
            {navItems.map((n) => (
              <Link
                key={n.to}
                to={n.to}
                className={`rounded-lg px-3 py-2 text-sm font-medium transition ${
                  pathname === n.to ? "text-brand-700" : "text-slate-600 hover:text-slate-900"
                }`}
              >
                {n.label}
              </Link>
            ))}
          </nav>
          <div className="flex items-center gap-2">
            <Link to="/login" className="btn-ghost hidden sm:inline-flex">Sign in</Link>
            <Link to="/start" className="btn-primary">Start Free</Link>
          </div>
        </div>
      </header>

      <main>{children}</main>

      <footer className="mt-24 border-t border-slate-100 bg-slate-50">
        <div className="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:grid-cols-2 md:grid-cols-4">
          <div>
            <Logo />
            <p className="mt-3 max-w-xs text-sm text-slate-500">
              The complete school operating system — academics, exams, fees, POS and more, in one platform.
            </p>
          </div>
          <div>
            <h4 className="mb-3 text-sm font-semibold text-slate-900">Product</h4>
            <ul className="space-y-2 text-sm text-slate-500">
              <li><Link to="/features" className="hover:text-brand-700">Features</Link></li>
              <li><Link to="/pricing" className="hover:text-brand-700">Pricing</Link></li>
              <li><Link to="/start" className="hover:text-brand-700">Start free trial</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="mb-3 text-sm font-semibold text-slate-900">Company</h4>
            <ul className="space-y-2 text-sm text-slate-500">
              <li><Link to="/faq" className="hover:text-brand-700">FAQ</Link></li>
              <li><Link to="/contact" className="hover:text-brand-700">Contact</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="mb-3 text-sm font-semibold text-slate-900">Get started</h4>
            <Link to="/start" className="btn-primary">Start Free</Link>
          </div>
        </div>
        <div className="border-t border-slate-200 py-4 text-center text-xs text-slate-400">
          © {new Date().getFullYear()} SAS — School Administration System. All rights reserved.
        </div>
      </footer>
    </div>
  );
}
