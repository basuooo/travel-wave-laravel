<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\LeadForm;
use App\Models\MarketingLandingPage;
use App\Models\TrackingIntegration;
use App\Support\MarketingLandingPageAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketingLandingPageController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        $items = MarketingLandingPage::query()
            ->with('leadForm')
            ->latest()
            ->paginate(15);

        $items->getCollection()->transform(function (MarketingLandingPage $item) {
            $item->setAttribute('analytics', MarketingLandingPageAnalytics::statsFor($item));

            return $item;
        });

        return view('admin.marketing-landing-pages.index', [
            'items' => $items,
            'summary' => MarketingLandingPageAnalytics::summary(),
            'topPages' => MarketingLandingPageAnalytics::topPages(),
        ]);
    }

    public function create()
    {
        return view('admin.marketing-landing-pages.form', $this->formViewData(new MarketingLandingPage([
            'status' => MarketingLandingPage::STATUS_DRAFT,
            'sections' => $this->defaultSections(),
        ]), false));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $page = MarketingLandingPage::create($data);

        return redirect()->route('admin.marketing-landing-pages.edit', $page)
            ->with('success', __('admin.marketing_landing_saved'));
    }

    public function show(MarketingLandingPage $marketing_landing_page)
    {
        return redirect()->route('admin.marketing-landing-pages.edit', $marketing_landing_page);
    }

    public function edit(MarketingLandingPage $marketing_landing_page)
    {
        return view('admin.marketing-landing-pages.form', $this->formViewData($marketing_landing_page, true));
    }

    public function update(Request $request, MarketingLandingPage $marketing_landing_page)
    {
        $marketing_landing_page->update($this->validatedData($request, $marketing_landing_page->id));

        return redirect()->route('admin.marketing-landing-pages.edit', $marketing_landing_page)
            ->with('success', __('admin.marketing_landing_updated'));
    }

    public function destroy(MarketingLandingPage $marketing_landing_page)
    {
        $marketing_landing_page->delete();

        return redirect()->route('admin.marketing-landing-pages.index')
            ->with('success', __('admin.marketing_landing_deleted'));
    }

    public function duplicate(MarketingLandingPage $marketing_landing_page)
    {
        $copy = $marketing_landing_page->replicate();
        $copy->internal_name = $marketing_landing_page->internal_name . ' Copy';
        $copy->slug = $this->uniqueSlug($marketing_landing_page->slug . '-copy');
        $copy->status = MarketingLandingPage::STATUS_DRAFT;
        $copy->save();

        return redirect()->route('admin.marketing-landing-pages.edit', $copy)
            ->with('success', __('admin.marketing_landing_duplicated'));
    }

    protected function formViewData(MarketingLandingPage $item, bool $isEdit): array
    {
        $sections = array_replace_recursive($this->defaultSections(), $item->sections ?? []);

        return [
            'item' => $item,
            'isEdit' => $isEdit,
            'sections' => $sections,
            'statuses' => [
                MarketingLandingPage::STATUS_DRAFT => __('admin.status_draft'),
                MarketingLandingPage::STATUS_PUBLISHED => __('admin.status_published'),
                MarketingLandingPage::STATUS_ARCHIVED => __('admin.status_archived'),
            ],
            'platforms' => [
                'meta_ads' => 'Meta Ads',
                'google_ads' => 'Google Ads',
                'tiktok_ads' => 'TikTok Ads',
                'snapchat_ads' => 'Snapchat Ads',
                'other' => 'Other',
            ],
            'forms' => LeadForm::query()->where('is_active', true)->orderBy('name')->get(),
            'trackingIntegrations' => TrackingIntegration::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'analytics' => $item->exists ? MarketingLandingPageAnalytics::statsFor($item) : null,
        ];
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        $validated = $request->validate([
            'internal_name' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('marketing_landing_pages', 'slug')->ignore($id)],
            'campaign_name' => ['nullable', 'string', 'max:255'],
            'ad_platform' => ['nullable', 'string', 'max:100'],
            'campaign_type' => ['nullable', 'string', 'max:100'],
            'traffic_source' => ['nullable', 'string', 'max:255'],
            'target_audience_note' => ['nullable', 'string'],
            'status' => ['required', Rule::in([
                MarketingLandingPage::STATUS_DRAFT,
                MarketingLandingPage::STATUS_PUBLISHED,
                MarketingLandingPage::STATUS_ARCHIVED,
            ])],
            'assigned_lead_form_id' => ['nullable', 'exists:lead_forms,id'],
            'tracking_integration_ids' => ['array'],
            'tracking_integration_ids.*' => ['integer', 'exists:tracking_integrations,id'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_description_en' => ['nullable', 'string'],
            'seo_description_ar' => ['nullable', 'string'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
            'final_url' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string'],
            'hero_background_image' => ['nullable', 'image'],
            'testimonial_background_image' => ['nullable', 'image'],
            'cta_background_image' => ['nullable', 'image'],
        ]);

        $existingSections = $request->input('_existing_sections', []);
        $current = $id ? MarketingLandingPage::query()->find($id) : null;
        $currentSections = $current?->sections ?? [];
        $mergedExisting = array_replace_recursive($currentSections, is_array($existingSections) ? $existingSections : []);
        $sections = $this->mapSections($request, $mergedExisting);

        return [
            'internal_name' => $validated['internal_name'],
            'title_en' => $validated['title_en'],
            'title_ar' => $validated['title_ar'],
            'slug' => $validated['slug'],
            'campaign_name' => $validated['campaign_name'] ?? null,
            'ad_platform' => $validated['ad_platform'] ?? null,
            'campaign_type' => $validated['campaign_type'] ?? null,
            'traffic_source' => $validated['traffic_source'] ?? null,
            'target_audience_note' => $validated['target_audience_note'] ?? null,
            'status' => $validated['status'],
            'assigned_lead_form_id' => $validated['assigned_lead_form_id'] ?? null,
            'tracking_integration_ids' => collect($request->input('tracking_integration_ids', []))->map(fn ($id) => (int) $id)->filter()->values()->all(),
            'sections' => $sections,
            'seo_title_en' => $validated['seo_title_en'] ?? null,
            'seo_title_ar' => $validated['seo_title_ar'] ?? null,
            'seo_description_en' => $validated['seo_description_en'] ?? null,
            'seo_description_ar' => $validated['seo_description_ar'] ?? null,
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
            'utm_content' => $validated['utm_content'] ?? null,
            'utm_term' => $validated['utm_term'] ?? null,
            'final_url' => $validated['final_url'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];
    }

    protected function mapSections(Request $request, array $existingSections = []): array
    {
        $heroImage = $this->uploadFile($request, 'hero_background_image', 'marketing/landing-pages', data_get($existingSections, 'hero.background_image'));
        $testimonialImage = $this->uploadFile($request, 'testimonial_background_image', 'marketing/landing-pages', data_get($existingSections, 'testimonials.background_image'));
        $ctaImage = $this->uploadFile($request, 'cta_background_image', 'marketing/landing-pages', data_get($existingSections, 'cta.background_image'));

        return [
            'hero' => [
                'enabled' => $request->boolean('sections.hero.enabled', true),
                'eyebrow_en' => $request->input('sections.hero.eyebrow_en'),
                'eyebrow_ar' => $request->input('sections.hero.eyebrow_ar'),
                'title_en' => $request->input('sections.hero.title_en'),
                'title_ar' => $request->input('sections.hero.title_ar'),
                'subtitle_en' => $request->input('sections.hero.subtitle_en'),
                'subtitle_ar' => $request->input('sections.hero.subtitle_ar'),
                'background_image' => $heroImage,
                'primary_button_text_en' => $request->input('sections.hero.primary_button_text_en'),
                'primary_button_text_ar' => $request->input('sections.hero.primary_button_text_ar'),
                'primary_button_url' => $request->input('sections.hero.primary_button_url'),
                'secondary_button_text_en' => $request->input('sections.hero.secondary_button_text_en'),
                'secondary_button_text_ar' => $request->input('sections.hero.secondary_button_text_ar'),
                'secondary_button_url' => $request->input('sections.hero.secondary_button_url'),
            ],
            'benefits' => [
                'enabled' => $request->boolean('sections.benefits.enabled', true),
                'title_en' => $request->input('sections.benefits.title_en'),
                'title_ar' => $request->input('sections.benefits.title_ar'),
                'subtitle_en' => $request->input('sections.benefits.subtitle_en'),
                'subtitle_ar' => $request->input('sections.benefits.subtitle_ar'),
                'items' => $this->mapSimpleItems($request->input('sections.benefits.items', []), ['title', 'text', 'meta']),
            ],
            'quick_info' => [
                'enabled' => $request->boolean('sections.quick_info.enabled', true),
                'title_en' => $request->input('sections.quick_info.title_en'),
                'title_ar' => $request->input('sections.quick_info.title_ar'),
                'subtitle_en' => $request->input('sections.quick_info.subtitle_en'),
                'subtitle_ar' => $request->input('sections.quick_info.subtitle_ar'),
                'items' => $this->mapSimpleItems($request->input('sections.quick_info.items', []), ['label', 'value']),
            ],
            'cta' => [
                'enabled' => $request->boolean('sections.cta.enabled', true),
                'eyebrow_en' => $request->input('sections.cta.eyebrow_en'),
                'eyebrow_ar' => $request->input('sections.cta.eyebrow_ar'),
                'title_en' => $request->input('sections.cta.title_en'),
                'title_ar' => $request->input('sections.cta.title_ar'),
                'description_en' => $request->input('sections.cta.description_en'),
                'description_ar' => $request->input('sections.cta.description_ar'),
                'background_image' => $ctaImage,
                'primary_button_text_en' => $request->input('sections.cta.primary_button_text_en'),
                'primary_button_text_ar' => $request->input('sections.cta.primary_button_text_ar'),
                'primary_button_url' => $request->input('sections.cta.primary_button_url'),
                'secondary_button_text_en' => $request->input('sections.cta.secondary_button_text_en'),
                'secondary_button_text_ar' => $request->input('sections.cta.secondary_button_text_ar'),
                'secondary_button_url' => $request->input('sections.cta.secondary_button_url'),
            ],
            'testimonials' => [
                'enabled' => $request->boolean('sections.testimonials.enabled', true),
                'title_en' => $request->input('sections.testimonials.title_en'),
                'title_ar' => $request->input('sections.testimonials.title_ar'),
                'subtitle_en' => $request->input('sections.testimonials.subtitle_en'),
                'subtitle_ar' => $request->input('sections.testimonials.subtitle_ar'),
                'background_image' => $testimonialImage,
                'items' => $this->mapSimpleItems($request->input('sections.testimonials.items', []), ['quote', 'author', 'role']),
            ],
            'faq' => [
                'enabled' => $request->boolean('sections.faq.enabled', true),
                'title_en' => $request->input('sections.faq.title_en'),
                'title_ar' => $request->input('sections.faq.title_ar'),
                'subtitle_en' => $request->input('sections.faq.subtitle_en'),
                'subtitle_ar' => $request->input('sections.faq.subtitle_ar'),
                'items' => $this->mapSimpleItems($request->input('sections.faq.items', []), ['question', 'answer']),
            ],
            'form' => [
                'enabled' => $request->boolean('sections.form.enabled', true),
                'title_en' => $request->input('sections.form.title_en'),
                'title_ar' => $request->input('sections.form.title_ar'),
                'subtitle_en' => $request->input('sections.form.subtitle_en'),
                'subtitle_ar' => $request->input('sections.form.subtitle_ar'),
            ],
        ];
    }

    protected function mapSimpleItems(array $items, array $fields): array
    {
        return collect($items)->map(function (array $item, int $index) use ($fields) {
            $row = [
                'sort_order' => (int) ($item['sort_order'] ?? ($index + 1)),
                'is_active' => !empty($item['is_active']),
            ];

            foreach ($fields as $field) {
                $row[$field . '_en'] = trim((string) ($item[$field . '_en'] ?? ''));
                $row[$field . '_ar'] = trim((string) ($item[$field . '_ar'] ?? ''));
            }

            $content = collect($row)->except(['sort_order', 'is_active'])->filter();

            return $content->isEmpty() ? null : $row;
        })->filter()->sortBy('sort_order')->values()->all();
    }

    protected function defaultSections(): array
    {
        return [
            'hero' => ['enabled' => true],
            'benefits' => ['enabled' => true, 'items' => []],
            'quick_info' => ['enabled' => true, 'items' => []],
            'cta' => ['enabled' => true],
            'testimonials' => ['enabled' => true, 'items' => []],
            'faq' => ['enabled' => true, 'items' => []],
            'form' => ['enabled' => true],
        ];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $candidate = $slug;
        $counter = 2;

        while (MarketingLandingPage::query()->where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
