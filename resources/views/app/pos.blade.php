@extends('layouts.app')
@section('title', 'Point of Sale')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Point of Sale</h1>
    <p class="mt-1 text-sm text-slate-500">Enter quantities, pick a payment method, and complete the sale.</p></div>

<form method="POST" action="{{ route('app.pos.checkout') }}">@csrf
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($products as $p)
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="font-semibold text-slate-800">{{ $p->name }}</p>
                <p class="text-xs text-slate-400">{{ $p->category }} · stock {{ $p->stock_qty }}</p>
                <p class="mt-1 font-bold text-brand-600">₦{{ number_format($p->selling_price) }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <label class="text-xs text-slate-500">Qty</label>
                    <input type="number" min="0" max="{{ $p->stock_qty }}" value="0" name="qty[{{ $p->id }}]"
                        class="w-20 rounded-lg border border-slate-300 px-2 py-1 text-sm">
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">No products yet — add some under Inventory.</p>
        @endforelse
    </div>

    <div class="mt-6 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <label class="text-sm font-medium text-slate-700">Payment method</label>
        <select name="payment_method" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="cash">Cash</option><option value="card">Card / POS</option><option value="transfer">Bank transfer</option>
        </select>
        <button class="rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Complete sale</button>
    </div>
</form>

<h3 class="mb-3 mt-8 text-lg font-semibold text-slate-900">Recent sales</h3>
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr>
            <th class="px-4 py-3 font-semibold">Reference</th><th class="px-4 py-3 font-semibold">Total</th>
            <th class="px-4 py-3 font-semibold">Method</th><th class="px-4 py-3 font-semibold">Date</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($sales as $s)
                <tr><td class="px-4 py-3 font-medium">{{ $s->reference }}</td>
                    <td class="px-4 py-3">₦{{ number_format($s->total) }}</td>
                    <td class="px-4 py-3">{{ $s->payment_method }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ optional($s->created_at)->format('M j, Y H:i') }}</td></tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No sales yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
