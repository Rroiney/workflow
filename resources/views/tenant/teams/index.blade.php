@extends('layouts.tenant')

@section('title', 'Teams')

@section('content')

@php
$user = Auth::guard('tenant')->user();
$isAdmin = $user && $user->role === 'admin';
$teamCount = $teams->count();
@endphp

<div
    x-data="{
        open: false,
        search: '',
        team: {
            name: '',
            manager: '',
            employees: [],
            created: ''
        },
        init() {
            this.$watch('open', value => {
                document.body.classList.toggle('overflow-hidden', value);
            });
        },
        matches(text) {
            const query = this.search.trim().toLowerCase();
            return query === '' || text.includes(query);
        }
    }"
    class="space-y-5">

    {{-- PAGE HEADER --}}
    <div class="rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">Teams</h1>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $teamCount }} {{ \Illuminate\Support\Str::plural('team', $teamCount) }} configured
                </p>
            </div>

            @if($isAdmin)
            <a href="{{ url()->current() }}/create"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                + Create Team
            </a>
            @endif
        </div>
    </div>

    {{-- FILTER --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <div class="w-full md:w-72">
            <label for="team-search" class="sr-only">Search teams</label>
            <input
                id="team-search"
                x-model="search"
                type="text"
                placeholder="Search by team or manager"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    {{-- TABLE (DESKTOP) --}}
    <div class="hidden overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm md:block">
        <table class="w-full text-sm">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-indigo-900">Team</th>
                    <th class="p-3 text-left font-semibold text-indigo-900">Manager</th>
                    @if($isAdmin)
                    <th class="w-28 p-3 text-left font-semibold text-indigo-900">Actions</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @forelse($teams as $team)
                <tr
                    x-show="matches(@js(strtolower($team->name . ' ' . ($team->manager?->name ?? ''))))"
                    x-cloak
                    class="odd:bg-white even:bg-slate-50 transition hover:bg-indigo-50">

                    <td class="p-3 font-medium text-slate-800">{{ $team->name }}</td>
                    <td class="p-3 text-slate-700">{{ $team->manager?->name ?? '-' }}</td>

                    @if($isAdmin)
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                title="View Team"
                                aria-label="View team"
                                class="rounded p-1 transition hover:bg-slate-200"
                                @click="
                                    team = {
                                        name: @js($team->name),
                                        manager: @js($team->manager?->name ?? '-'),
                                        employees: @js($team->users->pluck('name')->values()),
                                        created: @js($team->created_at->format('d M Y'))
                                    };
                                    open = true;
                                ">
                                <img src="{{ asset('icons/documents/view.png') }}" alt="View" class="h-5 w-5">
                            </button>

                            <a
                                href="/org/{{ request()->route('tenant') }}/teams/{{ $team->id }}/edit"
                                title="Edit Team"
                                aria-label="Edit team"
                                class="rounded p-1 transition hover:bg-slate-200">
                                <img src="{{ asset('icons/edit.png') }}" alt="Edit" class="h-5 w-5">
                            </a>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isAdmin ? 3 : 2 }}" class="p-8 text-center text-slate-500">
                        No teams found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CARDS (MOBILE) --}}
    <div class="space-y-3 md:hidden">
        @forelse($teams as $team)
        <article
            x-show="matches(@js(strtolower($team->name . ' ' . ($team->manager?->name ?? ''))))"
            x-cloak
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="font-semibold text-slate-800">{{ $team->name }}</h3>
            <p class="mt-1 text-sm text-slate-600">Manager: {{ $team->manager?->name ?? '-' }}</p>

            @if($isAdmin)
            <div class="mt-3 flex items-center gap-2">
                <button
                    type="button"
                    title="View Team"
                    aria-label="View team"
                    class="rounded p-1 transition hover:bg-slate-200"
                    @click="
                        team = {
                            name: @js($team->name),
                            manager: @js($team->manager?->name ?? '-'),
                            employees: @js($team->users->pluck('name')->values()),
                            created: @js($team->created_at->format('d M Y'))
                        };
                        open = true;
                    ">
                    <img src="{{ asset('icons/documents/view.png') }}" alt="View" class="h-5 w-5">
                </button>

                <a
                    href="/org/{{ request()->route('tenant') }}/teams/{{ $team->id }}/edit"
                    title="Edit Team"
                    aria-label="Edit team"
                    class="rounded p-1 transition hover:bg-slate-200">
                    <img src="{{ asset('icons/edit.png') }}" alt="Edit" class="h-5 w-5">
                </a>
            </div>
            @endif
        </article>
        @empty
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-center text-slate-500 shadow-sm">
            No teams found.
        </div>
        @endforelse
    </div>

    {{-- TEAM DETAILS MODAL --}}
    <div
        x-cloak
        x-show="open"
        @keydown.escape.window="open = false"
        @click.self="open = false"
        class="fixed inset-0 z-[100] h-screen w-screen overflow-y-auto bg-black/50">

        <div class="flex min-h-full items-center justify-center p-4">
            <div @click.stop class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl" x-transition>
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">Team Details</p>
                        <h2 class="mt-1 text-lg font-semibold text-slate-800" x-text="team.name"></h2>
                    </div>

                    <button
                        @click="open = false"
                        class="text-xl text-slate-400 transition hover:text-slate-600"
                        aria-label="Close">
                        &times;
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Manager</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="team.manager"></p>
                    </div>

                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Created On</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="team.created"></p>
                    </div>

                    <div class="rounded-lg bg-slate-50 p-3 sm:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Employees</p>
                        <template x-if="team.employees.length === 0">
                            <p class="mt-1 text-slate-400">No employees assigned</p>
                        </template>
                        <template x-if="team.employees.length > 0">
                            <p class="mt-1 text-slate-800">
                                <span x-text="team.employees.slice(0, 6).join(', ')"></span>
                                <template x-if="team.employees.length > 6">
                                    <span class="text-slate-500">
                                        , +<span x-text="team.employees.length - 6"></span> more
                                    </span>
                                </template>
                            </p>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
