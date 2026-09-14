@extends('layouts.app')
@section('title', 'School Settings')
@section('content')
<div class="mb-6 max-w-3xl"><h1 class="text-2xl font-bold text-slate-900">School Settings & Branding</h1>
    <p class="mt-1 text-sm text-slate-500">Customize how your school appears across SAS.</p></div>

<form method="POST" action="{{ route('app.settings.update') }}" class="max-w-3xl space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf @method('PUT')
    @if ($errors->any())<div class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach ([['name','School name'],['short_name','Short name'],['email','Email'],['phone','Phone'],['website','Website'],['logo_url','Logo URL']] as [$k,$l])
            <div><label class="mb-1 block text-sm font-medium text-slate-700">{{ $l }}</label>
                <input name="{{ $k }}" value="{{ old($k, $school->$k) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        @endforeach
    </div>
    <div><label class="mb-1 block text-sm font-medium text-slate-700">Address</label>
        <input name="address" value="{{ old('address', $school->address) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach ([['primary_color','Primary color','#16a34a'],['secondary_color','Secondary color','#15803d']] as [$k,$l,$d])
            <div><label class="mb-1 block text-sm font-medium text-slate-700">{{ $l }}</label>
                <div class="flex items-center gap-3">
                    <input type="color" value="{{ old($k, $school->$k ?: $d) }}" oninput="this.nextElementSibling.value=this.value" class="h-10 w-14 rounded border border-slate-300">
                    <input name="{{ $k }}" value="{{ old($k, $school->$k) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div></div>
        @endforeach
    </div>
    <div class="flex justify-end">
        <button class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Save settings</button>
    </div>
</form>
@endsection
