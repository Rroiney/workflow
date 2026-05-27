<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Tasks\StoreTaskRequest;
use App\Models\Task;
use App\Models\TenantUser;

class CreateTaskAction
{
    public function execute(StoreTaskRequest $request, TenantUser $user): Task
    {
        $task = Task::query()->create([
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'created_by' => $user->id,
        ]);

        $task->users()->sync($request->input('users', []));

        activity_log('task_created', "Task '{$task->title}' created", $task);

        return $task;
    }
}
