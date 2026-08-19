<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funnel;
use App\Services\Funnels\FunnelAnalyticsService;

class FunnelAnalyticsController extends Controller
{
    public function show(Funnel $funnel, FunnelAnalyticsService $analyticsService)
    {
        $metrics = $analyticsService->getMetrics($funnel);
        $stepDropOff = $analyticsService->getStepDropOff($funnel);

        $recentResponses = $funnel->responses()
            ->with(['result', 'inquiry'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.funnels.analytics', compact('funnel', 'metrics', 'stepDropOff', 'recentResponses'));
    }
}
