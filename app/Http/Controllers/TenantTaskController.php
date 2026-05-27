<?php

namespace App\Http\Controllers;

use App\Actions\Tenant\CreateTaskAction;
use App\Actions\Tenant\UpdateTaskAction;
use App\Actions\Tenant\UpdateTaskStatusAction;
use App\Http\Requests\Tenant\Tasks\StoreTaskRequest;
use App\Http\Requests\Tenant\Tasks\UpdateTaskRequest;
use App\Http\Requests\Tenant\Tasks\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Repositories\Tenant\TaskRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantTaskController extends Controller
{
    public function index(TaskRepository $taskRepository): View
    {
        $user = Auth::guard('tenant')->user();
        $this->authorize('viewAny', Task::class);

        $tasks = $taskRepository->getIndexTasks($user);

        return view('tenant.tasks.index', compact('tasks'));
    }


    public function create(TaskRepository $taskRepository): View
    {
        $user = Auth::guard('tenant')->user();
        $this->authorize('create', Task::class);

        $teamMembers = $taskRepository->getAssignableUsers($user);

        return view('tenant.tasks.create', compact('teamMembers'));
    }

    public function store(StoreTaskRequest $request, CreateTaskAction $createTaskAction)
    {
        $this->authorize('create', Task::class);

        $createTaskAction->execute($request, Auth::guard('tenant')->user());
        return redirect()
            ->route('tasks.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Task created successfully.');
    }

    public function updateStatus(
        string $tenant,
        Task $task,
        UpdateTaskStatusRequest $request,
        UpdateTaskStatusAction $updateTaskStatusAction
    )
    {
        $this->authorize('updateStatus', $task);

        $updateTaskStatusAction->execute($request, $task);

        return back()->with('success', 'Task status updated.');
    }

    public function edit(string $tenant, Task $task, TaskRepository $taskRepository): View
    {
        $this->authorize('update', $task);
        $users = $taskRepository->getAssignableUsers(Auth::guard('tenant')->user());

        return view('tenant.tasks.edit', compact('task', 'users'));
    }

    public function update(
        string $tenant,
        UpdateTaskRequest $request,
        Task $task,
        UpdateTaskAction $updateTaskAction
    )
    {
        $this->authorize('update', $task);
        $updateTaskAction->execute($request, $task);

        return redirect()
            ->route('tasks.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(string $tenant, Task $task)
    {
        $this->authorize('delete', $task);

        $taskTitle = $task->title;
        $task->delete();

        activity_log(
            'task_deleted',
            "deleted task '{$taskTitle}'",
            $task
        );

        return back()->with('success', 'Task deleted successfully');
    }

    public function showApi(string $tenant, Task $task, TaskRepository $taskRepository)
    {
        $this->authorize('view', $task);

        return response()->json($taskRepository->getTaskDetails($task));
    }
}
