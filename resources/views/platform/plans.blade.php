@extends('layouts.app')
@section('title', 'Subscription Plans')
@section('content')
@php $editing = request('edit') ? $plans->firstWhere('id', request('edit')) : null; @endphp
<div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Subscription Plans</h1>
    <p class="mt-1 text-sm text-slate-500">Configure pricing, limits and availability.</p></div>

@if ($errors->any())<div class="mb-4 rounded-lg bg-red-50 px-4 py-2 text-sm text-red-700">{{ $errors->first() }}</div>@endif

<div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 text-lg font-semibold text-slate-900">{{ $editing ? 'Edit plan' : 'New plan' }}</h3>
    <form method="POST" action="{{ $editing ? route('platform.plans.update', $editing->id) : route('platform.plans.store') }}">
        @csrf @if ($editing) @method('PUT') @endif
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                <input name="name" required value="{{ old('name', $editing->name ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Code</label>
                <input name="code" @if($editing) value="{{ $editing->code }}" readonly @else required @endif class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm {{ $editing?'bg-slate-100':'' }}"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Price / month</label>
                <input type="number" name="price_monthly" value="{{ old('price_monthly', $editing->price_monthly ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Currency</label>
                <input name="currency" value="{{ old('currency', $editing->currency ?? 'NGN') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Max students (blank = ∞)</label>
                <input type="number" name="max_students" value="{{ old('max_students', $editing->max_students ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Max teachers (blank = ∞)</label>
                <input type="number" name="max_teachers" value="{{ old('max_teachers', $editing->max_teachers ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $editing->sort_order ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div class="sm:col-span-2"><label class="mb-1 block text-sm font-medium text-slate-700">Features (comma-separated)</label>
                <input name="features" value="{{ old('features', $editing ? implode(', ', $editing->featureList()) : '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        </div>
        <div class="mt-3 flex gap-6">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_custom" value="1" @checked($editing->is_custom ?? false)> Custom pricing</label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($editing->is_active ?? true)> Active</label>
        </div>
        <div class="mt-4 flex gap-2">
            <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">{{ $editing ? 'Save plan' : 'Create plan' }}</button>
            @if ($editing)<a href="{{ route('platform.plans') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Cancel</a>@endif
        </div>
    </form>
</div>

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr>
            <th class="px-4 py-3 font-semibold">Plan</th><th class="px-4 py-3 font-semibold">Code</th>
            <th class="px-4 py-3 font-semibold">Price</th><th class="px-4 py-3 font-semibold">Max students</th>
            <th class="px-4 py-3 font-semibold">Max teachers</th><th class="px-4 py-3 font-semibold">Status</th>
            <th class="px-4 py-3 font-semibold">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($plans as $p)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">{{ $p->name }}</td><td class="px-4 py-3 text-slate-500">{{ $p->code }}</td>
                    <td class="px-4 py-3">{{ $p->is_custom ? 'Custom' : '₦'.number_format($p->price_monthly) }}</td>
                    <td class="px-4 py-3">{{ $p->max_students ?? '∞' }}</td><td class="px-4 py-3">{{ $p->max_teachers ?? '∞' }}</td>
                    <td class="px-4 py-3">@include('partials.badge', ['status' => $p->is_active ? 'active' : 'suspended'])</td>
                    <td class="px-4 py-3"><div class="flex gap-3">
                        <a href="{{ route('platform.plans', ['edit' => $p->id]) }}" class="text-xs font-medium text-slate-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('platform.plans.destroy', $p->id) }}" onsubmit="return confirm('Deactivate this plan?')">@csrf @method('DELETE')
                            <button class="text-xs font-medium text-red-600 hover:underline">Deactivate</button></form>
                    </div></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
