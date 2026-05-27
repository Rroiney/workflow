<?php

namespace Tests\Feature\Tenant;

use App\Models\ActivityLog;
use Tests\TestCase;

class TeamAndDashboardTest extends TestCase
{
    public function test_admin_team_update_reassigns_employee_to_new_team(): void
    {
        $admin = $this->signInTenantUser($this->createTenantUser('admin'));
        $managerOne = $this->createTenantUser('manager');
        $managerTwo = $this->createTenantUser('manager');
        $employee = $this->createTenantUser('employee');

        $oldTeam = $this->createTeam($managerOne, [$employee], ['name' => 'Old Team']);
        $newTeam = $this->createTeam($managerTwo, [], ['name' => 'New Team']);

        $response = $this->put($this->tenantUrl("/teams/{$newTeam->id}"), [
            'name' => 'New Team',
            'manager_id' => $managerTwo->id,
            'users' => [$employee->id],
        ]);

        $response->assertRedirect(route('teams.index', ['tenant' => $this->tenant->slug], false));
        $this->assertFalse($oldTeam->fresh()->users->contains('id', $employee->id));
        $this->assertTrue($newTeam->fresh()->users->contains('id', $employee->id));
    }

    public function test_employee_dashboard_only_counts_visible_records(): void
    {
        $employee = $this->signInTenantUser($this->createTenantUser('employee'));
        $manager = $this->createTenantUser('manager');
        $team = $this->createTeam($manager, [$employee]);
        $leaveType = $this->createLeaveType();

        $this->createTask([$employee], ['title' => 'Visible Task']);
        $this->createTask([$manager], ['title' => 'Hidden Task']);
        $this->createLeaveBalance($employee, $leaveType, 10);
        $this->createLeaveRequest($employee, $leaveType, ['status' => 'pending']);
        $visibleDocument = $this->createDocument($manager, [
            'visibility' => 'team',
            'team_id' => $team->id,
            'assigned_user_id' => null,
        ]);
        $this->createDocument($manager, [
            'visibility' => 'private',
            'assigned_user_id' => $manager->id,
        ]);

        ActivityLog::query()->create([
            'user_id' => $manager->id,
            'action' => 'document_uploaded',
            'subject_type' => \App\Models\Document::class,
            'subject_id' => $visibleDocument->id,
            'description' => 'Uploaded a team document',
        ]);

        $response = $this->get($this->tenantUrl('/home'));

        $response->assertOk();
        $response->assertViewHas('totalTasks', 1);
        $response->assertViewHas('pendingLeaveCount', 1);
        $response->assertViewHas('documentCount', 1);
        $this->assertCount(1, $response->viewData('activities'));
    }
}
