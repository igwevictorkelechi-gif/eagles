@extends('layouts.marketing')
@section('title', 'Pricing — SAS')
@section('content')
<div class="mx-auto max-w-6xl px-4 py-16">
    <div class="text-center"><h1 class="text-4xl font-extrabold text-slate-900">Simple, transparent pricing</h1>
        <p class="mt-3 text-slate-500">Start free. Upgrade as your school grows. Prices are configurable per platform.</p></div>
    <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        @foreach ($plans as $p)
            @php $featured = $p->code === 'professional'; @endphp
            <div class="flex flex-col rounded-xl border border-slate-200 bg-white p-6 shadow-sm {{ $featured ? 'ring-2 ring-brand-500' : '' }}">
                @if ($featured)<span class="mb-3 inline-flex w-fit rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-medium text-brand-800">Most popular</span>@endif
                <h3 class="text-lg font-bold text-slate-900">{{ $p->name }}</h3>
                <div class="mt-3">
                    @if ($p->is_custom)<span class="text-3xl font-extrabold text-slate-900">Custom</span>
                    @else<span class="text-3xl font-extrabold text-slate-900">₦{{ number_format($p->price_monthly) }}</span><span class="text-sm text-slate-400">/month</span>@endif
                </div>
                <ul class="mt-5 flex-1 space-y-2 text-sm text-slate-600">
                    <li class="flex gap-2"><span class="text-brand-600">✓</span>{{ $p->max_students ? 'Up to '.$p->max_students.' students' : 'Unlimited students' }}</li>
                    <li class="flex gap-2"><span class="text-brand-600">✓</span>{{ $p->max_teachers ? 'Up to '.$p->max_teachers.' teachers' : 'Unlimited teachers' }}</li>
                    @foreach (array_slice($p->featureList(), 0, 5) as $f)<li class="flex gap-2 capitalize"><span class="text-brand-600">✓</span>{{ str_replace('_',' ',$f) }}</li>@endforeach
                </ul>
                <a href="{{ route('register') }}" class="mt-6 rounded-lg px-4 py-2 text-center text-sm font-semibold {{ $featured ? 'bg-brand-600 text-white hover:bg-brand-700' : 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50' }}">{{ $p->is_custom ? 'Contact sales' : 'Start free' }}</a>
            </div>
        @endforeach
    </div>
    <p class="mt-8 text-center text-sm text-slate-400">All plans include a configurable 7 / 14 / 30-day trial. Payments via Flutterwave, Paystack or Stripe.</p>
</div>
@endsection
