<?php

namespace Tests\Feature\Tenant;

use Tests\TestCase;

class LeaveManagementTest extends TestCase
{
    public function test_employee_can_apply_for_leave_with_sufficient_balance(): void
    {
        $employee = $this->signInTenantUser($this->createTenantUser('employee'));
        $leaveType = $this->createLeaveType();
        $this->createLeaveBalance($employee, $leaveType, 5);

        $response = $this->post($this->tenantUrl('/leaves/apply/submit'), [
            'leave_type_id' => $leaveType->id,
            'from_date' => '2026-06-01',
            'to_date' => '2026-06-02',
            'reason' => 'Family event',
        ]);

        $response->assertRedirect(route('leaves.index', ['tenant' => $this->tenant->slug], false));
        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'status' => 'pending',
        ], 'tenant');
    }

    public function test_leave_application_fails_when_balance_is_insufficient(): void
    {
        $employee = $this->signInTenantUser($this->createTenantUser('employee'));
        $leaveType = $this->createLeaveType();
        $this->createLeaveBalance($employee, $leaveType, 1);

        $response = $this->from($this->tenantUrl('/leaves/apply'))
            ->post($this->tenantUrl('/leaves/apply/submit'), [
                'leave_type_id' => $leaveType->id,
                'from_date' => '2026-06-01',
                'to_date' => '2026-06-03',
            ]);

        $response->assertRedirect($this->tenantUrl('/leaves/apply'));
        $response->assertSessionHasErrors('leave_type_id');
    }

    public function test_manager_can_approve_leave_only_once_without_double_decrementing_balance(): void
    {
        $manager = $this->signInTenantUser($this->createTenantUser('manager'));
        $employee = $this->createTenantUser('employee');
        $leaveType = $this->createLeaveType();
        $balance = $this->createLeaveBalance($employee, $leaveType, 10);
        $leave = $this->createLeaveRequest($employee, $leaveType, [
            'from_date' => '2026-06-01',
            'to_date' => '2026-06-03',
        ]);

        $firstResponse = $this->post($this->tenantUrl("/leaves/{$leave->id}/status"), [
            'status' => 'approved',
        ]);

        $secondResponse = $this->post($this->tenantUrl("/leaves/{$leave->id}/status"), [
            'status' => 'approved',
        ]);

        $firstResponse->assertRedirect();
        $secondResponse->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave->id,
            'status' => 'approved',
            'approved_by' => $manager->id,
        ], 'tenant');
        $this->assertSame(7, $balance->fresh()->balance);
    }
}
