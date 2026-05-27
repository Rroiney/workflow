<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Teams\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class UpdateTeamAction
{
    public function execute(UpdateTeamRequest $request, Team $team): Team
    {
        $team->update([
            'name' => $request->string('name')->toString(),
            'manager_id' => $request->integer('manager_id'),
        ]);

        $userIds = $request->input('users', []);

        if ($userIds !== []) {
            DB::connection('tenant')
                ->table('team_user')
                ->whereIn('user_id', $userIds)
                ->delete();
        }

        $team->users()->sync($userIds);

        return $team->fresh('users');
    }
}
