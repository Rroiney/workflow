<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Middleware aliases
        $middleware->alias([
            'tenant.db'   => \App\Http\Middleware\SetTenantDatabase::class,
            'tenant.role' => \App\Http\Middleware\CheckTenantRole::class,
        ]);

        // Redirect unauthenticated users
        $middleware->redirectGuestsTo(function ($request) {

            // Get tenant slug from URL
            $tenant = $request->route('tenant');

            if ($tenant) {
                return url("/org/{$tenant}/login");
            }

            // Fallback (non-tenant routes)
            return '/';
        });
    })


    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
