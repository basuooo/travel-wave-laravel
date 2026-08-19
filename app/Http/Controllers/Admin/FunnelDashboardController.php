<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funnel;
use App\Services\Funnels\FunnelAnalyticsService;
use Illuminate\Http\Request;

class FunnelDashboardController extends Controller
{
    public function index(FunnelAnalyticsService $analyticsService)
    {
        $metrics = $analyticsService->getMetrics();
        $funnelsCount = Funnel::count();
        $publishedCount = Funnel::where('status', 'published')->count();
        $draftCount = Funnel::where('status', 'draft')->count();

        $funnels = Funnel::with('template')
            ->withCount(['responses', 'responses as completed_responses_count' => function ($q) {
                $q->where('is_completed', true);
            }])
            ->latest()
            ->paginate(15);

        return view('admin.funnels.dashboard', compact(
            'metrics',
            'funnelsCount',
            'publishedCount',
            'draftCount',
            'funnels'
        ));
    }
}
