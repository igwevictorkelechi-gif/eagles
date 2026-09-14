@extends('install.layout', ['step' => 'mail'])
@section('content')
<h1 class="text-xl font-bold text-slate-900">Email (SMTP)</h1>
<p class="mt-1 text-sm text-slate-500">
    Used for notifications and password resets. On WhoGoHost, create an email account in cPanel →
    <strong>Email Accounts</strong>, then use its SMTP details. You can skip this and set it later.
</p>

<form method="POST" action="{{ route('install.mail.save') }}" class="mt-5 space-y-4">
    @csrf
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">SMTP host</label>
            <input name="mail_host" value="{{ old('mail_host', $env['MAIL_HOST'] ?? '') }}" placeholder="mail.yourdomain.com" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Port</label>
            <input name="mail_port" value="{{ old('mail_port', $env['MAIL_PORT'] ?? '587') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Username</label>
            <input name="mail_username" value="{{ old('mail_username', $env['MAIL_USERNAME'] ?? '') }}" placeholder="no-reply@yourdomain.com" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
            <input name="mail_password" type="text" value="{{ old('mail_password') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Encryption</label>
            <select name="mail_encryption" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="tls" @selected(old('mail_encryption', $env['MAIL_ENCRYPTION'] ?? 'tls')==='tls')>TLS (port 587)</option>
                <option value="ssl" @selected(old('mail_encryption')==='ssl')>SSL (port 465)</option>
                <option value="none" @selected(old('mail_encryption')==='none')>None</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">From name</label>
            <input name="mail_from_name" value="{{ old('mail_from_name', $env['MAIL_FROM_NAME'] ?? 'SAS') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">From address</label>
            <input name="mail_from_address" value="{{ old('mail_from_address', $env['MAIL_FROM_ADDRESS'] ?? '') }}" placeholder="no-reply@yourdomain.com" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2 rounded-lg bg-slate-50 p-3">
            <label class="mb-1 block text-xs font-medium text-slate-600">Optional — send a test email to</label>
            <div class="flex gap-2">
                <input name="test_to" type="email" value="{{ old('test_to') }}" placeholder="you@example.com" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <button name="action" value="test" class="whitespace-nowrap rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Send test</button>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-between pt-2">
        <button name="action" value="skip" class="text-sm font-medium text-slate-500 hover:underline">Skip for now →</button>
        <button name="action" value="save" class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Save &amp; continue →</button>
    </div>
</form>
@endsection
