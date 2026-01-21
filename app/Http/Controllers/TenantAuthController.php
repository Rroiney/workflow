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
        ]);

        if (Auth::guard('tenant')->attempt($credentials)) {
            return redirect("/org/{$tenant}/home");
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    public function logout($tenant)
    {
        Auth::guard('tenant')->logout();
        return redirect("/org/{$tenant}/login");
    }
}
