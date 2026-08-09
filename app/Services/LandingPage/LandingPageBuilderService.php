<?php

namespace App\Services\LandingPage;

use App\Models\Destination;
use App\Models\LandingPage\Brand;
use App\Models\LandingPage\LpActivityLog;
use App\Models\LandingPage\LpLandingPage;
use App\Models\LandingPage\LpPageVersion;
use App\Models\LandingPage\LpSection;
use App\Models\LandingPage\LpTemplate;
use App\Models\VisaCountry;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LandingPageBuilderService
{
    /**
     * Create a new Landing Page from Scratch
     */
    public function createFromScratch(array $data, ?int $userId = null): LpLandingPage
    {
        $data['slug'] = $this->resolveUniqueSlug($data['slug'] ?? Str::slug($data['internal_name'] ?? 'landing-page'));
        $data['status'] = $data['status'] ?? LpLandingPage::STATUS_DRAFT;
        $data['is_active'] = $data['is_active'] ?? true;
        $data['structure'] = $data['structure'] ?? $this->defaultCanvasStructure();
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        $page = LpLandingPage::create($data);

        $this->createVersionSnapshot($page, 'Initial Scratch Draft', $userId);
        $this->logActivity($page, 'created_from_scratch', $userId, null, $page->toArray());

        return $page;
    }

    /**
     * Create a new Landing Page CLONE from an existing Template
     */
    public function createFromTemplate(LpTemplate $template, array $overrideData, ?int $userId = null): LpLandingPage
    {
        $data = array_merge([
            'internal_name' => $overrideData['internal_name'] ?? ($template->name_en . ' Copy'),
            'title_en' => $overrideData['title_en'] ?? $template->name_en,
            'title_ar' => $overrideData['title_ar'] ?? $template->name_ar,
            'slug' => $this->resolveUniqueSlug($overrideData['slug'] ?? ($template->slug . '-page')),
            'brand_id' => $overrideData['brand_id'] ?? $template->brand_id,
            'status' => LpLandingPage::STATUS_DRAFT,
            'is_active' => true,
            'structure' => $template->structure ?? $this->defaultCanvasStructure(),
            'created_by' => $userId,
            'updated_by' => $userId,
        ], $overrideData);

        $page = LpLandingPage::create($data);

        $this->createVersionSnapshot($page, 'Cloned from Template: ' . $template->name_en, $userId);
        $this->logActivity($page, 'created_from_template', $userId, ['template_id' => $template->id], $page->toArray());

        return $page;
    }

    /**
     * Save an existing Landing Page as a new Template
     */
    public function savePageAsTemplate(LpLandingPage $page, array $templateData, ?int $userId = null): LpTemplate
    {
        $data = [
            'brand_id' => $templateData['brand_id'] ?? $page->brand_id,
            'template_category_id' => $templateData['template_category_id'] ?? null,
            'name_en' => $templateData['name_en'] ?? ($page->internal_name . ' Template'),
            'name_ar' => $templateData['name_ar'] ?? ($page->title_ar ?: $page->internal_name),
            'description_en' => $templateData['description_en'] ?? null,
            'description_ar' => $templateData['description_ar'] ?? null,
            'slug' => $this->resolveUniqueTemplateSlug(Str::slug($templateData['name_en'] ?? $page->internal_name) . '-template'),
            'preview_image' => $templateData['preview_image'] ?? $page->og_image,
            'structure' => $page->structure,
            'settings' => [
                'header_mode' => $page->header_mode,
                'footer_mode' => $page->footer_mode,
                'seo_title_en' => $page->seo_title_en,
                'seo_title_ar' => $page->seo_title_ar,
            ],
            'is_active' => true,
            'is_global' => $templateData['is_global'] ?? true,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        $template = LpTemplate::create($data);
        $this->logActivity($page, 'saved_as_template', $userId, null, ['template_id' => $template->id]);

        return $template;
    }

    /**
     * Create a Version Snapshot for history
     */
    public function createVersionSnapshot(LpLandingPage $page, string $label = 'Manual Save', ?int $userId = null): LpPageVersion
    {
        $nextVersion = ((int) $page->versions()->max('version_number')) + 1;

        return LpPageVersion::create([
            'landing_page_id' => $page->id,
            'version_number' => $nextVersion,
            'label' => $label,
            'structure' => $page->structure,
            'settings' => [
                'status' => $page->status,
                'is_active' => $page->is_active,
                'header_mode' => $page->header_mode,
                'footer_mode' => $page->footer_mode,
                'seo_title_en' => $page->seo_title_en,
                'seo_title_ar' => $page->seo_title_ar,
            ],
            'created_by' => $userId,
        ]);
    }

    /**
     * Restore a Page Version
     */
    public function restoreVersion(LpLandingPage $page, LpPageVersion $version, ?int $userId = null): LpLandingPage
    {
        $before = $page->toArray();

        // Create a snapshot of current state before restoring
        $this->createVersionSnapshot($page, 'Pre-restore snapshot before v' . $version->version_number, $userId);

        $page->update([
            'structure' => $version->structure,
            'updated_by' => $userId,
        ]);

        $this->logActivity($page, 'version_restored', $userId, $before, $page->toArray());

        return $page;
    }

    /**
     * Dynamic Data Binding Resolver (e.g. Visa Countries, Destinations)
     */
    public function resolveDynamicBinding(string $sourceType, mixed $sourceId): array
    {
        switch ($sourceType) {
            case 'visa_country':
                $country = VisaCountry::query()->find($sourceId);
                if (! $country) return [];

                return [
                    'title_en' => $country->name_en,
                    'title_ar' => $country->name_ar,
                    'description_en' => $country->short_description_en ?? $country->meta_description_en,
                    'description_ar' => $country->short_description_ar ?? $country->meta_description_ar,
                    'featured_image' => asset('storage/' . ($country->hero_image ?: $country->flag_image)),
                    'flag_image' => asset('storage/' . $country->flag_image),
                    'price' => $country->price ?? null,
                    'currency' => 'SAR',
                    'requirements' => $country->requirements ?? [],
                ];

            case 'destination':
                $dest = Destination::query()->find($sourceId);
                if (! $dest) return [];

                return [
                    'title_en' => $dest->title_en,
                    'title_ar' => $dest->title_ar,
                    'description_en' => $dest->description_en,
                    'description_ar' => $dest->description_ar,
                    'featured_image' => asset('storage/' . ($dest->hero_image ?: $dest->featured_image)),
                    'price' => $dest->price ?? null,
                    'currency' => 'SAR',
                ];

            default:
                return [];
        }
    }

    /**
     * Export Landing Page Package to JSON
     */
    public function exportPackage(LpLandingPage $page): array
    {
        return [
            'generator' => 'TravelWave LP Builder v2.0',
            'exported_at' => now()->toIso8601String(),
            'page' => [
                'internal_name' => $page->internal_name,
                'title_en' => $page->title_en,
                'title_ar' => $page->title_ar,
                'slug' => $page->slug,
                'header_mode' => $page->header_mode,
                'footer_mode' => $page->footer_mode,
                'seo_title_en' => $page->seo_title_en,
                'seo_title_ar' => $page->seo_title_ar,
                'seo_description_en' => $page->seo_description_en,
                'seo_description_ar' => $page->seo_description_ar,
                'custom_css' => $page->custom_css,
                'custom_js' => $page->custom_js,
                'structure' => $page->structure,
            ],
        ];
    }

    /**
     * Import Package and Resolve Slug Conflicts safely
     */
    public function importPackage(array $package, string $mode = 'create_new', ?int $userId = null): LpLandingPage
    {
        $pageData = $package['page'] ?? [];
        $baseSlug = Str::slug($pageData['slug'] ?? $pageData['internal_name'] ?? 'imported-page');
        $slug = $this->resolveUniqueSlug($baseSlug);

        $data = [
            'internal_name' => ($pageData['internal_name'] ?? 'Imported Page') . ' (Imported)',
            'title_en' => $pageData['title_en'] ?? null,
            'title_ar' => $pageData['title_ar'] ?? null,
            'slug' => $slug,
            'header_mode' => $pageData['header_mode'] ?? 'website',
            'footer_mode' => $pageData['footer_mode'] ?? 'website',
            'seo_title_en' => $pageData['seo_title_en'] ?? null,
            'seo_title_ar' => $pageData['seo_title_ar'] ?? null,
            'seo_description_en' => $pageData['seo_description_en'] ?? null,
            'seo_description_ar' => $pageData['seo_description_ar'] ?? null,
            'custom_css' => $pageData['custom_css'] ?? null,
            'custom_js' => $pageData['custom_js'] ?? null,
            'structure' => $pageData['structure'] ?? $this->defaultCanvasStructure(),
            'status' => LpLandingPage::STATUS_DRAFT,
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
        ];

        $page = LpLandingPage::create($data);
        $this->createVersionSnapshot($page, 'Imported Package', $userId);
        $this->logActivity($page, 'imported_package', $userId, null, $page->toArray());

        return $page;
    }

    /**
     * Resolve Unique Slug with counter (e.g. france-visa -> france-visa-2)
     */
    public function resolveUniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $candidate = $slug;
        $counter = 2;

        while (LpLandingPage::withTrashed()->where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    public function resolveUniqueTemplateSlug(string $base): string
    {
        $slug = Str::slug($base);
        $candidate = $slug;
        $counter = 2;

        while (LpTemplate::where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    public function defaultCanvasStructure(): array
    {
        return [
            'version' => '2.0',
            'canvas' => [
                'background' => '#ffffff',
                'padding' => '0px',
            ],
            'elements' => [
                [
                    'id' => 'hero_section_1',
                    'type' => 'hero_section',
                    'category' => 'hero',
                    'content' => [
                        'eyebrow_en' => 'Special Travel Offer',
                        'eyebrow_ar' => 'عرض سياحي خاص',
                        'title_en' => 'Discover Your Next Destination',
                        'title_ar' => 'اكتشف وجهتك السياحية القادمة',
                        'subtitle_en' => 'Book your flights, hotel, and visa with ease and top quality service.',
                        'subtitle_ar' => 'احجز رحلتك، الفندق، والتأشيرة بكل سهولة وأعلى مستوى من الخدمة.',
                        'cta_text_en' => 'Book Now',
                        'cta_text_ar' => 'احجز الآن',
                        'cta_link' => '#lead_form_section',
                    ],
                    'style' => [
                        'background_color' => '#1e3a8a',
                        'text_color' => '#ffffff',
                        'padding_top' => '60px',
                        'padding_bottom' => '60px',
                    ],
                    'responsive' => [
                        'hide_desktop' => false,
                        'hide_tablet' => false,
                        'hide_mobile' => false,
                    ],
                ],
            ],
        ];
    }

    protected function logActivity(LpLandingPage $page, string $action, ?int $userId, ?array $before, ?array $after): void
    {
        LpActivityLog::create([
            'user_id' => $userId,
            'landing_page_id' => $page->id,
            'action' => $action,
            'entity_type' => 'LpLandingPage',
            'entity_id' => $page->id,
            'before_state' => $before,
            'after_state' => $after,
            'ip_address' => request()->ip(),
        ]);
    }
}
