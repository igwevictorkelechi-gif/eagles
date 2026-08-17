import { useEffect, useState } from "react";
import { api } from "@/lib/api";
import { PageHeader, fmtMoney, Spinner, Modal } from "@/components/ui";

interface Product { id: string; name: string; selling_price: number; stock_qty: number; category?: string; }
interface CartItem { product_id: string; name: string; price: number; qty: number; }

export default function POS() {
  const [products, setProducts] = useState<Product[] | null>(null);
  const [cart, setCart] = useState<CartItem[]>([]);
  const [query, setQuery] = useState("");
  const [method, setMethod] = useState("cash");
  const [receipt, setReceipt] = useState<{ reference: string; total: number } | null>(null);
  const [busy, setBusy] = useState(false);

  async function load() {
    const r = await api.get<{ data: Product[] }>("/admin/products");
    setProducts(r.data);
  }
  useEffect(() => { load(); }, []);

  function add(p: Product) {
    setCart((c) => {
      const found = c.find((i) => i.product_id === p.id);
      if (found) return c.map((i) => i.product_id === p.id ? { ...i, qty: i.qty + 1 } : i);
      return [...c, { product_id: p.id, name: p.name, price: p.selling_price, qty: 1 }];
    });
  }
  function setQty(id: string, qty: number) {
    if (qty <= 0) return setCart((c) => c.filter((i) => i.product_id !== id));
    setCart((c) => c.map((i) => i.product_id === id ? { ...i, qty } : i));
  }
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);

  async function checkout() {
    if (!cart.length) return;
    setBusy(true);
    try {
      const r = await api.post<{ data: { reference: string; total: number } }>("/admin/sales", { items: cart, payment_method: method });
      setReceipt(r.data);
      setCart([]);
      await load();
    } catch (e: any) { alert(e.message); } finally { setBusy(false); }
  }

  const filtered = (products || []).filter((p) => p.name.toLowerCase().includes(query.toLowerCase()));

  return (
    <div>
      <PageHeader title="Point of Sale" subtitle="Sell materials and generate receipts." />
      <div className="grid gap-6 lg:grid-cols-3">
        {/* Products */}
        <div className="lg:col-span-2">
          <input className="input mb-4" placeholder="Search products…" value={query} onChange={(e) => setQuery(e.target.value)} />
          {products === null ? <Spinner /> : (
            <div className="grid gap-3 sm:grid-cols-2 md:grid-cols-3">
              {filtered.map((p) => (
                <button key={p.id} onClick={() => add(p)} disabled={p.stock_qty <= 0}
                  className="card p-4 text-left transition hover:border-brand-400 hover:shadow-md disabled:opacity-40">
                  <p className="font-semibold text-slate-800">{p.name}</p>
                  <p className="text-xs text-slate-400">{p.category}</p>
                  <p className="mt-2 font-bold text-brand-600">{fmtMoney(p.selling_price)}</p>
                  <p className="text-xs text-slate-400">Stock: {p.stock_qty}</p>
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Cart */}
        <div className="card flex h-fit flex-col p-5">
          <h3 className="mb-3 text-lg font-semibold text-slate-900">Cart</h3>
          {cart.length === 0 ? (
            <p className="py-8 text-center text-sm text-slate-400">Tap products to add them.</p>
          ) : (
            <ul className="space-y-3">
              {cart.map((i) => (
                <li key={i.product_id} className="flex items-center justify-between gap-2">
                  <div className="min-w-0">
                    <p className="truncate text-sm font-medium text-slate-800">{i.name}</p>
                    <p className="text-xs text-slate-400">{fmtMoney(i.price)}</p>
                  </div>
                  <div className="flex items-center gap-1">
                    <button className="btn-outline !px-2 !py-0.5" onClick={() => setQty(i.product_id, i.qty - 1)}>−</button>
                    <span className="w-6 text-center text-sm">{i.qty}</span>
                    <button className="btn-outline !px-2 !py-0.5" onClick={() => setQty(i.product_id, i.qty + 1)}>＋</button>
                  </div>
                  <span className="w-16 text-right text-sm font-semibold">{fmtMoney(i.price * i.qty)}</span>
                </li>
              ))}
            </ul>
          )}
          <div className="mt-4 border-t border-slate-100 pt-4">
            <div className="flex justify-between text-lg font-bold">
              <span>Total</span><span className="text-brand-600">{fmtMoney(total)}</span>
            </div>
            <select className="input mt-3" value={method} onChange={(e) => setMethod(e.target.value)}>
              <option value="cash">Cash</option>
              <option value="card">Card / POS</option>
              <option value="transfer">Bank transfer</option>
            </select>
            <button className="btn-primary mt-3 w-full" disabled={busy || !cart.length} onClick={checkout}>
              {busy ? "Processing…" : "Complete sale"}
            </button>
          </div>
        </div>
      </div>

      <Modal open={!!receipt} onClose={() => setReceipt(null)} title="Sale complete ✅">
        {receipt && (
          <div className="text-center">
            <p className="text-sm text-slate-500">Receipt reference</p>
            <p className="text-lg font-bold text-slate-900">{receipt.reference}</p>
            <p className="mt-4 text-3xl font-extrabold text-brand-600">{fmtMoney(receipt.total)}</p>
            <p className="mt-1 text-sm text-slate-400">Payment recorded and stock updated.</p>
            <button className="btn-primary mt-6 w-full" onClick={() => setReceipt(null)}>New sale</button>
          </div>
        )}
      </Modal>
    </div>
  );
}
