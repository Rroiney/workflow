<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantTeamController extends Controller
{
    /**
     * List teams
     * - Admin: all teams
     * - Manager: only teams assigned to them
     */
    public function index()
    {
        $user = Auth::guard('tenant')->user();

        if ($user->role === 'admin') {
            $teams = Team::with('manager')->get();
        } elseif ($user->role === 'manager') {
            $teams = Team::with('manager')
                ->where('manager_id', $user->id)
                ->get();
        } else {
            abort(403, 'Unauthorized');
        }

        return view('tenant.teams.index', compact('teams'));
    }

    /**
     * Show create team form
     * - Admin only
     */
    public function create()
    {
        $user = Auth::guard('tenant')->user();

        // Admin only
        if ($user->role !== 'admin') {
            abort(403);
        }

        // Only managers can be team managers
        $managers = TenantUser::where('role', 'manager')->get();

        // Only employees can be assigned to teams
        $employees = TenantUser::where('role', 'employee')->get();

        return view('tenant.teams.create', compact('managers', 'employees'));
    }



    /**
     * Store team
     * - Admin only
     */
    public function store(Request $request)
    {
        $user = Auth::guard('tenant')->user();

        if ($user->role !== 'admin') {
            abort(403, 'Only admin can create teams');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:tenant.users,id',
            'users' => 'array',
            'users.*' => 'exists:tenant.users,id',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'manager_id' => $request->manager_id,
            'created_by' => $user->id, // track admin creator
        ]);

        // Assign employees to team (optional)
        $team->users()->sync($request->users ?? []);

        return redirect()
            ->route('teams.index', ['tenant' => request()->route('tenant')])
            ->with('success', 'Team created successfully.');
    }

    /**
     * Edit team
     * - Admin only
     */
    public function edit($tenant, $teamId)
    {
        $user = Auth::guard('tenant')->user();

        if ($user->role !== 'admin') {
            abort(403);
        }

        $team = Team::where('id', $teamId)->firstOrFail();

        $managers = TenantUser::where('role', 'manager')->get();
        $employees = TenantUser::where('role', 'employee')->get();

        $teamUserIds = $team->users()->pluck('users.id')->toArray();

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
    public function update(Request $request, $tenant, $teamId)
    {
        $team = Team::where('id', $teamId)->firstOrFail();
        $user = Auth::guard('tenant')->user();

        if ($user->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:tenant.users,id',
            'users' => 'array',
            'users.*' => 'exists:tenant.users,id',
        ]);

        // Update basic team info
        $team->update([
            'name' => $request->name,
            'manager_id' => $request->manager_id,
        ]);

        /**
         * ENFORCE: ONE EMPLOYEE → ONE TEAM
         *
         * We remove selected employees from any other team
         * before assigning them to this team.
         */
        $userIds = $request->users ?? [];

        if (!empty($userIds)) {
            foreach ($userIds as $userId) {
                // Detach employee from any other team
                DB::connection('tenant')
                    ->table('team_user')
                    ->where('user_id', $userId)
                    ->delete();
            }
        }

        // Attach employees to this team
        $team->users()->sync($userIds);

        return redirect()->route('teams.index', [
            'tenant' => request()->route('tenant')
        ])->with('success', 'Team updated successfully.');
    }
}
