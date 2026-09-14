@extends('layouts.marketing')
@section('title', 'SAS — Run your entire school on one platform')
@section('content')
<section class="bg-gradient-to-b from-brand-50 to-white">
    <div class="mx-auto max-w-6xl px-4 py-20 text-center md:py-28">
        <span class="inline-flex rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-medium text-brand-800">Multi-tenant School SaaS</span>
        <h1 class="mx-auto mt-4 max-w-3xl text-4xl font-extrabold leading-tight text-slate-900 md:text-6xl">Run your entire school on <span class="text-brand-600">one platform</span></h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-600">SAS is a complete school operating system — academics, CBT exams, attendance, fees, accounting, POS and administration, with isolated data for every school.</p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-6 py-3 text-base font-semibold text-white hover:bg-brand-700">Start Free</a>
            <a href="{{ route('pricing') }}" class="rounded-lg border border-slate-300 bg-white px-6 py-3 text-base font-semibold text-slate-700 hover:bg-slate-50">View Pricing</a>
        </div>
        <p class="mt-4 text-sm text-slate-400">No credit card required · 14-day trial · Green by default 🌿</p>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-16">
    <div class="mb-10 text-center"><h2 class="text-3xl font-bold text-slate-900">Everything a modern school needs</h2>
        <p class="mt-2 text-slate-500">One login for admins, teachers, students, parents and sales staff.</p></div>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([['🎓','Academics','Students, classes, subjects, sessions, terms, enrollments and results.'],['🖥️','CBT Examinations','Computer-based tests, mocks and practice exams with auto-grading.'],['🕒','Attendance','Student attendance plus staff clock-in / clock-out with hours tracking.'],['💳','Fees & Accounting','Fee structures, payments, income, expenses and profit / loss reports.'],['🛒','POS & Inventory','Sell books, uniforms and materials with stock and receipts.'],['📊','Reports & Analytics','Academic, financial, staff and inventory insights at a glance.']] as [$i,$t,$d])
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                <div class="text-3xl">{{ $i }}</div><h3 class="mt-3 text-lg font-semibold text-slate-900">{{ $t }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $d }}</p></div>
        @endforeach
    </div>
</section>

<section class="bg-slate-900 py-16 text-white">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-center text-3xl font-bold">Built for every role</h2>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([['Super Admin','Manage schools, plans, pricing, trials and platform analytics.'],['School Admin','Full control of one school — students, staff, finances and branding.'],['Teacher / Staff','Enter results, run exams, take attendance and clock in / out.'],['Student','Take CBT exams, view results, fees, timetable and announcements.'],['Parent','Follow children\'s results, attendance, fees and payments.'],['Sales Staff','Operate the POS, manage inventory and print receipts.']] as [$t,$d])
                <div class="rounded-xl border border-white/10 bg-white/5 p-5"><h3 class="font-semibold text-brand-300">{{ $t }}</h3><p class="mt-1 text-sm text-slate-300">{{ $d }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-4xl px-4 py-20 text-center">
    <h2 class="text-3xl font-bold text-slate-900">Ready to digitize your school?</h2>
    <p class="mt-3 text-slate-500">Set up your school in minutes and invite your team.</p>
    <a href="{{ route('register') }}" class="mt-6 inline-flex rounded-lg bg-brand-600 px-6 py-3 text-base font-semibold text-white hover:bg-brand-700">Start your free trial</a>
</section>
@endsection
