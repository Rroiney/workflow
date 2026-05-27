<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TenantUser;
use App\Repositories\Tenant\TaskRepository;

class TaskPolicy
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
    ) {
    }

    public function viewAny(TenantUser $user): bool
    {
        return $user !== null;
    }

    public function create(TenantUser $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function view(TenantUser $user, Task $task): bool
    {
        return $this->taskRepository->visibleTo($user)
            ->whereKey($task->getKey())
            ->exists();
    }

    public function update(TenantUser $user, Task $task): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $this->view($user, $task));
    }

    public function delete(TenantUser $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function updateStatus(TenantUser $user, Task $task): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $this->view($user, $task))
            || ($user->isEmployee() && $task->users()->where('users.id', $user->id)->exists());
    }
}
