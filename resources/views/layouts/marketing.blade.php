<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SAS — School Administration System')</title>
    <meta name="description" content="SAS — the complete school operating system. Multi-tenant School Administration SaaS.">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.pwa')
</head>
<body class="bg-white text-slate-800 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-100 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">S</span>
                <span class="text-lg font-extrabold text-slate-900">SAS</span>
            </a>
            <nav class="hidden items-center gap-1 md:flex">
                @foreach (['home'=>'Home','features'=>'Features','pricing'=>'Pricing','faq'=>'FAQ','contact'=>'Contact'] as $r=>$label)
                    <a href="{{ route($r) }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($r) ? 'text-brand-700' : 'text-slate-600 hover:text-slate-900' }}">{{ $label }}</a>
                @endforeach
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="hidden rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 sm:inline-flex">Sign in</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Start Free</a>
            </div>
        </div>
    </header>

    <main>@yield('content')</main>

    <footer class="mt-24 border-t border-slate-100 bg-slate-50">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:grid-cols-2 md:grid-cols-4">
            <div>
                <span class="text-lg font-extrabold text-slate-900">SAS</span>
                <p class="mt-3 max-w-xs text-sm text-slate-500">The complete school operating system — academics, exams, fees, POS and more, in one platform.</p>
            </div>
            <div><h4 class="mb-3 text-sm font-semibold text-slate-900">Product</h4>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li><a href="{{ route('features') }}" class="hover:text-brand-700">Features</a></li>
                    <li><a href="{{ route('pricing') }}" class="hover:text-brand-700">Pricing</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-brand-700">Start free trial</a></li>
                </ul></div>
            <div><h4 class="mb-3 text-sm font-semibold text-slate-900">Company</h4>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li><a href="{{ route('faq') }}" class="hover:text-brand-700">FAQ</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-brand-700">Contact</a></li>
                </ul></div>
            <div><h4 class="mb-3 text-sm font-semibold text-slate-900">Get started</h4>
                <a href="{{ route('register') }}" class="inline-flex rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Start Free</a></div>
        </div>
        <div class="border-t border-slate-200 py-4 text-center text-xs text-slate-400">© {{ date('Y') }} SAS — School Administration System.</div>
    </footer>
    @include('partials.pwa-register')
</body>
</html>
