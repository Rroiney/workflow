@extends('layouts.tenant')

@section('title', 'Create Team')

@section('content')

<div class="min-h-screen flex justify-center py-10 px-4">

    <form method="POST" class="w-full max-w-2xl">
        @csrf

        <div class="bg-white rounded-2xl p-8 space-y-6 ring-1 ring-slate-200/70">

            {{-- HEADER --}}
            <div>
                <h2 class="text-lg font-medium text-slate-900 tracking-tight">
                    Create Team
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Set up a team and assign a manager and employees
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
                    required
                    placeholder="Enter team name"
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
                    <option value="">Select manager</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}">
                            {{ $manager->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ASSIGN EMPLOYEES --}}
            <div>
                <p class="text-sm font-medium text-slate-700 mb-2">
                    Assign Employees
                </p>

                <div class="max-h-48 overflow-y-auto rounded-lg
                            border border-slate-200 bg-slate-50/40 p-3 space-y-2">

                    @forelse($employees as $employee)
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input
                                type="checkbox"
                                name="users[]"
                                value="{{ $employee->id }}"
                                class="rounded border-slate-300
                                       text-indigo-600
                                       focus:ring-0 focus:outline-none">
                            {{ $employee->name }}
                        </label>
                    @empty
                        <p class="text-sm text-slate-500">
                            No employees available.
                        </p>
                    @endforelse

                </div>
            </div>

            {{-- ACTION --}}
            <div class="pt-4 flex items-center gap-3">
                <button
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg
                           text-sm font-medium
                           hover:bg-indigo-700 transition">
                    Create Team
                </button>

                <a
                    href="{{ route('teams.index', ['tenant' => request()->route('tenant')]) }}"
                    class="px-5 py-2.5 rounded-lg border border-slate-200
                           text-sm text-slate-600
                           hover:bg-slate-50 hover:text-slate-800 transition">
                    Cancel
                </a>
            </div>

        </div>
    </form>

</div>

@endsection
