@extends('layouts.marketing')
@section('title', 'Features — SAS')
@section('content')
<div class="mx-auto max-w-6xl px-4 py-16">
    <div class="text-center"><h1 class="text-4xl font-extrabold text-slate-900">A complete school operating system</h1>
        <p class="mx-auto mt-3 max-w-2xl text-slate-500">Every module you need to run academics, finances and operations — designed to work together.</p></div>
    <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['Academic Management',['Students, teachers & staff','Classes, subjects, sessions & terms','Enrollments & timetables','Results entry, approval & lock','Report cards','Assignments & announcements']],
            ['CBT / Examinations',['CBT, class tests, mocks & practice','MCQ, true/false, multiple & short answer','Scheduling, duration & attempts','Randomization & negative marking','Countdown timer & autosave','Automatic grading']],
            ['Fees & Accounting',['Tuition, exam, transport & more','Fee payments & receipts','Income & expense tracking','Outstanding fees reports','Profit / loss statements','Transaction history']],
            ['Materials Sales / POS',['Product & inventory management','Cart, quantities & totals','Multiple payment methods','Auto stock deduction','Receipts & sales history','Low-stock thresholds']],
            ['Attendance',['Student attendance','Staff clock-in / clock-out','Hours-worked tracking','Late & early-departure stats','Device & location capture']],
            ['SaaS & Security',['Multi-tenant isolation (school_id)','Role-based permissions','Secure auth & password hashing','Subscriptions, trials & plans','Per-school branding','Audit logs']],
        ] as [$title,$items])
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-brand-700">{{ $title }}</h3>
                <ul class="mt-4 space-y-2">
                    @foreach ($items as $it)<li class="flex items-start gap-2 text-sm text-slate-600"><span class="mt-0.5 text-brand-600">✓</span>{{ $it }}</li>@endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>
@endsection
