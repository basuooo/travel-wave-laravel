<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UtmCampaign;
use App\Models\User;
use App\Support\AuditLogService;
use App\Support\MarketingCampaignAnalyticsService;
use App\Support\UtmBuilderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MarketingCampaignController extends Controller
{
    public function index(Request $request, MarketingCampaignAnalyticsService $analyticsService)
    {
        return view('admin.marketing.campaigns.index', $analyticsService->indexData($request));
    }

    public function create()
    {
        return view('admin.marketing.campaigns.form', [
            'campaign' => new UtmCampaign([
                'status' => UtmCampaign::STATUS_DRAFT,
                'start_date' => now()->toDateString(),
            ]),
            'isEdit' => false,
            'owners' => $this->owners(),
            'statuses' => UtmCampaign::statusOptions(),
            'generatedUrl' => null,
        ]);
    }

    public function store(Request $request, UtmBuilderService $builderService, AuditLogService $auditLogService)
    {
        $payload = $this->validatedPayload($request, $builderService);

        $campaign = UtmCampaign::query()->create($payload + [
            'created_by' => $request->user()?->id,
        ]);

        $auditLogService->log($request->user(), 'marketing_campaigns', 'created', $campaign, [
            'title' => __('admin.marketing_campaign_created'),
            'description' => $campaign->display_name,
            'new_values' => $this->auditValues($campaign->fresh('owner')),
            'changed_fields' => array_keys($this->auditValues($campaign->fresh('owner'))),
        ]);

        return redirect()
            ->route('admin.marketing-campaigns.index')
            ->with('success', __('admin.marketing_campaign_created'));
    }

    public function show(UtmCampaign $marketingCampaign, MarketingCampaignAnalyticsService $analyticsService)
    {
        return view('admin.marketing.campaigns.show', $analyticsService->showData($marketingCampaign));
    }

    public function edit(UtmCampaign $marketingCampaign, UtmBuilderService $builderService)
    {
        return view('admin.marketing.campaigns.form', [
            'campaign' => $marketingCampaign,
            'isEdit' => true,
            'owners' => $this->owners(),
            'statuses' => UtmCampaign::statusOptions(),
            'generatedUrl' => $builderService->buildUrl((string) $marketingCampaign->base_url, [
                'utm_source' => $marketingCampaign->utm_source,
                'utm_medium' => $marketingCampaign->utm_medium,
                'utm_campaign' => $marketingCampaign->utm_campaign,
                'utm_id' => $marketingCampaign->utm_id,
                'utm_term' => $marketingCampaign->utm_term,
                'utm_content' => $marketingCampaign->utm_content,
            ]),
        ]);
    }

    public function update(Request $request, UtmCampaign $marketingCampaign, UtmBuilderService $builderService, AuditLogService $auditLogService)
    {
        $before = $this->auditValues($marketingCampaign->loadMissing('owner'));
        $payload = $this->validatedPayload($request, $builderService, $marketingCampaign->id);
        $marketingCampaign->update($payload);

        $afterModel = $marketingCampaign->fresh('owner');
        $after = $this->auditValues($afterModel);
        $diff = $auditLogService->diff($before, $after);

        if ($diff['changed_fields'] !== []) {
            $auditLogService->log($request->user(), 'marketing_campaigns', 'updated', $afterModel, [
                'title' => __('admin.marketing_campaign_updated'),
                'description' => $afterModel->display_name,
                'old_values' => $diff['old_values'],
                'new_values' => $diff['new_values'],
                'changed_fields' => $diff['changed_fields'],
            ]);
        }

        return redirect()
            ->route('admin.marketing-campaigns.show', $marketingCampaign)
            ->with('success', __('admin.marketing_campaign_updated'));
    }

    protected function validatedPayload(Request $request, UtmBuilderService $builderService, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'campaign_code' => ['nullable', 'string', 'max:100', Rule::unique('utm_campaigns', 'campaign_code')->ignore($ignoreId)],
            'platform' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'campaign_type' => ['nullable', 'string', 'max:255'],
            'objective' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(UtmCampaign::statusOptions()))],
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'base_url' => ['required', 'url', 'max:2048'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_id' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'external_campaign_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $payload = [
            'display_name' => trim((string) $data['display_name']),
            'campaign_code' => $this->nullableString($data['campaign_code'] ?? null),
            'platform' => $this->nullableString($data['platform'] ?? null),
            'utm_medium' => $this->nullableString($data['utm_medium'] ?? null),
            'campaign_type' => $this->nullableString($data['campaign_type'] ?? null),
            'objective' => $this->nullableString($data['objective'] ?? null),
            'status' => $data['status'],
            'owner_user_id' => filled($data['owner_user_id'] ?? null) ? (int) $data['owner_user_id'] : null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'budget' => filled($data['budget'] ?? null) ? round((float) $data['budget'], 2) : null,
            'utm_source' => $this->nullableString($data['utm_source'] ?? null),
            'utm_campaign' => $this->nullableString($data['utm_campaign'] ?? null),
            'utm_id' => $this->nullableString($data['utm_id'] ?? null),
            'utm_term' => $this->nullableString($data['utm_term'] ?? null),
            'utm_content' => $this->nullableString($data['utm_content'] ?? null),
            'external_campaign_id' => $this->nullableString($data['external_campaign_id'] ?? null),
            'notes' => $this->nullableString($data['notes'] ?? null),
        ];

        $baseUrl = $this->nullableString($data['base_url'] ?? null);
        $payload['base_url'] = $baseUrl;
        $payload['generated_url'] = $baseUrl
            ? $builderService->buildUrl($baseUrl, [
                'utm_source' => $payload['utm_source'],
                'utm_medium' => $payload['utm_medium'],
                'utm_campaign' => $payload['utm_campaign'],
                'utm_id' => $payload['utm_id'],
                'utm_term' => $payload['utm_term'],
                'utm_content' => $payload['utm_content'],
            ])
            : null;

        return $payload;
    }

    protected function owners()
    {
        return User::query()->where('is_active', true)->orderBy('name')->get();
    }

    protected function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    protected function auditValues(UtmCampaign $campaign): array
    {
        return [
            'display_name' => $campaign->display_name,
            'campaign_code' => $campaign->campaign_code,
            'platform' => $campaign->platform,
            'utm_medium' => $campaign->utm_medium,
            'campaign_type' => $campaign->campaign_type,
            'status' => $campaign->localizedStatus(),
            'owner_user_id' => $campaign->owner?->name,
            'budget' => $campaign->budget,
            'start_date' => optional($campaign->start_date)?->toDateString(),
            'end_date' => optional($campaign->end_date)?->toDateString(),
            'utm_source' => $campaign->utm_source,
            'utm_campaign' => $campaign->utm_campaign,
        ];
    }
}
