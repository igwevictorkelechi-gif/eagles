@extends('install.layout', ['step' => 'requirements'])
@section('content')
<h1 class="text-xl font-bold text-slate-900">Welcome 👋</h1>
<p class="mt-1 text-sm text-slate-500">Let's get SAS running on your server. First, a quick server check.</p>

<ul class="mt-5 divide-y divide-slate-100 rounded-lg border border-slate-200">
    @foreach ($checks as [$label, $pass])
        <li class="flex items-center justify-between px-4 py-3 text-sm">
            <span class="text-slate-700">{{ $label }}</span>
            @if ($pass)
                <span class="inline-flex items-center gap-1 rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-medium text-brand-800">✓ OK</span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">✕ Fix needed</span>
            @endif
        </li>
    @endforeach
</ul>

@if ($ok)
    <a href="{{ route('install.database') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Continue to database →</a>
@else
    <div class="mt-6 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Please fix the items marked above, then refresh this page.
        <ul class="mt-2 list-disc pl-5 text-xs">
            <li>Set PHP to 8.2, 8.3 or 8.4 in cPanel → <strong>MultiPHP Manager</strong>.</li>
            <li>Set <code>storage/</code> and <code>bootstrap/cache/</code> to permission <strong>755</strong> in File Manager.</li>
        </ul>
    </div>
    <a href="{{ route('install.requirements') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Re-check</a>
@endif
@endsection
