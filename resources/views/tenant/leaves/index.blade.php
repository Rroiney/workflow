@extends('layouts.tenant')

@section('title', 'Leaves')

@section('content')

@php
$user = auth('tenant')->user();
$canApply = in_array($user->role, ['employee', 'manager']);
$showBalances = in_array($user->role, ['employee', 'manager']) && $balances?->isNotEmpty();
@endphp

<div
    x-data="{
        open: false,
        leave: {},
        init() {
            this.$watch('open', value => {
                document.body.classList.toggle('overflow-hidden', value);
            });
        },
        statusClass(status) {
            if (!status) return 'bg-slate-100 text-slate-700';
            const normalized = status.toLowerCase();
            if (normalized === 'approved') return 'bg-green-100 text-green-700';
            if (normalized === 'rejected') return 'bg-red-100 text-red-700';
            return 'bg-yellow-100 text-yellow-700';
        }
    }"
    class="space-y-5">

    {{-- PAGE HEADER --}}
    <div class="rounded-xl border border-indigo-100 bg-gradient-to-r from-indigo-50 to-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 md:text-2xl">Leaves</h1>
                <p class="mt-1 text-sm text-slate-600">Track balances, applications, and approvals in one place.</p>
            </div>

            @if($canApply)
            <a
                href="{{ route('leaves.apply', ['tenant' => request()->route('tenant')]) }}"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                Apply Leave
            </a>
            @endif
        </div>
    </div>

    {{-- LEAVE BALANCES --}}
    @if($showBalances)
    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Leave Balances</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach($balances as $balance)
            @php
            $icons = [
            'earned leave' => 'earned.png',
            'casual leave' => 'casual.png',
            'sick leave' => 'sick.png',
            ];
            $key = strtolower($balance->leaveType->name);
            $icon = $icons[$key] ?? 'default.png';
            @endphp

            <article class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50">
                    <img
                        src="{{ asset('icons/leaves/' . $icon) }}"
                        class="h-6 w-6 object-contain"
                        alt="{{ $balance->leaveType->name }}">
                </div>

                <div>
                    <p class="text-sm text-slate-500">{{ $balance->leaveType->name }}</p>
                    <p class="text-2xl font-semibold text-indigo-600">
                        {{ $balance->balance }}
                        <span class="text-sm font-medium text-slate-500">days</span>
                    </p>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    {{-- MY LEAVES --}}
    @if(isset($myLeaves))
    <section class="space-y-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">My Leaves</h2>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-3 text-left font-semibold text-indigo-900">Leave Date</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">Applied On</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">Type</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">Status</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($myLeaves as $leave)
                    <tr class="odd:bg-white even:bg-slate-50 transition hover:bg-slate-100">
                        <td class="p-3">{{ $leave->from_date }} → {{ $leave->to_date }}</td>
                        <td class="p-3">{{ $leave->created_at->format('M d, Y') }}</td>
                        <td class="p-3">{{ $leave->leaveType->name }}</td>
                        <td class="p-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="statusClass('{{ $leave->status }}')">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>

                        <td class="p-3">
                            <button
                                type="button"
                                class="rounded p-1 transition hover:bg-slate-200"
                                title="View"
                                aria-label="View leave details"
                                @click="
                                    leave = {
                                        employee: @js($leave->user->name),
                                        type: @js($leave->leaveType->name),
                                        dates: @js($leave->from_date . ' → ' . $leave->to_date),
                                        reason: @js($leave->reason ?? '—'),
                                        status: @js(ucfirst($leave->status)),
                                        applied: @js($leave->created_at->format('M d, Y'))
                                    };
                                    open = true;
                                ">
                                <img src="{{ asset('icons/documents/view.png') }}" class="h-5 w-5" alt="View">
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">No leave records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- APPROVALS --}}
    @if(isset($pendingLeaves))
    <section class="space-y-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Pending Approvals</h2>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-3 text-left font-semibold text-indigo-900">Employee</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">Type</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">Leave Date</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">Status</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">View Details</th>
                        <th class="p-3 text-left font-semibold text-indigo-900">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pendingLeaves as $leave)
                    <tr class="odd:bg-white even:bg-slate-50 transition hover:bg-slate-100">
                        <td class="p-3">{{ $leave->user->name }}</td>
                        <td class="p-3">{{ $leave->leaveType->name }}</td>
                        <td class="p-3">{{ $leave->from_date }} → {{ $leave->to_date }}</td>
                        <td class="p-3">
                            <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">Pending</span>
                        </td>

                        <td class="p-3">
                            <button
                                type="button"
                                class="rounded p-1 transition hover:bg-slate-200"
                                title="View"
                                aria-label="View leave details"
                                @click="
                                    leave = {
                                        employee: @js($leave->user->name),
                                        type: @js($leave->leaveType->name),
                                        dates: @js($leave->from_date . ' → ' . $leave->to_date),
                                        reason: @js($leave->reason ?? '—'),
                                        status: @js('Pending'),
                                        applied: @js($leave->created_at->format('M d, Y'))
                                    };
                                    open = true;
                                ">
                                <img src="{{ asset('icons/documents/view.png') }}" class="h-5 w-5" alt="View">
                            </button>
                        </td>

                        <td class="p-3">
                            <form method="POST" action="{{ route('leaves.status', ['tenant' => request()->route('tenant'), 'leave' => $leave->id]) }}">
                                @csrf
                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                    class="rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Action</option>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">No pending approvals.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- VIEW MODAL --}}
    <div
        x-show="open"
        x-cloak
        @keydown.escape.window="open = false"
        @click.self="open = false"
        class="fixed inset-0 z-[100] h-screen w-screen overflow-y-auto bg-black/50">

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl" x-transition>
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">Leave Details</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900" x-text="leave.type"></h3>
                    </div>

                    <button
                        type="button"
                        @click="open = false"
                        class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        aria-label="Close leave details">
                        &times;
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Employee</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="leave.employee"></p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="leave.status"></p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 sm:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Dates</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="leave.dates"></p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 sm:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Reason</p>
                        <p class="mt-1 whitespace-pre-line font-medium text-slate-800" x-text="leave.reason"></p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 sm:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Applied On</p>
                        <p class="mt-1 font-medium text-slate-800" x-text="leave.applied"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
