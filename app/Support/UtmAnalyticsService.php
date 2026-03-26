<?php

namespace App\Support;

use App\Models\CrmStatus;
use App\Models\Inquiry;
use App\Models\UtmCampaign;
use App\Models\UtmVisit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UtmAnalyticsService
{
    public function build(Request $request): array
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'medium' => ['nullable', 'string', 'max:255'],
            'campaign_id' => ['nullable', 'integer', 'exists:utm_campaigns,id'],
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'seller_id' => ['nullable', 'integer', 'exists:users,id'],
            'crm_status_id' => ['nullable', 'integer', 'exists:crm_statuses,id'],
            'landing_page' => ['nullable', 'string', 'max:2048'],
        ]);

        $visitsQuery = UtmVisit::query();
        $leadsQuery = Inquiry::query()->where(function ($query) {
            $query->whereNotNull('utm_campaign_id')
                ->orWhereNotNull('utm_source')
                ->orWhereNotNull('utm_campaign');
        });
        $campaignQuery = UtmCampaign::query()->with(['owner', 'creator']);

        if (! empty($filters['from_date'])) {
            $visitsQuery->where('visited_at', '>=', $request->date('from_date')->startOfDay());
            $leadsQuery->where('created_at', '>=', $request->date('from_date')->startOfDay());
        }

        if (! empty($filters['to_date'])) {
            $visitsQuery->where('visited_at', '<=', $request->date('to_date')->endOfDay());
            $leadsQuery->where('created_at', '<=', $request->date('to_date')->endOfDay());
        }

        foreach (['source' => 'utm_source', 'medium' => 'utm_medium', 'landing_page' => 'landing_page'] as $filterKey => $column) {
            if (! empty($filters[$filterKey])) {
                $visitsQuery->where($column, $filters[$filterKey]);
                $leadsQuery->where($column, $filters[$filterKey]);
            }
        }

        if (! empty($filters['campaign_id'])) {
            $visitsQuery->where('utm_campaign_id', $filters['campaign_id']);
            $leadsQuery->where('utm_campaign_id', $filters['campaign_id']);
            $campaignQuery->whereKey($filters['campaign_id']);
        }

        if (! empty($filters['owner_user_id'])) {
            $campaignQuery->where('owner_user_id', $filters['owner_user_id']);
            $ownerCampaignIds = (clone $campaignQuery)->pluck('id');
            $visitsQuery->whereIn('utm_campaign_id', $ownerCampaignIds);
            $leadsQuery->whereIn('utm_campaign_id', $ownerCampaignIds);
        }

        if (! empty($filters['seller_id'])) {
            $leadsQuery->where('assigned_user_id', $filters['seller_id']);
        }

        if (! empty($filters['crm_status_id'])) {
            $leadsQuery->where('crm_status_id', $filters['crm_status_id']);
        }

        $visits = (clone $visitsQuery)->get();
        $leads = (clone $leadsQuery)->with(['assignedUser', 'crmStatus', 'utmCampaign'])->get();
        $campaigns = $campaignQuery->latest()->get();
        $convertedStatusIds = CrmStatus::query()
            ->whereIn('slug', ['documents-complete', 'booked', 'converted', 'closed'])
            ->pluck('id')
            ->filter()
            ->values();
        $convertedLeadIds = $convertedStatusIds->isEmpty()
            ? collect()
            : $leads->whereIn('crm_status_id', $convertedStatusIds)->pluck('id');

        $campaignPerformance = $campaigns->map(function (UtmCampaign $campaign) use ($visits, $leads, $convertedLeadIds) {
            $campaignVisits = $visits->where('utm_campaign_id', $campaign->id);
            $campaignLeads = $leads->where('utm_campaign_id', $campaign->id);
            $converted = $campaignLeads->whereIn('id', $convertedLeadIds);
            $traffic = $campaignVisits->count();
            $leadCount = $campaignLeads->count();

            return [
                'campaign' => $campaign,
                'traffic' => $traffic,
                'leads' => $leadCount,
                'converted' => $converted->count(),
                'conversion_rate' => $traffic > 0 ? round(($leadCount / $traffic) * 100, 2) : 0,
            ];
        })->sortByDesc('leads')->values();

        $sourceRows = $this->groupBreakdown($visits, $leads, 'utm_source');
        $mediumRows = $this->groupBreakdown($visits, $leads, 'utm_medium');
        $landingRows = $this->groupBreakdown($visits, $leads, 'landing_page');

        $sellerRows = $leads->groupBy('assigned_user_id')->map(function (Collection $sellerLeads, $sellerId) use ($convertedLeadIds) {
            $seller = $sellerLeads->first()?->assignedUser;
            $leadCount = $sellerLeads->count();
            $converted = $sellerLeads->whereIn('id', $convertedLeadIds)->count();

            return [
                'seller' => $seller,
                'leads' => $leadCount,
                'converted' => $converted,
                'conversion_rate' => $leadCount > 0 ? round(($converted / $leadCount) * 100, 2) : 0,
            ];
        })->filter(fn (array $row) => $row['seller'])->sortByDesc('leads')->values();

        $dateRows = collect();
        $allDates = $visits->groupBy(fn (UtmVisit $visit) => optional($visit->visited_at)->toDateString())
            ->keys()
            ->merge($leads->groupBy(fn (Inquiry $lead) => optional($lead->created_at)->toDateString())->keys())
            ->filter()
            ->unique()
            ->sort();

        foreach ($allDates as $date) {
            $dateTraffic = $visits->filter(fn (UtmVisit $visit) => optional($visit->visited_at)->toDateString() === $date)->count();
            $dateLeads = $leads->filter(fn (Inquiry $lead) => optional($lead->created_at)->toDateString() === $date)->count();
            $dateRows->push([
                'date' => $date,
                'traffic' => $dateTraffic,
                'leads' => $dateLeads,
                'conversion_rate' => $dateTraffic > 0 ? round(($dateLeads / $dateTraffic) * 100, 2) : 0,
            ]);
        }

        return [
            'filters' => $filters,
            'summary' => [
                'campaigns' => $campaigns->count(),
                'traffic' => $visits->count(),
                'unique_visits' => $visits->pluck('session_key')->filter()->unique()->count(),
                'leads' => $leads->count(),
                'conversion_rate' => $visits->count() > 0 ? round(($leads->count() / $visits->count()) * 100, 2) : 0,
                'top_source' => $sourceRows->sortByDesc('leads')->first(),
                'top_medium' => $mediumRows->sortByDesc('leads')->first(),
                'top_campaign' => $campaignPerformance->first(),
                'top_landing_page' => $landingRows->sortByDesc('leads')->first(),
            ],
            'campaignPerformance' => $campaignPerformance,
            'sourceRows' => $sourceRows,
            'mediumRows' => $mediumRows,
            'landingRows' => $landingRows,
            'sellerRows' => $sellerRows,
            'dateRows' => $dateRows,
            'statuses' => CrmStatus::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'campaigns' => UtmCampaign::query()->latest()->get(),
            'owners' => User::query()->where('is_active', true)->orderBy('name')->get(),
        ];
    }

    protected function groupBreakdown(Collection $visits, Collection $leads, string $field): Collection
    {
        $visitRows = $visits->groupBy($field)->map(fn (Collection $items, $value) => [
            'label' => $value ?: '—',
            'traffic' => $items->count(),
            'leads' => 0,
        ]);

        $leadRows = $leads->groupBy($field)->map(fn (Collection $items, $value) => [
            'label' => $value ?: '—',
            'traffic' => 0,
            'leads' => $items->count(),
        ]);

        return $visitRows->mergeRecursive($leadRows)->map(function ($row) {
            $traffic = array_sum((array) ($row['traffic'] ?? 0));
            $leads = array_sum((array) ($row['leads'] ?? 0));

            return [
                'label' => is_array($row['label']) ? ($row['label'][0] ?? '—') : $row['label'],
                'traffic' => $traffic,
                'leads' => $leads,
                'conversion_rate' => $traffic > 0 ? round(($leads / $traffic) * 100, 2) : 0,
            ];
        })->sortByDesc('leads')->values();
    }
}
