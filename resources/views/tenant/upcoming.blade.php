@extends('layouts.tenant')

@section('title', 'Upcoming Features')

@section('content')

<div class="space-y-5">
    <div class="rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <h1 class="text-xl md:text-2xl font-semibold text-slate-900">Upcoming Features</h1>
        <p class="mt-1 text-sm text-slate-600">
            Preview planned modules and product improvements for WorkFlow.
        </p>
    </div>

    <section id="schedule-hub" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-slate-900">Schedule Hub</h2>
            <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">Planned</span>
        </div>
        <p class="mt-2 text-sm text-slate-600">
            A monthly calendar showing national holidays, approved leaves, and team availability at a glance.
        </p>
        <div class="mt-4 grid grid-cols-7 gap-2 text-xs">
            @for($i = 1; $i <= 21; $i++)
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-2 text-center text-slate-600">
                {{ $i }}
            </div>
            @endfor
        </div>
    </section>

    <section id="profile" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-slate-900">User Profile</h2>
            <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">Planned</span>
        </div>
        <p class="mt-2 text-sm text-slate-600">
            Personal details, role info, emergency contacts, and profile preferences in one place.
        </p>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Basic Info</p>
                <p class="mt-1 text-sm text-slate-700">Name, email, phone, designation</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Work Settings</p>
                <p class="mt-1 text-sm text-slate-700">Timezone, language, notifications</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Security</p>
                <p class="mt-1 text-sm text-slate-700">Password, sessions, device history</p>
            </div>
        </div>
    </section>

    <section id="activity" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-slate-900">My Activity</h2>
            <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">Planned</span>
        </div>
        <p class="mt-2 text-sm text-slate-600">
            Individual productivity dashboard with average working hours, active days, and task trends.
        </p>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Avg. Working Hours</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">7h 40m</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Focus Days / Week</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">4.2</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Tasks Closed / Week</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">11</p>
            </div>
        </div>
    </section>

    <section id="eod-reports" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-slate-900">EOD Work Reports</h2>
            <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-medium">Planned</span>
        </div>
        <p class="mt-2 text-sm text-slate-600">
            Daily end-of-day updates submitted by employees and auto-shared with managers for visibility.
        </p>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Completed Today</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">6 Tasks</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Blocked Items</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">2</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Manager Acknowledgement</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">84%</p>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Feature Roadmap</h2>
        <ul class="mt-3 space-y-2 text-sm text-slate-700">
            <li class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">Holiday calendar import (regional + company holidays)</li>
            <li class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">Team leave overlap warnings during task assignment</li>
            <li class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">Profile completion score and onboarding checklist</li>
            <li class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">Personal productivity insights with weekly trend reports</li>
            <li class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">EOD daily report workflow with manager digest and reminders</li>
        </ul>
    </section>
</div>

@endsection
