@extends('install.layout', ['step' => 'database'])
@section('content')
<h1 class="text-xl font-bold text-slate-900">Couldn't build the database</h1>
<p class="mt-1 text-sm text-slate-500">The tables could not be created. The database reported:</p>
<pre class="mt-3 overflow-x-auto rounded-lg bg-red-50 p-3 text-xs text-red-700">{{ $error }}</pre>
<p class="mt-4 text-sm text-slate-500">Common causes: the database user lacks privileges, or the details were wrong. Fix in cPanel, then try again.</p>
<div class="mt-5 flex gap-2">
    <a href="{{ route('install.database') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Edit database details</a>
    <a href="{{ route('install.run') }}" class="rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Try again</a>
</div>
@endsection
