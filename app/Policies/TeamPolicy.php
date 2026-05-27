<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\TenantUser;

class TeamPolicy
{
    public function viewAny(TenantUser $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function view(TenantUser $user, Team $team): bool
    {
        return $user->isAdmin() || $team->manager_id === $user->id;
    }

    public function create(TenantUser $user): bool
    {
        return $user->isAdmin();
    }

    public function update(TenantUser $user, Team $team): bool
    {
        return $user->isAdmin();
    }
}
