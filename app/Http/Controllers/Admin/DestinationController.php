<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.destinations.index', [
            'items' => Destination::orderBy('sort_order')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.destinations.form', ['item' => new Destination()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data = $this->transformData($request, $data);
        Destination::create($data);

        return redirect()->route('admin.destinations.index')->with('success', 'Destination created.');
    }

    public function show(Destination $destination)
    {
        return redirect()->route('admin.destinations.edit', $destination);
    }

    public function edit(Destination $destination)
    {
        return view('admin.destinations.form', ['item' => $destination]);
    }

    public function update(Request $request, Destination $destination)
    {
        $data = $this->validatedData($request, $destination->id);
        $data = $this->transformData($request, $data, $destination);
        $destination->update($data);

        return redirect()->route('admin.destinations.index')->with('success', 'Destination updated.');
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();

        return back()->with('success', 'Destination deleted.');
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:destinations,slug,' . $id],
            'destination_type' => ['required', 'in:domestic,visa'],
            'excerpt_en' => ['nullable', 'string'],
            'excerpt_ar' => ['nullable', 'string'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'subtitle_ar' => ['nullable', 'string', 'max:255'],
            'hero_badge_en' => ['nullable', 'string', 'max:255'],
            'hero_badge_ar' => ['nullable', 'string', 'max:255'],
            'hero_title_en' => ['nullable', 'string', 'max:255'],
            'hero_title_ar' => ['nullable', 'string', 'max:255'],
            'hero_subtitle_en' => ['nullable', 'string'],
            'hero_subtitle_ar' => ['nullable', 'string'],
            'hero_cta_text_en' => ['nullable', 'string', 'max:255'],
            'hero_cta_text_ar' => ['nullable', 'string', 'max:255'],
            'hero_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta_text_en' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta_text_ar' => ['nullable', 'string', 'max:255'],
            'hero_secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_overlay_opacity' => ['nullable', 'numeric', 'between:0,0.95'],
            'overview_en' => ['nullable', 'string'],
            'overview_ar' => ['nullable', 'string'],
            'quick_info_title_en' => ['nullable', 'string', 'max:255'],
            'quick_info_title_ar' => ['nullable', 'string', 'max:255'],
            'about_title_en' => ['nullable', 'string', 'max:255'],
            'about_title_ar' => ['nullable', 'string', 'max:255'],
            'about_description_en' => ['nullable', 'string'],
            'about_description_ar' => ['nullable', 'string'],
            'detailed_title_en' => ['nullable', 'string', 'max:255'],
            'detailed_title_ar' => ['nullable', 'string', 'max:255'],
            'detailed_description_en' => ['nullable', 'string'],
            'detailed_description_ar' => ['nullable', 'string'],
            'best_time_title_en' => ['nullable', 'string', 'max:255'],
            'best_time_title_ar' => ['nullable', 'string', 'max:255'],
            'best_time_description_en' => ['nullable', 'string'],
            'best_time_description_ar' => ['nullable', 'string'],
            'highlights_title_en' => ['nullable', 'string', 'max:255'],
            'highlights_title_ar' => ['nullable', 'string', 'max:255'],
            'services_title_en' => ['nullable', 'string', 'max:255'],
            'services_title_ar' => ['nullable', 'string', 'max:255'],
            'services_intro_en' => ['nullable', 'string'],
            'services_intro_ar' => ['nullable', 'string'],
            'documents_title_en' => ['nullable', 'string', 'max:255'],
            'documents_title_ar' => ['nullable', 'string', 'max:255'],
            'documents_subtitle_en' => ['nullable', 'string'],
            'documents_subtitle_ar' => ['nullable', 'string'],
            'steps_title_en' => ['nullable', 'string', 'max:255'],
            'steps_title_ar' => ['nullable', 'string', 'max:255'],
            'pricing_title_en' => ['nullable', 'string', 'max:255'],
            'pricing_title_ar' => ['nullable', 'string', 'max:255'],
            'pricing_notes_en' => ['nullable', 'string'],
            'pricing_notes_ar' => ['nullable', 'string'],
            'faq_title_en' => ['nullable', 'string', 'max:255'],
            'faq_title_ar' => ['nullable', 'string', 'max:255'],
            'cta_title_en' => ['nullable', 'string', 'max:255'],
            'cta_title_ar' => ['nullable', 'string', 'max:255'],
            'cta_text_en' => ['nullable', 'string'],
            'cta_text_ar' => ['nullable', 'string'],
            'cta_button_en' => ['nullable', 'string', 'max:255'],
            'cta_button_ar' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'cta_secondary_button_en' => ['nullable', 'string', 'max:255'],
            'cta_secondary_button_ar' => ['nullable', 'string', 'max:255'],
            'cta_secondary_url' => ['nullable', 'string', 'max:255'],
            'form_title_en' => ['nullable', 'string', 'max:255'],
            'form_title_ar' => ['nullable', 'string', 'max:255'],
            'form_subtitle_en' => ['nullable', 'string'],
            'form_subtitle_ar' => ['nullable', 'string'],
            'form_submit_text_en' => ['nullable', 'string', 'max:255'],
            'form_submit_text_ar' => ['nullable', 'string', 'max:255'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_title_ar' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string'],
            'meta_description_ar' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'hero_image' => ['nullable', 'image'],
            'featured_image' => ['nullable', 'image'],
            'hero_mobile_image' => ['nullable', 'image'],
            'flag_image' => ['nullable', 'image'],
            'about_image' => ['nullable', 'image'],
            'cta_background_image' => ['nullable', 'image'],
            'gallery_files.*' => ['nullable', 'image'],
        ]);
    }

    protected function transformData(Request $request, array $data, ?Destination $destination = null): array
    {
        $data['hero_image'] = $this->uploadFile($request, 'hero_image', 'destinations', $destination?->hero_image);
        $data['featured_image'] = $this->uploadFile($request, 'featured_image', 'destinations', $destination?->featured_image);
        $data['hero_mobile_image'] = $this->uploadFile($request, 'hero_mobile_image', 'destinations', $destination?->hero_mobile_image);
        $data['flag_image'] = $this->uploadFile($request, 'flag_image', 'destinations', $destination?->flag_image);
        $data['about_image'] = $this->uploadFile($request, 'about_image', 'destinations', $destination?->about_image);
        $data['cta_background_image'] = $this->uploadFile($request, 'cta_background_image', 'destinations', $destination?->cta_background_image);
        $data['quick_info_items'] = $this->mapQuickInfoItems($request->input('quick_info_items', []));
        $data['about_points'] = $this->mapLocalizedTextItems($request->input('about_points_en'), $request->input('about_points_ar'));
        $data['highlight_items'] = $this->mapHighlightItems($request->input('highlight_items', []));
        $data['service_items'] = $this->mapServiceItems($request->input('service_items', []));
        $data['document_items'] = $this->mapDocumentItems($request->input('document_items', []));
        $data['step_items'] = $this->mapStepItems($request->input('step_items', []));
        $data['pricing_items'] = $this->mapPricingItems($request->input('pricing_items', []));
        $data['faqs'] = $this->mapStructuredFaqs($request->input('faq_items', []));
        $data['gallery'] = $this->uploadMultipleFiles($request, 'gallery_files', 'destinations/gallery', $destination?->gallery ?? []);
        $data['hero_overlay_opacity'] = $request->filled('hero_overlay_opacity')
            ? round((float) $request->input('hero_overlay_opacity'), 2)
            : ($destination?->hero_overlay_opacity ?? 0.45);
        $data['form_visible_fields'] = array_values(array_filter(
            $request->input('form_visible_fields', []),
            fn ($field) => in_array($field, $this->availableFormFields(), true)
        ));
        $data['show_hero'] = $request->boolean('show_hero');
        $data['show_quick_info'] = $request->boolean('show_quick_info');
        $data['show_about'] = $request->boolean('show_about');
        $data['show_detailed'] = $request->boolean('show_detailed');
        $data['show_best_time'] = $request->boolean('show_best_time');
        $data['show_highlights'] = $request->boolean('show_highlights');
        $data['show_services'] = $request->boolean('show_services');
        $data['show_documents'] = $request->boolean('show_documents');
        $data['show_steps'] = $request->boolean('show_steps');
        $data['show_pricing'] = $request->boolean('show_pricing');
        $data['show_faq'] = $request->boolean('show_faq');
        $data['show_cta'] = $request->boolean('show_cta');
        $data['show_form'] = $request->boolean('show_form');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['excerpt_en'] = $data['excerpt_en'] ?? null;
        $data['excerpt_ar'] = $data['excerpt_ar'] ?? null;
        $data['excerpt_en'] = filled($data['excerpt_en']) ? trim($data['excerpt_en']) : $this->buildExcerpt('en', $data);
        $data['excerpt_ar'] = filled($data['excerpt_ar']) ? trim($data['excerpt_ar']) : $this->buildExcerpt('ar', $data);

        return $data;
    }

    protected function buildExcerpt(string $locale, array $data): string
    {
        $suffix = '_' . $locale;
        $quickInfo = collect($data['quick_info_items'] ?? [])
            ->filter(fn (array $item) => ! empty($item['is_active']))
            ->sortBy('sort_order')
            ->map(fn (array $item) => trim((string) ($item['value' . $suffix] ?? '')))
            ->filter()
            ->take(3)
            ->values();

        $serviceTitles = collect($data['service_items'] ?? [])
            ->filter(fn (array $item) => ! empty($item['is_active']))
            ->sortBy('sort_order')
            ->map(fn (array $item) => trim((string) ($item['title' . $suffix] ?? '')))
            ->filter()
            ->take(2)
            ->values();

        $intro = trim((string) (($data['subtitle' . $suffix] ?? '') ?: ($data['about_description' . $suffix] ?? '') ?: ($data['overview' . $suffix] ?? '')));
        $parts = [];

        if ($intro !== '') {
            $parts[] = $intro;
        }

        if ($quickInfo->isNotEmpty()) {
            $parts[] = $locale === 'ar'
                ? 'أهم التفاصيل: ' . $quickInfo->implode('، ') . '.'
                : 'Key details: ' . $quickInfo->implode(', ') . '.';
        }

        if ($serviceTitles->isNotEmpty()) {
            $parts[] = $locale === 'ar'
                ? 'تشمل الخدمة عادة ' . $serviceTitles->implode('، ') . '.'
                : 'Typical support includes ' . $serviceTitles->implode(', ') . '.';
        }

        return Str::of(implode(' ', $parts))
            ->replaceMatches('/\s+/', ' ')
            ->trim(" \t\n\r\0\x0B.,")
            ->finish('.');
    }

    protected function mapQuickInfoItems(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            return [
                'label_en' => trim($item['label_en'] ?? ''),
                'label_ar' => trim($item['label_ar'] ?? ''),
                'value_en' => trim($item['value_en'] ?? ''),
                'value_ar' => trim($item['value_ar'] ?? ''),
                'icon' => trim($item['icon'] ?? ''),
                'sort_order' => (int) ($item['sort_order'] ?? $index + 1),
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapHighlightItems(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            return [
                'title_en' => trim($item['title_en'] ?? ''),
                'title_ar' => trim($item['title_ar'] ?? ''),
                'description_en' => trim($item['description_en'] ?? ''),
                'description_ar' => trim($item['description_ar'] ?? ''),
                'image' => trim($item['image'] ?? ''),
                'icon' => trim($item['icon'] ?? ''),
                'sort_order' => (int) ($item['sort_order'] ?? $index + 1),
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapServiceItems(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            return [
                'title_en' => trim($item['title_en'] ?? ''),
                'title_ar' => trim($item['title_ar'] ?? ''),
                'description_en' => trim($item['description_en'] ?? ''),
                'description_ar' => trim($item['description_ar'] ?? ''),
                'icon' => trim($item['icon'] ?? ''),
                'sort_order' => (int) ($item['sort_order'] ?? $index + 1),
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapDocumentItems(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            return [
                'title_en' => trim($item['title_en'] ?? ''),
                'title_ar' => trim($item['title_ar'] ?? ''),
                'description_en' => trim($item['description_en'] ?? ''),
                'description_ar' => trim($item['description_ar'] ?? ''),
                'icon' => trim($item['icon'] ?? ''),
                'sort_order' => (int) ($item['sort_order'] ?? $index + 1),
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapStepItems(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            $sortOrder = (int) ($item['sort_order'] ?? $index + 1);

            return [
                'title_en' => trim($item['title_en'] ?? ''),
                'title_ar' => trim($item['title_ar'] ?? ''),
                'description_en' => trim($item['description_en'] ?? ''),
                'description_ar' => trim($item['description_ar'] ?? ''),
                'icon' => trim($item['icon'] ?? ''),
                'step_number' => (int) ($item['step_number'] ?? $sortOrder),
                'sort_order' => $sortOrder,
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapPricingItems(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            return [
                'label_en' => trim($item['label_en'] ?? ''),
                'label_ar' => trim($item['label_ar'] ?? ''),
                'value_en' => trim($item['value_en'] ?? ''),
                'value_ar' => trim($item['value_ar'] ?? ''),
                'note_en' => trim($item['note_en'] ?? ''),
                'note_ar' => trim($item['note_ar'] ?? ''),
                'sort_order' => (int) ($item['sort_order'] ?? $index + 1),
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapStructuredFaqs(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            return [
                'question_en' => trim($item['question_en'] ?? ''),
                'question_ar' => trim($item['question_ar'] ?? ''),
                'answer_en' => trim($item['answer_en'] ?? ''),
                'answer_ar' => trim($item['answer_ar'] ?? ''),
                'sort_order' => (int) ($item['sort_order'] ?? $index + 1),
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapRepeaterItems(array $items, callable $callback): array
    {
        $mapped = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $row = $callback($item, $index);
            $hasContent = collect($row)
                ->except(['sort_order', 'step_number', 'is_active'])
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->isNotEmpty();

            if (! $hasContent) {
                continue;
            }

            $mapped[] = $row;
        }

        return collect($mapped)->sortBy('sort_order')->values()->all();
    }

    protected function availableFormFields(): array
    {
        return ['email', 'travel_date', 'return_date', 'travelers_count', 'message'];
    }
}
