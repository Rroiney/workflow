<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTenantRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('tenant')->user();

        if (!$user) {
            abort(403);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
