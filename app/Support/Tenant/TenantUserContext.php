<?php

namespace App\Support\Tenant;

use App\Models\TenantUser;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class TenantUserContext
{
    public function memberTeamIds(TenantUser $user): array
    {
        return $user->teams()->pluck('teams.id')->all();
    }

    public function managedTeamIds(TenantUser $user): array
    {
        if (!$user->isManager()) {
            return [];
        }

        return Team::query()
            ->where('manager_id', $user->id)
            ->pluck('id')
            ->all();
    }

    public function managedTeamId(TenantUser $user): ?int
    {
        $teamIds = $this->managedTeamIds($user);

        return count($teamIds) === 1 ? $teamIds[0] : null;
    }

    public function accessibleTeamIds(TenantUser $user): array
    {
        return array_values(array_unique(array_merge(
            $this->memberTeamIds($user),
            $this->managedTeamIds($user),
        )));
    }

    public function managedTeamMemberIds(TenantUser $user): array
    {
        $managedTeamIds = $this->managedTeamIds($user);

        if ($managedTeamIds === []) {
            return [];
        }

        return DB::connection('tenant')
            ->table('team_user')
            ->whereIn('team_id', $managedTeamIds)
            ->pluck('user_id')
            ->unique()
            ->values()
            ->all();
    }
}
