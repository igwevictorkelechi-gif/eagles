@extends('layouts.marketing')
@section('title', 'Sign in — SAS')
@section('content')
<div class="mx-auto flex min-h-[80vh] max-w-md flex-col justify-center px-4 py-12">
    <form method="POST" action="{{ route('login') }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        @csrf
        <div><h1 class="text-2xl font-bold text-slate-900">Welcome back</h1><p class="mt-1 text-sm text-slate-500">Sign in to your SAS account.</p></div>
        @if ($errors->any())<div class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <div><label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        <div><label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
            <input type="password" name="password" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        <button class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Sign in</button>
        <p class="text-center text-sm text-slate-500">No account? <a href="{{ route('register') }}" class="font-semibold text-brand-700">Start a free trial</a></p>
    </form>
    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4 text-sm shadow-sm">
        <p class="mb-2 font-semibold text-slate-700">Demo accounts <span class="font-normal text-slate-400">(password: Password123!)</span></p>
        <ul class="grid grid-cols-2 gap-1 text-xs text-slate-600">
            <li>Super Admin — super@sas.app</li><li>School Admin — admin@greenfield.edu</li>
            <li>Teacher — teacher@greenfield.edu</li><li>Sales — sales@greenfield.edu</li>
            <li>Student — student@greenfield.edu</li>
        </ul>
    </div>
</div>
@endsection
