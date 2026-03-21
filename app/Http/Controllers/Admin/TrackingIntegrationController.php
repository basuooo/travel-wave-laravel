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
            'placementOptions' => TrackingManager::placementOptions(),
            'visibilityModes' => TrackingManager::visibilityModeOptions(),
            'visibilityTargets' => TrackingManager::visibilityTargetOptions(),
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

        if (in_array($type, [TrackingIntegration::TYPE_GTM, TrackingIntegration::TYPE_GA4, TrackingIntegration::TYPE_META_PIXEL], true) && $code === '') {
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
