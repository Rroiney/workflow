@extends('layouts.tenant')

@section('title', 'Edit Team')

@section('content')

<div class="min-h-screen flex justify-center py-10 px-4">

    <div class="w-full max-w-2xl">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200
                        px-4 py-2 text-sm text-green-700">
            {{ session('success') }}
        </div>
        @endif

        {{-- ERROR MESSAGES --}}
        @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200
                        px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST"
            action="/org/{{ request()->route('tenant') }}/teams/{{ $team->id }}">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl p-8 space-y-6 ring-1 ring-slate-200/70">

                {{-- HEADER --}}
                <div>
                    <h2 class="text-lg font-medium text-slate-900 tracking-tight">
                        Edit Team
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Update team details, manager, and members
                    </p>
                </div>

                {{-- TEAM NAME --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Team Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $team->name) }}"
                        required
                        class="w-full rounded-lg border border-slate-200 bg-white
                               px-3 py-2 text-sm
                               focus:outline-none focus:ring-0
                               focus:border-indigo-500
                               focus:bg-indigo-50/30
                               transition">
                </div>

                {{-- MANAGER --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Manager
                    </label>

                    <select
                        name="manager_id"
                        required
                        class="w-full rounded-lg border border-slate-200 bg-white
                               px-3 py-2 text-sm
                               focus:outline-none focus:ring-0
                               focus:border-indigo-500
                               focus:bg-indigo-50/30
                               transition">
                        @foreach($managers as $manager)
                        <option value="{{ $manager->id }}"
                            @selected($manager->id === $team->manager_id)>
                            {{ $manager->name }} ({{ $manager->email }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- TEAM MEMBERS --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Team Members
                    </label>

                    <div class="max-h-48 overflow-y-auto rounded-lg
                border border-slate-200 bg-slate-50/40 p-3 space-y-2">

                        @forelse($employees as $employee)
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input
                                type="checkbox"
                                name="users[]"
                                value="{{ $employee->id }}"
                                @checked(in_array($employee->id, $teamUserIds))
                            class="rounded border-slate-300
                            text-indigo-600
                            focus:outline-none focus:ring-0">
                            <span>
                                {{ $employee->name }}
                                <span class="text-xs text-slate-400">
                                    ({{ $employee->email }})
                                </span>
                            </span>
                        </label>
                        @empty
                        <p class="text-sm text-slate-500">
                            No employees available.
                        </p>
                        @endforelse

                    </div>

                    <p class="text-xs text-slate-500 mt-1">
                        Employees can belong to only one team.
                    </p>
                </div>


                {{-- ACTIONS --}}
                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="submit"
                        class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg
                               text-sm font-medium
                               hover:bg-indigo-700 transition">
                        Update Team
                    </button>

                    <a
                        href="/org/{{ request()->route('tenant') }}/teams"
                        class="px-5 py-2.5 rounded-lg border border-slate-200
                               text-sm text-slate-600
                               hover:bg-slate-50 hover:text-slate-800 transition">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

</div>

@endsection