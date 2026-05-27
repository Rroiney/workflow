<?php

namespace Tests;

use App\Models\Document;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\Team;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    protected Tenant $tenant;

    protected string $centralDatabasePath;

    protected string $tenantDatabasePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configureDatabases();
        $this->migrateDatabases();
        $this->tenant = $this->createTenantRecord();
    }

    protected function configureDatabases(): void
    {
        $this->centralDatabasePath = database_path('testing.sqlite');
        $this->tenantDatabasePath = database_path('testing-tenant.sqlite');

        @unlink($this->centralDatabasePath);
        @unlink($this->tenantDatabasePath);

        touch($this->centralDatabasePath);
        touch($this->tenantDatabasePath);

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', $this->centralDatabasePath);
        Config::set('database.connections.tenant', [
            'driver' => 'sqlite',
            'database' => $this->tenantDatabasePath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        DB::purge('sqlite');
        DB::purge('tenant');
        DB::reconnect('sqlite');
        DB::reconnect('tenant');
    }

    protected function migrateDatabases(): void
    {
        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--force' => true,
        ]);

        Artisan::call('migrate:fresh', [
            '--database' => 'tenant',
            '--path' => database_path('migrations/tenant'),
            '--realpath' => true,
            '--force' => true,
        ]);
    }

    protected function createTenantRecord(array $attributes = []): Tenant
    {
        return Tenant::query()->create(array_merge([
            'name' => 'Acme Inc',
            'slug' => 'acme',
            'db_name' => $this->tenantDatabasePath,
            'db_username' => '',
            'db_password' => '',
            'db_host' => '',
            'db_port' => '',
            'status' => 'active',
        ], $attributes));
    }

    protected function tenantUrl(string $path): string
    {
        return "/org/{$this->tenant->slug}{$path}";
    }

    protected function createTenantUser(string $role = 'employee', array $attributes = []): TenantUser
    {
        return TenantUser::query()->create(array_merge([
            'name' => ucfirst($role) . ' User',
            'email' => $role . '-' . uniqid() . '@example.test',
            'password' => Hash::make('password'),
            'role' => $role,
        ], $attributes));
    }

    protected function signInTenantUser(?TenantUser $user = null): TenantUser
    {
        $user ??= $this->createTenantUser();
        $this->actingAs($user, 'tenant');

        return $user;
    }

    protected function createTeam(?TenantUser $manager = null, array $users = [], array $attributes = []): Team
    {
        $manager ??= $this->createTenantUser('manager');

        $team = Team::query()->create(array_merge([
            'name' => 'Team ' . uniqid(),
            'manager_id' => $manager->id,
        ], $attributes));

        $team->users()->sync(collect($users)->map(fn ($user) => $user->id)->all());

        return $team->fresh('users');
    }

    protected function createTask(array $users = [], array $attributes = []): Task
    {
        $creator = $attributes['creator'] ?? $this->createTenantUser('manager');
        unset($attributes['creator']);

        $task = Task::query()->create(array_merge([
            'title' => 'Task ' . uniqid(),
            'description' => 'Task description',
            'status' => 'todo',
            'created_by' => $creator->id,
        ], $attributes));

        $task->users()->sync(collect($users)->map(fn ($user) => $user->id)->all());

        return $task->fresh('users');
    }

    protected function createLeaveType(array $attributes = []): LeaveType
    {
        return LeaveType::query()->create(array_merge([
            'name' => 'Annual Leave',
            'yearly_quota' => 12,
        ], $attributes));
    }

    protected function createLeaveBalance(TenantUser $user, LeaveType $leaveType, int $balance = 10): LeaveBalance
    {
        return LeaveBalance::query()->create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'balance' => $balance,
        ]);
    }

    protected function createLeaveRequest(TenantUser $user, LeaveType $leaveType, array $attributes = []): LeaveRequest
    {
        return LeaveRequest::query()->create(array_merge([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'from_date' => '2026-06-01',
            'to_date' => '2026-06-03',
            'reason' => 'Need time off',
            'status' => 'pending',
        ], $attributes));
    }

    protected function createDocument(TenantUser $user, array $attributes = []): Document
    {
        return Document::query()->create(array_merge([
            'uploaded_by' => $user->id,
            'title' => 'Document ' . uniqid(),
            'file_name' => 'file.pdf',
            'file_path' => 'tenants/' . $this->tenant->slug . '/documents/file.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1200,
            'visibility' => 'private',
            'assigned_user_id' => $user->id,
            'team_id' => null,
        ], $attributes));
    }
}
