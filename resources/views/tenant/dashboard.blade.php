@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')

@php
use App\Models\Tenant;
$tenantData = Tenant::where('slug', request()->route('tenant'))->first();
@endphp


{{-- GREETING --}}
<div class="mb-6">
    <h2 class="text-2xl font-semibold text-slate-800">
        Good {{ now()->hour < 12 ? 'Morning' : 'Evening' }},
        {{ auth('tenant')->user()->name }}
    </h2>
    <p class="text-sm text-slate-500 mt-1">
        What’s on your plate today at {{$tenantData->name}}
    </p>
</div>

{{-- MAIN DASHBOARD GRID --}}
<div class="grid grid-cols-1 {{ auth('tenant')->user()->role === 'admin' ? '' : 'xl:grid-cols-3' }} gap-6">

    {{-- LEFT / PRIMARY CONTENT --}}
    <div class="{{ auth('tenant')->user()->role === 'admin' ? '' : 'xl:col-span-2' }} space-y-6">

        {{-- STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- TOTAL TASKS --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-slate-500">Total Tasks</p>

                <p class="text-3xl font-semibold text-slate-800 mt-2">
                    {{ $totalTasks }}
                </p>

                <p class="text-xs text-slate-400 mt-1">
                    @if(auth('tenant')->user()->role === 'admin')
                    Across all teams
                    @elseif(auth('tenant')->user()->role === 'manager')
                    Across your teams
                    @else
                    Assigned to you
                    @endif
                </p>
            </div>

            {{-- PENDING LEAVES --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-slate-500">Pending Leaves</p>

                <p class="text-3xl font-semibold text-amber-500 mt-2">
                    {{ $pendingLeaveCount }}
                </p>

                <p class="text-xs text-slate-400 mt-1">
                    Awaiting approval
                </p>
            </div>

            {{-- DOCUMENTS --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-slate-500">Documents</p>

                <p class="text-3xl font-semibold text-indigo-600 mt-2">
                    {{ $documentCount }}
                </p>

                <p class="text-xs text-slate-400 mt-1">
                    Company files
                </p>
            </div>

        </div>


        {{-- RECENT ACTIVITY --}}
        <div class="bg-white rounded-xl shadow-sm p-5">

            <h3 class="text-sm font-semibold text-slate-700 mb-4">
                Recent Activity
            </h3>

            @if($activities->isEmpty())
            <p class="text-sm text-slate-500">
                No recent activity.
            </p>
            @else
            <ul class="text-sm max-h-72 overflow-y-auto space-y-2">
                @foreach($activities as $log)
                <li class="p-3 rounded-lg
                                   odd:bg-slate-50 even:bg-white
                                   hover:bg-indigo-50 transition
                                   flex justify-between gap-4">

                    <div class="text-slate-700">
                        <span class="font-medium text-slate-800">
                            {{ $log->user?->name ?? 'System' }}
                        </span>
                        <span class="text-slate-600">
                            {{ $log->description }}
                        </span>
                    </div>

                    <span class="text-xs text-slate-400 whitespace-nowrap">
                        {{ $log->created_at->diffForHumans() }}
                    </span>
                </li>
                @endforeach
            </ul>
            @endif

        </div>

    </div>

    {{-- RIGHT / SECONDARY CONTENT --}}
    @if(auth('tenant')->user()->role !== 'admin')
    <div class="space-y-6">

        {{-- MY TEAM --}}
        <div class="bg-white rounded-xl shadow-sm p-5">

            <h3 class="text-sm font-semibold text-slate-700 mb-4">
                My Team
            </h3>

            @php
            $user = auth('tenant')->user();
            $officeIP = '103.101.212.168';
            $today = now()->toDateString();

            $team = null;
            $members = collect();

            // MANAGER → their managed team
            if ($user->role === 'manager') {
            $team = \App\Models\Team::with('users')
            ->where('manager_id', $user->id)
            ->first();

            if ($team) {
            $members = $team->users->where('id', '!=', $user->id);
            }
            }

            // EMPLOYEE → assigned team
            elseif ($user->role === 'employee') {
            $team = $user->teams()
            ->with('manager', 'users')
            ->first();

            if ($team) {
            // Add manager first
            if ($team->manager && $team->manager->id !== $user->id) {
            $members->push($team->manager);
            }

            // Add teammates (excluding self)
            $members = $members->merge(
            $team->users->where('id', '!=', $user->id)
            );
            }
            }
            @endphp

            @if($team && $members->isNotEmpty())
            <ul class="space-y-2 text-sm">

                @foreach($members->take(5) as $member)
                @php
                // STATUS LOGIC
                if (
                !$member->last_login_at ||
                $member->last_login_at->toDateString() !== $today
                ) {
                $status = 'Unavailable';
                $statusClass = 'bg-slate-100 text-slate-500';
                } elseif ($member->last_login_ip === $officeIP) {
                $status = 'WFO';
                $statusClass = 'bg-emerald-100 text-emerald-700';
                } else {
                $status = 'WFH';
                $statusClass = 'bg-indigo-100 text-indigo-700';
                }

                $avatar = $member->profile_photo_path
                ? asset('storage/' . $member->profile_photo_path)
                : null;
                @endphp

                <li
                    class="p-3 rounded-lg
                           odd:bg-slate-50 even:bg-white
                           hover:bg-indigo-50 transition
                           flex items-start justify-between gap-4">

                    {{-- LEFT --}}
                    <div class="flex items-start gap-3">

                        {{-- AVATAR --}}
                        @if($avatar)
                        <img
                            src="{{ $avatar }}"
                            class="w-9 h-9 rounded-full object-cover"
                            alt="{{ $member->name }}">
                        @else
                        <div
                            class="w-9 h-9 rounded-full bg-indigo-100
                                       flex items-center justify-center
                                       text-indigo-600 font-semibold text-sm">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        @endif

                        {{-- NAME + ROLE --}}
                        <div>
                            <p class="font-medium text-slate-800 leading-tight">
                                {{ $member->name }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $member->job_title ?? ucfirst($member->role) }}
                            </p>

                            {{-- EMAIL TOOLTIP --}}
                            <div class="relative inline-block mt-1 group">
                                <img
                                    src="{{ asset('icons/mail.png') }}"
                                    class="w-4 h-4 opacity-70 hover:opacity-100 cursor-pointer"
                                    alt="Email">

                                <div
                                    class="fixed z-[9999]
                                           mt-2 ml-2
                                           bg-slate-800 text-white text-xs
                                           px-2 py-1 rounded
                                           whitespace-nowrap
                                           opacity-0 group-hover:opacity-100
                                           transition pointer-events-none">
                                    {{ $member->email }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $statusClass }}">
                        {{ $status }}
                    </span>

                </li>
                @endforeach
            </ul>

            {{-- +X MORE --}}
            @if($members->count() > 5)
            <p class="mt-2 text-xs text-slate-500 text-center">
                +{{ $members->count() - 5 }} more team members
            </p>
            @endif
            @else
            <p class="text-sm text-slate-500">
                You are not assigned to a team.
            </p>
            @endif

        </div>



        {{-- ANNOUNCEMENTS --}}
        <!-- <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">
                Announcements
            </h3>

            <p class="text-sm text-slate-500">
                No new announcements.
            </p>
        </div> -->

    </div>
    @endif

</div>

@endsection