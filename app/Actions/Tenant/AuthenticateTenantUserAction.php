<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Auth\LoginTenantRequest;
use Illuminate\Support\Facades\Auth;

class AuthenticateTenantUserAction
{
    public function execute(LoginTenantRequest $request): bool
    {
        $remember = $request->boolean('remember');

        if (!Auth::guard('tenant')->attempt($request->credentials(), $remember)) {
            return false;
        }

        $request->session()->regenerate();

        return true;
    }
}
