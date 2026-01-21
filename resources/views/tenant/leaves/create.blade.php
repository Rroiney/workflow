@extends('layouts.tenant')

@section('title', 'Apply Leave')

@section('content')

<div class="min-h-screen flex justify-center py-10 px-4">

    <form method="POST"
        action="/org/{{ request()->route('tenant') }}/leaves/apply/submit"
        class="w-full max-w-2xl">
        @csrf

        <div class="bg-white rounded-2xl p-8 space-y-6 ring-1 ring-slate-200/70">

            {{-- HEADER --}}
            <div>
                <h2 class="text-lg font-medium text-slate-900 tracking-tight">
                    Apply for Leave
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Select leave type and date range
                </p>
            </div>

            {{-- LEAVE TYPE --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Leave Type
                </label>

                <select
                    name="leave_type_id"
                    required
                    class="w-full rounded-lg border border-slate-200 bg-white
                           px-3 py-2 text-sm
                           focus:outline-none focus:ring-0
                           focus:border-indigo-500
                           focus:bg-indigo-50/30
                           transition">
                    @foreach($leaveTypes as $type)
                    <option value="{{ $type->id }}">
                        {{ $type->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- DATE RANGE --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        From Date
                    </label>

                    <input
                        type="date"
                        name="from_date"
                        required
                        class="w-full rounded-lg border border-slate-200 bg-white
                               px-3 py-2 text-sm
                               focus:outline-none focus:ring-0
                               focus:border-indigo-500
                               focus:bg-indigo-50/30
                               transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        To Date
                    </label>

                    <input
                        type="date"
                        name="to_date"
                        required
                        class="w-full rounded-lg border border-slate-200 bg-white
                               px-3 py-2 text-sm
                               focus:outline-none focus:ring-0
                               focus:border-indigo-500
                               focus:bg-indigo-50/30
                               transition">
                </div>
            </div>

            {{-- REASON --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Reason <span class="text-slate-400">(optional)</span>
                </label>

                <textarea
                    name="reason"
                    rows="3"
                    placeholder="Brief reason for leave"
                    class="w-full rounded-lg border border-slate-200 bg-white
                           px-3 py-2 text-sm
                           focus:outline-none focus:ring-0
                           focus:border-indigo-500
                           focus:bg-indigo-50/30
                           transition"></textarea>
            </div>

            {{-- ACTION --}}
            <div class="pt-4 flex items-center gap-3">
                <button
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg
                           text-sm font-medium
                           hover:bg-indigo-700 transition">
                    Submit Leave
                </button>

                <a
                    href="{{ route('leaves.index', ['tenant' => request()->route('tenant')]) }}"
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