<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;

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
            'gallery_files.*' => ['nullable', 'image'],
        ]);
    }

    protected function transformData(Request $request, array $data, ?Destination $destination = null): array
    {
        $data['hero_image'] = $this->uploadFile($request, 'hero_image', 'destinations', $destination?->hero_image);
        $data['highlights'] = $this->mapLocalizedTextItems($request->input('highlights_en'), $request->input('highlights_ar'));
        $data['packages'] = $this->mapLocalizedTextItems($request->input('packages_en'), $request->input('packages_ar'));
        $data['included_items'] = $this->mapLocalizedTextItems($request->input('included_en'), $request->input('included_ar'));
        $data['excluded_items'] = $this->mapLocalizedTextItems($request->input('excluded_en'), $request->input('excluded_ar'));
        $data['itinerary'] = $this->mapLocalizedTextItems($request->input('itinerary_en'), $request->input('itinerary_ar'));
        $data['faqs'] = $this->mapFaqs(
            $request->input('faq_question_en'),
            $request->input('faq_answer_en'),
            $request->input('faq_question_ar'),
            $request->input('faq_answer_ar')
        );
        $data['gallery'] = $this->uploadMultipleFiles($request, 'gallery_files', 'destinations/gallery', $destination?->gallery ?? []);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
