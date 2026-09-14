@extends('install.layout', ['step' => 'finished'])
@section('content')
<div class="text-center">
    <div class="text-4xl">🎉</div>
    <h1 class="mt-2 text-xl font-bold text-slate-900">SAS is installed!</h1>
    <p class="mt-1 text-sm text-slate-500">Your database is set up and demo data is loaded.</p>
</div>

<div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
    <p class="font-semibold">⚠ Before you share this site publicly</p>
    <p class="mt-1">Sign in as the Super Admin and the School Admin below, then <strong>change these demo passwords</strong> (or delete the demo users). They are the same for everyone until you do.</p>
</div>

<div class="mt-4 overflow-hidden rounded-lg border border-slate-200">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-2">Role</th><th class="px-4 py-2">Email</th><th class="px-4 py-2">Password</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
            <tr><td class="px-4 py-2">Super Admin</td><td class="px-4 py-2">super@sas.app</td><td class="px-4 py-2">Password123!</td></tr>
            <tr><td class="px-4 py-2">School Admin</td><td class="px-4 py-2">admin@greenfield.edu</td><td class="px-4 py-2">Password123!</td></tr>
            <tr><td class="px-4 py-2">Teacher</td><td class="px-4 py-2">teacher@greenfield.edu</td><td class="px-4 py-2">Password123!</td></tr>
            <tr><td class="px-4 py-2">Student</td><td class="px-4 py-2">student@greenfield.edu</td><td class="px-4 py-2">Password123!</td></tr>
        </tbody>
    </table>
</div>

<div class="mt-6 flex gap-2">
    <a href="{{ url('/') }}" class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">View site</a>
    <a href="{{ url('/login') }}" class="flex-1 rounded-lg bg-brand-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-brand-700">Sign in →</a>
</div>
@endsection
