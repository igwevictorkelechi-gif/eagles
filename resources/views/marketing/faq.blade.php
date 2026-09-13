@extends('layouts.marketing')
@section('title', 'FAQ — SAS')
@section('content')
<div class="mx-auto max-w-3xl px-4 py-16">
    <h1 class="text-center text-4xl font-extrabold text-slate-900">Frequently asked questions</h1>
    <div class="mt-10 space-y-4">
        @foreach ([
            ['Is my school\'s data isolated from others?','Yes. SAS is fully multi-tenant — every record is tagged with your school_id and users can never access another school\'s information.'],
            ['Can I use my own school branding?','Absolutely. School Admins can change the school name, logo, colors and contact details.'],
            ['How does the free trial work?','New schools start on a configurable trial (7, 14 or 30 days) with full access; upgrade, downgrade or move to Free anytime.'],
            ['What exams are supported?','CBT, class tests, mock, practice, internal and entrance examinations — with MCQ, true/false, multiple-answer and short-answer questions and automatic grading.'],
            ['Which payment providers do you support?','Flutterwave, Paystack or Stripe, verified via secure server-side webhooks.'],
            ['Can teachers and students have their own logins?','Yes — dedicated experiences for Super Admin, School Admin, Teacher/Staff, Student, Parent and Sales Staff, each with role-based permissions.'],
        ] as [$q,$a])
            <details class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <summary class="flex cursor-pointer list-none items-center justify-between font-semibold text-slate-900">{{ $q }}<span class="text-brand-600 transition group-open:rotate-45">＋</span></summary>
                <p class="mt-3 text-sm text-slate-600">{{ $a }}</p>
            </details>
        @endforeach
    </div>
</div>
@endsection
