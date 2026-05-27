<?php

namespace App\Http\Controllers;

use App\Actions\Tenant\CreateTeamAction;
use App\Actions\Tenant\UpdateTeamAction;
use App\Http\Requests\Tenant\Teams\StoreTeamRequest;
use App\Http\Requests\Tenant\Teams\UpdateTeamRequest;
use App\Models\Team;
use App\Models\TenantUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantTeamController extends Controller
{
    /**
     * List teams
     * - Admin: all teams
     * - Manager: only teams assigned to them
     */
    public function index(): View
    {
        $user = Auth::guard('tenant')->user();
        $this->authorize('viewAny', Team::class);

        if ($user->isAdmin()) {
            $teams = Team::with('manager')->get();
        } else {
            $teams = Team::with('manager')
                ->where('manager_id', $user->id)
                ->get();
        }

        return view('tenant.teams.index', compact('teams'));
    }

    /**
     * Show create team form
     * - Admin only
     */
    public function create(): View
    {
        $this->authorize('create', Team::class);

        $managers = TenantUser::where('role', 'manager')->get();
        $employees = TenantUser::where('role', 'employee')->get();

        return view('tenant.teams.create', compact('managers', 'employees'));
    }



    /**
     * Store team
     * - Admin only
     */
    public function store(StoreTeamRequest $request, CreateTeamAction $createTeamAction)
    {
        $this->authorize('create', Team::class);
        $createTeamAction->execute($request);

        return redirect()
            ->route('teams.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Team created successfully.');
    }

    /**
     * Edit team
     * - Admin only
     */
    public function edit(string $tenant, Team $team): View
    {
        $this->authorize('update', $team);

        $managers = TenantUser::where('role', 'manager')->get();
        $employees = TenantUser::where('role', 'employee')->get();

        $teamUserIds = $team->users->pluck('id')->toArray();

        return view('tenant.teams.edit', compact(
            'team',
            'managers',
            'employees',
            'teamUserIds'
        ));
    }

    /**
     * Update team
     * - Admin only
     */
    public function update(
        UpdateTeamRequest $request,
        string $tenant,
        Team $team,
        UpdateTeamAction $updateTeamAction
    )
    {
        $this->authorize('update', $team);
        $updateTeamAction->execute($request, $team);

        return redirect()->route('teams.index', [
            'tenant' => request()->route('tenant')
        ])->with('success', 'Team updated successfully.');
    }
}
