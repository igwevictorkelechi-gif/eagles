<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        return view('app.pos', [
            'products' => Product::orderBy('name')->get(),
            'sales' => Sale::orderByDesc('created_at')->limit(20)->get(),
        ]);
    }

    // Cart posted as items[]=product_id and qty[product_id]=n
    public function checkout(Request $request)
    {
        $qty = $request->input('qty', []);
        $method = $request->input('payment_method', 'cash');
        $ids = collect($qty)->filter(fn ($q) => (int) $q > 0)->keys();
        if ($ids->isEmpty()) {
            return redirect()->route('app.pos')->with('err', 'Add at least one item.');
        }

        $schoolId = Auth::user()->school_id;
        $ref = 'RCP-' . strtoupper(base_convert((string) time(), 10, 36));

        DB::transaction(function () use ($ids, $qty, $method, $schoolId, $ref) {
            $products = Product::whereIn('id', $ids->all())->get()->keyBy('id');
            $total = 0;
            $sale = Sale::create([
                'reference' => $ref,
                'total' => 0,
                'payment_method' => $method,
                'sold_by' => Auth::id(),
            ]);
            foreach ($ids as $pid) {
                $p = $products[$pid] ?? null;
                if (! $p) continue;
                $q = (int) $qty[$pid];
                $sub = $q * (int) $p->selling_price;
                $total += $sub;
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'qty' => $q,
                    'price' => $p->selling_price,
                    'subtotal' => $sub,
                ]);
                $p->decrement('stock_qty', $q);
            }
            $sale->update(['total' => $total]);
            Transaction::create([
                'type' => 'income',
                'category' => 'sales',
                'description' => 'POS sale ' . $ref,
                'amount' => $total,
                'date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('app.pos')->with('ok', 'Sale complete — receipt ' . $ref . '.');
    }
}
