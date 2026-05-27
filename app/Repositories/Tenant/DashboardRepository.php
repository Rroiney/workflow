<?php

namespace App\Repositories\Tenant;

use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\TenantUser;
use App\Support\Tenant\TenantUserContext;
use Illuminate\Support\Collection;

class DashboardRepository
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly LeaveRepository $leaveRepository,
        private readonly DocumentRepository $documentRepository,
        private readonly TenantUserContext $tenantUserContext,
    ) {
    }

    public function getDashboardData(TenantUser $user): array
    {
        return [
            'activities' => $this->recentActivities($user),
            'totalTasks' => $this->taskRepository->getOpenTaskCount($user),
            'pendingLeaveCount' => $this->leaveRepository->getPendingCountForDashboard($user),
            'documentCount' => $this->documentRepository->countVisibleFor($user),
        ];
    }

    public function recentActivities(TenantUser $user): Collection
    {
        $teamIds = $this->tenantUserContext->accessibleTeamIds($user);
        $managedTeamUserIds = $this->tenantUserContext->managedTeamMemberIds($user);

        return ActivityLog::query()
            ->with('user')
            ->latest()
            ->limit(50)
            ->get()
            ->filter(function (ActivityLog $log) use ($user, $teamIds, $managedTeamUserIds) {
                if ($log->subject_type === Document::class) {
                    $document = Document::query()->find($log->subject_id);

                    if (!$document || $document->visibility === 'private') {
                        return false;
                    }

                    return $document->visibility === 'org'
                        || in_array($document->team_id, $teamIds, true);
                }

                if ($log->subject_type === LeaveRequest::class) {
                    $leave = LeaveRequest::query()->find($log->subject_id);

                    if (!$leave) {
                        return false;
                    }

                    return $user->isAdmin()
                        || $user->isManager()
                        || $leave->user_id === $user->id;
                }

                if ($log->subject_type === Task::class) {
                    $task = Task::query()->with('users')->find($log->subject_id);

                    if (!$task) {
                        return false;
                    }

                    if ($user->isAdmin()) {
                        return true;
                    }

                    if ($user->isEmployee()) {
                        return $task->users->contains('id', $user->id);
                    }

                    return $task->users
                        ->pluck('id')
                        ->intersect($managedTeamUserIds)
                        ->isNotEmpty();
                }

                return false;
            })
            ->take(10)
            ->values();
    }
}
