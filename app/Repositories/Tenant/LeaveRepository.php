<?php

namespace App\Repositories\Tenant;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\TenantUser;
use Illuminate\Support\Collection;

class LeaveRepository
{
    public function getIndexData(TenantUser $user): array
    {
        $balances = null;
        $myLeaves = null;
        $pendingLeaves = null;

        if ($user->isEmployee() || $user->isManager()) {
            $balances = LeaveBalance::query()
                ->with('leaveType')
                ->where('user_id', $user->id)
                ->get();

            $myLeaves = LeaveRequest::query()
                ->with('leaveType')
                ->where('user_id', $user->id)
                ->get();
        }

        if ($user->isManager() || $user->isAdmin()) {
            $pendingLeaves = LeaveRequest::query()
                ->with(['user', 'leaveType'])
                ->where('user_id', '!=', $user->id)
                ->where('status', 'pending')
                ->get();
        }

        return compact('balances', 'myLeaves', 'pendingLeaves');
    }

    public function getPendingCountForDashboard(TenantUser $user): int
    {
        $query = LeaveRequest::query()->where('status', 'pending');

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        }

        return $query->count();
    }
}
