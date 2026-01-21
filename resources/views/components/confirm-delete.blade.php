<div
    x-show="confirmOpen"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40">

    <div class="bg-white w-full max-w-sm rounded-xl shadow-lg p-6">

        <h3 class="text-lg font-semibold text-slate-800 mb-2">
            Confirm Delete
        </h3>

        <p class="text-sm text-slate-600 mb-5">
            Are you sure you want to delete this?
            <span class="font-medium text-slate-800"
                x-text="confirmLabel"></span>
            <br>
            This action cannot be undone.
        </p>

        <div class="flex justify-end gap-3">

            <button
                @click="confirmOpen = false"
                class="px-4 py-2 text-sm rounded-lg
                       border border-slate-300
                       text-slate-700 hover:bg-slate-100">
                Cancel
            </button>

            <button
                @click="document.getElementById(confirmFormId).submit()"
                class="px-4 py-2 text-sm rounded-lg
                       bg-red-600 text-white hover:bg-red-700">
                Delete
            </button>

        </div>
    </div>
</div>