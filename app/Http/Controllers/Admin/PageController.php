<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.pages.index', [
            'pages' => Page::orderBy('key')->get(),
        ]);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug,' . $page->id],
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
        ]);

        $data['hero_image'] = $this->uploadFile($request, 'hero_image', 'pages', $page->hero_image);
        $data['sections'] = $this->pageSectionsFromRequest($request, $page->key, $page->sections ?? []);
        $data['is_active'] = $request->boolean('is_active', true);

        $page->update($data);

        return back()->with('success', 'Page content updated successfully.');
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
}
