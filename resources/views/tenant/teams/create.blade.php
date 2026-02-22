@extends('layouts.tenant')

@section('title', 'Create Team')

@section('content')

<div class="mx-auto w-full max-w-5xl px-4 py-8 md:py-10">

    <div class="mb-5 rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">Create Team</h1>
                <p class="mt-1 text-sm text-slate-600">Set up a team with a manager and assign employees.</p>
            </div>

            <a
                href="{{ route('teams.index', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 transition hover:bg-white hover:text-slate-800">
                Back to Teams
            </a>
        </div>
    </div>

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

    <form method="POST" class="space-y-5">
        @csrf

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
                        value="{{ old('name') }}"
                        required
                        placeholder="Enter team name"
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
                        <option value="">Select manager</option>
                        @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" @selected(old('manager_id') == $manager->id)>
                            {{ $manager->name }}
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
                    Assign Employees
                </label>
                <p class="mt-1 text-xs text-slate-500">Select one or more employees for this team.</p>
            </div>

            <div class="max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-3">
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @forelse($employees as $employee)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50/50">
                        <input
                            type="checkbox"
                            name="users[]"
                            value="{{ $employee->id }}"
                            @checked(collect(old('users', []))->contains($employee->id))
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="truncate">{{ $employee->name }}</span>
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
                href="{{ route('teams.index', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-slate-800">
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700">
                Create Team
            </button>
        </div>
    </form>

</div>

@endsection
