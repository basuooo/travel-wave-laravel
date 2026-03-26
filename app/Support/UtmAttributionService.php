<?php

namespace App\Support;

use App\Models\MarketingLandingPage;
use App\Models\UtmCampaign;
use App\Models\UtmVisit;
use Illuminate\Http\Request;

class UtmAttributionService
{
    protected const SESSION_KEY = 'utm_attribution';

    public function captureFromRequest(Request $request): void
    {
        if (! $this->requestHasUtmParameters($request)) {
            return;
        }

        $payload = $this->normalizePayload([
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_id' => $request->query('utm_id'),
            'utm_term' => $request->query('utm_term'),
            'utm_content' => $request->query('utm_content'),
            'landing_page' => $request->fullUrl(),
            'referrer' => $request->headers->get('referer'),
            'touch_at' => now(),
        ]);

        $this->rememberPayload($request, $payload);
        $this->createVisit($request, $payload);
    }

    public function captureLandingPageTouch(Request $request, MarketingLandingPage $landingPage): void
    {
        $payload = $this->normalizePayload([
            'utm_source' => $landingPage->utm_source ?: $request->query('utm_source'),
            'utm_medium' => $landingPage->utm_medium ?: $request->query('utm_medium'),
            'utm_campaign' => $landingPage->utm_campaign ?: $request->query('utm_campaign'),
            'utm_id' => $request->query('utm_id'),
            'utm_term' => $landingPage->utm_term ?: $request->query('utm_term'),
            'utm_content' => $landingPage->utm_content ?: $request->query('utm_content'),
            'landing_page' => $request->fullUrl(),
            'referrer' => $request->headers->get('referer'),
            'touch_at' => now(),
        ]);

        if (! $this->payloadHasAttribution($payload)) {
            return;
        }

        $this->rememberPayload($request, $payload);

        if (! $this->requestHasUtmParameters($request)) {
            $this->createVisit($request, $payload);
        }
    }

    public function attributesForInquiry(Request $request): array
    {
        $sessionPayload = $request->session()->get(self::SESSION_KEY, []);
        $first = $sessionPayload['first'] ?? [];
        $last = $sessionPayload['last'] ?? [];

        $currentRequestPayload = $this->normalizePayload([
            'utm_source' => $request->input('utm_source'),
            'utm_medium' => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'utm_id' => $request->input('utm_id'),
            'utm_term' => $request->input('utm_term'),
            'utm_content' => $request->input('utm_content'),
            'landing_page' => $request->input('landing_page') ?: $request->fullUrl(),
            'referrer' => $request->headers->get('referer'),
            'touch_at' => now(),
        ]);

        $last = $this->payloadHasAttribution($currentRequestPayload)
            ? array_merge($last, array_filter($currentRequestPayload, fn ($value) => filled($value)))
            : $last;
        $first = $first ?: $last;
        $campaign = $this->resolveCampaign($last);

        return [
            'utm_campaign_id' => $campaign?->id,
            'campaign_name' => $last['utm_campaign'] ?? null,
            'utm_source' => $last['utm_source'] ?? null,
            'utm_medium' => $last['utm_medium'] ?? null,
            'utm_campaign' => $last['utm_campaign'] ?? null,
            'utm_id' => $last['utm_id'] ?? null,
            'utm_term' => $last['utm_term'] ?? null,
            'utm_content' => $last['utm_content'] ?? null,
            'landing_page' => $last['landing_page'] ?? null,
            'referrer' => $last['referrer'] ?? null,
            'first_touch_at' => $first['touch_at'] ?? null,
            'last_touch_at' => $last['touch_at'] ?? null,
            'first_utm_source' => $first['utm_source'] ?? null,
            'first_utm_medium' => $first['utm_medium'] ?? null,
            'first_utm_campaign' => $first['utm_campaign'] ?? null,
            'first_utm_id' => $first['utm_id'] ?? null,
            'first_utm_term' => $first['utm_term'] ?? null,
            'first_utm_content' => $first['utm_content'] ?? null,
            'first_landing_page' => $first['landing_page'] ?? null,
            'first_referrer' => $first['referrer'] ?? null,
        ];
    }

    public function requestHasUtmParameters(Request $request): bool
    {
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_id', 'utm_term', 'utm_content'] as $key) {
            if (filled($request->query($key))) {
                return true;
            }
        }

        return false;
    }

    protected function createVisit(Request $request, array $payload): void
    {
        $campaign = $this->resolveCampaign($payload);

        UtmVisit::query()->create([
            'utm_campaign_id' => $campaign?->id,
            'session_key' => $request->session()->getId(),
            'utm_source' => $payload['utm_source'] ?? null,
            'utm_medium' => $payload['utm_medium'] ?? null,
            'utm_campaign' => $payload['utm_campaign'] ?? null,
            'utm_id' => $payload['utm_id'] ?? null,
            'utm_term' => $payload['utm_term'] ?? null,
            'utm_content' => $payload['utm_content'] ?? null,
            'landing_page' => $payload['landing_page'] ?? null,
            'referrer' => $payload['referrer'] ?? null,
            'request_path' => $request->path(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visited_at' => $payload['touch_at'] ?? now(),
        ]);
    }

    protected function rememberPayload(Request $request, array $payload): void
    {
        $stored = $request->session()->get(self::SESSION_KEY, []);
        $first = $stored['first'] ?? null;

        if (! $first || ! $this->payloadHasAttribution($first)) {
            $stored['first'] = $payload;
        }

        $stored['last'] = $payload;
        $request->session()->put(self::SESSION_KEY, $stored);
    }

    protected function resolveCampaign(array $payload): ?UtmCampaign
    {
        if (! $this->payloadHasAttribution($payload)) {
            return null;
        }

        $query = UtmCampaign::query();

        if (filled($payload['utm_id'] ?? null)) {
            $match = (clone $query)->where('utm_id', $payload['utm_id'])->first();
            if ($match) {
                return $match;
            }
        }

        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $column) {
            if (filled($payload[$column] ?? null)) {
                $query->where($column, $payload[$column]);
            }
        }

        return $query->latest('id')->first();
    }

    protected function payloadHasAttribution(array $payload): bool
    {
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_id', 'utm_term', 'utm_content'] as $key) {
            if (filled($payload[$key] ?? null)) {
                return true;
            }
        }

        return false;
    }

    protected function normalizePayload(array $payload): array
    {
        $normalized = [];

        foreach ($payload as $key => $value) {
            if ($key === 'touch_at') {
                $normalized[$key] = $value;
                continue;
            }

            $normalized[$key] = filled($value) ? trim((string) $value) : null;
        }

        return $normalized;
    }
}
