<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Tasks\UpdateTaskStatusRequest;
use App\Models\Task;

class UpdateTaskStatusAction
{
    public function execute(UpdateTaskStatusRequest $request, Task $task): Task
    {
        $oldStatus = $task->status;
        $newStatus = $request->string('status')->toString();

        $task->update(['status' => $newStatus]);

        if ($oldStatus !== 'done' && $newStatus === 'done') {
            activity_log('task_completed', "marked task '{$task->title}' as completed", $task);
        } elseif ($newStatus === 'in_progress') {
            activity_log('task_in_progress', "marked task '{$task->title}' as in progress", $task);
        } else {
            activity_log('task_todo', "marked task '{$task->title}' as to do", $task);
        }

        return $task->fresh('users');
    }
}
