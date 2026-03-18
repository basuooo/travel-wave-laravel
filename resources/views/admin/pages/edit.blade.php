@extends('layouts.admin')

@section('page_title', 'Edit Page: ' . $page->key)
@section('page_description', 'Manage bilingual hero content, repeatable sections, FAQs, and CTA blocks.')

@section('content')
@php($sections = $page->sections ?? [])
<form method="post" enctype="multipart/form-data" action="{{ route('admin.pages.update', $page) }}">
    @csrf
    @method('PUT')

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Core Page Content</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Title EN</label><input class="form-control" name="title_en" value="{{ old('title_en', $page->title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" name="title_ar" value="{{ old('title_ar', $page->title_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $page->slug) }}"></div>
            <div class="col-md-4"><label class="form-label">Hero Badge EN</label><input class="form-control" name="hero_badge_en" value="{{ old('hero_badge_en', $page->hero_badge_en) }}"></div>
            <div class="col-md-4"><label class="form-label">Hero Badge AR</label><input class="form-control text-end" dir="rtl" name="hero_badge_ar" value="{{ old('hero_badge_ar', $page->hero_badge_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Title EN</label><input class="form-control" name="hero_title_en" value="{{ old('hero_title_en', $page->hero_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Title AR</label><input class="form-control text-end" dir="rtl" name="hero_title_ar" value="{{ old('hero_title_ar', $page->hero_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Subtitle EN</label><textarea class="form-control" name="hero_subtitle_en" rows="3">{{ old('hero_subtitle_en', $page->hero_subtitle_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Hero Subtitle AR</label><textarea class="form-control text-end" dir="rtl" name="hero_subtitle_ar" rows="3">{{ old('hero_subtitle_ar', $page->hero_subtitle_ar) }}</textarea></div>
            <div class="col-md-4"><label class="form-label">Primary CTA EN</label><input class="form-control" name="hero_primary_cta_text_en" value="{{ old('hero_primary_cta_text_en', $page->hero_primary_cta_text_en) }}"></div>
            <div class="col-md-4"><label class="form-label">Primary CTA AR</label><input class="form-control text-end" dir="rtl" name="hero_primary_cta_text_ar" value="{{ old('hero_primary_cta_text_ar', $page->hero_primary_cta_text_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">Primary CTA URL</label><input class="form-control" name="hero_primary_cta_url" value="{{ old('hero_primary_cta_url', $page->hero_primary_cta_url) }}"></div>
            <div class="col-md-4"><label class="form-label">Secondary CTA EN</label><input class="form-control" name="hero_secondary_cta_text_en" value="{{ old('hero_secondary_cta_text_en', $page->hero_secondary_cta_text_en) }}"></div>
            <div class="col-md-4"><label class="form-label">Secondary CTA AR</label><input class="form-control text-end" dir="rtl" name="hero_secondary_cta_text_ar" value="{{ old('hero_secondary_cta_text_ar', $page->hero_secondary_cta_text_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">Secondary CTA URL</label><input class="form-control" name="hero_secondary_cta_url" value="{{ old('hero_secondary_cta_url', $page->hero_secondary_cta_url) }}"></div>
            <div class="col-md-6"><label class="form-label">Intro Title EN</label><input class="form-control" name="intro_title_en" value="{{ old('intro_title_en', $page->intro_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Intro Title AR</label><input class="form-control text-end" dir="rtl" name="intro_title_ar" value="{{ old('intro_title_ar', $page->intro_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Intro Body EN</label><textarea class="form-control" name="intro_body_en" rows="4">{{ old('intro_body_en', $page->intro_body_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Intro Body AR</label><textarea class="form-control text-end" dir="rtl" name="intro_body_ar" rows="4">{{ old('intro_body_ar', $page->intro_body_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Meta Title EN</label><input class="form-control" name="meta_title_en" value="{{ old('meta_title_en', $page->meta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Title AR</label><input class="form-control text-end" dir="rtl" name="meta_title_ar" value="{{ old('meta_title_ar', $page->meta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Description EN</label><textarea class="form-control" name="meta_description_en" rows="3">{{ old('meta_description_en', $page->meta_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Meta Description AR</label><textarea class="form-control text-end" dir="rtl" name="meta_description_ar" rows="3">{{ old('meta_description_ar', $page->meta_description_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Hero Image</label><input type="file" class="form-control" name="hero_image"></div>
            <div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active))><label class="form-check-label">Active</label></div>
        </div>
    </div>

    @if($page->key === 'home')
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Homepage Services</h2>
            @for($i = 0; $i < 6; $i++)
                <div class="row g-3 mb-4 border-bottom pb-3">
                    <div class="col-md-2"><label class="form-label">Icon</label><input class="form-control" name="services_icon[]" value="{{ $sections['services'][$i]['icon'] ?? '' }}"></div>
                    <div class="col-md-5"><label class="form-label">Title EN</label><input class="form-control" name="services_title_en[]" value="{{ $sections['services'][$i]['title_en'] ?? '' }}"></div>
                    <div class="col-md-5"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" name="services_title_ar[]" value="{{ $sections['services'][$i]['title_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" name="services_text_en[]" rows="2">{{ $sections['services'][$i]['text_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" name="services_text_ar[]" rows="2">{{ $sections['services'][$i]['text_ar'] ?? '' }}</textarea></div>
                </div>
            @endfor
        </div>
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Why Choose Us / How It Works</h2>
            @for($i = 0; $i < 5; $i++)
                <div class="row g-3 mb-4 border-bottom pb-3">
                    <div class="col-md-6"><label class="form-label">Why Title EN</label><input class="form-control" name="why_title_en[]" value="{{ $sections['why_choose_us'][$i]['title_en'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Why Title AR</label><input class="form-control text-end" dir="rtl" name="why_title_ar[]" value="{{ $sections['why_choose_us'][$i]['title_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Why Text EN</label><textarea class="form-control" name="why_text_en[]" rows="2">{{ $sections['why_choose_us'][$i]['text_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Why Text AR</label><textarea class="form-control text-end" dir="rtl" name="why_text_ar[]" rows="2">{{ $sections['why_choose_us'][$i]['text_ar'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Step Title EN</label><input class="form-control" name="steps_title_en[]" value="{{ $sections['how_it_works'][$i]['title_en'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Step Title AR</label><input class="form-control text-end" dir="rtl" name="steps_title_ar[]" value="{{ $sections['how_it_works'][$i]['title_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Step Text EN</label><textarea class="form-control" name="steps_text_en[]" rows="2">{{ $sections['how_it_works'][$i]['text_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Step Text AR</label><textarea class="form-control text-end" dir="rtl" name="steps_text_ar[]" rows="2">{{ $sections['how_it_works'][$i]['text_ar'] ?? '' }}</textarea></div>
                </div>
            @endfor
        </div>
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Promo, Inquiry, and Final CTA</h2>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Promo Title EN</label><input class="form-control" name="promo_title_en" value="{{ $sections['promo']['title_en'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Promo Title AR</label><input class="form-control text-end" dir="rtl" name="promo_title_ar" value="{{ $sections['promo']['title_ar'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Promo Text EN</label><textarea class="form-control" name="promo_text_en" rows="2">{{ $sections['promo']['text_en'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Promo Text AR</label><textarea class="form-control text-end" dir="rtl" name="promo_text_ar" rows="2">{{ $sections['promo']['text_ar'] ?? '' }}</textarea></div>
                <div class="col-md-4"><label class="form-label">Promo Button EN</label><input class="form-control" name="promo_button_en" value="{{ $sections['promo']['button_en'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Promo Button AR</label><input class="form-control text-end" dir="rtl" name="promo_button_ar" value="{{ $sections['promo']['button_ar'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Promo URL</label><input class="form-control" name="promo_url" value="{{ $sections['promo']['url'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Inquiry Title EN</label><input class="form-control" name="inquiry_title_en" value="{{ $sections['inquiry']['title_en'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Inquiry Title AR</label><input class="form-control text-end" dir="rtl" name="inquiry_title_ar" value="{{ $sections['inquiry']['title_ar'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Inquiry Text EN</label><textarea class="form-control" name="inquiry_text_en" rows="2">{{ $sections['inquiry']['text_en'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Inquiry Text AR</label><textarea class="form-control text-end" dir="rtl" name="inquiry_text_ar" rows="2">{{ $sections['inquiry']['text_ar'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Final CTA Title EN</label><input class="form-control" name="final_cta_title_en" value="{{ $sections['final_cta']['title_en'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Final CTA Title AR</label><input class="form-control text-end" dir="rtl" name="final_cta_title_ar" value="{{ $sections['final_cta']['title_ar'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Final CTA Text EN</label><textarea class="form-control" name="final_cta_text_en" rows="2">{{ $sections['final_cta']['text_en'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Final CTA Text AR</label><textarea class="form-control text-end" dir="rtl" name="final_cta_text_ar" rows="2">{{ $sections['final_cta']['text_ar'] ?? '' }}</textarea></div>
                <div class="col-md-4"><label class="form-label">Final CTA Button EN</label><input class="form-control" name="final_cta_button_en" value="{{ $sections['final_cta']['button_en'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Final CTA Button AR</label><input class="form-control text-end" dir="rtl" name="final_cta_button_ar" value="{{ $sections['final_cta']['button_ar'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Final CTA URL</label><input class="form-control" name="final_cta_url" value="{{ $sections['final_cta']['url'] ?? '' }}"></div>
            </div>
        </div>
    @else
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Feature Blocks, FAQs, and CTA</h2>
            @for($i = 0; $i < 5; $i++)
                <div class="row g-3 mb-4 border-bottom pb-3">
                    <div class="col-md-6"><label class="form-label">Feature Title EN</label><input class="form-control" name="feature_title_en[]" value="{{ $sections['feature_blocks'][$i]['title_en'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Feature Title AR</label><input class="form-control text-end" dir="rtl" name="feature_title_ar[]" value="{{ $sections['feature_blocks'][$i]['title_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Feature Text EN</label><textarea class="form-control" name="feature_text_en[]" rows="2">{{ $sections['feature_blocks'][$i]['text_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Feature Text AR</label><textarea class="form-control text-end" dir="rtl" name="feature_text_ar[]" rows="2">{{ $sections['feature_blocks'][$i]['text_ar'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">FAQ Question EN</label><input class="form-control" name="faq_question_en[]" value="{{ $sections['faqs'][$i]['question_en'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">FAQ Question AR</label><input class="form-control text-end" dir="rtl" name="faq_question_ar[]" value="{{ $sections['faqs'][$i]['question_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">FAQ Answer EN</label><textarea class="form-control" name="faq_answer_en[]" rows="2">{{ $sections['faqs'][$i]['answer_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">FAQ Answer AR</label><textarea class="form-control text-end" dir="rtl" name="faq_answer_ar[]" rows="2">{{ $sections['faqs'][$i]['answer_ar'] ?? '' }}</textarea></div>
                </div>
            @endfor
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">CTA Title EN</label><input class="form-control" name="cta_title_en" value="{{ $sections['cta']['title_en'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">CTA Title AR</label><input class="form-control text-end" dir="rtl" name="cta_title_ar" value="{{ $sections['cta']['title_ar'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">CTA Text EN</label><textarea class="form-control" name="cta_text_en" rows="2">{{ $sections['cta']['text_en'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">CTA Text AR</label><textarea class="form-control text-end" dir="rtl" name="cta_text_ar" rows="2">{{ $sections['cta']['text_ar'] ?? '' }}</textarea></div>
                <div class="col-md-4"><label class="form-label">CTA Button EN</label><input class="form-control" name="cta_button_en" value="{{ $sections['cta']['button_en'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">CTA Button AR</label><input class="form-control text-end" dir="rtl" name="cta_button_ar" value="{{ $sections['cta']['button_ar'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">CTA URL</label><input class="form-control" name="cta_url" value="{{ $sections['cta']['url'] ?? '' }}"></div>
            </div>
        </div>
    @endif

    <button class="btn btn-primary">Save Page</button>
</form>
@endsection
