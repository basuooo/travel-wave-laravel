<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funnel;
use App\Services\Funnels\FunnelAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FunnelDashboardController extends Controller
{
    public function index(FunnelAnalyticsService $analyticsService)
    {
        $needsMigration = ! Schema::hasTable('funnels') || ! Schema::hasTable('funnel_responses');

        if ($needsMigration) {
            $metrics = $analyticsService->getMetrics();
            $funnelsCount = 0;
            $publishedCount = 0;
            $draftCount = 0;
            $funnels = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

            return view('admin.funnels.dashboard', compact(
                'metrics',
                'funnelsCount',
                'publishedCount',
                'draftCount',
                'funnels',
                'needsMigration'
            ));
        }

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
            'funnels',
            'needsMigration'
        ));
    }
}
