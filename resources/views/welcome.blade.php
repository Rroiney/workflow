@extends('layouts.public')

@section('title', 'WorkFlow | Home')

@section('content')
<style>
    html {
        scroll-behavior: smooth;
    }

    @keyframes client-slide {
        from {
            transform: translateX(0);
        }
        to {
            transform: translateX(-50%);
        }
    }

    .client-track {
        animation: client-slide 24s linear infinite;
    }
</style>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <header class="relative z-[120] mb-8">
        <div class="relative overflow-visible rounded-2xl border border-indigo-100 bg-white/90 px-4 py-4 shadow-sm backdrop-blur">
            <div class="grid grid-cols-1 items-center gap-4 md:grid-cols-[auto_1fr_auto]">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 md:justify-self-start">
                    <img src="{{ asset('assets/branding/workflow-logo.png') }}" alt="Workflow logo" class="h-9 w-9 rounded-lg object-cover" />
                    <span class="text-lg font-semibold tracking-tight text-slate-800">WorkFlow</span>
                </a>

                <nav class="flex flex-wrap items-center justify-center gap-2 text-sm md:justify-self-center">
                    <button type="button" data-scroll-target="features" class="rounded-full px-4 py-2 font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">Features</button>
                    <button type="button" data-scroll-target="upcoming" class="rounded-full px-4 py-2 font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">Upcoming</button>
                    <button type="button" data-scroll-target="why-us" class="rounded-full px-4 py-2 font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">Why Us</button>
                    <button type="button" data-scroll-target="contact" class="rounded-full px-4 py-2 font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">Contact</button>
                </nav>

                <div
                    x-data="{
                        open: false,
                        preference: 'system',
                        effectiveTheme: 'light',
                        syncTheme() {
                            this.preference = window.workflowTheme?.getPreference?.() ?? 'system';
                            this.effectiveTheme = window.workflowTheme?.getEffectiveTheme?.() ?? 'light';
                        },
                        setTheme(preference) {
                            window.workflowTheme?.setPreference?.(preference);
                            this.syncTheme();
                            this.open = false;
                        }
                    }"
                    x-init="
                        syncTheme();
                        window.addEventListener('workflow-theme-changed', () => syncTheme());
                    "
                    class="relative z-[130] md:justify-self-end">
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50"
                        :title="preference === 'system' ? 'System theme' : (preference === 'dark' ? 'Dark theme' : 'Light theme')"
                        aria-label="Theme switcher">
                        <svg x-show="preference === 'light'" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2.5M12 19.5V22M4.93 4.93l1.77 1.77M17.3 17.3l1.77 1.77M2 12h2.5M19.5 12H22M4.93 19.07l1.77-1.77M17.3 6.7l1.77-1.77"></path>
                        </svg>

                        <svg x-show="preference === 'dark'" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 14.7A9 9 0 0 1 9.3 3a1 1 0 0 0-1.26 1.26A7 7 0 1 0 19.74 16a1 1 0 0 0 1.26-1.3Z"></path>
                        </svg>

                        <div x-show="preference === 'system'" x-cloak class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="4" width="18" height="12" rx="2"></rect>
                                <path d="M8 20h8M12 16v4"></path>
                            </svg>
                            <span class="absolute -right-1 -top-1 inline-flex h-2.5 w-2.5 rounded-full"
                                :class="effectiveTheme === 'dark' ? 'bg-indigo-400' : 'bg-amber-400'"></span>
                        </div>
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        @click.outside="open = false"
                        x-transition.origin.top.right
                        class="absolute right-0 top-full mt-3 w-44 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl z-[140]">
                        <div class="mb-2 px-2 pt-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                            Theme
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <button
                                type="button"
                                @click="setTheme('light')"
                                class="flex flex-col items-center gap-2 rounded-xl px-3 py-3 transition hover:bg-slate-50"
                                :class="preference === 'light' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'text-slate-700'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M12 2v2.5M12 19.5V22M4.93 4.93l1.77 1.77M17.3 17.3l1.77 1.77M2 12h2.5M19.5 12H22M4.93 19.07l1.77-1.77M17.3 6.7l1.77-1.77"></path>
                                </svg>
                                <span class="text-[11px] font-medium">Light</span>
                            </button>

                            <button
                                type="button"
                                @click="setTheme('dark')"
                                class="flex flex-col items-center gap-2 rounded-xl px-3 py-3 transition hover:bg-slate-50"
                                :class="preference === 'dark' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'text-slate-700'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21 14.7A9 9 0 0 1 9.3 3a1 1 0 0 0-1.26 1.26A7 7 0 1 0 19.74 16a1 1 0 0 0 1.26-1.3Z"></path>
                                </svg>
                                <span class="text-[11px] font-medium">Dark</span>
                            </button>

                            <button
                                type="button"
                                @click="setTheme('system')"
                                class="flex flex-col items-center gap-2 rounded-xl px-3 py-3 transition hover:bg-slate-50"
                                :class="preference === 'system' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'text-slate-700'">
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <rect x="3" y="4" width="18" height="12" rx="2"></rect>
                                        <path d="M8 20h8M12 16v4"></path>
                                    </svg>
                                    <span class="absolute -right-1 -top-1 inline-flex h-2.5 w-2.5 rounded-full"
                                        :class="effectiveTheme === 'dark' ? 'bg-indigo-400' : 'bg-amber-400'"></span>
                                </div>
                                <span class="text-[11px] font-medium">Auto</span>
                            </button>
                        </div>

                        <p class="mt-2 px-2 pb-1 text-center text-[11px] text-slate-400" x-show="preference === 'system'" x-cloak>
                            Using <span x-text="effectiveTheme"></span> from your device
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>
<section class="relative overflow-hidden rounded-3xl border border-indigo-200 bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600 p-8 md:p-12 text-white">
        <div class="absolute -top-20 -right-16 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-16 -left-10 h-44 w-44 rounded-full bg-indigo-200/20 blur-2xl"></div>

        <div class="relative max-w-3xl">
            <p class="text-xs uppercase tracking-[0.18em] text-indigo-100">Workflow System</p>
            <h1 class="mt-4 text-3xl md:text-5xl font-semibold leading-tight">
                Manage work without the chaos
            </h1>
            <p class="mt-4 text-indigo-100 text-base md:text-lg max-w-2xl">
                Bring tasks, leaves, and documents into one clean workspace your team can use from day one.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ url('org/primenest/login') }}"
                   class="inline-flex items-center rounded-lg bg-white px-6 py-3 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50">
                    Login
                </a>
                <button type="button" data-scroll-target="contact"
                   class="inline-flex items-center rounded-lg border border-white/40 bg-white/10 px-6 py-3 text-sm font-medium text-white transition hover:bg-white/20">
                    Request Demo
                </button>
            </div>
        </div>
    </section>

    <section id="features" class="mt-14 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Task Tracking</h2>
            <p class="mt-2 text-sm text-slate-600">Assign ownership, track progress, and close work faster.</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Leave Workflows</h2>
            <p class="mt-2 text-sm text-slate-600">Approvals stay transparent for managers and employees.</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Document Hub</h2>
            <p class="mt-2 text-sm text-slate-600">Keep files organized, searchable, and secure.</p>
        </article>
    </section>

    <section id="clients" class="mt-14 rounded-3xl border border-indigo-100 bg-white p-8 md:p-10 overflow-hidden">
        <div class="text-center mb-6">
            <h3 class="text-2xl md:text-3xl font-semibold text-slate-900">Our Clients</h3>
            <p class="mt-2 text-sm md:text-base text-slate-600">Trusted by teams from fast-growing startups to enterprise groups.</p>
        </div>

        <div class="relative">
            <div class="absolute left-0 top-0 h-full w-12 bg-gradient-to-r from-white to-transparent z-10"></div>
            <div class="absolute right-0 top-0 h-full w-12 bg-gradient-to-l from-white to-transparent z-10"></div>

            <div class="client-track flex w-max gap-4 md:gap-6">
                @php
                    $clients = ['Nimbus Labs', 'BlueOrbit', 'VertexOne', 'Northline', 'AstraCore', 'CloudMint', 'PrimeNest', 'EchoSpark'];
                    $clientLoop = array_merge($clients, $clients);
                @endphp

                @foreach($clientLoop as $client)
                    <div class="flex h-16 min-w-[170px] items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-5 text-sm font-semibold text-slate-700 shadow-sm">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-gradient-to-br from-indigo-500 to-purple-500 text-[10px] font-bold text-white mr-2">{{ strtoupper(substr($client, 0, 1)) }}</span>
                        {{ $client }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="why-us" class="mt-14 rounded-3xl border border-slate-200 bg-white p-8 md:p-10">
        <h3 class="text-2xl md:text-3xl font-semibold text-slate-900 text-center">Why Us</h3>
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                <p class="font-semibold text-slate-800">Fast Team Adoption</p>
                <p class="mt-2 text-sm text-slate-600">Clean UI and simple workflows reduce onboarding time.</p>
            </div>
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                <p class="font-semibold text-slate-800">Reliable Performance</p>
                <p class="mt-2 text-sm text-slate-600">High availability and stable operations for everyday work.</p>
            </div>
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                <p class="font-semibold text-slate-800">Secure by Design</p>
                <p class="mt-2 text-sm text-slate-600">Role-based controls and protected data handling.</p>
            </div>
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5">
                <p class="font-semibold text-slate-800">Scales With You</p>
                <p class="mt-2 text-sm text-slate-600">Built to support growing teams across multiple functions.</p>
            </div>
        </div>
    </section>

    <section id="upcoming" class="mt-14 rounded-3xl border border-indigo-200 bg-white p-8 md:p-10">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-2xl md:text-3xl font-semibold text-slate-900">Upcoming Features</h3>
            <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">Roadmap</span>
        </div>
        <p class="mt-3 text-slate-600">
            We are actively building the next set of modules to improve planning, visibility, and team productivity.
        </p>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm font-semibold text-slate-800">Schedule Hub</p>
                <p class="mt-1 text-sm text-slate-600">Calendar with national holidays, approved leaves, and team availability.</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm font-semibold text-slate-800">User Profile</p>
                <p class="mt-1 text-sm text-slate-600">Centralized profile details, preferences, and security settings.</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm font-semibold text-slate-800">My Activity Insights</p>
                <p class="mt-1 text-sm text-slate-600">Personal dashboard for average working hours and task trends.</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-sm font-semibold text-slate-800">EOD Work Reports</p>
                <p class="mt-1 text-sm text-slate-600">Daily work report submissions with manager summaries and reminders.</p>
            </article>
        </div>
    </section>

    <section id="contact" class="mt-14 rounded-3xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-purple-50 p-8 md:p-12 text-center">
        <h3 class="text-2xl md:text-3xl font-semibold text-slate-900">Contact Us</h3>
        <p class="mt-3 text-slate-600 max-w-2xl mx-auto">
            Want a walkthrough for your team? Reach us at <span class="font-medium text-indigo-700">support@workflow.com</span> or start now.
        </p>
        <a href="/org/primenest/login"
           class="mt-6 inline-flex items-center rounded-lg bg-indigo-600 px-7 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
            Get Started
        </a>
    </section>
</div>
<script>
    document.querySelectorAll('[data-scroll-target]').forEach(function (el) {
        el.addEventListener('click', function () {
            var sectionId = el.getAttribute('data-scroll-target');
            var target = document.getElementById(sectionId);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>
@endsection
