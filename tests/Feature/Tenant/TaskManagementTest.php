<?php

namespace Tests\Feature\Tenant;

use App\Models\Task;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    public function test_employee_only_sees_assigned_tasks(): void
    {
        $employee = $this->signInTenantUser($this->createTenantUser('employee'));
        $otherEmployee = $this->createTenantUser('employee');

        $visibleTask = $this->createTask([$employee]);
        $hiddenTask = $this->createTask([$otherEmployee]);

        $response = $this->get($this->tenantUrl('/tasks'));

        $response->assertOk();
        $response->assertSee($visibleTask->title);
        $response->assertDontSee($hiddenTask->title);
    }

    public function test_manager_can_create_update_and_delete_a_task_for_team_members(): void
    {
        $manager = $this->signInTenantUser($this->createTenantUser('manager'));
        $employee = $this->createTenantUser('employee');
        $this->createTeam($manager, [$employee]);

        $createResponse = $this->post($this->tenantUrl('/tasks/create'), [
            'title' => 'Prepare report',
            'description' => 'Monthly metrics',
            'users' => [$employee->id],
        ]);

        $task = Task::query()->where('title', 'Prepare report')->firstOrFail();

        $createResponse->assertRedirect(route('tasks.index', ['tenant' => $this->tenant->slug], false));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Prepare report'], 'tenant');

        $updateResponse = $this->put($this->tenantUrl("/tasks/{$task->id}"), [
            'title' => 'Prepare revised report',
            'description' => 'Updated monthly metrics',
            'users' => [$employee->id],
        ]);

        $updateResponse->assertRedirect(route('tasks.index', ['tenant' => $this->tenant->slug], false));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Prepare revised report'], 'tenant');

        $deleteResponse = $this->delete($this->tenantUrl("/tasks/{$task->id}"));

        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id], 'tenant');
    }

    public function test_employee_can_update_status_of_own_task_but_not_others(): void
    {
        $employee = $this->signInTenantUser($this->createTenantUser('employee'));
        $otherEmployee = $this->createTenantUser('employee');

        $ownTask = $this->createTask([$employee]);
        $otherTask = $this->createTask([$otherEmployee]);

        $allowed = $this->post($this->tenantUrl("/tasks/{$ownTask->id}/status"), [
            'status' => 'done',
        ]);

        $denied = $this->post($this->tenantUrl("/tasks/{$otherTask->id}/status"), [
            'status' => 'done',
        ]);

        $allowed->assertRedirect();
        $denied->assertForbidden();
        $this->assertDatabaseHas('tasks', ['id' => $ownTask->id, 'status' => 'done'], 'tenant');
        $this->assertDatabaseHas('tasks', ['id' => $otherTask->id, 'status' => 'todo'], 'tenant');
    }
}
