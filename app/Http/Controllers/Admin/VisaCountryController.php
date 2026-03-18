<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Http\Request;

class VisaCountryController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.visa-countries.index', [
            'items' => VisaCountry::with('category')->orderBy('sort_order')->paginate(15),
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

    public function destroy(VisaCountry $visa_country)
    {
        $visa_country->delete();

        return back()->with('success', 'Visa country deleted.');
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
            'overview_en' => ['nullable', 'string'],
            'overview_ar' => ['nullable', 'string'],
            'processing_time_en' => ['nullable', 'string'],
            'processing_time_ar' => ['nullable', 'string'],
            'fees_en' => ['nullable', 'string'],
            'fees_ar' => ['nullable', 'string'],
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
        ]);
    }

    protected function transformData(Request $request, array $data, ?VisaCountry $country = null): array
    {
        $data['hero_image'] = $this->uploadFile($request, 'hero_image', 'visa-countries', $country?->hero_image);
        $data['highlights'] = $this->mapLocalizedTextItems($request->input('highlights_en'), $request->input('highlights_ar'));
        $data['required_documents'] = $this->mapLocalizedTextItems($request->input('documents_en'), $request->input('documents_ar'));
        $data['application_steps'] = $this->mapLocalizedTextItems($request->input('steps_en'), $request->input('steps_ar'));
        $data['services'] = $this->mapLocalizedTextItems($request->input('services_en'), $request->input('services_ar'));
        $data['faqs'] = $this->mapFaqs(
            $request->input('faq_question_en'),
            $request->input('faq_answer_en'),
            $request->input('faq_question_ar'),
            $request->input('faq_answer_ar')
        );
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
