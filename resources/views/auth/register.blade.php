@extends('layouts.marketing')
@section('title', 'Start free trial — SAS')
@section('content')
<div class="mx-auto flex min-h-[80vh] max-w-md flex-col justify-center px-4 py-12">
    <form method="POST" action="{{ route('register') }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        @csrf
        <div><h1 class="text-2xl font-bold text-slate-900">Start your free trial</h1><p class="mt-1 text-sm text-slate-500">Create your school and admin account — 14 days free.</p></div>
        @if ($errors->any())<div class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <div><label class="mb-1 block text-sm font-medium text-slate-700">School name</label>
            <input name="school_name" value="{{ old('school_name') }}" required autofocus placeholder="Greenfield Academy" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="mb-1 block text-sm font-medium text-slate-700">First name</label>
                <input name="admin_first_name" value="{{ old('admin_first_name') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Last name</label>
                <input name="admin_last_name" value="{{ old('admin_last_name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        </div>
        <div><label class="mb-1 block text-sm font-medium text-slate-700">Work email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        <div><label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
            <input type="password" name="password" required minlength="6" placeholder="At least 6 characters" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        <button class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Create school & start trial</button>
        <p class="text-center text-sm text-slate-500">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-700">Sign in</a></p>
    </form>
</div>
@endsection
