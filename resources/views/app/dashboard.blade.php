@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@php $u = auth()->user(); @endphp
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Welcome, {{ explode(' ', $u->name)[0] }} 👋</h1>
    <p class="mt-1 text-sm text-slate-500">Here's what's happening at your school today.</p>
</div>

<div class="mb-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([['Students',$counts['students'],'text-brand-600'],['Teachers',$counts['teachers'],'text-blue-600'],['Classes',$counts['classes'],'text-purple-600'],['Examinations',$counts['exams'],'text-amber-600']] as [$l,$v,$c])
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">{{ $l }}</p>
            <p class="mt-2 text-3xl font-bold {{ $c }}">{{ $v }}</p>
        </div>
    @endforeach
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([['Total Income',$finance['income'],'text-brand-600'],['Total Expenses',$finance['expense'],'text-red-600'],['Net Profit',$finance['profit'],$finance['profit']>=0?'text-brand-600':'text-red-600'],['Outstanding Fees',$finance['outstanding'],'text-amber-600']] as [$l,$v,$c])
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">{{ $l }}</p>
            <p class="mt-2 text-2xl font-bold {{ $c }}">₦{{ number_format((int) $v) }}</p>
        </div>
    @endforeach
</div>

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 text-lg font-semibold text-slate-900">Latest announcements</h3>
    @forelse ($announcements as $a)
        <div class="mb-3 border-l-2 border-brand-500 pl-3">
            <p class="font-medium text-slate-800">{{ $a->title }}</p>
            <p class="text-sm text-slate-500">{{ $a->body }}</p>
        </div>
    @empty
        <p class="text-sm text-slate-400">No announcements yet.</p>
    @endforelse
</div>
@endsection
