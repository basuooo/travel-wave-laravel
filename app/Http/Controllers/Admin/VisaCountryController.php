<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Support\MediaLibraryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisaCountryController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.visa-countries.index', [
            'items' => VisaCountry::with('category')->orderBy('sort_order')->paginate(15),
        ]);
    }

    public function trash()
    {
        return view('admin.visa-countries.trash', [
            'items' => VisaCountry::onlyTrashed()->with(['category', 'deletedBy'])->orderByDesc('deleted_at')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.visa-countries.form', [
            'item' => new VisaCountry(),
            'categories' => VisaCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data = $this->transformData($request, $data);
        VisaCountry::create($data);

        return redirect()->route('admin.visa-countries.index')->with('success', 'Visa country created.');
    }

    public function show(VisaCountry $visaCountry)
    {
        return redirect()->route('admin.visa-countries.edit', $visaCountry);
    }

    public function edit(VisaCountry $visa_country)
    {
        return view('admin.visa-countries.form', [
            'item' => $visa_country,
            'categories' => VisaCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, VisaCountry $visa_country)
    {
        $data = $this->validatedData($request, $visa_country->id);
        $data = $this->transformData($request, $data, $visa_country);
        $visa_country->update($data);

        return redirect()->route('admin.visa-countries.index')->with('success', 'Visa country updated.');
    }

    public function duplicate(VisaCountry $visa_country)
    {
        $copy = $visa_country->replicate();
        $copy->name_en = trim($visa_country->name_en . ' Copy');
        $copy->name_ar = trim($visa_country->name_ar . ' - نسخة');
        $copy->slug = VisaCountry::makeUniqueSlug(($visa_country->slug ?: $visa_country->name_en) . '-copy');
        $copy->is_active = false;
        $copy->is_featured = false;
        $copy->save();

        return redirect()->route('admin.visa-countries.edit', $copy)->with('success', 'Visa country duplicated.');
    }

    public function export(VisaCountry $visa_country)
    {
        $data = $visa_country->makeHidden(['id', 'created_at', 'updated_at', 'deleted_at', 'deleted_by'])->toArray();
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response($json)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="visa-country-' . $visa_country->slug . '.json"');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:json'],
        ]);

        $data = json_decode(file_get_contents($request->file('file')->path()), true);

        if (!$data || !isset($data['name_en'])) {
            return back()->with('error', 'Invalid JSON file.');
        }

        $data['slug'] = VisaCountry::makeUniqueSlug($data['slug'] . '-imported');
        $data['name_en'] .= ' (Imported)';
        $data['name_ar'] .= ' (مستورد)';
        $data['is_active'] = false;

        // Ensure visa_category_id is present and exists
        if (!isset($data['visa_category_id']) || !VisaCategory::where('id', $data['visa_category_id'])->exists()) {
            $data['visa_category_id'] = VisaCategory::first()?->id;
        }

        if (!$data['visa_category_id']) {
            return back()->with('error', 'No visa categories found. Please create a category first.');
        }

        $newCountry = VisaCountry::create($data);

        return redirect()->route('admin.visa-countries.edit', $newCountry)->with('success', 'Visa country imported as draft.');
    }

    public function destroy(VisaCountry $visa_country)
    {
        $visa_country->forceFill(['deleted_by' => auth()->id()])->save();
        $visa_country->delete();

        return redirect()->route('admin.visa-countries.index')->with('success', 'Visa country moved to trash.');
    }

    public function restore(int $visa_country)
    {
        $item = VisaCountry::onlyTrashed()->findOrFail($visa_country);
        $item->restore();
        $item->forceFill(['deleted_by' => null])->save();

        return redirect()->route('admin.visa-countries.trash')->with('success', 'Visa country restored.');
    }

    public function forceDestroy(int $visa_country)
    {
        $item = VisaCountry::onlyTrashed()->findOrFail($visa_country);
        $item->forceDelete();

        return redirect()->route('admin.visa-countries.trash')->with('success', 'Visa country deleted permanently.');
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'visa_category_id' => ['required', 'exists:visa_categories,id'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:visa_countries,slug,' . $id],
            'excerpt_en' => ['nullable', 'string'],
            'excerpt_ar' => ['nullable', 'string'],
            'hero_badge_en' => ['nullable', 'string', 'max:255'],
            'hero_badge_ar' => ['nullable', 'string', 'max:255'],
            'hero_title_en' => ['nullable', 'string', 'max:255'],
            'hero_title_ar' => ['nullable', 'string', 'max:255'],
            'hero_subtitle_en' => ['nullable', 'string'],
            'hero_subtitle_ar' => ['nullable', 'string'],
            'hero_cta_text_en' => ['nullable', 'string', 'max:255'],
            'hero_cta_text_ar' => ['nullable', 'string', 'max:255'],
            'hero_cta_url' => ['nullable', 'string', 'max:255'],
            'hero_overlay_opacity' => ['nullable', 'numeric', 'between:0,0.95'],
            'overview_en' => ['nullable', 'string'],
            'overview_ar' => ['nullable', 'string'],
            'visa_type_en' => ['nullable', 'string', 'max:255'],
            'visa_type_ar' => ['nullable', 'string', 'max:255'],
            'stay_duration_en' => ['nullable', 'string', 'max:255'],
            'stay_duration_ar' => ['nullable', 'string', 'max:255'],
            'quick_summary_destination_label_en' => ['nullable', 'string', 'max:255'],
            'quick_summary_destination_label_ar' => ['nullable', 'string', 'max:255'],
            'quick_summary_destination_icon' => ['nullable', 'string', 'max:255'],
            'introduction_title_en' => ['nullable', 'string', 'max:255'],
            'introduction_title_ar' => ['nullable', 'string', 'max:255'],
            'introduction_badge_en' => ['nullable', 'string', 'max:255'],
            'introduction_badge_ar' => ['nullable', 'string', 'max:255'],
            'detailed_title_en' => ['nullable', 'string', 'max:255'],
            'detailed_title_ar' => ['nullable', 'string', 'max:255'],
            'detailed_description_en' => ['nullable', 'string'],
            'detailed_description_ar' => ['nullable', 'string'],
            'best_time_badge_en' => ['nullable', 'string', 'max:255'],
            'best_time_badge_ar' => ['nullable', 'string', 'max:255'],
            'best_time_title_en' => ['nullable', 'string', 'max:255'],
            'best_time_title_ar' => ['nullable', 'string', 'max:255'],
            'best_time_description_en' => ['nullable', 'string'],
            'best_time_description_ar' => ['nullable', 'string'],
            'highlights_section_label_en' => ['nullable', 'string', 'max:255'],
            'highlights_section_label_ar' => ['nullable', 'string', 'max:255'],
            'highlights_section_title_en' => ['nullable', 'string', 'max:255'],
            'highlights_section_title_ar' => ['nullable', 'string', 'max:255'],
            'highlight_items.*.image_file' => ['nullable', 'image'],
            'why_choose_title_en' => ['nullable', 'string', 'max:255'],
            'why_choose_title_ar' => ['nullable', 'string', 'max:255'],
            'why_choose_intro_en' => ['nullable', 'string'],
            'why_choose_intro_ar' => ['nullable', 'string'],
            'documents_title_en' => ['nullable', 'string', 'max:255'],
            'documents_title_ar' => ['nullable', 'string', 'max:255'],
            'documents_subtitle_en' => ['nullable', 'string'],
            'documents_subtitle_ar' => ['nullable', 'string'],
            'steps_title_en' => ['nullable', 'string', 'max:255'],
            'steps_title_ar' => ['nullable', 'string', 'max:255'],
            'processing_time_en' => ['nullable', 'string'],
            'processing_time_ar' => ['nullable', 'string'],
            'fees_en' => ['nullable', 'string'],
            'fees_ar' => ['nullable', 'string'],
            'fees_title_en' => ['nullable', 'string', 'max:255'],
            'fees_title_ar' => ['nullable', 'string', 'max:255'],
            'fees_notes_en' => ['nullable', 'string'],
            'fees_notes_ar' => ['nullable', 'string'],
            'faq_title_en' => ['nullable', 'string', 'max:255'],
            'faq_title_ar' => ['nullable', 'string', 'max:255'],
            'support_title_en' => ['nullable', 'string', 'max:255'],
            'support_title_ar' => ['nullable', 'string', 'max:255'],
            'support_subtitle_en' => ['nullable', 'string'],
            'support_subtitle_ar' => ['nullable', 'string'],
            'support_button_en' => ['nullable', 'string', 'max:255'],
            'support_button_ar' => ['nullable', 'string', 'max:255'],
            'support_button_link' => ['nullable', 'string', 'max:255'],
            'map_title_en' => ['nullable', 'string', 'max:255'],
            'map_title_ar' => ['nullable', 'string', 'max:255'],
            'map_description_en' => ['nullable', 'string'],
            'map_description_ar' => ['nullable', 'string'],
            'map_embed_code' => ['nullable', 'string'],
            'inquiry_form_title_en' => ['nullable', 'string', 'max:255'],
            'inquiry_form_title_ar' => ['nullable', 'string', 'max:255'],
            'inquiry_form_subtitle_en' => ['nullable', 'string'],
            'inquiry_form_subtitle_ar' => ['nullable', 'string'],
            'inquiry_form_button_en' => ['nullable', 'string', 'max:255'],
            'inquiry_form_button_ar' => ['nullable', 'string', 'max:255'],
            'inquiry_form_success_en' => ['nullable', 'string'],
            'inquiry_form_success_ar' => ['nullable', 'string'],
            'inquiry_form_default_service_type' => ['nullable', 'string', 'max:255'],
            'inquiry_form_label_en' => ['nullable', 'string', 'max:255'],
            'inquiry_form_label_ar' => ['nullable', 'string', 'max:255'],
            'cta_title_en' => ['nullable', 'string', 'max:255'],
            'cta_title_ar' => ['nullable', 'string', 'max:255'],
            'cta_text_en' => ['nullable', 'string'],
            'cta_text_ar' => ['nullable', 'string'],
            'cta_button_en' => ['nullable', 'string', 'max:255'],
            'cta_button_ar' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_title_ar' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string'],
            'meta_description_ar' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'hero_image' => ['nullable', 'image'],
            'hero_mobile_image' => ['nullable', 'image'],
            'flag_image' => ['nullable', 'image'],
            'intro_image' => ['nullable', 'image'],
            'final_cta_background_image' => ['nullable', 'image'],
            'og_image' => ['nullable', 'image'],
            'content_mode' => ['nullable', 'string', 'in:normal,html'],
            'html_content_en' => ['nullable', 'string'],
            'html_content_ar' => ['nullable', 'string'],
        ]);
    }

    protected function transformData(Request $request, array $data, ?VisaCountry $country = null): array
    {
        $data['hero_image'] = $this->uploadFile($request, 'hero_image', 'visa-countries', $country?->hero_image);
        $data['hero_mobile_image'] = $this->uploadFile($request, 'hero_mobile_image', 'visa-countries', $country?->hero_mobile_image);
        $data['flag_image'] = $this->uploadFile($request, 'flag_image', 'visa-countries', $country?->flag_image);
        $data['intro_image'] = $this->uploadFile($request, 'intro_image', 'visa-countries', $country?->intro_image);
        $data['final_cta_background_image'] = $this->uploadFile($request, 'final_cta_background_image', 'visa-countries', $country?->final_cta_background_image);
        $data['og_image'] = $this->uploadFile($request, 'og_image', 'visa-countries', $country?->og_image);
        $data['highlights'] = $this->mapHighlightItems($request, $request->input('highlight_items', []));
        $data['quick_summary_items'] = $this->mapQuickSummaryItems($request->input('quick_summary_items', []));
        $data['introduction_points'] = $this->mapLocalizedTextItems($request->input('introduction_points_en'), $request->input('introduction_points_ar'));
        $data['required_documents'] = $this->mapLocalizedTextItems($request->input('documents_en'), $request->input('documents_ar'));
        $data['application_steps'] = $this->mapLocalizedTextItems($request->input('steps_en'), $request->input('steps_ar'));
        $data['services'] = $this->mapLocalizedTextItems($request->input('services_en'), $request->input('services_ar'));
        $data['why_choose_items'] = $this->mapWhyChooseItems($request->input('why_choose_items', []));
        $data['document_items'] = $this->mapDocumentItems($request->input('document_items', []));
        $data['step_items'] = $this->mapStepItems($request->input('step_items', []));
        $data['fee_items'] = $this->mapFeeItems($request->input('fee_items', []));
        $data['faqs'] = $this->mapStructuredFaqs($request->input('faq_items', []));
        $data['inquiry_form_visible_fields'] = array_values(array_filter(
            $request->input('inquiry_form_visible_fields', []),
            fn ($field) => in_array($field, $this->availableInquiryFields(), true)
        ));
        $data['hero_overlay_opacity'] = $request->filled('hero_overlay_opacity')
            ? round((float) $request->input('hero_overlay_opacity'), 2)
            : ($country?->hero_overlay_opacity ?? 0.45);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['support_is_active'] = $request->boolean('support_is_active');
        $data['map_is_active'] = $request->boolean('map_is_active');
        $data['inquiry_form_is_active'] = $request->boolean('inquiry_form_is_active');
        $data['final_cta_is_active'] = $request->boolean('final_cta_is_active');

        $data['content_mode'] = $request->input('content_mode', 'normal');
        $data['html_content_en'] = $request->input('html_content_en');
        $data['html_content_ar'] = $request->input('html_content_ar');

        $data['excerpt_en'] = $data['excerpt_en'] ?? null;
        $data['excerpt_ar'] = $data['excerpt_ar'] ?? null;

        $data['excerpt_en'] = filled($data['excerpt_en']) ? trim($data['excerpt_en']) : $this->buildExcerpt('en', $data);
        $data['excerpt_ar'] = filled($data['excerpt_ar']) ? trim($data['excerpt_ar']) : $this->buildExcerpt('ar', $data);

        return $data;
    }

    protected function buildExcerpt(string $locale, array $data): string
    {
        $suffix = '_' . $locale;
        $visaType = trim((string) ($data['visa_type' . $suffix] ?? ''));
        $stayDuration = trim((string) ($data['stay_duration' . $suffix] ?? ''));
        $processingTime = trim((string) ($data['processing_time' . $suffix] ?? ''));
        $documents = $this->summarizeDocumentNames($data['document_items'] ?? [], $locale);
        $support = $this->summarizeWhyChoose($data['why_choose_items'] ?? [], $locale);
        $fallbackText = trim(strip_tags((string) (($data['overview' . $suffix] ?? '') ?: ($data['detailed_description' . $suffix] ?? ''))));

        if ($locale === 'ar') {
            $parts = array_values(array_filter([
                $visaType ? 'تشمل الخدمة ' . $visaType . '.' : null,
                $stayDuration ? 'مدة الإقامة المعتادة ' . $stayDuration . '.' : null,
                $processingTime ? 'المدة المتوقعة للمعالجة ' . $processingTime . '.' : null,
                $documents ? 'أبرز المستندات المطلوبة: ' . $documents . '.' : null,
                $support ? 'توفر Travel Wave دعماً يشمل ' . $support . '.' : null,
            ]));
        } else {
            $parts = array_values(array_filter([
                $visaType ? 'Visa type: ' . $visaType . '.' : null,
                $stayDuration ? 'Typical stay allowance: ' . $stayDuration . '.' : null,
                $processingTime ? 'Expected processing time: ' . $processingTime . '.' : null,
                $documents ? 'Common required documents include ' . $documents . '.' : null,
                $support ? 'Travel Wave support includes ' . $support . '.' : null,
            ]));
        }

        $summary = trim(implode(' ', $parts));

        if ($summary !== '') {
            return $summary;
        }

        return Str::of($fallbackText)
            ->replaceMatches('/\s+/', ' ')
            ->trim(" \t\n\r\0\x0B.,")
            ->finish('.');
    }

    protected function summarizeDocumentNames(array $items, string $locale): string
    {
        $key = 'name_' . $locale;

        $names = collect($items)
            ->filter(fn (array $item) => ! empty($item['is_active']))
            ->sortBy('sort_order')
            ->map(fn (array $item) => trim((string) ($item[$key] ?? '')))
            ->filter()
            ->take(3)
            ->values()
            ->all();

        return $this->joinList($names, $locale);
    }

    protected function summarizeWhyChoose(array $items, string $locale): string
    {
        $key = 'title_' . $locale;

        $titles = collect($items)
            ->filter(fn (array $item) => ! empty($item['is_active']))
            ->sortBy('sort_order')
            ->map(fn (array $item) => trim((string) ($item[$key] ?? '')))
            ->filter()
            ->take(2)
            ->values()
            ->all();

        return $this->joinList($titles, $locale);
    }

    protected function joinList(array $items, string $locale): string
    {
        $items = array_values(array_filter(array_map('trim', $items)));
        $count = count($items);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $items[0];
        }

        if ($count === 2) {
            return $locale === 'ar'
                ? $items[0] . ' و' . $items[1]
                : $items[0] . ' and ' . $items[1];
        }

        $last = array_pop($items);

        return $locale === 'ar'
            ? implode('، ', $items) . '، و' . $last
            : implode(', ', $items) . ', and ' . $last;
    }

    protected function mapWhyChooseItems(array $items): array
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

    protected function mapHighlightItems(Request $request, array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) use ($request) {
            $currentImage = trim((string) ($item['existing_image'] ?? ''));
            $removeImage = ! empty($item['remove_image']);
            $image = $removeImage ? null : $currentImage;

            if ($image) {
                MediaLibraryService::syncExistingFile($image, 'visa-countries/highlights');
            }

            if ($request->hasFile("highlight_items.$index.image_file")) {
                $file = $request->file("highlight_items.$index.image_file");
                $image = $file->store('visa-countries/highlights', 'public');
                MediaLibraryService::registerUploadedFile($file, $image, 'visa-countries/highlights');
            }

            return [
                'title_en' => trim($item['title_en'] ?? ''),
                'title_ar' => trim($item['title_ar'] ?? ''),
                'description_en' => trim($item['description_en'] ?? ''),
                'description_ar' => trim($item['description_ar'] ?? ''),
                'image' => $image,
                'sort_order' => (int) ($item['sort_order'] ?? $index + 1),
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapQuickSummaryItems(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            return [
                'label_en' => trim($item['label_en'] ?? $item['title_en'] ?? ''),
                'label_ar' => trim($item['label_ar'] ?? $item['title_ar'] ?? ''),
                'value_en' => trim($item['value_en'] ?? ''),
                'value_ar' => trim($item['value_ar'] ?? ''),
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
                'name_en' => trim($item['name_en'] ?? ''),
                'name_ar' => trim($item['name_ar'] ?? ''),
                'description_en' => trim($item['description_en'] ?? ''),
                'description_ar' => trim($item['description_ar'] ?? ''),
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
                'sort_order' => $sortOrder,
                'step_number' => (int) ($item['step_number'] ?? $sortOrder),
                'is_active' => ! empty($item['is_active']),
            ];
        });
    }

    protected function mapFeeItems(array $items): array
    {
        return $this->mapRepeaterItems($items, function (array $item, int $index) {
            return [
                'label_en' => trim($item['label_en'] ?? ''),
                'label_ar' => trim($item['label_ar'] ?? ''),
                'value_en' => trim($item['value_en'] ?? ''),
                'value_ar' => trim($item['value_ar'] ?? ''),
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

    protected function availableInquiryFields(): array
    {
        return ['full_name', 'phone', 'whatsapp_number', 'email', 'service_type', 'destination', 'travel_date', 'message'];
    }
}
