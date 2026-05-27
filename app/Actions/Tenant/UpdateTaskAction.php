<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Tasks\UpdateTaskRequest;
use App\Models\Task;

class UpdateTaskAction
{
    public function execute(UpdateTaskRequest $request, Task $task): Task
    {
        $task->update([
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description'),
        ]);

        $task->users()->sync($request->input('users', []));

        activity_log('task_updated', "updated task '{$task->title}'", $task);

        return $task->fresh('users');
    }
}
