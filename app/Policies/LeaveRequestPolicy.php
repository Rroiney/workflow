<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\TenantUser;

class LeaveRequestPolicy
{
    public function viewAny(TenantUser $user): bool
    {
        return $user !== null;
    }

    public function create(TenantUser $user): bool
    {
        return $user->isEmployee() || $user->isManager();
    }

    public function updateStatus(TenantUser $user, LeaveRequest $leaveRequest): bool
    {
        return ($user->isAdmin() || $user->isManager())
            && $leaveRequest->user_id !== $user->id;
    }
}
