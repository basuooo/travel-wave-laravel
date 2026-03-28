<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.pages.index', [
            'pages' => Page::query()->orderByDesc('is_active')->orderBy('key')->orderBy('title_en')->get(),
        ]);
    }

    public function trash()
    {
        return view('admin.pages.trash', [
            'pages' => Page::onlyTrashed()
                ->with('deletedBy')
                ->orderByDesc('deleted_at')
                ->orderBy('title_en')
                ->get(),
        ]);
    }

    public function create()
    {
        return view('admin.pages.create', [
            'page' => new Page([
                'is_active' => false,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['key'] = filled($data['key'] ?? null)
            ? trim((string) $data['key'])
            : $this->makeUniqueKey($data['slug'] ?: $data['title_en']);
        $data['slug'] = Page::makeUniqueSlug($data['slug'] ?: $data['title_en']);
        $data['hero_image'] = $this->uploadFile($request, 'hero_image', 'pages');
        $data['sections'] = $this->pageSectionsFromRequest($request, $data['key'], []);
        $data['is_active'] = $request->boolean('is_active', false);

        $page = Page::query()->create($data);

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validatedData($request, $page);

        $data['hero_image'] = $this->uploadFile($request, 'hero_image', 'pages', $page->hero_image);
        $data['key'] = $page->isCorePage()
            ? $page->key
            : (filled($data['key'] ?? null)
                ? trim((string) $data['key'])
                : $this->makeUniqueKey($data['slug'] ?: $data['title_en'], $page->id));
        $data['slug'] = Page::makeUniqueSlug($data['slug'] ?: $data['title_en'], $page->id);
        $data['sections'] = $this->pageSectionsFromRequest($request, $data['key'], $page->sections ?? []);
        $data['is_active'] = $request->boolean('is_active', true);

        $page->update($data);

        return back()->with('success', 'Page content updated successfully.');
    }

    public function duplicate(Page $page)
    {
        $copy = $page->replicate();
        $copy->key = $this->makeUniqueKey($page->key . '_copy');
        $copy->slug = Page::makeUniqueSlug(($page->slug ?: $page->key) . '-copy');
        $copy->title_en = trim($page->title_en . ' Copy');
        $copy->title_ar = trim($page->title_ar . ' - نسخة');
        $copy->is_active = false;
        $copy->save();

        return redirect()->route('admin.pages.edit', $copy)->with('success', 'Page duplicated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->forceFill([
            'deleted_by' => $this->guardedAdminUser()?->id,
        ])->save();

        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page moved to trash successfully.');
    }

    public function restore(int $page)
    {
        $page = Page::onlyTrashed()->findOrFail($page);
        $page->restore();
        $page->forceFill([
            'deleted_by' => null,
        ])->save();

        return redirect()->route('admin.pages.trash')->with('success', 'Page restored successfully.');
    }

    public function forceDestroy(int $page)
    {
        $page = Page::onlyTrashed()->findOrFail($page);
        $page->forceDelete();

        return redirect()->route('admin.pages.trash')->with('success', 'Page deleted permanently.');
    }

    protected function validatedData(Request $request, ?Page $page = null): array
    {
        return $request->validate([
            'key' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:pages,key' . ($page ? ',' . $page->id : '')],
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug' . ($page ? ',' . $page->id : '')],
            'hero_badge_en' => ['nullable', 'string', 'max:255'],
            'hero_badge_ar' => ['nullable', 'string', 'max:255'],
            'hero_title_en' => ['nullable', 'string', 'max:255'],
            'hero_title_ar' => ['nullable', 'string', 'max:255'],
            'hero_subtitle_en' => ['nullable', 'string'],
            'hero_subtitle_ar' => ['nullable', 'string'],
            'hero_primary_cta_text_en' => ['nullable', 'string', 'max:255'],
            'hero_primary_cta_text_ar' => ['nullable', 'string', 'max:255'],
            'hero_primary_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta_text_en' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta_text_ar' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'intro_title_en' => ['nullable', 'string', 'max:255'],
            'intro_title_ar' => ['nullable', 'string', 'max:255'],
            'intro_body_en' => ['nullable', 'string'],
            'intro_body_ar' => ['nullable', 'string'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_title_ar' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string'],
            'meta_description_ar' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image'],
            'service_cta_background_image' => ['nullable', 'image'],
            'story_image' => ['nullable', 'image'],
            'professionalism_image' => ['nullable', 'image'],
            'content_cta_background_image' => ['nullable', 'image'],
        ]);
    }

    protected function makeUniqueKey(string $value, ?int $ignoreId = null): string
    {
        $base = Str::of(trim($value))
            ->lower()
            ->replace(['-', ' '], '_')
            ->snake()
            ->value() ?: 'page';
        $candidate = $base;
        $counter = 2;

        while (Page::query()
            ->withTrashed()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('key', $candidate)
            ->exists()) {
            $candidate = $base . '_' . $counter;
            $counter++;
        }

        return $candidate;
    }

    protected function guardedAdminUser()
    {
        return auth()->user();
    }

    protected function pageSectionsFromRequest(Request $request, string $key, array $existing): array
    {
        $sections = match ($key) {
            'home' => [
                'services' => $this->mapLocalizedBlocks(
                    $request->input('services_title_en'),
                    $request->input('services_title_ar'),
                    $request->input('services_text_en'),
                    $request->input('services_text_ar'),
                    $request->input('services_icon')
                ),
                'why_choose_us' => $this->mapLocalizedBlocks(
                    $request->input('why_title_en'),
                    $request->input('why_title_ar'),
                    $request->input('why_text_en'),
                    $request->input('why_text_ar')
                ),
                'how_it_works' => $this->mapLocalizedBlocks(
                    $request->input('steps_title_en'),
                    $request->input('steps_title_ar'),
                    $request->input('steps_text_en'),
                    $request->input('steps_text_ar')
                ),
                'promo' => [
                    'title_en' => $request->input('promo_title_en'),
                    'title_ar' => $request->input('promo_title_ar'),
                    'text_en' => $request->input('promo_text_en'),
                    'text_ar' => $request->input('promo_text_ar'),
                    'button_en' => $request->input('promo_button_en'),
                    'button_ar' => $request->input('promo_button_ar'),
                    'url' => $request->input('promo_url'),
                ],
                'inquiry' => [
                    'title_en' => $request->input('inquiry_title_en'),
                    'title_ar' => $request->input('inquiry_title_ar'),
                    'text_en' => $request->input('inquiry_text_en'),
                    'text_ar' => $request->input('inquiry_text_ar'),
                ],
                'final_cta' => [
                    'title_en' => $request->input('final_cta_title_en'),
                    'title_ar' => $request->input('final_cta_title_ar'),
                    'text_en' => $request->input('final_cta_text_en'),
                    'text_ar' => $request->input('final_cta_text_ar'),
                    'button_en' => $request->input('final_cta_button_en'),
                    'button_ar' => $request->input('final_cta_button_ar'),
                    'url' => $request->input('final_cta_url'),
                ],
            ],
            'visas', 'domestic', 'flights', 'hotels' => $this->serviceSectionsFromRequest($request, $existing),
            'about', 'contact' => $this->contentSectionsFromRequest($request, $existing),
            default => [
                'feature_blocks' => $this->mapLocalizedBlocks(
                    $request->input('feature_title_en'),
                    $request->input('feature_title_ar'),
                    $request->input('feature_text_en'),
                    $request->input('feature_text_ar')
                ),
                'faqs' => $this->mapFaqs(
                    $request->input('faq_question_en'),
                    $request->input('faq_answer_en'),
                    $request->input('faq_question_ar'),
                    $request->input('faq_answer_ar')
                ),
                'cta' => [
                    'title_en' => $request->input('cta_title_en'),
                    'title_ar' => $request->input('cta_title_ar'),
                    'text_en' => $request->input('cta_text_en'),
                    'text_ar' => $request->input('cta_text_ar'),
                    'button_en' => $request->input('cta_button_en'),
                    'button_ar' => $request->input('cta_button_ar'),
                    'url' => $request->input('cta_url'),
                ],
            ],
        };

        $filtered = array_filter($sections, fn ($value) => ! empty($value));

        return empty($filtered) ? $existing : $filtered;
    }

    protected function serviceSectionsFromRequest(Request $request, array $existing): array
    {
        return [
            'featured_section' => [
                'enabled' => $request->boolean('featured_enabled'),
                'eyebrow_en' => trim((string) $request->input('featured_eyebrow_en', data_get($existing, 'featured_section.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('featured_eyebrow_ar', data_get($existing, 'featured_section.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('featured_title_en', data_get($existing, 'featured_section.title_en', ''))),
                'title_ar' => trim((string) $request->input('featured_title_ar', data_get($existing, 'featured_section.title_ar', ''))),
                'subtitle_en' => trim((string) $request->input('featured_subtitle_en', data_get($existing, 'featured_section.subtitle_en', ''))),
                'subtitle_ar' => trim((string) $request->input('featured_subtitle_ar', data_get($existing, 'featured_section.subtitle_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('featured_items', []), [
                    'title',
                    'subtitle',
                    'meta',
                    'badge',
                    'button',
                ], ['url'], ['is_active'], ['sort_order']),
            ],
            'features_section' => [
                'enabled' => $request->boolean('features_enabled'),
                'eyebrow_en' => trim((string) $request->input('features_eyebrow_en', data_get($existing, 'features_section.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('features_eyebrow_ar', data_get($existing, 'features_section.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('features_title_en', data_get($existing, 'features_section.title_en', ''))),
                'title_ar' => trim((string) $request->input('features_title_ar', data_get($existing, 'features_section.title_ar', ''))),
                'subtitle_en' => trim((string) $request->input('features_subtitle_en', data_get($existing, 'features_section.subtitle_en', ''))),
                'subtitle_ar' => trim((string) $request->input('features_subtitle_ar', data_get($existing, 'features_section.subtitle_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('feature_items', []), [
                    'title',
                    'text',
                ], ['tag'], ['is_active'], ['sort_order']),
            ],
            'cards_section' => [
                'enabled' => $request->boolean('cards_enabled'),
                'eyebrow_en' => trim((string) $request->input('cards_eyebrow_en', data_get($existing, 'cards_section.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('cards_eyebrow_ar', data_get($existing, 'cards_section.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('cards_title_en', data_get($existing, 'cards_section.title_en', ''))),
                'title_ar' => trim((string) $request->input('cards_title_ar', data_get($existing, 'cards_section.title_ar', ''))),
                'subtitle_en' => trim((string) $request->input('cards_subtitle_en', data_get($existing, 'cards_section.subtitle_en', ''))),
                'subtitle_ar' => trim((string) $request->input('cards_subtitle_ar', data_get($existing, 'cards_section.subtitle_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('card_items', []), [
                    'title',
                    'meta',
                    'price',
                    'button',
                    'highlights',
                ], ['url'], ['is_active'], ['sort_order']),
            ],
            'steps_section' => [
                'enabled' => $request->boolean('steps_enabled'),
                'eyebrow_en' => trim((string) $request->input('steps_section_eyebrow_en', data_get($existing, 'steps_section.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('steps_section_eyebrow_ar', data_get($existing, 'steps_section.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('steps_section_title_en', data_get($existing, 'steps_section.title_en', ''))),
                'title_ar' => trim((string) $request->input('steps_section_title_ar', data_get($existing, 'steps_section.title_ar', ''))),
                'subtitle_en' => trim((string) $request->input('steps_section_subtitle_en', data_get($existing, 'steps_section.subtitle_en', ''))),
                'subtitle_ar' => trim((string) $request->input('steps_section_subtitle_ar', data_get($existing, 'steps_section.subtitle_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('step_section_items', []), [
                    'title',
                    'description',
                ], [], ['is_active'], ['sort_order', 'number']),
            ],
            'grid_section' => [
                'enabled' => $request->boolean('grid_enabled'),
                'eyebrow_en' => trim((string) $request->input('grid_eyebrow_en', data_get($existing, 'grid_section.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('grid_eyebrow_ar', data_get($existing, 'grid_section.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('grid_title_en', data_get($existing, 'grid_section.title_en', ''))),
                'title_ar' => trim((string) $request->input('grid_title_ar', data_get($existing, 'grid_section.title_ar', ''))),
                'subtitle_en' => trim((string) $request->input('grid_subtitle_en', data_get($existing, 'grid_section.subtitle_en', ''))),
                'subtitle_ar' => trim((string) $request->input('grid_subtitle_ar', data_get($existing, 'grid_section.subtitle_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('grid_items', []), [
                    'title',
                    'chip',
                    'text',
                ], ['url'], ['is_active'], ['sort_order']),
            ],
            'quick_info_section' => [
                'enabled' => $request->boolean('quick_info_enabled'),
                'eyebrow_en' => trim((string) $request->input('quick_info_eyebrow_en', data_get($existing, 'quick_info_section.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('quick_info_eyebrow_ar', data_get($existing, 'quick_info_section.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('quick_info_title_en', data_get($existing, 'quick_info_section.title_en', ''))),
                'title_ar' => trim((string) $request->input('quick_info_title_ar', data_get($existing, 'quick_info_section.title_ar', ''))),
                'subtitle_en' => trim((string) $request->input('quick_info_subtitle_en', data_get($existing, 'quick_info_section.subtitle_en', ''))),
                'subtitle_ar' => trim((string) $request->input('quick_info_subtitle_ar', data_get($existing, 'quick_info_section.subtitle_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('quick_info_items', []), [
                    'title',
                    'value',
                ], ['tone'], ['is_active'], ['sort_order']),
            ],
            'faq_section' => [
                'enabled' => $request->boolean('faq_enabled'),
                'eyebrow_en' => trim((string) $request->input('faq_section_eyebrow_en', data_get($existing, 'faq_section.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('faq_section_eyebrow_ar', data_get($existing, 'faq_section.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('faq_section_title_en', data_get($existing, 'faq_section.title_en', ''))),
                'title_ar' => trim((string) $request->input('faq_section_title_ar', data_get($existing, 'faq_section.title_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('service_faq_items', []), [
                    'question',
                    'answer',
                ], [], ['is_active'], ['sort_order']),
            ],
            'cta_section' => [
                'enabled' => $request->boolean('service_cta_enabled'),
                'eyebrow_en' => trim((string) $request->input('service_cta_eyebrow_en', data_get($existing, 'cta_section.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('service_cta_eyebrow_ar', data_get($existing, 'cta_section.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('service_cta_title_en', data_get($existing, 'cta_section.title_en', ''))),
                'title_ar' => trim((string) $request->input('service_cta_title_ar', data_get($existing, 'cta_section.title_ar', ''))),
                'description_en' => trim((string) $request->input('service_cta_description_en', data_get($existing, 'cta_section.description_en', ''))),
                'description_ar' => trim((string) $request->input('service_cta_description_ar', data_get($existing, 'cta_section.description_ar', ''))),
                'background_image' => $this->uploadFile($request, 'service_cta_background_image', 'pages', data_get($existing, 'cta_section.background_image')),
                'buttons' => $this->mapLocalizedRows((array) $request->input('service_cta_buttons', []), [
                    'text',
                ], ['url', 'variant'], ['is_active'], ['sort_order']),
            ],
        ];
    }

    protected function contentSectionsFromRequest(Request $request, array $existing): array
    {
        return [
            'story' => $this->mapContentStorySection($request, 'story', $existing),
            'mission' => $this->mapContentCardSection($request, 'mission', $existing),
            'why_choose' => $this->mapContentCardSection($request, 'why_choose', $existing),
            'services' => $this->mapContentCardSection($request, 'services', $existing),
            'stats' => [
                'enabled' => $request->boolean('stats_enabled'),
                'eyebrow_en' => trim((string) $request->input('stats_eyebrow_en', data_get($existing, 'stats.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('stats_eyebrow_ar', data_get($existing, 'stats.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('stats_title_en', data_get($existing, 'stats.title_en', ''))),
                'title_ar' => trim((string) $request->input('stats_title_ar', data_get($existing, 'stats.title_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('stats_items', []), [
                    'label',
                    'text',
                ], ['value'], ['is_active'], ['sort_order']),
            ],
            'professionalism' => $this->mapContentStorySection($request, 'professionalism', $existing),
            'contact_info' => $this->mapContentCardSection($request, 'contact_info', $existing),
            'quick_help' => $this->mapContentCardSection($request, 'quick_help', $existing),
            'faq' => [
                'enabled' => $request->boolean('content_faq_enabled'),
                'eyebrow_en' => trim((string) $request->input('content_faq_eyebrow_en', data_get($existing, 'faq.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('content_faq_eyebrow_ar', data_get($existing, 'faq.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('content_faq_title_en', data_get($existing, 'faq.title_en', ''))),
                'title_ar' => trim((string) $request->input('content_faq_title_ar', data_get($existing, 'faq.title_ar', ''))),
                'items' => $this->mapLocalizedRows((array) $request->input('content_faq_items', []), [
                    'question',
                    'answer',
                ], [], ['is_active'], ['sort_order']),
            ],
            'cta' => [
                'enabled' => $request->boolean('content_cta_enabled'),
                'eyebrow_en' => trim((string) $request->input('content_cta_eyebrow_en', data_get($existing, 'cta.eyebrow_en', ''))),
                'eyebrow_ar' => trim((string) $request->input('content_cta_eyebrow_ar', data_get($existing, 'cta.eyebrow_ar', ''))),
                'title_en' => trim((string) $request->input('content_cta_title_en', data_get($existing, 'cta.title_en', ''))),
                'title_ar' => trim((string) $request->input('content_cta_title_ar', data_get($existing, 'cta.title_ar', ''))),
                'description_en' => trim((string) $request->input('content_cta_description_en', data_get($existing, 'cta.description_en', ''))),
                'description_ar' => trim((string) $request->input('content_cta_description_ar', data_get($existing, 'cta.description_ar', ''))),
                'background_image' => $this->uploadFile($request, 'content_cta_background_image', 'pages', data_get($existing, 'cta.background_image')),
                'buttons' => $this->mapLocalizedRows((array) $request->input('content_cta_buttons', []), [
                    'text',
                ], ['url', 'variant'], ['is_active'], ['sort_order']),
            ],
        ];
    }

    protected function mapContentStorySection(Request $request, string $prefix, array $existing): array
    {
        return [
            'enabled' => $request->boolean($prefix . '_enabled'),
            'eyebrow_en' => trim((string) $request->input($prefix . '_eyebrow_en', data_get($existing, $prefix . '.eyebrow_en', ''))),
            'eyebrow_ar' => trim((string) $request->input($prefix . '_eyebrow_ar', data_get($existing, $prefix . '.eyebrow_ar', ''))),
            'title_en' => trim((string) $request->input($prefix . '_title_en', data_get($existing, $prefix . '.title_en', ''))),
            'title_ar' => trim((string) $request->input($prefix . '_title_ar', data_get($existing, $prefix . '.title_ar', ''))),
            'description_en' => trim((string) $request->input($prefix . '_description_en', data_get($existing, $prefix . '.description_en', ''))),
            'description_ar' => trim((string) $request->input($prefix . '_description_ar', data_get($existing, $prefix . '.description_ar', ''))),
            'image' => $this->uploadFile($request, $prefix . '_image', 'pages', data_get($existing, $prefix . '.image')),
            'reverse' => $request->boolean($prefix . '_reverse', data_get($existing, $prefix . '.reverse', false)),
            'points' => $this->mapLocalizedRows((array) $request->input($prefix . '_points', []), [
                'text',
            ], [], ['is_active'], ['sort_order']),
        ];
    }

    protected function mapContentCardSection(Request $request, string $prefix, array $existing): array
    {
        return [
            'enabled' => $request->boolean($prefix . '_enabled'),
            'eyebrow_en' => trim((string) $request->input($prefix . '_eyebrow_en', data_get($existing, $prefix . '.eyebrow_en', ''))),
            'eyebrow_ar' => trim((string) $request->input($prefix . '_eyebrow_ar', data_get($existing, $prefix . '.eyebrow_ar', ''))),
            'title_en' => trim((string) $request->input($prefix . '_title_en', data_get($existing, $prefix . '.title_en', ''))),
            'title_ar' => trim((string) $request->input($prefix . '_title_ar', data_get($existing, $prefix . '.title_ar', ''))),
            'subtitle_en' => trim((string) $request->input($prefix . '_subtitle_en', data_get($existing, $prefix . '.subtitle_en', ''))),
            'subtitle_ar' => trim((string) $request->input($prefix . '_subtitle_ar', data_get($existing, $prefix . '.subtitle_ar', ''))),
            'variant' => trim((string) $request->input($prefix . '_variant', data_get($existing, $prefix . '.variant', 'default'))),
            'columns' => trim((string) $request->input($prefix . '_columns', data_get($existing, $prefix . '.columns', 'col-md-6 col-xl-4'))),
            'items' => $this->mapLocalizedRows((array) $request->input($prefix . '_items', []), [
                'title',
                'meta',
                'text',
                'link_label',
            ], ['url', 'icon'], ['is_active'], ['sort_order']),
        ];
    }

    protected function mapLocalizedRows(
        array $rows,
        array $localizedFields = [],
        array $plainFields = [],
        array $booleanFields = [],
        array $numberFields = []
    ): array {
        return collect($rows)
            ->map(function ($row) use ($localizedFields, $plainFields, $booleanFields, $numberFields) {
                $row = is_array($row) ? $row : [];
                $item = [];

                foreach ($localizedFields as $field) {
                    $item[$field . '_en'] = trim((string) ($row[$field . '_en'] ?? ''));
                    $item[$field . '_ar'] = trim((string) ($row[$field . '_ar'] ?? ''));
                }

                foreach ($plainFields as $field) {
                    $item[$field] = trim((string) ($row[$field] ?? ''));
                }

                foreach ($booleanFields as $field) {
                    $item[$field] = (bool) ($row[$field] ?? false);
                }

                foreach ($numberFields as $field) {
                    $item[$field] = filled($row[$field] ?? null) ? (int) $row[$field] : null;
                }

                return $item;
            })
            ->filter(function (array $item) use ($localizedFields, $plainFields) {
                foreach ($localizedFields as $field) {
                    if (filled($item[$field . '_en'] ?? null) || filled($item[$field . '_ar'] ?? null)) {
                        return true;
                    }
                }

                foreach ($plainFields as $field) {
                    if (filled($item[$field] ?? null)) {
                        return true;
                    }
                }

                return false;
            })
            ->values()
            ->all();
    }
}
