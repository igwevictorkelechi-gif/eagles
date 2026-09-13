@extends('layouts.marketing')
@section('title', 'Contact — SAS')
@section('content')
<div class="mx-auto max-w-2xl px-4 py-16">
    <div class="text-center"><h1 class="text-4xl font-extrabold text-slate-900">Get in touch</h1>
        <p class="mt-3 text-slate-500">Questions about SAS? Send us a message and we'll get back to you.</p></div>
    <form class="mt-10 space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm" onsubmit="event.preventDefault(); this.innerHTML='<p class=\'text-center text-brand-700 font-medium py-8\'>✅ Thanks! Your message has been received.</p>';">
        <div class="grid gap-4 sm:grid-cols-2">
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Full name</label><input required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Email</label><input type="email" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        </div>
        <div><label class="mb-1 block text-sm font-medium text-slate-700">School name</label><input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
        <div><label class="mb-1 block text-sm font-medium text-slate-700">Message</label><textarea rows="4" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea></div>
        <button class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Send message</button>
    </form>
    <div class="mt-8 grid gap-4 text-center text-sm text-slate-500 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="font-semibold text-slate-700">Email</p><p>hello@sas.app</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="font-semibold text-slate-700">Phone</p><p>+234 800 000 0000</p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-4"><p class="font-semibold text-slate-700">Hours</p><p>Mon–Fri, 9am–5pm</p></div>
    </div>
</div>
@endsection
