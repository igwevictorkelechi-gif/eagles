@extends('layouts.app')
@section('title', 'Subscriptions')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Subscriptions</h1>
    <p class="mt-1 text-sm text-slate-500">Manage school subscriptions and billing status.</p></div>
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr>
            <th class="px-4 py-3 font-semibold">School</th><th class="px-4 py-3 font-semibold">Plan</th>
            <th class="px-4 py-3 font-semibold">Price</th><th class="px-4 py-3 font-semibold">Cycle</th>
            <th class="px-4 py-3 font-semibold">Status</th><th class="px-4 py-3 font-semibold">Set status</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($subs as $s)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">{{ $s->school_name }}</td><td class="px-4 py-3">{{ $s->plan_name }}</td>
                    <td class="px-4 py-3">₦{{ number_format($s->price_monthly) }}</td><td class="px-4 py-3">{{ $s->billing_cycle }}</td>
                    <td class="px-4 py-3">@include('partials.badge', ['status' => $s->status])</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('platform.subscriptions.update', $s->id) }}">@csrf @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-2 py-1 text-xs">
                                @foreach (['trial','active','expired','suspended','cancelled'] as $st)
                                    <option value="{{ $st }}" @selected($s->status===$st)>{{ $st }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">No subscriptions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
