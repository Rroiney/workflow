@extends('layouts.tenant')

@section('title', 'Leaves')

@section('content')

@php
$user = auth('tenant')->user();

@endphp

<div x-data="{
    open:false,
    leave:{}
}" class="space-y-8">

    {{-- APPLY LEAVE --}}
    @if(in_array($user->role, ['employee','manager']))
    <a href="/org/{{ request()->route('tenant') }}/leaves/apply"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg inline-block
              hover:bg-indigo-700 transition">
        Apply Leave
    </a>
    @endif

    {{-- ================= LEAVE BALANCES ================= --}}
    @if(in_array(auth('tenant')->user()->role, ['employee', 'manager']) && $balances?->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

        <div class="bg-white rounded-2xl p-5 shadow-sm flex items-center gap-5">

            {{-- ICON --}}
            <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-indigo-50">
                <img src="{{ asset('icons/leaves/' . $icon) }}"
                    class="w-6 h-6 object-contain"
                    alt="{{ $balance->leaveType->name }}">
            </div>

            {{-- TEXT --}}
            <div>
                <p class="text-sm text-slate-500">
                    {{ $balance->leaveType->name }}
                </p>
                <p class="text-2xl font-semibold text-indigo-600">
                    {{ $balance->balance }}
                    <span class="text-sm text-slate-500">days</span>
                </p>
            </div>

        </div>
        @endforeach
    </div>
    @endif


    {{-- ================= MY LEAVES ================= --}}
    @if(isset($myLeaves))
    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="p-3 text-left">Leave Date</th>
                    <th class="p-3 text-left">Applied On</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($myLeaves as $leave)
                <tr class=" hover:bg-slate-50">

                    <td class="p-3">
                        {{ $leave->from_date }} → {{ $leave->to_date }}
                    </td>

                    <td class="p-3">
                        {{ $leave->created_at->format('M d, Y') }}
                    </td>

                    <td class="p-3">
                        {{ $leave->leaveType->name }}
                    </td>

                    <td class="p-3">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($leave->status === 'approved') bg-green-100 text-green-700
                            @elseif($leave->status === 'rejected') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700
                            @endif">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </td>

                    <td class="p-3">
                        <button
                            class="hover:opacity-80"
                            @click="
                                leave = {
                                    employee: '{{ $leave->user->name }}',
                                    type: '{{ $leave->leaveType->name }}',
                                    dates: '{{ $leave->from_date }} → {{ $leave->to_date }}',
                                    reason: '{{ $leave->reason ?? '—' }}',
                                    status: '{{ ucfirst($leave->status) }}',
                                    applied: '{{ $leave->created_at->format('M d, Y') }}'
                                };
                                open = true;
                            ">
                            <img src="{{ asset('icons/documents/view.png') }}" class="w-5 h-5">
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">
                        No leave records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- ================= APPROVALS ================= --}}
    @if(isset($pendingLeaves))
    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-sm">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="p-3 text-left">Employee</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Leave Date</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">View Details</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pendingLeaves as $leave)
                <tr class="hover:bg-slate-50">
                    <td class="p-3">{{ $leave->user->name }}</td>
                    <td class="p-3">{{ $leave->leaveType->name }}</td>
                    <td class="p-3">{{ $leave->from_date }} → {{ $leave->to_date }}</td>

                    <td class="p-3">
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                            Pending
                        </span>
                    </td>

                    <td class="p-3">
                        <button
                            class="hover:opacity-80"
                            @click="
                                leave = {
                                    employee: '{{ $leave->user->name }}',
                                    type: '{{ $leave->leaveType->name }}',
                                    dates: '{{ $leave->from_date }} → {{ $leave->to_date }}',
                                    reason: '{{ $leave->reason ?? '—' }}',
                                    status: 'Pending',
                                    applied: '{{ $leave->created_at->format('M d, Y') }}'
                                };
                                open = true;
                            ">
                            <img src="{{ asset('icons/documents/view.png') }}" class="w-5 h-5">
                        </button>
                    </td>

                    <td class="p-3 flex items-center gap-3">

                        {{-- APPROVE / REJECT --}}
                        <form method="POST"
                            action="/org/{{ request()->route('tenant') }}/leaves/{{ $leave->id }}/status">
                            @csrf
                            <select name="status"
                                onchange="this.form.submit()"
                                class="rounded-md text-sm border-slate-300">
                                <option value="">Action</option>
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </form>

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">
                        No pending approvals.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- ================= VIEW MODAL ================= --}}
    <div x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

        <div class="bg-white w-full max-w-lg rounded-xl p-6 shadow-lg">
            <div class="flex justify-between mb-4">
                <h2 class="text-lg font-semibold">Leave Details</h2>
                <button @click="open=false">&times;</button>
            </div>

            <div class="space-y-2 text-sm text-slate-700">
                <div><strong>Employee:</strong> <span x-text="leave.employee"></span></div>
                <div><strong>Type:</strong> <span x-text="leave.type"></span></div>
                <div><strong>Dates:</strong> <span x-text="leave.dates"></span></div>
                <div><strong>Reason:</strong> <span x-text="leave.reason"></span></div>
                <div><strong>Status:</strong> <span x-text="leave.status"></span></div>
                <div><strong>Applied On:</strong> <span x-text="leave.applied"></span></div>
            </div>
        </div>
    </div>

</div>
@endsection