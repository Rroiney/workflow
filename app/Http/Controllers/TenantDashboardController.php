<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('tenant')->user();

        /*
        |--------------------------------------------------------------------------
        | TEAM IDS (for manager / employee)
        |--------------------------------------------------------------------------
        */
        $userTeamIds = $user->teams
            ? $user->teams->pluck('id')->toArray()
            : [];

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD STATS
        |--------------------------------------------------------------------------
        */

        // ---------------- TASK COUNTS ----------------
        $totalTasks = 0;

        if ($user->role === 'admin') {

            // Admin → all unfinished tasks
            $totalTasks = Task::whereIn('status', ['todo', 'in_progress'])
                ->count();
        } elseif ($user->role === 'manager') {

            // Get teams managed by this manager
            $managedTeamIds = Team::where('manager_id', $user->id)
                ->pluck('id');

            // Get users under those teams
            $teamUserIds = DB::connection('tenant')
                ->table('team_user')
                ->whereIn('team_id', $managedTeamIds)
                ->pluck('user_id');

            // Count tasks assigned to ANY of those users
            $totalTasks = Task::whereIn('status', ['todo', 'in_progress'])
                ->whereHas('users', function ($q) use ($teamUserIds) {
                    $q->whereIn('users.id', $teamUserIds);
                })
                ->count();
        } else {
            // Employee → tasks assigned to them
            $totalTasks = Task::whereIn('status', ['todo', 'in_progress'])
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->count();
        }


        // ---------------- LEAVE COUNT ----------------
        if (in_array($user->role, ['admin', 'manager'])) {

            $pendingLeaveCount = LeaveRequest::where('status', 'pending')->count();
        } else {

            $pendingLeaveCount = LeaveRequest::where('status', 'pending')
                ->where('user_id', $user->id)
                ->count();
        }

        // ---------------- DOCUMENT COUNT ----------------
        if ($user->role === 'admin') {

            $documentCount = Document::count();
        } else {

            $documentCount = Document::where(function ($q) use ($userTeamIds, $user) {
                $q->where('visibility', 'org')
                    ->orWhere(function ($q) use ($userTeamIds) {
                        $q->where('visibility', 'team')
                            ->whereIn('team_id', $userTeamIds);
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('visibility', 'private')
                            ->where('uploaded_by', $user->id);
                    });
            })->count();
        }

        /*
        |--------------------------------------------------------------------------
        | RECENT ACTIVITY
        |--------------------------------------------------------------------------
        */

        $logs = ActivityLog::with('user')
            ->latest()
            ->limit(50)
            ->get();

        $activities = $logs->filter(function ($log) use ($user, $userTeamIds) {

            /*
            |----------------------------------------------------------------------
            | DOCUMENT ACTIVITY
            |----------------------------------------------------------------------
            */
            if ($log->subject_type === Document::class) {
                $document = Document::find($log->subject_id);
                if (!$document) return false;

                if ($document->visibility === 'private') {
                    return false;
                }

                if ($document->visibility === 'org') {
                    return true;
                }

                if ($document->visibility === 'team') {
                    return in_array($document->team_id, $userTeamIds);
                }

                return false;
            }

            /*
            |----------------------------------------------------------------------
            | LEAVE ACTIVITY
            |----------------------------------------------------------------------
            */
            if ($log->subject_type === LeaveRequest::class) {
                $leave = LeaveRequest::find($log->subject_id);
                if (!$leave) return false;

                // Admin & Manager see all leave activities
                if (in_array($user->role, ['admin', 'manager'])) {
                    return true;
                }

                // Employee sees only own leave activity
                return $leave->user_id === $user->id;
            }

            /*
            |----------------------------------------------------------------------
            | TASK ACTIVITY
            |----------------------------------------------------------------------
            */
            if ($log->subject_type === Task::class) {

                $task = Task::with('users')->find($log->subject_id);
                if (!$task) return false;

                // Admin
                if ($user->role === 'admin') {
                    return true;
                }

                // Employee
                if ($user->role === 'employee') {
                    return $task->users->contains('id', $user->id);
                }

                // Manager
                if ($user->role === 'manager') {

                    $managedTeamIds = \App\Models\Team::where('manager_id', $user->id)
                        ->pluck('id')
                        ->toArray();

                    if (empty($managedTeamIds)) {
                        return false;
                    }

                    $teamUserIds = DB::connection('tenant')
                        ->table('team_user')
                        ->whereIn('team_id', $managedTeamIds)
                        ->pluck('user_id')
                        ->toArray();

                    return $task->users
                        ->pluck('id')
                        ->intersect($teamUserIds)
                        ->isNotEmpty();
                }

                return false;
            }

            return false;
        })->take(10);

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('tenant.dashboard', compact(
            'activities',
            'totalTasks',
            'pendingLeaveCount',
            'documentCount'
        ));
    }
}
