<?php

namespace App\Services\Funnels;

use App\Models\Funnel;
use App\Models\FunnelResponse;
use Illuminate\Support\Facades\DB;

class FunnelAnalyticsService
{
    /**
     * Get aggregate metric overview for dashboard or funnel detail view.
     */
    public function getMetrics(?Funnel $funnel = null): array
    {
        $query = FunnelResponse::query();
        if ($funnel) {
            $query->where('funnel_id', $funnel->id);
        }

        $totalResponses = (clone $query)->count();
        $totalCompletions = (clone $query)->where('is_completed', true)->count();
        $totalLeads = (clone $query)->whereNotNull('crm_inquiry_id')->count();

        $qualifiedLeads = (clone $query)->whereHas('result', function ($q) {
            $q->where('title', 'like', '%Qualified%')
              ->orWhere('title', 'like', '%مؤهل%')
              ->orWhere('min_score', '>=', 60);
        })->count();

        $conversionRate = $totalResponses > 0
            ? round(($totalCompletions / $totalResponses) * 100, 1)
            : 0;

        $qualificationRate = $totalCompletions > 0
            ? round(($qualifiedLeads / $totalCompletions) * 100, 1)
            : 0;

        $avgScore = round((float) ((clone $query)->avg('score') ?? 0), 1);

        return [
            'total_visitors' => $totalResponses,
            'total_starts' => $totalResponses,
            'total_completions' => $totalCompletions,
            'total_leads' => $totalLeads,
            'qualified_leads' => $qualifiedLeads,
            'conversion_rate' => $conversionRate,
            'qualification_rate' => $qualificationRate,
            'average_score' => $avgScore,
        ];
    }

    /**
     * Calculate step drop-off analytics.
     */
    public function getStepDropOff(Funnel $funnel): array
    {
        if (! Schema::hasTable('funnel_responses') || ! Schema::hasTable('funnel_response_answers')) {
            return [];
        }

        $steps = $funnel->steps()->with('elements')->get();
        $totalResponses = $funnel->responses()->count();
        $stepData = [];

        $previousCount = $totalResponses;

        foreach ($steps as $index => $step) {
            // Count responses that answered elements in this step
            $elementIds = $step->elements->pluck('id')->all();
            $stepCompletions = $totalResponses > 0
                ? DB::table('funnel_response_answers')
                    ->whereIn('element_id', $elementIds)
                    ->distinct('response_id')
                    ->count('response_id')
                : 0;

            if ($index === 0 && $stepCompletions === 0 && $totalResponses > 0) {
                $stepCompletions = $totalResponses;
            }

            $dropOffCount = max(0, $previousCount - $stepCompletions);
            $dropOffRate = $previousCount > 0
                ? round(($dropOffCount / $previousCount) * 100, 1)
                : 0;

            $stepData[] = [
                'step_id' => $step->id,
                'step_title' => $step->title,
                'step_type' => $step->step_type,
                'visitors' => $stepCompletions,
                'drop_off_count' => $dropOffCount,
                'drop_off_rate' => $dropOffRate,
            ];

            $previousCount = $stepCompletions;
        }

        return $stepData;
    }
}
