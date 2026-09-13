@extends('layouts.app')
@section('title', 'Platform Overview')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Platform Overview</h1>
    <p class="mt-1 text-sm text-slate-500">Monitor the entire SAS platform.</p></div>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ([['Total Schools',$counts['schools'],'text-brand-600'],['Active Subscriptions',$counts['active'],'text-blue-600'],['Schools on Trial',$counts['trials'],'text-amber-600'],['Total Users',$counts['users'],'text-purple-600'],['Active Plans',$counts['plans'],'text-slate-700']] as [$l,$v,$c])
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">{{ $l }}</p><p class="mt-2 text-3xl font-bold {{ $c }}">{{ $v }}</p></div>
    @endforeach
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Monthly Recurring Revenue</p><p class="mt-2 text-3xl font-bold text-brand-600">₦{{ number_format($mrr) }}</p><p class="mt-1 text-xs text-slate-400">From active subscriptions</p></div>
</div>
@endsection
