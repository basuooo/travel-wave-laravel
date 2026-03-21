<?php

namespace App\Support;

use App\Models\Inquiry;
use App\Models\MarketingLandingPage;
use App\Models\MarketingLandingPageEvent;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class MarketingLandingPageAnalytics
{
    public static function statsFor(MarketingLandingPage $landingPage, ?CarbonInterface $from = null, ?CarbonInterface $to = null): array
    {
        $events = $landingPage->events()->when($from, fn ($query) => $query->where('occurred_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('occurred_at', '<=', $to))
            ->get();

        $pageViews = $events->where('event_type', MarketingLandingPageEvent::TYPE_PAGE_VIEW);
        $ctaClicks = $events->where('event_type', MarketingLandingPageEvent::TYPE_CTA_CLICK)->count();
        $whatsappClicks = $events->where('event_type', MarketingLandingPageEvent::TYPE_WHATSAPP_CLICK)->count();
        $formEvents = $events->where('event_type', MarketingLandingPageEvent::TYPE_FORM_SUBMIT)->count();

        $leadCount = Inquiry::query()
            ->where('marketing_landing_page_id', $landingPage->id)
            ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('created_at', '<=', $to))
            ->count();

        $visits = $pageViews->count();
        $uniqueVisits = $pageViews->pluck('session_key')->filter()->unique()->count();

        $sourceMode = $pageViews->pluck('source')->filter()->mode();

        return [
            'visits' => $visits,
            'unique_visits' => $uniqueVisits,
            'form_submissions' => max($leadCount, $formEvents),
            'whatsapp_clicks' => $whatsappClicks,
            'cta_clicks' => $ctaClicks,
            'leads' => $leadCount,
            'conversion_rate' => $visits > 0 ? round(($leadCount / $visits) * 100, 2) : 0,
            'top_source' => is_array($sourceMode) ? ($sourceMode[0] ?? null) : null,
        ];
    }

    public static function summary(): array
    {
        $pages = MarketingLandingPage::query()->get();
        $events = MarketingLandingPageEvent::query()->get();
        $pageViews = $events->where('event_type', MarketingLandingPageEvent::TYPE_PAGE_VIEW);
        $leads = Inquiry::query()->whereNotNull('marketing_landing_page_id')->count();

        $bestPage = $pages->map(function (MarketingLandingPage $page) {
            return [
                'page' => $page,
                'stats' => self::statsFor($page),
            ];
        })->sortByDesc(fn (array $row) => $row['stats']['conversion_rate'])
            ->first();

        return [
            'landing_pages' => $pages->count(),
            'active_campaigns' => $pages->where('status', MarketingLandingPage::STATUS_PUBLISHED)->count(),
            'visits' => $pageViews->count(),
            'leads' => $leads,
            'best_page' => $bestPage,
            'latest_submissions' => Inquiry::query()
                ->whereNotNull('marketing_landing_page_id')
                ->latest()
                ->limit(6)
                ->with('marketingLandingPage')
                ->get(),
        ];
    }

    public static function topPages(int $limit = 5): Collection
    {
        return MarketingLandingPage::query()->get()
            ->map(function (MarketingLandingPage $page) {
                return [
                    'page' => $page,
                    'stats' => self::statsFor($page),
                ];
            })
            ->sortByDesc(fn (array $row) => $row['stats']['leads'])
            ->take($limit)
            ->values();
    }
}
