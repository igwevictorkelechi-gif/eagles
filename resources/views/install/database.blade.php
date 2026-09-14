@extends('install.layout', ['step' => 'database'])
@section('content')
<h1 class="text-xl font-bold text-slate-900">Database connection</h1>
<p class="mt-1 text-sm text-slate-500">
    Create a MySQL database in cPanel → <strong>MySQL Databases</strong> (create a database, a user,
    then add the user to the database with <em>All Privileges</em>). Enter those details below —
    we'll test the connection before saving.
</p>

<form method="POST" action="{{ route('install.database.save') }}" class="mt-5 space-y-4">
    @csrf
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Database host</label>
            <input name="db_host" value="{{ old('db_host', $env['DB_HOST'] ?? 'localhost') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <p class="mt-1 text-xs text-slate-400">On WhoGoHost this is usually <code>localhost</code>.</p>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Port</label>
            <input name="db_port" value="{{ old('db_port', $env['DB_PORT'] ?? '3306') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Database name</label>
            <input name="db_database" value="{{ old('db_database', $env['DB_DATABASE'] ?? '') }}" required placeholder="cpaneluser_sas" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Database username</label>
            <input name="db_username" value="{{ old('db_username', $env['DB_USERNAME'] ?? '') }}" required placeholder="cpaneluser_sasuser" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Database password</label>
            <input name="db_password" type="text" value="{{ old('db_password') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Your site URL</label>
            <input name="app_url" value="{{ old('app_url', $env['APP_URL'] ?? '') }}" placeholder="https://yourdomain.com" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
    </div>
    <div class="flex items-center justify-between pt-2">
        <a href="{{ route('install.requirements') }}" class="text-sm font-medium text-slate-500 hover:underline">← Back</a>
        <button class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Test &amp; continue →</button>
    </div>
</form>
@endsection
