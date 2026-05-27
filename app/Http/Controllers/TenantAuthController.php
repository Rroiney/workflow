<?php

namespace App\Http\Controllers;

use App\Actions\Tenant\AuthenticateTenantUserAction;
use App\Http\Requests\Tenant\Auth\LoginTenantRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantAuthController extends Controller
{
    public function showLogin(string $tenant): View
    {
        return view('tenant.login', compact('tenant'));
    }

    public function login(
        LoginTenantRequest $request,
        AuthenticateTenantUserAction $authenticateTenantUserAction,
        string $tenant
    ): RedirectResponse
    {
        if ($authenticateTenantUserAction->execute($request)) {
            return redirect()->route('home', ['tenant' => $tenant]);
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    public function logout(\Illuminate\Http\Request $request, string $tenant): RedirectResponse
    {
        Auth::guard('tenant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/org/{$tenant}/login");
    }
}
