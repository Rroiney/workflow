<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\Team;
use App\Policies\DocumentPolicy;
use App\Policies\LeaveRequestPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') && request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(LeaveRequest::class, LeaveRequestPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);

        Route::bind('task', fn (string $value) => Task::query()->findOrFail($value));
        Route::bind('leave', fn (string $value) => LeaveRequest::query()->findOrFail($value));
        Route::bind('document', fn (string $value) => Document::query()->findOrFail($value));
        Route::bind('team', fn (string $value) => Team::query()->findOrFail($value));
    }
}
