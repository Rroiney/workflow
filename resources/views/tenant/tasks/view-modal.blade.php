<div x-data="{ open:false, task:null }"
    x-on:open-task.window="
        task = $event.detail;
        open = true;
     "
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

    <div class="bg-white w-full max-w-xl rounded-xl shadow-lg p-6">

        <div class="flex justify-between items-start mb-4">
            <h2 class="text-lg font-semibold text-slate-800"
                x-text="task.title"></h2>

            <button @click="open=false" class="text-slate-400 hover:text-slate-600">
                ✕
            </button>
        </div>

        <p class="text-sm text-slate-600 mb-4"
            x-text="task.description"></p>

        <div class="space-y-3 text-sm">

            <div>
                <span class="font-medium text-slate-700">Assigned:</span>
                <span x-text="task.assignees"></span>
            </div>

            <div>
                <span class="font-medium text-slate-700">Status:</span>
                <span x-text="task.status"></span>
            </div>

            <div>
                <span class="font-medium text-slate-700">Last updated by:</span>
                <span x-text="task.updated_by"></span>
            </div>

            <div>
                <span class="font-medium text-slate-700">Updated on:</span>
                <span x-text="task.updated_at"></span>
            </div>

        </div>
    </div>
</div>

<script>
    function openTask(taskId) {
        fetch(`/api/tasks/${taskId}`)
            .then(res => res.json())
            .then(data => {
                window.dispatchEvent(
                    new CustomEvent('open-task', {
                        detail: data
                    })
                );
            });
    }
</script>