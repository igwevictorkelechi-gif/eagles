@php
    $map = [
        'active'=>'bg-brand-100 text-brand-800','approved'=>'bg-brand-100 text-brand-800','paid'=>'bg-brand-100 text-brand-800',
        'income'=>'bg-brand-100 text-brand-800','trial'=>'bg-blue-100 text-blue-800','submitted'=>'bg-blue-100 text-blue-800',
        'partial'=>'bg-amber-100 text-amber-800','pending'=>'bg-amber-100 text-amber-800','draft'=>'bg-slate-100 text-slate-700',
        'unpaid'=>'bg-red-100 text-red-700','rejected'=>'bg-red-100 text-red-700','suspended'=>'bg-red-100 text-red-700',
        'expired'=>'bg-red-100 text-red-700','expense'=>'bg-red-100 text-red-700','locked'=>'bg-purple-100 text-purple-700',
    ];
    $cls = $map[$status] ?? 'bg-slate-100 text-slate-700';
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cls }}">{{ $status }}</span>
