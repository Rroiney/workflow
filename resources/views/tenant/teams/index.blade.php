@extends('layouts.tenant')

@section('title', 'Teams')

@section('content')

@php
$user = Auth::guard('tenant')->user();
@endphp

<div
    x-data="{
        open: false,
        team: {
            name: '',
            manager: '',
            employees: [],
            created: ''
        }
    }"
    class="space-y-4">

    {{-- PAGE ACTION --}}
    @if($user && $user->role === 'admin')
    <a href="{{ url()->current() }}/create"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg inline-block
                  hover:bg-indigo-700 transition">
        + Create Team
    </a>
    @endif

    {{-- TEAMS TABLE --}}
    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-indigo-900">
                        Team
                    </th>
                    <th class="p-3 text-left font-semibold text-indigo-900">
                        Manager
                    </th>

                    @if($user && $user->role === 'admin')
                    <th class="p-3 text-left font-semibold text-indigo-900 w-24">
                        Actions
                    </th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @forelse($teams as $team)
                <tr class="odd:bg-slate-50 even:bg-white hover:bg-indigo-50 transition">

                    {{-- TEAM NAME --}}
                    <td class="p-3 font-medium text-slate-800">
                        {{ $team->name }}
                    </td>

                    {{-- MANAGER --}}
                    <td class="p-3 text-slate-700">
                        {{ $team->manager?->name ?? '-' }}
                    </td>

                    {{-- ACTIONS --}}
                    @if($user && $user->role === 'admin')
                    <td class="p-3">
                        <div class="flex items-center gap-3">

                            {{-- VIEW --}}
                            <button
                                title="View Team"
                                class="hover:opacity-80"
                                @click="
                                            team = {
                                                name: '{{ $team->name }}',
                                                manager: '{{ $team->manager?->name ?? '-' }}',
                                                employees: [
                                                    @foreach($team->users as $member)
                                                        '{{ $member->name }}',
                                                    @endforeach
                                                ],
                                                created: '{{ $team->created_at->format('d M Y') }}'
                                            };
                                            open = true;
                                        ">
                                <img
                                    src="{{ asset('icons/documents/view.png') }}"
                                    alt="View"
                                    class="w-5 h-5">
                            </button>

                            {{-- EDIT --}}
                            <a href="/org/{{ request()->route('tenant') }}/teams/{{ $team->id }}/edit"
                                title="Edit Team"
                                class="hover:opacity-80">
                                <img
                                    src="{{ asset('icons/edit.png') }}"
                                    alt="Edit"
                                    class="w-5 h-5">
                            </a>

                        </div>
                    </td>
                    @endif

                </tr>
                @empty
                <tr>
                    <td colspan="{{ $user && $user->role === 'admin' ? 3 : 2 }}"
                        class="p-6 text-center text-slate-500">
                        No teams found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- TEAM DETAILS MODAL --}}
    <div
        x-cloak
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

        <div
            @click.stop
            class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6">

            {{-- HEADER --}}
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-lg font-semibold text-slate-800">
                    Team Details
                </h2>

                <button
                    @click="open = false"
                    class="text-slate-400 hover:text-slate-600 text-xl">
                    &times;
                </button>
            </div>

            {{-- BODY --}}
            <div class="space-y-3 text-sm text-slate-700">

                <div>
                    <strong>Team Name:</strong>
                    <span x-text="team.name"></span>
                </div>

                <div>
                    <strong>Manager:</strong>
                    <span x-text="team.manager"></span>
                </div>

                <div>
                    <strong>Employees:</strong>

                    <span class="text-slate-700">
                        <template x-if="team.employees.length === 0">
                            <span class="text-slate-400">No employees assigned</span>
                        </template>

                        <template x-if="team.employees.length > 0">
                            <span>
                                <span
                                    x-text="team.employees.slice(0, 4).join(', ')">
                                </span>

                                <template x-if="team.employees.length > 4">
                                    <span class="text-slate-400">
                                        , +<span x-text="team.employees.length - 4"></span> more
                                    </span>
                                </template>
                            </span>
                        </template>
                    </span>
                </div>


                <div>
                    <strong>Created On:</strong>
                    <span x-text="team.created"></span>
                </div>

            </div>

        </div>
    </div>

</div>

@endsection