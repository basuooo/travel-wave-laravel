<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingIntegration;
use App\Support\TrackingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TrackingIntegrationController extends Controller
{
    public function index()
    {
        return view('admin.tracking-integrations.index', [
            'items' => TrackingIntegration::query()->orderBy('sort_order')->latest('id')->paginate(20),
            'typeLabels' => TrackingManager::integrationTypeOptions(),
            'placementLabels' => TrackingManager::placementOptions(),
        ]);
    }

    public function create()
    {
        return view('admin.tracking-integrations.form', $this->formViewData(new TrackingIntegration([
            'integration_type' => TrackingIntegration::TYPE_GTM,
            'placement' => 'standard',
            'visibility_mode' => 'all',
            'sort_order' => 1,
            'is_active' => true,
        ]), false));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        TrackingIntegration::create($data);

        return redirect()->route('admin.tracking-integrations.index')
            ->with('success', __('admin.tracking_saved'));
    }

    public function edit(TrackingIntegration $tracking_integration)
    {
        return view('admin.tracking-integrations.form', $this->formViewData($tracking_integration, true));
    }

    public function update(Request $request, TrackingIntegration $tracking_integration)
    {
        $data = $this->validatedData($request, $tracking_integration->id);

        $tracking_integration->update($data);

        return redirect()->route('admin.tracking-integrations.index')
            ->with('success', __('admin.tracking_updated'));
    }

    public function destroy(TrackingIntegration $tracking_integration)
    {
        $tracking_integration->delete();

        return redirect()->route('admin.tracking-integrations.index')
            ->with('success', __('admin.tracking_deleted'));
    }

    public function duplicate(TrackingIntegration $tracking_integration)
    {
        $copy = $tracking_integration->replicate();
        $copy->name = $tracking_integration->name . ' Copy';
        $copy->slug = $this->uniqueSlug($tracking_integration->slug . '-copy');
        $copy->is_active = false;
        $copy->save();

        return redirect()->route('admin.tracking-integrations.edit', $copy)
            ->with('success', __('admin.tracking_duplicated'));
    }

    protected function formViewData(TrackingIntegration $item, bool $isEdit): array
    {
        return [
            'item' => $item,
            'isEdit' => $isEdit,
            'typeOptions' => TrackingManager::integrationTypeOptions(),
            'typeGroups' => TrackingManager::toolGroupOptions(),
            'placementOptions' => TrackingManager::placementOptions(),
            'visibilityModes' => TrackingManager::visibilityModeOptions(),
            'visibilityTargets' => TrackingManager::visibilityTargetOptions(),
            'toolFieldConfig' => $this->toolFieldConfig(),
        ];
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('tracking_integrations', 'slug')->ignore($id)],
            'integration_type' => ['required', Rule::in(array_keys(TrackingManager::integrationTypeOptions()))],
            'platform' => ['nullable', 'string', 'max:100'],
            'tracking_code' => ['nullable', 'string', 'max:255'],
            'script_code' => ['nullable', 'string'],
            'settings' => ['array'],
            'settings.conversion_label' => ['nullable', 'string', 'max:255'],
            'settings.notes' => ['nullable', 'string'],
            'placement' => ['required', Rule::in(array_keys(TrackingManager::placementOptions()))],
            'notes' => ['nullable', 'string'],
            'visibility_mode' => ['required', Rule::in(array_keys(TrackingManager::visibilityModeOptions()))],
            'visibility_targets' => ['array'],
            'visibility_targets.*' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $this->validateTrackingCode($validated);

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'integration_type' => $validated['integration_type'],
            'platform' => $validated['platform'] ?? null,
            'tracking_code' => filled($validated['tracking_code'] ?? null) ? trim($validated['tracking_code']) : null,
            'script_code' => filled($validated['script_code'] ?? null) ? trim($validated['script_code']) : null,
            'settings' => $this->normalizedSettings($request->input('settings', []), $validated['integration_type']),
            'placement' => $validated['placement'],
            'notes' => $validated['notes'] ?? null,
            'visibility_mode' => $validated['visibility_mode'],
            'visibility_targets' => collect($request->input('visibility_targets', []))->filter()->values()->all(),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    protected function validateTrackingCode(array $validated): void
    {
        $type = $validated['integration_type'];
        $code = trim((string) ($validated['tracking_code'] ?? ''));
        $script = trim((string) ($validated['script_code'] ?? ''));
        $conversionLabel = trim((string) data_get($validated, 'settings.conversion_label', ''));

        if (in_array($type, [
            TrackingIntegration::TYPE_GTM,
            TrackingIntegration::TYPE_GA4,
            TrackingIntegration::TYPE_META_PIXEL,
            TrackingIntegration::TYPE_TIKTOK_PIXEL,
            TrackingIntegration::TYPE_SNAP_PIXEL,
            TrackingIntegration::TYPE_X_PIXEL,
            TrackingIntegration::TYPE_LINKEDIN_INSIGHT,
            TrackingIntegration::TYPE_PINTEREST_TAG,
            TrackingIntegration::TYPE_GOOGLE_ADS,
            TrackingIntegration::TYPE_MICROSOFT_CLARITY,
        ], true) && $code === '') {
            throw ValidationException::withMessages([
                'tracking_code' => [__('validation.required', ['attribute' => 'tracking code'])],
            ]);
        }

        if ($type === TrackingIntegration::TYPE_CUSTOM_SCRIPT && $script === '' && $code === '') {
            throw ValidationException::withMessages([
                'script_code' => [__('validation.required', ['attribute' => 'script code'])],
            ]);
        }

        if ($type === TrackingIntegration::TYPE_GTM && $code !== '' && ! preg_match('/^GTM-[A-Z0-9]+$/i', $code)) {
            throw ValidationException::withMessages([
                'tracking_code' => [__('admin.validation_gtm')],
            ]);
        }

        if ($type === TrackingIntegration::TYPE_GA4 && $code !== '' && ! preg_match('/^G-[A-Z0-9]+$/i', $code)) {
            throw ValidationException::withMessages([
                'tracking_code' => [__('admin.validation_ga4')],
            ]);
        }

        if ($type === TrackingIntegration::TYPE_META_PIXEL && $code !== '' && ! preg_match('/^[0-9]+$/', $code)) {
            throw ValidationException::withMessages([
                'tracking_code' => [__('admin.validation_meta_pixel')],
            ]);
        }

        if (in_array($type, [
            TrackingIntegration::TYPE_TIKTOK_PIXEL,
            TrackingIntegration::TYPE_SNAP_PIXEL,
            TrackingIntegration::TYPE_X_PIXEL,
            TrackingIntegration::TYPE_LINKEDIN_INSIGHT,
            TrackingIntegration::TYPE_PINTEREST_TAG,
            TrackingIntegration::TYPE_MICROSOFT_CLARITY,
        ], true) && $code !== '' && ! preg_match('/^[A-Za-z0-9\-_]+$/', $code)) {
            throw ValidationException::withMessages([
                'tracking_code' => [__('admin.validation_tracking_generic')],
            ]);
        }

        if ($type === TrackingIntegration::TYPE_GOOGLE_ADS && $code !== '' && ! preg_match('/^(AW-)?[0-9]+$/', $code)) {
            throw ValidationException::withMessages([
                'tracking_code' => [__('admin.validation_google_ads')],
            ]);
        }

        if ($type === TrackingIntegration::TYPE_GOOGLE_ADS && $conversionLabel !== '' && ! preg_match('/^[A-Za-z0-9\-_]+$/', $conversionLabel)) {
            throw ValidationException::withMessages([
                'settings.conversion_label' => [__('admin.validation_conversion_label')],
            ]);
        }
    }

    protected function normalizedSettings(array $settings, string $type): ?array
    {
        $normalized = collect($settings)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();

        if ($type !== TrackingIntegration::TYPE_GOOGLE_ADS) {
            unset($normalized['conversion_label']);
        }

        return $normalized ?: null;
    }

    protected function toolFieldConfig(): array
    {
        return [
            TrackingIntegration::TYPE_GTM => [
                'tracking_code_label' => __('admin.gtm_container_id'),
                'tracking_code_placeholder' => 'GTM-XXXXXXX',
            ],
            TrackingIntegration::TYPE_GA4 => [
                'tracking_code_label' => __('admin.ga4_measurement_id'),
                'tracking_code_placeholder' => 'G-XXXXXXXXXX',
            ],
            TrackingIntegration::TYPE_META_PIXEL => [
                'tracking_code_label' => __('admin.meta_pixel_id'),
                'tracking_code_placeholder' => '123456789012345',
            ],
            TrackingIntegration::TYPE_TIKTOK_PIXEL => [
                'tracking_code_label' => __('admin.tiktok_pixel_id'),
                'tracking_code_placeholder' => 'C123ABC456DEF',
            ],
            TrackingIntegration::TYPE_SNAP_PIXEL => [
                'tracking_code_label' => __('admin.snap_pixel_id'),
                'tracking_code_placeholder' => '11111111-2222-3333-4444-555555555555',
            ],
            TrackingIntegration::TYPE_X_PIXEL => [
                'tracking_code_label' => __('admin.x_pixel_id'),
                'tracking_code_placeholder' => 'oel66',
            ],
            TrackingIntegration::TYPE_LINKEDIN_INSIGHT => [
                'tracking_code_label' => __('admin.linkedin_partner_id'),
                'tracking_code_placeholder' => '1234567',
            ],
            TrackingIntegration::TYPE_PINTEREST_TAG => [
                'tracking_code_label' => __('admin.pinterest_tag_id'),
                'tracking_code_placeholder' => '2612345678901',
            ],
            TrackingIntegration::TYPE_GOOGLE_ADS => [
                'tracking_code_label' => __('admin.google_ads_conversion_id'),
                'tracking_code_placeholder' => 'AW-123456789',
                'extra_fields' => [
                    'conversion_label' => [
                        'label' => __('admin.google_ads_conversion_label'),
                        'placeholder' => 'AbCdEFgHiJkLmNoP',
                    ],
                ],
            ],
            TrackingIntegration::TYPE_MICROSOFT_CLARITY => [
                'tracking_code_label' => __('admin.microsoft_clarity_project_id'),
                'tracking_code_placeholder' => 'abcd1234ef',
            ],
            TrackingIntegration::TYPE_CUSTOM_SCRIPT => [
                'tracking_code_label' => __('admin.tracking_code'),
                'tracking_code_placeholder' => __('admin.optional_code_reference'),
            ],
        ];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $candidate = $slug;
        $counter = 2;

        while (TrackingIntegration::query()->where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
