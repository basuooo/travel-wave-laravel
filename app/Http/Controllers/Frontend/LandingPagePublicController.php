<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage\LpLandingPage;
use App\Models\MarketingLandingPageEvent;
use App\Models\TrackingIntegration;
use App\Support\TrackingManager;
use App\Support\UtmAttributionService;
use Illuminate\Http\Request;

class LandingPagePublicController extends Controller
{
    /**
     * Display a published Landing Page to public visitors
     */
    public function show(string $slug, Request $request)
    {
        $landingPage = LpLandingPage::where('slug', $slug)->first();

        if (! $landingPage || ! $landingPage->isPublished()) {
            abort(404, 'Landing page not found or inactive.');
        }

        $request->session()->start();

        // Capture UTM Attribution
        app(UtmAttributionService::class)->captureLandingPageTouch($request, $landingPage);

        // Record Page View Analytics Event
        MarketingLandingPageEvent::query()->create([
            'marketing_landing_page_id' => $landingPage->id,
            'event_type' => MarketingLandingPageEvent::TYPE_PAGE_VIEW,
            'session_key' => $request->session()->getId(),
            'source' => $landingPage->utm_source ?: $request->query('utm_source'),
            'medium' => $landingPage->utm_medium ?: $request->query('utm_medium'),
            'campaign' => $landingPage->utm_campaign ?: $request->query('utm_campaign'),
            'content' => $landingPage->utm_content ?: $request->query('utm_content'),
            'term' => $landingPage->utm_term ?: $request->query('utm_term'),
            'referrer' => $request->headers->get('referer'),
            'path' => $request->path(),
            'payload' => [
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ],
            'occurred_at' => now(),
        ]);

        $pageTrackingIntegrations = TrackingIntegration::query()
            ->whereIn('id', $landingPage->tracking_integration_ids ?? [])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $trackingContext = array_merge(
            TrackingManager::contextFromRequest($request),
            [
                'page_type' => 'landing_page',
                'page_name' => $landingPage->internal_name,
                'landing_page_id' => $landingPage->id,
                'campaign_name' => $landingPage->campaign_name,
            ]
        );

        return view('frontend.landing-pages.show', [
            'landingPage' => $landingPage->load('leadForm.fields'),
            'pageTrackingIntegrations' => $pageTrackingIntegrations,
            'trackingContext' => $trackingContext,
        ]);
    }
}
