<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\KpiDashboardService;
use Illuminate\Http\Request;

class KpiDashboardController extends Controller
{
    public function index(Request $request, KpiDashboardService $dashboardService)
    {
        return view('admin.kpi.dashboard', $dashboardService->build($request, $request->user()));
    }
}
