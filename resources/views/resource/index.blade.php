@extends('layouts.app')
@section('title', $title)
@section('content')
@php $editingRow = $editing ?? null; @endphp

<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $title }}</h1>
        @isset($subtitle)<p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>@endisset
    </div>
    <a href="{{ route($route) }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">＋ New {{ $singular }}</a>
</div>

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 px-4 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
@endif

<div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 text-lg font-semibold text-slate-900">{{ $editingRow ? 'Edit '.$singular : 'Add '.$singular }}</h3>
    <form method="POST" action="{{ $editingRow ? route($route.'.update', $editingRow->id) : route($route.'.store') }}">
        @csrf
        @if ($editingRow) @method('PUT') @endif
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($fields as $f)
                @php
                    $type = $f['type'] ?? 'text';
                    $val = old($f['name'], $editingRow->{$f['name']} ?? '');
                @endphp
                <div class="{{ $type === 'textarea' ? 'sm:col-span-2' : '' }}">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ $f['label'] }}</label>
                    @if ($type === 'select')
                        <select name="{{ $f['name'] }}" @if($f['required']??false) required @endif
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                            <option value="">Select…</option>
                            @foreach ($f['options'] as $ov => $ol)
                                <option value="{{ $ov }}" @selected((string)$val === (string)$ov)>{{ $ol }}</option>
                            @endforeach
                        </select>
                    @elseif ($type === 'textarea')
                        <textarea name="{{ $f['name'] }}" rows="3" @if($f['required']??false) required @endif
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100">{{ $val }}</textarea>
                    @else
                        <input type="{{ $type }}" name="{{ $f['name'] }}" value="{{ $val }}" @if($f['required']??false) required @endif
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-4 flex gap-2">
            <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">{{ $editingRow ? 'Save changes' : 'Create '.$singular }}</button>
            @if ($editingRow)<a href="{{ route($route) }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>@endif
        </div>
    </form>
</div>

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    @foreach ($columns as $col)<th class="whitespace-nowrap px-4 py-3 font-semibold">{{ $col['label'] }}</th>@endforeach
                    <th class="px-4 py-3 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rows as $row)
                    <tr class="hover:bg-slate-50">
                        @foreach ($columns as $col)
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700">
                                @php $v = isset($col['value']) ? $col['value']($row) : ($row->{$col['key']} ?? '—'); @endphp
                                @if (!empty($col['money'])) ₦{{ number_format((int) ($row->{$col['key']} ?? 0)) }}
                                @elseif (!empty($col['badge'])) @include('partials.badge', ['status' => $v])
                                @else {{ $v }} @endif
                            </td>
                        @endforeach
                        <td class="whitespace-nowrap px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route($route, ['edit' => $row->id]) }}" class="text-xs font-medium text-slate-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route($route.'.destroy', $row->id) }}" onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-medium text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns)+1 }}" class="px-4 py-12 text-center text-slate-400">No {{ strtolower($title) }} yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
