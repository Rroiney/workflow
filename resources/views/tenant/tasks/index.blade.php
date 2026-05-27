@extends('layouts.tenant')

@section('title', 'Tasks')

@section('content')

@php
$user = auth('tenant')->user();
$isManagerOrAdmin = $user->isAdmin() || $user->isManager();
$taskCount = $tasks->count();
@endphp

<div
    x-data="{
        confirmOpen: false,
        confirmFormId: '',
        confirmLabel: '',
        open: false,
        task: {},
        init() {
            this.$watch('open', value => {
                document.body.classList.toggle('overflow-hidden', value);
            });
        },
        search: '',
        statusFilter: '',
        matches(text, status) {
            const query = this.search.trim().toLowerCase();
            const matchesText = query === '' || text.includes(query);
            const matchesStatus = this.statusFilter === '' || this.statusFilter === status;
            return matchesText && matchesStatus;
        },
        clearFilters() {
            this.search = '';
            this.statusFilter = '';
        }
    }"
    class="space-y-5">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900">Tasks</h1>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $taskCount }} {{ \Illuminate\Support\Str::plural('task', $taskCount) }} in this workspace
                </p>
            </div>

            @if($isManagerOrAdmin)
            <a href="{{ route('tasks.create', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                + Create Task
            </a>
            @endif
        </div>
    </div>

    {{-- ================= FILTERS ================= --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <div class="flex flex-wrap items-center gap-3">
            <div class="w-full md:w-72">
                <label for="task-search" class="sr-only">Search tasks</label>
                <input
                    id="task-search"
                    x-model="search"
                    type="text"
                    placeholder="Search by task title or assignee"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="w-full md:w-48">
                <label for="task-status-filter" class="sr-only">Filter by status</label>
                <select
                    id="task-status-filter"
                    x-model="statusFilter"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="todo">Todo</option>
                    <option value="in_progress">In Progress</option>
                    <option value="done">Done</option>
                </select>
            </div>

            <button
                type="button"
                @click="clearFilters"
                class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50">
                Clear
            </button>
        </div>
    </div>

    {{-- ================= VIEW TASK MODAL ================= --}}
    <div
        x-show="open"
        x-cloak
        @keydown.escape.window="open = false"
        @click.self="open = false"
        class="fixed inset-0 z-[100] h-screen w-screen overflow-y-auto bg-black/50">

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl" x-transition>
                <div class="mb-4 flex items-start justify-between gap-4">
                    <h2 class="text-lg font-semibold text-slate-800" x-text="task.title"></h2>

                    <button
                        type="button"
                        @click="open = false"
                        class="text-xl leading-none text-slate-400 transition hover:text-slate-600"
                        aria-label="Close task details">
                        &times;
                    </button>
                </div>

                <p class="mb-5 text-sm text-slate-600" x-text="task.description"></p>

                <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Assigned</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="task.assignees"></p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="task.status"></p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Last updated by</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="task.updatedBy"></p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Updated on</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="task.updatedAt"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= TASK TABLE (DESKTOP) ================= --}}
    <div class="hidden overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm md:block">
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
                @php
                $canUpdateStatus = $user->can('updateStatus', $task);
                @endphp
                <tr
                    x-show="matches(@js(strtolower($task->title . ' ' . $task->users->pluck('name')->join(' '))), '{{ $task->status }}')"
                    x-cloak
                    class="odd:bg-slate-50 even:bg-white transition hover:bg-slate-100">

                    <td class="p-3 font-medium text-slate-800">
                        {{ $task->title }}
                    </td>

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

                    <td class="p-3">
                        @if($canUpdateStatus)
                        <form method="POST" action="{{ route('tasks.status', ['tenant' => request()->route('tenant'),'task' => $task->id]) }}">
                            @csrf
                            <select
                                name="status"
                                onchange="this.form.submit()"
                                class="rounded-md border-slate-300 px-2 py-1 text-xs
                                       focus:border-indigo-500 focus:ring-indigo-500
                                       @if($task->status === 'done') bg-green-100 text-green-700
                                       @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-700
                                       @else bg-slate-200 text-slate-700
                                       @endif">
                                <option value="todo" @selected($task->status==='todo')>Todo</option>
                                <option value="in_progress" @selected($task->status==='in_progress')>In Progress</option>
                                <option value="done" @selected($task->status==='done')>Done</option>
                            </select>
                        </form>
                        @else
                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                            @if($task->status === 'done') bg-green-100 text-green-700
                            @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-700
                            @else bg-slate-200 text-slate-700
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                        @endif
                    </td>

                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="rounded p-1 transition hover:bg-slate-200"
                                title="View"
                                aria-label="View task"
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
                                <img src="{{ asset('icons/documents/view.png') }}" class="h-5 w-5" alt="View">
                            </button>

                            @if($isManagerOrAdmin)
                            <a
                                href="{{ url('/org/' . request()->route('tenant') . '/tasks/' . $task->id . '/edit') }}"
                                title="Edit"
                                aria-label="Edit task"
                                class="rounded p-1 transition hover:bg-slate-200">
                                <img src="{{ asset('icons/edit.png') }}" class="h-5 w-5" alt="Edit">
                            </a>

                            <form
                                id="delete-task-{{ $task->id }}"
                                method="POST"
                                action="{{ route('tasks.destroy', ['tenant' => request()->route('tenant'), 'task' => $task->id]) }}">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="button"
                                    title="Delete"
                                    aria-label="Delete task"
                                    class="rounded p-1 transition hover:bg-red-50"
                                    @click="
                                        confirmFormId = 'delete-task-{{ $task->id }}';
                                        confirmLabel = '{{ $task->title }}';
                                        confirmOpen = true;
                                    ">
                                    <img src="{{ asset('icons/documents/delete.png') }}" class="h-5 w-5" alt="Delete">
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center">
                        <p class="text-slate-600">No tasks yet.</p>
                        @if($isManagerOrAdmin)
                        <a
                            href="{{ route('tasks.create', ['tenant' => request()->route('tenant')]) }}"
                            class="mt-3 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                            Create your first task
                        </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= TASK CARDS (MOBILE) ================= --}}
    <div class="space-y-3 md:hidden">
        @forelse($tasks as $task)
        @php
        $canUpdateStatus = $user->can('updateStatus', $task);
        @endphp
        <article
            x-show="matches(@js(strtolower($task->title . ' ' . $task->users->pluck('name')->join(' '))), '{{ $task->status }}')"
            x-cloak
            class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <h3 class="font-semibold text-slate-800">{{ $task->title }}</h3>

                <button
                    type="button"
                    class="rounded p-1 transition hover:bg-slate-200"
                    title="View"
                    aria-label="View task"
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
                    <img src="{{ asset('icons/documents/view.png') }}" class="h-5 w-5" alt="View">
                </button>
            </div>

            <p class="mt-2 text-sm text-slate-600">
                @if($task->users->count())
                {{ $task->users->take(2)->pluck('name')->join(', ') }}
                @if($task->users->count() > 2)
                <span class="text-xs text-slate-400">+{{ $task->users->count() - 2 }} more</span>
                @endif
                @else
                Unassigned
                @endif
            </p>

            <div class="mt-3 flex items-center justify-between gap-3">
                <div>
                    @if($canUpdateStatus)
                    <form method="POST" action="{{ route('tasks.status', ['tenant' => request()->route('tenant'), 'task' => $task->id]) }}">
                        @csrf
                        <select
                            name="status"
                            onchange="this.form.submit()"
                            class="rounded-md border-slate-300 px-2 py-1 text-xs
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   @if($task->status === 'done') bg-green-100 text-green-700
                                   @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-700
                                   @else bg-slate-200 text-slate-700
                                   @endif">
                            <option value="todo" @selected($task->status==='todo')>Todo</option>
                            <option value="in_progress" @selected($task->status==='in_progress')>In Progress</option>
                            <option value="done" @selected($task->status==='done')>Done</option>
                        </select>
                    </form>
                    @else
                    <span
                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                        @if($task->status === 'done') bg-green-100 text-green-700
                        @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-700
                        @else bg-slate-200 text-slate-700
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                    @endif
                </div>

                @if($isManagerOrAdmin)
                <div class="flex items-center gap-2">
                    <a
                        href="{{ url('/org/' . request()->route('tenant') . '/tasks/' . $task->id . '/edit') }}"
                        title="Edit"
                        aria-label="Edit task"
                        class="rounded p-1 transition hover:bg-slate-200">
                        <img src="{{ asset('icons/edit.png') }}" class="h-5 w-5" alt="Edit">
                    </a>

                    <form
                        id="mobile-delete-task-{{ $task->id }}"
                        method="POST"
                        action="{{ route('tasks.destroy', ['tenant' => request()->route('tenant'), 'task' => $task->id]) }}">
                        @csrf
                        @method('DELETE')

                        <button
                            type="button"
                            title="Delete"
                            aria-label="Delete task"
                            class="rounded p-1 transition hover:bg-red-50"
                            @click="
                                confirmFormId = 'mobile-delete-task-{{ $task->id }}';
                                confirmLabel = '{{ $task->title }}';
                                confirmOpen = true;
                            ">
                            <img src="{{ asset('icons/documents/delete.png') }}" class="h-5 w-5" alt="Delete">
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </article>
        @empty
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            <p class="text-slate-600">No tasks yet.</p>
            @if($isManagerOrAdmin)
            <a
                href="{{ route('tasks.create', ['tenant' => request()->route('tenant')]) }}"
                class="mt-3 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                Create your first task
            </a>
            @endif
        </div>
        @endforelse
    </div>

    <x-confirm-delete />
</div>
@endsection
