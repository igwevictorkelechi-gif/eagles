@php
    use Illuminate\Support\Facades\Route as R;
    $user = auth()->user();
    $school = $user->school_id ? \App\Models\School::find($user->school_id) : null;
    $roleLabels = [
        'super_admin'=>'Super Admin','school_admin'=>'School Admin','teacher'=>'Teacher',
        'staff'=>'Staff','student'=>'Student','parent'=>'Parent','sales_staff'=>'Sales Staff',
    ];
    $nav = [];
    if ($user->role === 'super_admin') {
        $nav = [
            ['platform.dashboard','Overview','🏠'],['platform.schools','Schools','🏫'],
            ['platform.plans','Plans','🏷️'],['platform.subscriptions','Subscriptions','💠'],
        ];
    } elseif ($user->role === 'school_admin') {
        $nav = [
            ['app.dashboard','Dashboard','🏠'],['app.students','Students','🎓'],['app.teachers','Teachers','👩‍🏫'],
            ['app.classes','Classes','🏫'],['app.subjects','Subjects','📚'],['app.results','Results','📝'],
            ['app.exams','Examinations','🖥️'],['app.announcements','Announcements','📢'],['app.fees','Fees','💳'],
            ['app.accounting','Accounting','📒'],['app.inventory','Inventory','📦'],['app.pos','POS / Sales','🛒'],
            ['app.settings','Settings','⚙️'],
        ];
    } elseif (in_array($user->role, ['teacher','staff'])) {
        $nav = [
            ['app.dashboard','Dashboard','🏠'],['app.students','Students','🎓'],['app.classes','Classes','🏫'],
            ['app.results','Results','📝'],['app.exams','Examinations','🖥️'],['app.announcements','Announcements','📢'],
        ];
    } elseif ($user->role === 'sales_staff') {
        $nav = [['app.pos','POS','🛒'],['app.inventory','Products','📦']];
    } else {
        $nav = [['student.dashboard','Dashboard','🏠']];
    }
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SAS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: {
            50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',300:'#86efac',400:'#4ade80',
            500:'{{ $school->primary_color ?? '#22c55e' }}',600:'{{ $school->primary_color ?? '#16a34a' }}',
            700:'#15803d',800:'#166534',900:'#14532d' } } } } };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:Inter,system-ui,sans-serif}</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="flex min-h-screen">
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform border-r border-slate-200 bg-white transition-transform md:static md:translate-x-0">
        <div class="flex h-16 items-center gap-2 border-b border-slate-100 px-5">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">S</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-slate-900">{{ $school->name ?? 'SAS Platform' }}</p>
                <p class="text-xs text-slate-400">{{ $roleLabels[$user->role] ?? $user->role }}</p>
            </div>
        </div>
        <nav class="flex flex-col gap-0.5 overflow-y-auto p-3" style="max-height:calc(100vh - 4rem)">
            @foreach ($nav as [$route,$label,$icon])
                <a href="{{ route($route) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($route) ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    <span class="w-5 text-center">{{ $icon }}</span>{{ $label }}
                </a>
            @endforeach
        </nav>
    </aside>
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4">
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 md:hidden">☰</button>
            <div class="hidden md:block"></div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $roleLabels[$user->role] ?? $user->role }}</p>
                </div>
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{{ strtoupper(substr($user->first_name,0,1).substr($user->last_name ?? '',0,1)) }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Logout</button>
                </form>
            </div>
        </header>
        <main class="flex-1 p-4 md:p-8">
            @if (session('ok'))
                <div class="mb-4 rounded-lg bg-brand-50 px-4 py-2 text-sm text-brand-700">{{ session('ok') }}</div>
            @endif
            @if (session('err'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-2 text-sm text-red-700">{{ session('err') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
