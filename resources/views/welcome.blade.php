@extends('layouts.public')

@section('title', 'Welcome')

@section('content')

{{-- HERO SECTION WITH VISUAL --}}
<div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600 rounded-3xl p-10 md:p-14 mb-20 text-white">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top,_white,_transparent_70%)]"></div>

    <div class="relative grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <div x-data x-init="setTimeout(() => $el.classList.remove('opacity-0','translate-y-6'), 50)"
             class="transition-all duration-700 opacity-0 translate-y-6">

            <h1 class="text-4xl md:text-5xl font-semibold leading-tight">
                Everything your company needs
                <span class="block text-indigo-200">to run smoothly</span>
            </h1>

            <p class="mt-5 text-indigo-100 text-lg">
                A modern SaaS platform to manage tasks, leaves, documents, and teams —
                designed for clarity, speed, and scale.
            </p>

            <div class="mt-8 flex flex-wrap gap-4">
                <a href="/org/codeclouds/login"
                   class="bg-white text-indigo-600 px-7 py-3 rounded-lg text-sm font-semibold hover:bg-indigo-50 transition shadow">
                    Login
                </a>
                <a href="/org/codeclouds/login"
                   class="bg-indigo-500/30 backdrop-blur border border-white/30 px-7 py-3 rounded-lg text-sm font-medium hover:bg-indigo-500/40 transition">
                    Request Demo
                </a>
            </div>
        </div>

        {{-- HERO IMAGE --}}
        <div class="relative hidden md:block">
            <img src="{{ asset('landing/dashboard-preview.png') }}" alt="Dashboard preview"
                 class="rounded-2xl shadow-2xl transform hover:scale-[1.02] transition duration-700" />
        </div>
    </div>
</div>

{{-- TRUST BADGES / STATS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20 text-center">
    <div>
        <p class="text-3xl font-semibold text-slate-800">24×7</p>
        <p class="text-sm text-slate-500">Support Assistance</p>
    </div>
    <div>
        <p class="text-3xl font-semibold text-slate-800">99.9%</p>
        <p class="text-sm text-slate-500">System Uptime</p>
    </div>
    <div>
        <p class="text-3xl font-semibold text-slate-800">Secure</p>
        <p class="text-sm text-slate-500">Tenant Isolation</p>
    </div>
    <div>
        <p class="text-3xl font-semibold text-slate-800">Scalable</p>
        <p class="text-sm text-slate-500">For growing teams</p>
    </div>
</div>

{{-- FEATURES WITH ILLUSTRATIONS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-24">

    @php
    $features = [
        ['Task Management', 'Plan, assign, and track tasks effortlessly across teams.', 'landing/feature-tasks.png'],
        ['Leave Management', 'Transparent leave workflows with fast approvals.', 'landing/feature-leaves.png'],
        ['Document Hub', 'Securely store and share company documents.', 'landing/feature-docs.png'],
    ];
    @endphp

    @foreach($features as $index => $feature)
    <div x-data x-init="setTimeout(() => $el.classList.remove('opacity-0','translate-y-8'), {{ $index * 120 }})"
         class="bg-white rounded-2xl shadow-sm p-8 transition-all duration-700 opacity-0 translate-y-8 hover:shadow-lg">

        <img src="{{ $feature[2] }}" alt="{{ $feature[0] }}" class="w-full h-40 object-contain mb-6" />

        <h3 class="font-semibold text-slate-800 mb-2">{{ $feature[0] }}</h3>
        <p class="text-sm text-slate-500">{{ $feature[1] }}</p>
    </div>
    @endforeach

</div>

{{-- WHY CHOOSE US --}}
<div class="bg-white rounded-3xl shadow-sm p-12 mb-24">
    <h2 class="text-3xl font-semibold text-slate-800 mb-10 text-center">
        Why teams choose us
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
        <div class="flex gap-4">
            <span class="text-indigo-600 text-xl">✔</span>
            <p class="text-slate-600">24×7 dedicated support & quick issue resolution</p>
        </div>
        <div class="flex gap-4">
            <span class="text-indigo-600 text-xl">✔</span>
            <p class="text-slate-600">Role-based access for admins, managers, and employees</p>
        </div>
        <div class="flex gap-4">
            <span class="text-indigo-600 text-xl">✔</span>
            <p class="text-slate-600">Secure multi-tenant architecture with isolated databases</p>
        </div>
        <div class="flex gap-4">
            <span class="text-indigo-600 text-xl">✔</span>
            <p class="text-slate-600">Minimal learning curve with intuitive UI</p>
        </div>
    </div>
</div>

{{-- FINAL CTA --}}
<div class="relative overflow-hidden bg-slate-900 rounded-3xl p-14 text-center text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/30 to-purple-600/30"></div>

    <div class="relative max-w-2xl mx-auto">
        <h3 class="text-3xl font-semibold mb-4">Ready to transform your workplace?</h3>
        <p class="text-slate-300 mb-8">
            Start managing your company with clarity, confidence, and control.
        </p>
        <a href="/org/codeclouds/login"
           class="inline-block bg-white text-slate-900 px-9 py-3 rounded-lg text-sm font-semibold hover:bg-slate-100 transition">
            Get Started
        </a>
    </div>
</div>

@endsection