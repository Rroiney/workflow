<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TenantUser;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantTaskController extends Controller
{
    public function index()
    {
        $user = Auth::guard('tenant')->user();

        // Admin sees all tasks
        if ($user->role === 'admin') {
            $tasks = Task::with('users')->latest()->get();
        }

        // Manager sees tasks assigned to members of their team
        elseif ($user->role === 'manager') {

            // Teams managed by this manager
            $managedTeamIds = Team::where('manager_id', $user->id)->pluck('id');

            // Employees under those teams
            $teamUserIds = DB::connection('tenant')
                ->table('team_user')
                ->whereIn('team_id', $managedTeamIds)
                ->pluck('user_id');

            $tasks = Task::whereHas('users', function ($query) use ($teamUserIds) {
                $query->whereIn('users.id', $teamUserIds);
            })
                ->with('users')
                ->latest()
                ->get();
        }

        // Employee sees only tasks assigned to them
        else {
            $tasks = Task::whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
                ->with('users')
                ->latest()
                ->get();
        }

        return view('tenant.tasks.index', compact('tasks'));
    }


    public function create()
    {
        $user = Auth::guard('tenant')->user();

        // Admin: all employees
        if ($user->role === 'admin') {
            $teamMembers = TenantUser::where('role', 'employee')->get();
        }
        // Manager: only their team members
        else {
            $teamId = Team::where('manager_id', $user->id)->value('id');

            $teamMembers = TenantUser::whereHas('teams', function ($q) use ($teamId) {
                $q->where('teams.id', $teamId);
            })->get();
        }

        return view('tenant.tasks.create', compact('teamMembers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:tenant.users,id',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::guard('tenant')->id(),
        ]);

        // Attach assigned users
        $task->users()->sync($request->users);

        activity_log(
            'task_created',
            "Task '{$task->title}' created",
            $task
        );

        return redirect()
            ->route('tasks.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Task created successfully.');
    }

    public function updateStatus($tenant, $taskId, Request $request)
    {
        $task = Task::findOrFail($taskId);

        // Store old status (important)
        $oldStatus = $task->status;

        $task->update([
            'status' => $request->status,
        ]);

        if ($oldStatus !== 'done' && $request->status === 'done') {
            activity_log(
                'task_completed',
                "marked task '{$task->title}' as completed",
                $task
            );
        } else if ($request->status === 'in_progress') {
            activity_log(
                'task_in_progress',
                "marked task '{$task->title}' as in progress",
                $task
            );
        } else {
            activity_log(
                'task_todo',
                "marked task '{$task->title}' as to do",
                $task
            );
        }

        return back()->with('success', 'Task status updated.');
    }

    private function managerCanAccessTask($user, Task $task)
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role !== 'manager') {
            return false;
        }

        // Teams managed by this manager
        $managedTeamIds = Team::where('manager_id', $user->id)->pluck('id');

        // Employees in those teams
        $teamUserIds = DB::connection('tenant')
            ->table('team_user')
            ->whereIn('team_id', $managedTeamIds)
            ->pluck('user_id');

        // Check if task is assigned to any team member
        return $task->users()->whereIn('users.id', $teamUserIds)->exists();
    }

    public function edit($tenant, Task $task)
    {
        $user = Auth::guard('tenant')->user();

        if (!$this->managerCanAccessTask($user, $task)) {
            abort(403);
        }

        // Team members manager can assign to
        $teamUserIds = DB::connection('tenant')
            ->table('team_user')
            ->pluck('user_id');

        $users = TenantUser::whereIn('id', $teamUserIds)->get();

        return view('tenant.tasks.edit', compact('task', 'users'));
    }

    public function update($tenant, Request $request, Task $task)
    {
        $user = Auth::guard('tenant')->user();

        if (!$this->managerCanAccessTask($user, $task)) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'users' => 'required|array',
            'users.*' => 'exists:tenant.users,id',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Sync assigned users
        $task->users()->sync($request->users);

        activity_log(
            'task_updated',
            "updated task '{$task->title}'",
            $task
        );

        return redirect()
            ->route('tasks.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Task updated successfully.');
    }

    public function destroy($tenant, Task $task)
    {
        $user = Auth::guard('tenant')->user();

        if (!$this->managerCanAccessTask($user, $task)) {
            abort(403);
        }

        $taskTitle = $task->title;
        $task->delete();

        activity_log(
            'task_deleted',
            "deleted task '{$taskTitle}'",
            $task
        );

        return back()->with('success', 'Task deleted successfully');
    }
}
