@extends('layouts.tenant')

@section('title', 'Edit Task')

@section('content')

<div class="min-h-screen flex justify-center py-10 px-4">

    <form method="POST"
        action="{{ route('tasks.update', ['tenant' => request()->route('tenant'), 'task' => $task->id]) }}"
        class="w-full max-w-2xl">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl p-8 space-y-6 ring-1 ring-slate-200/70">

            {{-- HEADER --}}
            <div>
                <h2 class="text-lg font-medium text-slate-900 tracking-tight">
                    Edit Task
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Update task details or reassign team members
                </p>
            </div>

            {{-- TITLE --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Task Title
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $task->title) }}"
                    required
                    class="w-full rounded-lg border border-slate-200 bg-white
                           px-3 py-2 text-sm
                           focus:outline-none focus:ring-0
                           focus:border-indigo-500
                           focus:bg-indigo-50/30
                           transition">
            </div>

            {{-- DESCRIPTION --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Description
                </label>

                <textarea
                    name="description"
                    rows="3"
                    class="w-full rounded-lg border border-slate-200 bg-white
                           px-3 py-2 text-sm
                           focus:outline-none focus:ring-0
                           focus:border-indigo-500
                           focus:bg-indigo-50/30
                           transition">{{ old('description', $task->description) }}</textarea>
            </div>

            {{-- ASSIGN USERS --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Assign Team Members
                </label>

                <p class="text-xs text-slate-500 mb-2">
                    Select one or more users
                </p>

                <div class="max-h-48 overflow-y-auto rounded-lg
                border border-slate-200 bg-slate-50/40 p-3 space-y-2">

                    @foreach($users as $user)
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input
                            type="checkbox"
                            name="users[]"
                            value="{{ $user->id }}"
                            @checked($task->users->pluck('id')->contains($user->id))
                        class="rounded border-slate-300
                        text-indigo-600
                        focus:outline-none focus:ring-0">

                        <span>{{ $user->name }}</span>
                    </label>
                    @endforeach

                </div>
            </div>


            {{-- ACTIONS --}}
            <div class="pt-4 flex items-center gap-3">
                <button
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg
                           text-sm font-medium
                           hover:bg-indigo-700 transition">
                    Update Task
                </button>

                <a
                    href="{{ route('tasks.index', ['tenant' => request()->route('tenant')]) }}"
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