<?php

namespace App\Support;

use App\Models\CrmStatus;
use App\Models\Inquiry;
use App\Models\UtmCampaign;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MarketingCampaignAnalyticsService
{
    public function indexData(Request $request): array
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:255'],
            'medium' => ['nullable', 'string', 'max:255'],
            'campaign_type' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
        ]);

        $query = UtmCampaign::query()
            ->with(['owner', 'creator'])
            ->withCount(['visits', 'inquiries', 'customers'])
            ->latest();

        if (! empty($filters['q'])) {
            $needle = '%' . trim((string) $filters['q']) . '%';
            $query->where(function ($builder) use ($needle) {
                $builder->where('display_name', 'like', $needle)
                    ->orWhere('campaign_code', 'like', $needle)
                    ->orWhere('utm_campaign', 'like', $needle)
                    ->orWhere('utm_source', 'like', $needle)
                    ->orWhere('platform', 'like', $needle);
            });
        }

        foreach (['platform', 'utm_medium' => 'medium', 'campaign_type', 'status', 'owner_user_id'] as $column => $filterKey) {
            if (is_int($column)) {
                $column = $filterKey;
            }

            if (! empty($filters[$filterKey])) {
                $query->where($column, $filters[$filterKey]);
            }
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('start_date', '>=', $request->date('from_date'));
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('end_date', '<=', $request->date('to_date'));
        }

        $items = $query->paginate(20)->withQueryString();

        return [
            'filters' => $filters,
            'items' => $items,
            'owners' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'platforms' => UtmCampaign::query()->whereNotNull('platform')->distinct()->orderBy('platform')->pluck('platform'),
            'media' => UtmCampaign::query()->whereNotNull('utm_medium')->distinct()->orderBy('utm_medium')->pluck('utm_medium'),
            'types' => UtmCampaign::query()->whereNotNull('campaign_type')->distinct()->orderBy('campaign_type')->pluck('campaign_type'),
            'statuses' => UtmCampaign::statusOptions(),
            'summary' => $this->summary($items),
        ];
    }

    public function showData(UtmCampaign $campaign): array
    {
        $campaign->load(['owner', 'creator', 'visits', 'inquiries.assignedUser', 'inquiries.crmStatus', 'inquiries.crmCustomer', 'customers.assignedUser']);

        $leads = $campaign->inquiries;
        $customers = $campaign->customers;
        $visits = $campaign->visits;
        $statusRows = $leads->groupBy(fn (Inquiry $lead) => $lead->crmStatus?->localizedName() ?: __('admin.no_status'))
            ->map(fn (Collection $items, string $label) => ['label' => $label, 'count' => $items->count()])
            ->sortByDesc('count')
            ->values();
        $sellerRows = $leads->groupBy('assigned_user_id')
            ->map(function (Collection $items) {
                $seller = $items->first()?->assignedUser;

                return [
                    'seller' => $seller,
                    'leads' => $items->count(),
                    'customers' => $items->filter(fn (Inquiry $lead) => $lead->crmCustomer !== null)->count(),
                ];
            })
            ->filter(fn (array $row) => $row['seller'])
            ->sortByDesc('leads')
            ->values();

        return [
            'campaign' => $campaign,
            'summary' => [
                'traffic' => $visits->count(),
                'unique_visits' => $visits->pluck('session_key')->filter()->unique()->count(),
                'leads' => $leads->count(),
                'customers' => $customers->count(),
                'delayed_leads' => app(CrmDelayedLeadService::class)->annotate($leads)->where('is_delayed', true)->count(),
                'conversion_rate' => $leads->count() > 0 ? round(($customers->count() / $leads->count()) * 100, 2) : 0,
            ],
            'statusRows' => $statusRows,
            'sellerRows' => $sellerRows,
            'recentLeads' => $leads->sortByDesc('created_at')->take(10)->values(),
            'recentCustomers' => $customers->sortByDesc('converted_at')->take(10)->values(),
        ];
    }

    protected function summary(LengthAwarePaginator $items): array
    {
        $collection = $items->getCollection();
        $top = $collection->sortByDesc('inquiries_count')->first();

        return [
            'total_campaigns' => UtmCampaign::query()->count(),
            'active_campaigns' => UtmCampaign::query()->where('status', UtmCampaign::STATUS_ACTIVE)->count(),
            'campaigns_with_leads' => UtmCampaign::query()->has('inquiries')->count(),
            'top_campaign' => $top,
        ];
    }
}
