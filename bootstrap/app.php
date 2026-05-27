<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetTenantDatabase;

$app = Application::configure(
    basePath: dirname(__DIR__)
)
    ->registered(function (Application $app) {
        // On cPanel shared hosting we deploy the full app into "workflow-app"
        // and serve the sibling "workflow" directory as the public web root.
        if (basename(dirname(__DIR__)) === 'workflow-app') {
            $sharedPublicPath = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'workflow';

            if (is_dir($sharedPublicPath)) {
                $app->usePublicPath($sharedPublicPath);
            }
        }
    })
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Prepend tenant database middleware to web group
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

return $app;
