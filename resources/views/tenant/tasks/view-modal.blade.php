<div x-data="{
        open: false,
        task: null,
        statusClass(status) {
            if (!status) return 'bg-slate-100 text-slate-600';
            const normalized = status.toLowerCase();
            if (normalized === 'done') return 'bg-green-100 text-green-700';
            if (normalized === 'in_progress' || normalized === 'in progress') return 'bg-yellow-100 text-yellow-700';
            return 'bg-slate-100 text-slate-700';
        }
    }"
    x-on:open-task.window="
        task = $event.detail;
        open = true;
     "
    x-show="open"
    @keydown.escape.window="open = false"
    @click.self="open = false"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div
        x-transition
        class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-xl md:p-7"
        role="dialog"
        aria-modal="true">

        <div class="mb-5 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">Task Details</p>
                <h2 class="mt-1 text-lg font-semibold text-slate-900" x-text="task?.title || 'Task'"></h2>
            </div>

            <button
                type="button"
                @click="open = false"
                class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                aria-label="Close task modal">
                <span aria-hidden="true">✕</span>
            </button>
        </div>

        <div class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-sm text-slate-700 whitespace-pre-line"
                x-text="task?.description || 'No description provided.'"></p>
        </div>

        <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
            <div class="rounded-lg bg-white ring-1 ring-slate-200 px-4 py-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Assigned</p>
                <p class="mt-1 font-medium text-slate-800" x-text="task?.assignees || 'Unassigned'"></p>
            </div>

            <div class="rounded-lg bg-white ring-1 ring-slate-200 px-4 py-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                <span
                    class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                    :class="statusClass(task?.status)"
                    x-text="task?.status || 'Todo'"></span>
            </div>

            <div class="rounded-lg bg-white ring-1 ring-slate-200 px-4 py-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Last updated by</p>
                <p class="mt-1 font-medium text-slate-800" x-text="task?.updated_by || task?.updatedBy || 'System'"></p>
            </div>

            <div class="rounded-lg bg-white ring-1 ring-slate-200 px-4 py-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Updated on</p>
                <p class="mt-1 font-medium text-slate-800" x-text="task?.updated_at || task?.updatedAt || '—'"></p>
            </div>
        </div>
    </div>
</div>

<script>
    function openTask(taskId) {
        fetch(`/api/tasks/${taskId}`)
            .then(res => {
                if (!res.ok) throw new Error('Unable to load task details.');
                return res.json();
            })
            .then(data => {
                window.dispatchEvent(
                    new CustomEvent('open-task', {
                        detail: data
                    })
                );
            })
            .catch(() => {
                window.dispatchEvent(
                    new CustomEvent('open-task', {
                        detail: {
                            title: 'Task details unavailable',
                            description: 'We could not load this task right now. Please try again.',
                            assignees: '—',
                            status: 'todo',
                            updated_by: 'System',
                            updated_at: '—'
                        }
                    })
                );
            });
    }
</script>
