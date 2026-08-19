<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmLeadSource;
use App\Models\CrmServiceType;
use App\Models\Funnel;
use App\Models\FunnelElement;
use App\Models\FunnelResult;
use App\Models\FunnelStep;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FunnelBuilderController extends Controller
{
    public function edit(Funnel $funnel)
    {
        $funnel->load(['steps.elements', 'results', 'conditions']);

        $leadSources = CrmLeadSource::where('is_active', true)->get();
        $serviceTypes = CrmServiceType::where('is_active', true)->get();

        return view('admin.funnels.builder', compact('funnel', 'leadSources', 'serviceTypes'));
    }

    public function update(Request $request, Funnel $funnel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:funnels,slug,' . $funnel->id,
            'status' => 'nullable|string|in:draft,published,unpublished',
            'design_settings' => 'nullable|array',
            'tracking_settings' => 'nullable|array',
            'crm_settings' => 'nullable|array',
            'seo_settings' => 'nullable|array',
            'steps' => 'nullable|array',
            'results' => 'nullable|array',
        ]);

        $funnel->update([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['slug']),
            'status' => $validated['status'] ?? $funnel->status,
            'design_settings' => $validated['design_settings'] ?? $funnel->design_settings,
            'tracking_settings' => $validated['tracking_settings'] ?? $funnel->tracking_settings,
            'crm_settings' => $validated['crm_settings'] ?? $funnel->crm_settings,
            'seo_settings' => $validated['seo_settings'] ?? $funnel->seo_settings,
        ]);

        // Sync Steps & Elements if provided in payload
        if (isset($validated['steps']) && is_array($validated['steps'])) {
            $existingStepIds = [];

            foreach ($validated['steps'] as $sIndex => $stepData) {
                $stepId = $stepData['id'] ?? null;
                $step = FunnelStep::updateOrCreate(
                    ['id' => $stepId, 'funnel_id' => $funnel->id],
                    [
                        'funnel_id' => $funnel->id,
                        'title' => $stepData['title'] ?? 'Step ' . ($sIndex + 1),
                        'subtitle' => $stepData['subtitle'] ?? null,
                        'step_type' => $stepData['step_type'] ?? 'question',
                        'sort_order' => $sIndex + 1,
                        'is_hidden' => ! empty($stepData['is_hidden']),
                    ]
                );
                $existingStepIds[] = $step->id;

                if (isset($stepData['elements']) && is_array($stepData['elements'])) {
                    $existingElementIds = [];

                    foreach ($stepData['elements'] as $eIndex => $elData) {
                        $elId = $elData['id'] ?? null;
                        $props = $elData['properties'] ?? [];
                        if (isset($elData['is_required'])) {
                            $props['is_required'] = (bool) $elData['is_required'];
                        }

                        $element = FunnelElement::updateOrCreate(
                            ['id' => $elId, 'step_id' => $step->id],
                            [
                                'step_id' => $step->id,
                                'element_type' => $elData['element_type'] ?? 'text',
                                'label' => $elData['label'] ?? null,
                                'question_key' => $elData['question_key'] ?? ('q_' . Str::random(6)),
                                'properties' => $props,
                                'sort_order' => $eIndex + 1,
                            ]
                        );
                        $existingElementIds[] = $element->id;
                    }

                    // Delete elements removed from step
                    $step->elements()->whereNotIn('id', $existingElementIds)->delete();
                }
            }

            // Delete steps removed from funnel
            $funnel->steps()->whereNotIn('id', $existingStepIds)->delete();
        }

        // Sync Results if provided in payload
        if (isset($validated['results']) && is_array($validated['results'])) {
            $existingResultIds = [];

            foreach ($validated['results'] as $rIndex => $rData) {
                $rId = $rData['id'] ?? null;
                $result = FunnelResult::updateOrCreate(
                    ['id' => $rId, 'funnel_id' => $funnel->id],
                    [
                        'funnel_id' => $funnel->id,
                        'title' => $rData['title'] ?? 'Result',
                        'description' => $rData['description'] ?? null,
                        'image_url' => $rData['image_url'] ?? null,
                        'min_score' => isset($rData['min_score']) && $rData['min_score'] !== '' ? (int) $rData['min_score'] : null,
                        'max_score' => isset($rData['max_score']) && $rData['max_score'] !== '' ? (int) $rData['max_score'] : null,
                        'cta_label' => $rData['cta_label'] ?? 'Contact Us',
                        'cta_type' => $rData['cta_type'] ?? 'button',
                        'cta_url' => $rData['cta_url'] ?? null,
                        'cta_whatsapp_number' => $rData['cta_whatsapp_number'] ?? null,
                        'logic_conditions' => $rData['logic_conditions'] ?? null,
                        'sort_order' => $rIndex + 1,
                    ]
                );
                $existingResultIds[] = $result->id;
            }

            $funnel->results()->whereNotIn('id', $existingResultIds)->delete();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('admin.funnel_saved_successfully'),
                'funnel_id' => $funnel->id,
                'slug' => $funnel->slug,
            ]);
        }

        return redirect()->back()->with('success', __('admin.funnel_saved_successfully'));
    }
}
