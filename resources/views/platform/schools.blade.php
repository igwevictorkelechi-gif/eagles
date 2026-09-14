@extends('layouts.app')
@section('title', 'Schools')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Schools</h1>
    <p class="mt-1 text-sm text-slate-500">All schools registered on the platform.</p></div>
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr>
            <th class="px-4 py-3 font-semibold">School</th><th class="px-4 py-3 font-semibold">Email</th>
            <th class="px-4 py-3 font-semibold">Plan</th><th class="px-4 py-3 font-semibold">Subscription</th>
            <th class="px-4 py-3 font-semibold">Students</th><th class="px-4 py-3 font-semibold">Status</th>
            <th class="px-4 py-3 font-semibold">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($schools as $s)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">
                        {{ $s->name }}
                        @php $url = \App\Support\Tenant::url($s); @endphp
                        @if ($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="mt-0.5 block text-xs font-normal text-brand-600 hover:underline">{{ preg_replace('#^https?://#', '', $url) }}</a>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $s->email ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $s->plan_name ?? '—' }}</td>
                    <td class="px-4 py-3">@if($s->sub_status)@include('partials.badge', ['status' => $s->sub_status])@else — @endif</td>
                    <td class="px-4 py-3">{{ $s->students_count }}</td>
                    <td class="px-4 py-3">@include('partials.badge', ['status' => $s->status])</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('platform.schools.toggle', $s->id) }}">@csrf @method('PUT')
                            <button class="text-xs font-medium {{ $s->status==='suspended'?'text-brand-600':'text-red-600' }} hover:underline">{{ $s->status==='suspended'?'Reactivate':'Suspend' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">No schools registered yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
