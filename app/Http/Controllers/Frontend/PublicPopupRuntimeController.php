<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PopupManager\Popup;
use App\Models\PopupManager\PopupAnalytic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPopupRuntimeController extends Controller
{
    public function getAvailablePopups(Request $request): JsonResponse
    {
        Popup::ensureTableSchema();

        $pageUrl = $request->input('url', '');
        $device = strtolower($request->input('device', 'desktop'));
        $utmSource = $request->input('utm_source');

        $activePopups = Popup::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $eligiblePopups = [];

        foreach ($activePopups as $popup) {
            $conditions = $popup->condition_settings ?? [];

            // Device Check
            if (isset($conditions['devices']) && is_array($conditions['devices'])) {
                if (isset($conditions['devices'][$device]) && ! $conditions['devices'][$device]) {
                    continue;
                }
            }

            // Pages / URL Matching Check
            $pagesMode = $conditions['pages_mode'] ?? 'all';
            if ($pagesMode === 'specific' && ! empty($conditions['specific_urls'])) {
                $matched = false;
                foreach ((array) $conditions['specific_urls'] as $pattern) {
                    if (str_contains($pageUrl, $pattern)) {
                        $matched = true;
                        break;
                    }
                }
                if (! $matched) {
                    continue;
                }
            }

            $eligiblePopups[] = [
                'id' => $popup->id,
                'name' => $popup->name,
                'layout' => $popup->layout,
                'priority' => $popup->priority,
                'trigger' => $popup->trigger_settings ?? [
                    'mode' => 'random_time',
                    'min_delay_seconds' => 20,
                    'max_delay_seconds' => 60,
                ],
                'frequency' => $popup->frequency_settings ?? ['mode' => 'once_per_session'],
                'size' => $popup->size_settings ?? [],
                'overlay' => $popup->overlay_settings ?? ['enable' => true, 'close_on_click' => true],
                'close_button' => $popup->close_button_settings ?? ['show' => true],
                'animation' => $popup->animation_settings ?? ['type' => 'fade_zoom'],
                'html' => is_array($popup->structure) ? ($popup->structure['html'] ?? '') : '',
                'exit_warning' => is_array($popup->structure) ? ($popup->structure['exit_warning'] ?? []) : [],
                'custom_css' => $popup->custom_css ?? '',
                'custom_js' => $popup->custom_js ?? '',
                'assigned_lead_form_id' => $popup->assigned_lead_form_id,
            ];
        }

        return response()->json([
            'success' => true,
            'popups' => $eligiblePopups,
        ]);
    }

    public function trackEvent(Request $request): JsonResponse
    {
        PopupAnalytic::ensureTableSchema();

        $validated = $request->validate([
            'popup_id' => ['required', 'exists:popups,id'],
            'event_type' => ['required', 'in:impression,click,conversion,close'],
            'page_url' => ['nullable', 'string'],
            'device' => ['nullable', 'string'],
            'utm_source' => ['nullable', 'string'],
            'utm_campaign' => ['nullable', 'string'],
        ]);

        PopupAnalytic::create([
            'popup_id' => $validated['popup_id'],
            'event_type' => $validated['event_type'],
            'page_url' => $validated['page_url'] ?? $request->header('referer'),
            'device' => $validated['device'] ?? 'desktop',
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Increment counter in Popup
        $popup = Popup::find($validated['popup_id']);
        if ($popup) {
            if ($validated['event_type'] === PopupAnalytic::EVENT_IMPRESSION) {
                $popup->increment('views_count');
            } elseif ($validated['event_type'] === PopupAnalytic::EVENT_CLICK) {
                $popup->increment('clicks_count');
            } elseif ($validated['event_type'] === PopupAnalytic::EVENT_CONVERSION) {
                $popup->increment('conversions_count');
            }
        }

        return response()->json(['success' => true]);
    }
}
