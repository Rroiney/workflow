@extends('layouts.tenant')

@section('title', 'Apply Leave')

@section('content')

<div class="mx-auto w-full max-w-5xl px-4 py-8 md:py-10">

    {{-- PAGE HEADER --}}
    <div class="mb-5 rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">Apply for Leave</h1>
                <p class="mt-1 text-sm text-slate-600">Choose leave type, date range, and add context if needed.</p>
            </div>

            <a
                href="{{ route('leaves.index', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 transition hover:bg-white hover:text-slate-800">
                Back to Leaves
            </a>
        </div>
    </div>

    <form method="POST"
        action="{{ route('leaves.store', ['tenant' => request()->route('tenant')]) }}"
        class="space-y-5">
        @csrf

        @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-medium">Please fix the following:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-7">
            <div class="grid grid-cols-1 gap-5">
                {{-- LEAVE TYPE --}}
                <div>
                    <label for="leave_type_id" class="mb-1 block text-sm font-medium text-slate-700">
                        Leave Type
                    </label>
                    <select
                        id="leave_type_id"
                        name="leave_type_id"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($leaveTypes as $type)
                        <option
                            value="{{ $type->id }}"
                            @selected(old('leave_type_id') == $type->id)>
                            {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('leave_type_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DATE RANGE --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="from_date" class="mb-1 block text-sm font-medium text-slate-700">
                            From Date
                        </label>
                        <input
                            id="from_date"
                            type="date"
                            name="from_date"
                            value="{{ old('from_date') }}"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                                   focus:border-indigo-500 focus:ring-indigo-500">
                        @error('from_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="to_date" class="mb-1 block text-sm font-medium text-slate-700">
                            To Date
                        </label>
                        <input
                            id="to_date"
                            type="date"
                            name="to_date"
                            value="{{ old('to_date') }}"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                                   focus:border-indigo-500 focus:ring-indigo-500">
                        @error('to_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- REASON --}}
                <div>
                    <label for="reason" class="mb-1 block text-sm font-medium text-slate-700">
                        Reason <span class="font-normal text-slate-400">(optional)</span>
                    </label>
                    <textarea
                        id="reason"
                        name="reason"
                        rows="4"
                        placeholder="Brief reason for leave"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                               focus:border-indigo-500 focus:ring-indigo-500">{{ old('reason') }}</textarea>
                    @error('reason')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a
                href="{{ route('leaves.index', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-slate-800">
                Cancel
            </a>

            <button
                type="submit"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700">
                Submit Leave
            </button>
        </div>
    </form>

</div>

@endsection
