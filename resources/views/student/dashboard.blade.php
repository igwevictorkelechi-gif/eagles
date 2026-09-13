@extends('layouts.app')
@section('title', 'Student Dashboard')
@section('content')
@php
    $outstanding = $fees->sum(fn ($f) => (int) $f->amount - (int) $f->amount_paid);
    $avg = $results->count() ? round($results->avg('total_score')) : 0;
@endphp
<div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Hello, {{ explode(' ', $user->name)[0] }} 👋</h1>
    <p class="mt-1 text-sm text-slate-500">{{ collect([$student->admission_no ?? null, $className])->filter()->implode(' · ') }}</p></div>

<div class="mb-6 grid gap-4 sm:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Subjects graded</p><p class="mt-2 text-3xl font-bold text-brand-600">{{ $results->count() }}</p></div>
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Average score</p><p class="mt-2 text-3xl font-bold text-blue-600">{{ $avg }}%</p></div>
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Outstanding fees</p><p class="mt-2 text-3xl font-bold {{ $outstanding>0?'text-amber-600':'text-brand-600' }}">₦{{ number_format($outstanding) }}</p></div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <h3 class="mb-3 text-lg font-semibold text-slate-900">My results</h3>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Subject</th><th class="px-4 py-3">Score</th><th class="px-4 py-3">Grade</th><th class="px-4 py-3">Remark</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($results as $r)
                        <tr><td class="px-4 py-3">{{ $r->subject_name }}</td><td class="px-4 py-3 font-semibold">{{ $r->total_score }}</td>
                            <td class="px-4 py-3 font-bold text-brand-700">{{ $r->grade }}</td><td class="px-4 py-3 text-slate-500">{{ $r->remark }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No approved results yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <h3 class="mb-3 text-lg font-semibold text-slate-900">Announcements</h3>
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm divide-y divide-slate-100">
            @forelse ($announcements as $a)
                <div class="p-4"><p class="font-medium text-slate-800">{{ $a->title }}</p><p class="text-sm text-slate-500">{{ $a->body }}</p></div>
            @empty
                <p class="p-6 text-sm text-slate-400">No announcements.</p>
            @endforelse
        </div>
        <h3 class="mb-3 mt-6 text-lg font-semibold text-slate-900">My fees</h3>
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Paid</th><th class="px-4 py-3">Balance</th><th class="px-4 py-3">Status</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($fees as $f)
                        <tr><td class="px-4 py-3">₦{{ number_format($f->amount) }}</td><td class="px-4 py-3">₦{{ number_format($f->amount_paid) }}</td>
                            <td class="px-4 py-3">₦{{ number_format($f->amount - $f->amount_paid) }}</td><td class="px-4 py-3">@include('partials.badge', ['status' => $f->status])</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No fees assigned.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
