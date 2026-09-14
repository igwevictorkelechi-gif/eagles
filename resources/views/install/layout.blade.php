<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAS — Setup</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.pwa')
</head>
<body class="min-h-screen bg-gradient-to-br from-brand-50 to-slate-100 text-slate-800 antialiased">
@php $steps = ['requirements'=>'Requirements','database'=>'Database','mail'=>'Email','finished'=>'Done']; $current = $step ?? 'requirements'; @endphp
<div class="mx-auto max-w-2xl px-4 py-10">
    <div class="mb-6 flex items-center gap-2">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-base font-bold text-white">S</span>
        <span class="text-xl font-extrabold text-slate-900">SAS Setup</span>
    </div>

    <ol class="mb-6 flex items-center gap-2 text-xs font-medium">
        @foreach ($steps as $key => $label)
            @php $active = $key === $current; @endphp
            <li class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $active ? 'bg-brand-600 text-white' : 'bg-white text-slate-400 border border-slate-200' }}">{{ $loop->iteration }}</span>
                <span class="{{ $active ? 'text-slate-900' : 'text-slate-400' }}">{{ $label }}</span>
                @unless ($loop->last)<span class="text-slate-300">→</span>@endunless
            </li>
        @endforeach
    </ol>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        @if (session('ok'))
            <div class="mb-4 rounded-lg bg-brand-50 px-4 py-2 text-sm text-brand-700">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </div>

    <p class="mt-6 text-center text-xs text-slate-400">SAS — School Administration System</p>
</div>
    @include('partials.pwa-register')
</body>
</html>
