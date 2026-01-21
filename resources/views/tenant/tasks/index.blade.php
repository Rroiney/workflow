@extends('layouts.tenant')

@section('title', 'Tasks')

@section('content')

@php
$user = auth('tenant')->user();
$isManagerOrAdmin = $user->isAdmin() || $user->isManager();
@endphp


<div x-data="{
    confirmOpen: false,
    confirmFormId: '',
    confirmLabel: '',
    open:false, task:{}
}">


    {{-- ================= VIEW TASK MODAL ================= --}}
    <div x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

        <div class="bg-white w-full max-w-xl rounded-xl shadow-lg p-6">

            <div class="flex justify-between items-start mb-4">
                <h2 class="text-lg font-semibold text-slate-800"
                    x-text="task.title"></h2>

                <button @click="open=false"
                    class="text-slate-400 hover:text-slate-600 text-xl">
                    &times;
                </button>
            </div>

            <p class="text-sm text-slate-600 mb-4"
                x-text="task.description"></p>

            <div class="space-y-2 text-sm text-slate-700">
                <div><strong>Assigned:</strong> <span x-text="task.assignees"></span></div>
                <div><strong>Status:</strong> <span x-text="task.status"></span></div>
                <div><strong>Last updated by:</strong> <span x-text="task.updatedBy"></span></div>
                <div><strong>Updated on:</strong> <span x-text="task.updatedAt"></span></div>
            </div>
        </div>
    </div>

    {{-- ================= CREATE TASK ================= --}}
    @if($isManagerOrAdmin)
    <a href="{{ route('tasks.create', ['tenant' => request()->route('tenant')]) }}"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg mb-4 inline-block hover:bg-indigo-700 transition">
        + Create Task
    </a>
    @endif

    {{-- ================= TASK TABLE ================= --}}
    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="p-3 text-left font-semibold text-indigo-900">Title</th>
                    <th class="p-3 text-left font-semibold text-indigo-900">Assigned</th>
                    <th class="p-3 text-left font-semibold text-indigo-900">Status</th>
                    <th class="p-3 text-left font-semibold text-indigo-900">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tasks as $task)
                <tr class="odd:bg-slate-50 even:bg-white hover:bg-slate-100 transition">

                    {{-- TITLE --}}
                    <td class="p-3 font-medium text-slate-800">
                        {{ $task->title }}
                    </td>

                    {{-- ASSIGNED USERS --}}
                    <td class="p-3 text-slate-700">
                        @if($task->users->count())
                        {{ $task->users->take(2)->pluck('name')->join(', ') }}
                        @if($task->users->count() > 2)
                        <span class="text-xs text-slate-400">
                            +{{ $task->users->count() - 2 }} more
                        </span>
                        @endif
                        @else
                        <span class="text-slate-400">Unassigned</span>
                        @endif
                    </td>

                    {{-- STATUS (SINGLE DROPDOWN) --}}
                    <td class="p-3">
                        <form method="POST"
                            action="/org/{{ request()->route('tenant') }}/tasks/{{ $task->id }}/status">
                            @csrf
                            <select name="status"
                                onchange="this.form.submit()"
                                class="rounded-md text-xs px-2 py-1
                                           border-slate-300
                                           focus:border-indigo-500
                                           focus:ring-indigo-500
                                           @if($task->status === 'done') bg-green-100 text-green-700
                                           @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-700
                                           @else bg-slate-200 text-slate-700
                                           @endif">
                                <option value="todo" @selected($task->status==='todo')>
                                    Todo
                                </option>
                                <option value="in_progress" @selected($task->status==='in_progress')>
                                    In Progress
                                </option>
                                <option value="done" @selected($task->status==='done')>
                                    Done
                                </option>
                            </select>
                        </form>
                    </td>

                    {{-- ACTIONS --}}
                    <td class="p-3">
                        <div class="flex items-center gap-3">

                            {{-- VIEW --}}
                            <button
                                class="hover:opacity-80"
                                title="View"
                                data-title="{{ $task->title }}"
                                data-description="{{ $task->description ?? '—' }}"
                                data-assignees="{{ $task->users->pluck('name')->join(', ') ?: 'Unassigned' }}"
                                data-status="{{ ucfirst(str_replace('_',' ', $task->status)) }}"
                                data-updated-by="{{ $task->creator?->name ?? 'System' }}"
                                data-updated-at="{{ $task->updated_at->format('d M Y, h:i A') }}"
                                @click="
        task = {
            title: $el.dataset.title,
            description: $el.dataset.description,
            assignees: $el.dataset.assignees,
            status: $el.dataset.status,
            updatedBy: $el.dataset.updatedBy,
            updatedAt: $el.dataset.updatedAt
        };
        open = true;
    ">
                                <img src="{{ asset('icons/documents/view.png') }}" class="w-5 h-5">
                            </button>


                            {{-- EDIT --}}
                            @if($isManagerOrAdmin)
                            <a href="{{ url('/org/' . request()->route('tenant') . '/tasks/' . $task->id . '/edit') }}"
                                title="Edit"
                                class="hover:opacity-80">
                                <img src="{{ asset('icons/edit.png') }}" class="w-5 h-5">
                            </a>
                            @endif

                            {{-- DELETE --}}
                            @if($isManagerOrAdmin)
                            <form
                                id="delete-task-{{ $task->id }}"
                                method="POST"
                                action="{{ route('tasks.destroy', [
        'tenant' => request()->route('tenant'),
        'task'   => $task->id
    ]) }}">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="button"
                                    title="Delete"
                                    class="hover:opacity-80"
                                    @click="
            confirmFormId = 'delete-task-{{ $task->id }}';
            confirmLabel = '{{ $task->title }}';
            confirmOpen = true;
        ">
                                    <img src="{{ asset('icons/documents/delete.png') }}" class="w-5 h-5">
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-slate-500">
                        No tasks to show
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
    <x-confirm-delete />
</div>
@endsection