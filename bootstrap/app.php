<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetTenantDatabase;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->prependToGroup('web', SetTenantDatabase::class);

        // Middleware aliases
        $middleware->alias([
            'tenant.db'   => SetTenantDatabase::class,
            'tenant.role' => \App\Http\Middleware\CheckTenantRole::class,
        ]);

        // Redirect unauthenticated users
        $middleware->redirectGuestsTo(function ($request) {

            $tenant = $request->route('tenant');

            if ($tenant) {
                return url("/org/{$tenant}/login");
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
