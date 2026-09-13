@extends('layouts.app')
@section('title', 'Results')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Results</h1>
    <p class="mt-1 text-sm text-slate-500">Enter, approve and lock student results.</p></div>

@if ($errors->any())<div class="mb-4 rounded-lg bg-red-50 px-4 py-2 text-sm text-red-700">{{ $errors->first() }}</div>@endif

<div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 text-lg font-semibold text-slate-900">Enter result</h3>
    <form method="POST" action="{{ route('app.results.store') }}">@csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Student</label>
                <select name="student_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select student…</option>
                    @foreach ($students as $id => $name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
                </select></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Subject</label>
                <select name="subject_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Select subject…</option>
                    @foreach ($subjects as $id => $name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
                </select></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">CA score (max 40)</label>
                <input type="number" step="any" name="ca_score" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Exam score (max 60)</label>
                <input type="number" step="any" name="exam_score" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        </div>
        <p class="mt-2 text-xs text-slate-400">Total and grade are calculated automatically on save.</p>
        <button class="mt-4 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Save result</button>
    </form>
</div>

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr>
                <th class="px-4 py-3 font-semibold">Student</th><th class="px-4 py-3 font-semibold">Subject</th>
                <th class="px-4 py-3 font-semibold">CA</th><th class="px-4 py-3 font-semibold">Exam</th>
                <th class="px-4 py-3 font-semibold">Total</th><th class="px-4 py-3 font-semibold">Grade</th>
                <th class="px-4 py-3 font-semibold">Status</th>@if($isAdmin)<th class="px-4 py-3 font-semibold">Actions</th>@endif
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rows as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $r->student_name }}</td><td class="px-4 py-3">{{ $r->subject_name }}</td>
                        <td class="px-4 py-3">{{ $r->ca_score }}</td><td class="px-4 py-3">{{ $r->exam_score }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $r->total_score }}</td>
                        <td class="px-4 py-3 font-bold text-brand-700">{{ $r->grade }}</td>
                        <td class="px-4 py-3">@include('partials.badge', ['status' => $r->status])</td>
                        @if($isAdmin)
                        <td class="px-4 py-3"><div class="flex gap-2">
                            @foreach (['approved'=>'Approve','rejected'=>'Reject','locked'=>'Lock'] as $st=>$lbl)
                                @if ($r->status !== $st)
                                <form method="POST" action="{{ route('app.results.status', $r->id) }}">@csrf @method('PUT')
                                    <input type="hidden" name="status" value="{{ $st }}">
                                    <button class="text-xs font-medium {{ $st==='approved'?'text-brand-600':($st==='locked'?'text-purple-600':'text-red-600') }} hover:underline">{{ $lbl }}</button>
                                </form>
                                @endif
                            @endforeach
                        </div></td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-slate-400">No results recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
