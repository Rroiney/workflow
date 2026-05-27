<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Teams\StoreTeamRequest;
use App\Models\Team;

class CreateTeamAction
{
    public function execute(StoreTeamRequest $request): Team
    {
        $team = Team::query()->create([
            'name' => $request->string('name')->toString(),
            'manager_id' => $request->integer('manager_id'),
        ]);

        $team->users()->sync($request->input('users', []));

        return $team;
    }
}
