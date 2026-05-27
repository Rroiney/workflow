<?php

namespace App\Repositories\Tenant;

use App\Models\Task;
use App\Models\TenantUser;
use App\Models\Team;
use App\Support\Tenant\TenantUserContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TaskRepository
{
    public function __construct(
        private readonly TenantUserContext $tenantUserContext,
    ) {
    }

    public function visibleTo(TenantUser $user): Builder
    {
        $query = Task::query()->with('users');

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isManager()) {
            $teamUserIds = $this->tenantUserContext->managedTeamMemberIds($user);

            return $query->whereHas('users', function (Builder $builder) use ($teamUserIds) {
                $builder->whereIn('users.id', $teamUserIds ?: [-1]);
            });
        }

        return $query->whereHas('users', function (Builder $builder) use ($user) {
            $builder->where('users.id', $user->id);
        });
    }

    public function getIndexTasks(TenantUser $user): Collection
    {
        return $this->visibleTo($user)->latest()->get();
    }

    public function getOpenTaskCount(TenantUser $user): int
    {
        return $this->visibleTo($user)
            ->whereIn('status', ['todo', 'in_progress'])
            ->count();
    }

    public function getAssignableUsers(TenantUser $user): Collection
    {
        if ($user->isAdmin()) {
            return TenantUser::query()
                ->where('role', 'employee')
                ->orderBy('name')
                ->get();
        }

        $teamId = $this->tenantUserContext->managedTeamId($user);

        if (!$teamId) {
            return collect();
        }

        return TenantUser::query()
            ->whereHas('teams', function (Builder $query) use ($teamId) {
                $query->where('teams.id', $teamId);
            })
            ->orderBy('name')
            ->get();
    }

    public function getTaskDetails(Task $task): array
    {
        $task->loadMissing(['users', 'creator']);

        return [
            'title' => $task->title,
            'description' => $task->description,
            'status' => ucfirst(str_replace('_', ' ', $task->status)),
            'assignees' => $task->users->pluck('name')->join(', '),
            'updated_by' => $task->creator?->name ?? 'System',
            'updated_at' => $task->updated_at->format('d M Y, h:i A'),
        ];
    }
}
