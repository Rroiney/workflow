<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantTeamController;
use App\Http\Controllers\TenantTaskController;
use App\Http\Controllers\TenantLeaveController;
use App\Http\Controllers\TenantDocumentController;
use App\Http\Controllers\TenantDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/org/{tenant}/test-db', function () {
    return DB::connection('tenant')->select('SHOW TABLES');
})->middleware('tenant.db');

Route::prefix('org/{tenant}')
    ->middleware('tenant.db')
    ->group(function () {

        Route::get('/login', [TenantAuthController::class, 'showLogin']);
        Route::post('/login', [TenantAuthController::class, 'login']);

        Route::get('/home', [TenantDashboardController::class, 'index'])
            ->middleware('auth:tenant')
            ->name('home');

        // Route::get('/teams/{teamId}/edit', function ($tenant, $teamId) {
        //     dd('ROUTE HIT', $tenant, $teamId);
        // });


        Route::get('/admin-only', function () {
            return 'Admin area';
        })->middleware(['auth:tenant', 'tenant.role:admin']);

        Route::get('/manager-area', function () {
            return 'Manager or Admin area';
        })->middleware(['auth:tenant', 'tenant.role:admin,manager']);

        Route::post('/logout', [TenantAuthController::class, 'logout'])
            ->middleware('auth:tenant');

        Route::middleware(['auth:tenant', 'tenant.role:admin'])->group(function () {

            // ---------------- TEAMS ----------------
            Route::get('/teams/{teamId}/edit', [TenantTeamController::class, 'edit'])
                ->name('teams.edit');

            // Update team (PUT)
            Route::put('/teams/{teamId}', [TenantTeamController::class, 'update']);

            // List teams
            Route::get('/teams', [TenantTeamController::class, 'index'])
                ->name('teams.index');

            // Show create team form (GET)
            Route::get('/teams/create', [TenantTeamController::class, 'create'])
                ->name('teams.create');
                
            // Store team (POST)
            Route::post('/teams/create', [TenantTeamController::class, 'store']);
        });

        Route::middleware(['auth:tenant'])->group(function () {

            // ---------------- TASKS ----------------
            Route::get('/tasks', [TenantTaskController::class, 'index'])
                ->name('tasks.index');

            // Show create task form (GET)
            Route::get('/tasks/create', [TenantTaskController::class, 'create'])
                ->middleware('tenant.role:admin,manager')
                ->name('tasks.create');

            // Store task (POST)
            Route::post('/tasks/create', [TenantTaskController::class, 'store'])
                ->middleware('tenant.role:admin,manager')
                ->name('tasks.store');

            // Update task status
            Route::post('/tasks/{taskId}/status', [TenantTaskController::class, 'updateStatus']);

            // Edit task form
            Route::get('/tasks/{task}/edit', [TenantTaskController::class, 'edit'])
                ->middleware('tenant.role:admin,manager')
                ->name('tasks.edit');

            // Update task
            Route::put('/tasks/{task}', [TenantTaskController::class, 'update'])
                ->middleware('tenant.role:admin,manager')
                ->name('tasks.update');

            // Delete task
            Route::delete('/tasks/{task}', [TenantTaskController::class, 'destroy'])
                ->middleware('tenant.role:admin,manager')
                ->name('tasks.destroy');

            Route::get('/api/tasks/{task}', function ($tenant, \App\Models\Task $task) {

                return response()->json([
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => ucfirst(str_replace('_', ' ', $task->status)),
                    'assignees' => $task->users->pluck('name')->join(', '),
                    'updated_by' => $task->creator?->name ?? 'System',
                    'updated_at' => $task->updated_at->format('d M Y, h:i A'),
                ]);
            })->middleware('auth:tenant');


            // ---------------- LEAVES ----------------
            Route::get('/leaves', [TenantLeaveController::class, 'index'])->name('leaves.index');

            // Employee + Manager → apply leave
            Route::middleware('tenant.role:employee,manager')->group(function () {
                Route::get('/leaves/apply', [TenantLeaveController::class, 'create'])
                    ->name('leaves.apply');
                Route::post('/leaves/apply/submit', [TenantLeaveController::class, 'store']);
            });

            Route::middleware('tenant.role:manager,admin')->group(function () {
                Route::post(
                    '/leaves/{leaveId}/status',
                    [TenantLeaveController::class, 'updateStatus']
                );
            });
            Route::middleware('tenant.role:manager,admin')->group(function () {
                Route::post(
                    '/leaves/{leaveId}/status',
                    [TenantLeaveController::class, 'updateStatus']
                );
            });

            //---------------- DOCUMENTS ----------------
            Route::get('/documents', [TenantDocumentController::class, 'index'])
                ->name('documents.index');

            Route::get('/documents/upload', [TenantDocumentController::class, 'create'])
                ->name('documents.upload');

            Route::post('/documents/upload', [TenantDocumentController::class, 'store']);

            Route::get('/documents/{document}/download', [TenantDocumentController::class, 'download'])
                ->name('documents.download');

            Route::get(
                '/documents/{document}/preview',
                [TenantDocumentController::class, 'preview']
            )->name('documents.preview');

            Route::delete('/documents/{document}', [TenantDocumentController::class, 'destroy'])
                ->name('documents.destroy');
        });
    });
