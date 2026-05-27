<?php

namespace App\Http\Controllers;

use App\Repositories\Tenant\DashboardRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantDashboardController extends Controller
{
    public function index(DashboardRepository $dashboardRepository): View
    {
        $user = Auth::guard('tenant')->user();
        $dashboard = $dashboardRepository->getDashboardData($user);

        return view('tenant.dashboard', $dashboard);
    }
}
