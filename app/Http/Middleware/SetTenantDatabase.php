<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetTenantDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, \Closure $next)
    {
        $slug = $request->route('tenant');

        if (!$slug) {
            return response('Tenant not specified', 404);
        }

        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            return response('Tenant not found', 404);
        }

        Config::set('database.connections.tenant.database', $tenant->db_name);
        Config::set('database.connections.tenant.username', $tenant->db_username);
        Config::set('database.connections.tenant.password', $tenant->db_password);
        Config::set('database.connections.tenant.host', $tenant->db_host);
        Config::set('database.connections.tenant.port', $tenant->db_port);

        DB::purge('tenant');
        DB::reconnect('tenant');

        return $next($request);
    }
}
