<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmStatus;
use App\Models\UtmCampaign;
use App\Models\User;
use App\Support\UtmAnalyticsService;
use App\Support\UtmBuilderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UtmController extends Controller
{
    public function dashboard(Request $request, UtmAnalyticsService $analyticsService)
    {
        return view('admin.utm.dashboard', $analyticsService->build($request));
    }

    public function index()
    {
        $items = UtmCampaign::query()
            ->with(['owner', 'creator'])
            ->withCount(['visits', 'inquiries'])
            ->latest()
            ->paginate(20);

        return view('admin.utm.index', [
            'items' => $items,
        ]);
    }

    public function create(Request $request, UtmBuilderService $builderService)
    {
        $campaign = new UtmCampaign([
            'status' => UtmCampaign::STATUS_ACTIVE,
            'base_url' => $request->query('base_url'),
        ]);

        return view('admin.utm.form', [
            'item' => $campaign,
            'isEdit' => false,
            'generatedUrl' => filled($campaign->base_url) ? $builderService->buildUrl($campaign->base_url, []) : null,
            'owners' => $this->owners(),
            'statuses' => $this->statuses(),
            'crmStatuses' => CrmStatus::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, UtmBuilderService $builderService)
    {
        $payload = $this->validatedPayload($request, $builderService);

        if ($this->duplicateGeneratedUrlExists($payload['generated_url'])) {
            return back()->withInput()->withErrors([
                'base_url' => __('admin.utm_duplicate_generated_url'),
            ]);
        }

        UtmCampaign::query()->create($payload + [
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.utm.index')
            ->with('success', __('admin.utm_campaign_saved'));
    }

    public function edit(UtmCampaign $campaign, UtmBuilderService $builderService)
    {
        return view('admin.utm.form', [
            'item' => $campaign,
            'isEdit' => true,
            'generatedUrl' => $builderService->buildUrl($campaign->base_url, [
                'utm_source' => $campaign->utm_source,
                'utm_medium' => $campaign->utm_medium,
                'utm_campaign' => $campaign->utm_campaign,
                'utm_id' => $campaign->utm_id,
                'utm_term' => $campaign->utm_term,
                'utm_content' => $campaign->utm_content,
            ]),
            'owners' => $this->owners(),
            'statuses' => $this->statuses(),
            'crmStatuses' => CrmStatus::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, UtmCampaign $campaign, UtmBuilderService $builderService)
    {
        $payload = $this->validatedPayload($request, $builderService, $campaign->id);

        if ($this->duplicateGeneratedUrlExists($payload['generated_url'], $campaign->id)) {
            return back()->withInput()->withErrors([
                'base_url' => __('admin.utm_duplicate_generated_url'),
            ]);
        }

        $campaign->update($payload);

        return redirect()->route('admin.utm.index')
            ->with('success', __('admin.utm_campaign_updated'));
    }

    protected function validatedPayload(Request $request, UtmBuilderService $builderService, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'base_url' => ['required', 'url', 'max:2048'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_id' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:255'],
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ]);

        return $builderService->validatedPayload($data);
    }

    protected function duplicateGeneratedUrlExists(string $generatedUrl, ?int $ignoreId = null): bool
    {
        return UtmCampaign::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('generated_url', $generatedUrl)
            ->exists();
    }

    protected function owners()
    {
        return User::query()->where('is_active', true)->orderBy('name')->get();
    }

    protected function statuses(): array
    {
        return [
            UtmCampaign::STATUS_ACTIVE => __('admin.utm_status_active'),
            UtmCampaign::STATUS_PAUSED => __('admin.utm_status_paused'),
            UtmCampaign::STATUS_ENDED => __('admin.utm_status_ended'),
        ];
    }
}
