@extends('layouts.app')
@section('title', 'Billing & Subscription')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Billing & Subscription</h1>
    <p class="mt-1 text-sm text-slate-500">Manage your plan and pay securely via Paystack or CheqPay.</p>
</div>

@php $currency = $currentPlan->currency ?? 'NGN'; $sym = $currency === 'NGN' ? '₦' : ''; @endphp

<div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">Current plan</p>
            <p class="text-xl font-bold text-slate-900">{{ $currentPlan->name ?? '—' }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-slate-500">Status</p>
            @include('partials.badge', ['status' => $subscription->status ?? 'trial'])
        </div>
        @if ($subscription?->trial_ends_at && $subscription->status === 'trial')
            <div class="text-right"><p class="text-sm text-slate-500">Trial ends</p>
                <p class="font-medium text-slate-800">{{ \Illuminate\Support\Carbon::parse($subscription->trial_ends_at)->format('M j, Y') }}</p></div>
        @endif
        @if ($subscription?->current_period_end)
            <div class="text-right"><p class="text-sm text-slate-500">Renews / expires</p>
                <p class="font-medium text-slate-800">{{ \Illuminate\Support\Carbon::parse($subscription->current_period_end)->format('M j, Y') }}</p></div>
        @endif
    </div>
</div>

@if (count($gateways) === 0)
    <div class="mb-6 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800">
        No payment gateway is configured yet. Add your <strong>Paystack</strong> and/or <strong>CheqPay</strong> API keys to <code>.env</code> to enable paid upgrades.
    </div>
@endif

<h2 class="mb-3 text-lg font-semibold text-slate-900">Choose a plan</h2>
<div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
    @foreach ($plans as $plan)
        @php $isCurrent = $currentPlan && $currentPlan->id === $plan->id && ($subscription->status ?? '') === 'active'; @endphp
        <div class="flex flex-col rounded-xl border {{ $plan->code === 'professional' ? 'border-brand-500 ring-1 ring-brand-500' : 'border-slate-200' }} bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h3>
            <div class="mt-2">
                @if ($plan->is_custom)
                    <span class="text-2xl font-extrabold text-slate-900">Custom</span>
                @else
                    <span class="text-2xl font-extrabold text-slate-900">{{ $sym }}{{ number_format($plan->price_monthly) }}</span>
                    <span class="text-sm text-slate-400">/mo</span>
                @endif
            </div>
            <div class="mt-4 flex-1 text-sm text-slate-600">
                <p>{{ $plan->max_students ? 'Up to '.$plan->max_students.' students' : 'Unlimited students' }}</p>
                <p>{{ $plan->max_teachers ? 'Up to '.$plan->max_teachers.' teachers' : 'Unlimited teachers' }}</p>
            </div>

            @if ($isCurrent)
                <span class="mt-5 rounded-lg bg-brand-50 px-4 py-2 text-center text-sm font-semibold text-brand-700">Current plan</span>
            @elseif ($plan->is_custom)
                <a href="{{ route('contact') }}" class="mt-5 rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Contact sales</a>
            @elseif ((int) $plan->price_monthly === 0)
                <span class="mt-5 rounded-lg bg-slate-100 px-4 py-2 text-center text-sm font-semibold text-slate-500">Free</span>
            @elseif (count($gateways) === 0)
                <span class="mt-5 rounded-lg bg-slate-100 px-4 py-2 text-center text-sm font-semibold text-slate-400">Set up a gateway</span>
            @else
                <form method="POST" action="{{ route('app.billing.checkout') }}" class="mt-5 space-y-2">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($gateways as $g)
                            <button name="gateway" value="{{ $g->key() }}"
                                class="flex-1 rounded-lg bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700">
                                Pay · {{ $g->label() }}
                            </button>
                        @endforeach
                    </div>
                </form>
            @endif
        </div>
    @endforeach
</div>

<h2 class="mb-3 mt-8 text-lg font-semibold text-slate-900">Payment history</h2>
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr>
            <th class="px-4 py-2">Date</th><th class="px-4 py-2">Reference</th><th class="px-4 py-2">Gateway</th>
            <th class="px-4 py-2">Amount</th><th class="px-4 py-2">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($payments as $p)
                <tr>
                    <td class="px-4 py-2 text-slate-500">{{ optional($p->created_at)->format('M j, Y H:i') }}</td>
                    <td class="px-4 py-2 font-mono text-xs">{{ $p->reference }}</td>
                    <td class="px-4 py-2 capitalize">{{ $p->gateway }}</td>
                    <td class="px-4 py-2">{{ $sym }}{{ number_format($p->amount) }}</td>
                    <td class="px-4 py-2">@include('partials.badge', ['status' => $p->status === 'success' ? 'paid' : $p->status])</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
