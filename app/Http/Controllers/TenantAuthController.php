<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantAuthController extends Controller
{
    public function showLogin($tenant)
    {
        return view('tenant.login', compact('tenant'));
    }

    public function login(Request $request, $tenant)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'nullable|boolean',
        ]);

        $remember = $request->boolean('remember');
        unset($credentials['remember']);

        if (Auth::guard('tenant')->attempt($credentials, $remember)) {
            // ✅ REQUIRED
            $request->session()->regenerate();

            return redirect()->route('home', ['tenant' => $tenant]);
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    public function logout(Request $request, $tenant)
    {
        Auth::guard('tenant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/org/{$tenant}/login");
    }
}
