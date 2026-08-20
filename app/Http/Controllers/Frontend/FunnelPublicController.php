<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Funnel;
use App\Models\FunnelResponse;
use App\Services\Funnels\FunnelAnalyticsService;
use App\Services\Funnels\FunnelCrmSyncService;
use App\Services\Funnels\FunnelExecutionEngine;
use App\Services\Funnels\FunnelTrackingService;
use App\Support\UtmAttributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FunnelPublicController extends Controller
{
    public function show(string $slug, Request $request, FunnelTrackingService $trackingService)
    {
        $funnel = Funnel::where('slug', $slug)
            ->with(['steps.elements', 'results', 'conditions'])
            ->firstOrFail();

        $isAdmin = auth()->check();
        $isPreview = $request->has('preview') || $isAdmin;

        if ($funnel->template_id && $funnel->template) {
            $welcomeStep = $funnel->steps->firstWhere('step_type', 'welcome');
            $hasInvalidWelcomeInput = $welcomeStep && $welcomeStep->elements->whereIn('element_type', ['text_input', 'short_answer', 'input', 'text_input'])->count() > 0;
            if ($funnel->steps->isEmpty() || $hasInvalidWelcomeInput || Str::contains($funnel->slug, 'travel-giveaway-survey')) {
                $funnel->steps()->delete();
                $funnel->results()->delete();
                app(\App\Http\Controllers\Admin\FunnelController::class)->importSchemaToFunnel($funnel, $funnel->template->schema_data ?? []);
                $funnel->load(['steps.elements', 'results', 'conditions']);
            }
        }

        if (! $funnel->isPublished() && ! $isPreview) {
            abort(404, __('admin.funnel_not_found_or_unpublished'));
        }

        $headerScripts = $trackingService->getHeaderScripts($funnel);
        $bodyScripts = $trackingService->getBodyScripts($funnel);
        $dispatcherJs = $trackingService->getEventDispatcherJs();

        return view('frontend.funnels.show', compact('funnel', 'headerScripts', 'bodyScripts', 'dispatcherJs', 'isPreview'));
    }

    public function submit(string $slug, Request $request, FunnelExecutionEngine $engine, FunnelCrmSyncService $crmService)
    {
        $funnel = Funnel::where('slug', $slug)
            ->with(['steps.elements', 'results', 'conditions'])
            ->firstOrFail();

        $answers = $request->input('answers', []);
        $sessionId = $request->input('session_id') ?: Str::uuid()->toString();
        $isPreview = (bool) $request->input('is_preview', false);

        // 1. Calculate Score
        $score = $engine->calculateScore($funnel, $answers);

        // 2. Resolve Result Outcome
        $result = $engine->resolveResult($funnel, $score, $answers);

        // Extract UTM data
        $utmData = app(UtmAttributionService::class)->attributesForInquiry($request);

        if ($isPreview) {
            return response()->json([
                'success' => true,
                'is_preview' => true,
                'score' => $score,
                'result' => $result,
                'message' => 'Preview submission successful (No production CRM sync)',
            ]);
        }

        // 3. Save FunnelResponse record
        $response = FunnelResponse::create([
            'funnel_id' => $funnel->id,
            'funnel_result_id' => $result?->id,
            'session_id' => $sessionId,
            'visitor_ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'score' => $score,
            'is_completed' => true,
            'completed_at' => now(),
            'utm_data' => $utmData,
        ]);

        // 4. Save itemized answers
        $funnel->loadMissing('steps.elements');
        $recordedKeys = [];

        foreach ($funnel->steps as $step) {
            foreach ($step->elements as $element) {
                $key = $element->question_key ?: $element->id;
                $ansVal = $answers[$element->id] ?? $answers[$element->question_key] ?? null;

                if ($ansVal !== null) {
                    $formattedVal = is_array($ansVal) ? implode(', ', $ansVal) : (string) $ansVal;
                    $response->answers()->create([
                        'element_id' => $element->id,
                        'question_label' => $element->label ?: ($element->question_key ?: ('Question ' . $element->id)),
                        'answer_value' => $formattedVal,
                    ]);
                    $recordedKeys[] = (string) $element->id;
                    if ($element->question_key) {
                        $recordedKeys[] = (string) $element->question_key;
                    }
                }
            }
        }

        // Save any direct top-level submitted answer fields (e.g. full_name, phone, email) not tied to single element ID
        foreach ($answers as $k => $v) {
            if (! in_array((string) $k, $recordedKeys, true) && filled($v)) {
                $response->answers()->create([
                    'element_id' => null,
                    'question_label' => (string) $k,
                    'answer_value' => is_array($v) ? implode(', ', $v) : (string) $v,
                ]);
            }
        }

        // 5. Sync to Travel Wave CRM (Inquiry model)
        $crmSynced = $crmService->syncToCrm($response);

        return response()->json([
            'success' => true,
            'response_id' => $response->id,
            'score' => $score,
            'result' => [
                'id' => $result?->id,
                'title' => $result?->title,
                'description' => $result?->description,
                'image_url' => $result?->image_url,
                'cta_label' => $result?->cta_label,
                'cta_type' => $result?->cta_type,
                'cta_url' => $result?->cta_url,
                'cta_whatsapp_number' => $result?->cta_whatsapp_number,
            ],
            'crm_synced' => $crmSynced,
        ]);
    }

    public function trackStepView(string $slug, Request $request)
    {
        // Simple endpoint for step drop-off recording
        return response()->json(['success' => true]);
    }
}
