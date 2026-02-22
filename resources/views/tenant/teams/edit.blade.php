@extends('layouts.tenant')

@section('title', 'Edit Team')

@section('content')

<div class="mx-auto w-full max-w-5xl px-4 py-8 md:py-10">

    <div class="mb-5 rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">Edit Team</h1>
                <p class="mt-1 text-sm text-slate-600">Update team details, manager, and assigned members.</p>
            </div>

            <a
                href="/org/{{ request()->route('tenant') }}/teams"
                class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 transition hover:bg-white hover:text-slate-800">
                Back to Teams
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="font-medium">Please fix the following:</p>
        <ul class="mt-1 list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST"
        action="/org/{{ request()->route('tenant') }}/teams/{{ $team->id }}"
        class="space-y-5">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-7">
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-slate-700">
                        Team Name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $team->name) }}"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="manager_id" class="mb-1 block text-sm font-medium text-slate-700">
                        Manager
                    </label>

                    <select
                        id="manager_id"
                        name="manager_id"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($managers as $manager)
                        <option value="{{ $manager->id }}"
                            @selected(old('manager_id', $team->manager_id) == $manager->id)>
                            {{ $manager->name }} ({{ $manager->email }})
                        </option>
                        @endforeach
                    </select>
                    @error('manager_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-7">
            <div class="mb-3">
                <label class="block text-sm font-medium text-slate-700">
                    Team Members
                </label>
                <p class="mt-1 text-xs text-slate-500">
                    Employees can belong to only one team.
                </p>
            </div>

            <div class="max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3">
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @forelse($employees as $employee)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50/50">
                        <input
                            type="checkbox"
                            name="users[]"
                            value="{{ $employee->id }}"
                            @checked(in_array($employee->id, old('users', $teamUserIds)))
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="truncate">
                            {{ $employee->name }}
                            <span class="text-xs text-slate-400">({{ $employee->email }})</span>
                        </span>
                    </label>
                    @empty
                    <p class="text-sm text-slate-500">No employees available.</p>
                    @endforelse
                </div>
            </div>

            @error('users')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('users.*')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <a
                href="/org/{{ request()->route('tenant') }}/teams"
                class="inline-flex items-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-slate-800">
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700">
                Update Team
            </button>
        </div>
    </form>

</div>

@endsection
