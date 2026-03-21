@extends('layouts.admin')

@php
    $whyChooseItems = old('why_choose_items', $item->why_choose_items ?: collect($item->services ?? [])->map(fn ($service, $index) => ['title_en' => $service['text_en'] ?? '', 'title_ar' => $service['text_ar'] ?? '', 'description_en' => '', 'description_ar' => '', 'icon' => '', 'sort_order' => $index + 1, 'is_active' => true])->all());
    $documentItems = old('document_items', $item->document_items ?: collect($item->required_documents ?? [])->map(fn ($document, $index) => ['name_en' => $document['text_en'] ?? '', 'name_ar' => $document['text_ar'] ?? '', 'description_en' => '', 'description_ar' => '', 'sort_order' => $index + 1, 'is_active' => true])->all());
    $stepItems = old('step_items', $item->step_items ?: collect($item->application_steps ?? [])->map(fn ($step, $index) => ['title_en' => $step['text_en'] ?? '', 'title_ar' => $step['text_ar'] ?? '', 'description_en' => '', 'description_ar' => '', 'step_number' => $index + 1, 'sort_order' => $index + 1, 'is_active' => true])->all());
    $feeItems = old('fee_items', $item->fee_items ?: []);
    $faqItems = old('faq_items', $item->faqs ?: []);
    $quickSummaryItems = old('quick_summary_items', $item->quick_summary_items ?: []);
    $visibleFields = old('inquiry_form_visible_fields', $item->inquiry_form_visible_fields ?: ['full_name', 'phone', 'whatsapp_number', 'email', 'service_type', 'destination', 'travel_date', 'message']);
    $detailHighlightsEn = old('highlights_en', collect($item->highlights ?? [])->pluck('text_en')->all());
    $detailHighlightsAr = old('highlights_ar', collect($item->highlights ?? [])->pluck('text_ar')->all());
    $introPointsEn = old('introduction_points_en', collect($item->introduction_points ?? [])->pluck('text_en')->all());
    $introPointsAr = old('introduction_points_ar', collect($item->introduction_points ?? [])->pluck('text_ar')->all());
@endphp

@section('page_title', $item->exists ? 'Edit Visa Country Template' : 'Create Visa Country Template')
@section('page_description', 'Reusable bilingual visa page editor with hero, quick facts, repeatable trust blocks, FAQs, inquiry settings, and SEO.')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.visa-countries.update', $item) : route('admin.visa-countries.store') }}">
    @csrf
    @if($item->exists)
        @method('PUT')
    @endif

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">Core Settings</h2>
                <p class="text-muted mb-0">Country identity, slug, publish state, and sort order.</p>
            </div>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Published</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $item->is_featured))>
                    <label class="form-check-label" for="is_featured">Featured</label>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Visa Category</label>
                <select class="form-select" name="visa_category_id" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('visa_category_id', $item->visa_category_id) == $category->id)>{{ $category->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Country Name EN</label>
                <input class="form-control" name="name_en" value="{{ old('name_en', $item->name_en) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Country Name AR</label>
                <input class="form-control text-end" dir="rtl" name="name_ar" value="{{ old('name_ar', $item->name_ar) }}" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Slug</label>
                <input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Hero Section</h2>
        <div class="row g-3">
            <div class="col-lg-4">
                <label class="form-label">Desktop Hero Image</label>
                <input type="file" class="form-control" name="hero_image" accept="image/*">
                @if($item->hero_image)
                    <img src="{{ asset('storage/' . $item->hero_image) }}" alt="" class="img-fluid rounded mt-3 border">
                @endif
            </div>
            <div class="col-lg-4">
                <label class="form-label">Mobile Hero Image</label>
                <input type="file" class="form-control" name="hero_mobile_image" accept="image/*">
                @if($item->hero_mobile_image)
                    <img src="{{ asset('storage/' . $item->hero_mobile_image) }}" alt="" class="img-fluid rounded mt-3 border">
                @endif
            </div>
            <div class="col-lg-4">
                <label class="form-label">Flag Image</label>
                <input type="file" class="form-control" name="flag_image" accept="image/*">
                @if($item->flag_image)
                    <img src="{{ asset('storage/' . $item->flag_image) }}" alt="" class="img-fluid rounded mt-3 border" style="max-height: 140px;">
                @endif
            </div>
            <div class="col-lg-4">
                <label class="form-label">Hero Overlay Opacity</label>
                <input class="form-control" type="number" min="0" max="0.95" step="0.05" name="hero_overlay_opacity" value="{{ old('hero_overlay_opacity', $item->hero_overlay_opacity ?? 0.45) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero Badge EN</label>
                <input class="form-control" name="hero_badge_en" value="{{ old('hero_badge_en', $item->hero_badge_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero Badge AR</label>
                <input class="form-control text-end" dir="rtl" name="hero_badge_ar" value="{{ old('hero_badge_ar', $item->hero_badge_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero Title EN</label>
                <input class="form-control" name="hero_title_en" value="{{ old('hero_title_en', $item->hero_title_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero Title AR</label>
                <input class="form-control text-end" dir="rtl" name="hero_title_ar" value="{{ old('hero_title_ar', $item->hero_title_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero Subtitle EN</label>
                <textarea class="form-control" rows="4" name="hero_subtitle_en">{{ old('hero_subtitle_en', $item->hero_subtitle_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero Subtitle AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="4" name="hero_subtitle_ar">{{ old('hero_subtitle_ar', $item->hero_subtitle_ar) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Hero CTA Text EN</label>
                <input class="form-control" name="hero_cta_text_en" value="{{ old('hero_cta_text_en', $item->hero_cta_text_en) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Hero CTA Text AR</label>
                <input class="form-control text-end" dir="rtl" name="hero_cta_text_ar" value="{{ old('hero_cta_text_ar', $item->hero_cta_text_ar) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Hero CTA Link</label>
                <input class="form-control" name="hero_cta_url" value="{{ old('hero_cta_url', $item->hero_cta_url) }}">
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Quick Summary Cards</h2>
                <p class="text-muted mb-0">Editable summary cards shown directly under the hero.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="quick-summary">Add summary card</button>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-3"><label class="form-label">Visa Type EN</label><input class="form-control" name="visa_type_en" value="{{ old('visa_type_en', $item->visa_type_en) }}"></div>
            <div class="col-md-3"><label class="form-label">Visa Type AR</label><input class="form-control text-end" dir="rtl" name="visa_type_ar" value="{{ old('visa_type_ar', $item->visa_type_ar) }}"></div>
            <div class="col-md-3"><label class="form-label">Stay Duration EN</label><input class="form-control" name="stay_duration_en" value="{{ old('stay_duration_en', $item->stay_duration_en) }}"></div>
            <div class="col-md-3"><label class="form-label">Stay Duration AR</label><input class="form-control text-end" dir="rtl" name="stay_duration_ar" value="{{ old('stay_duration_ar', $item->stay_duration_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Processing Time EN</label><textarea class="form-control" rows="3" name="processing_time_en">{{ old('processing_time_en', $item->processing_time_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Processing Time AR</label><textarea class="form-control text-end" dir="rtl" rows="3" name="processing_time_ar">{{ old('processing_time_ar', $item->processing_time_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Approximate Fees EN</label><textarea class="form-control" rows="3" name="fees_en">{{ old('fees_en', $item->fees_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Approximate Fees AR</label><textarea class="form-control text-end" dir="rtl" rows="3" name="fees_ar">{{ old('fees_ar', $item->fees_ar) }}</textarea></div>
        </div>
        <div class="row gy-3" data-repeater-list="quick-summary">
            @foreach($quickSummaryItems as $index => $summary)
                <div class="col-12" data-repeater-item>
                    <div class="border rounded-4 p-3">
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">Card Title EN</label><input class="form-control" data-field="title_en" name="quick_summary_items[{{ $index }}][title_en]" value="{{ $summary['title_en'] ?? '' }}"></div>
                            <div class="col-md-3"><label class="form-label">Card Title AR</label><input class="form-control text-end" dir="rtl" data-field="title_ar" name="quick_summary_items[{{ $index }}][title_ar]" value="{{ $summary['title_ar'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label">Value EN</label><input class="form-control" data-field="value_en" name="quick_summary_items[{{ $index }}][value_en]" value="{{ $summary['value_en'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label">Value AR</label><input class="form-control text-end" dir="rtl" data-field="value_ar" name="quick_summary_items[{{ $index }}][value_ar]" value="{{ $summary['value_ar'] ?? '' }}"></div>
                            <div class="col-md-1"><label class="form-label">Icon</label><input class="form-control" data-field="icon" name="quick_summary_items[{{ $index }}][icon]" value="{{ $summary['icon'] ?? '' }}"></div>
                            <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" type="number" data-field="sort_order" name="quick_summary_items[{{ $index }}][sort_order]" value="{{ $summary['sort_order'] ?? ($index + 1) }}"></div>
                            <div class="col-md-2"><div class="form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" data-field="is_active" name="quick_summary_items[{{ $index }}][is_active]" value="1" @checked($summary['is_active'] ?? true)><label class="form-check-label">Active</label></div></div>
                            <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Overview and Detailed Explanation</h2>
        <div class="row g-3">
            <div class="col-lg-4">
                <label class="form-label">Intro Section Image</label>
                <input type="file" class="form-control" name="intro_image" accept="image/*">
                @if($item->intro_image)
                    <img src="{{ asset('storage/' . $item->intro_image) }}" alt="" class="img-fluid rounded mt-3 border">
                @endif
            </div>
            <div class="col-lg-8">
                <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Introduction Title EN</label>
                <input class="form-control" name="introduction_title_en" value="{{ old('introduction_title_en', $item->introduction_title_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Introduction Title AR</label>
                <input class="form-control text-end" dir="rtl" name="introduction_title_ar" value="{{ old('introduction_title_ar', $item->introduction_title_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Short Overview EN</label>
                <textarea class="form-control" rows="5" name="overview_en">{{ old('overview_en', $item->overview_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Short Overview AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="5" name="overview_ar">{{ old('overview_ar', $item->overview_ar) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Intro Badge EN</label>
                <input class="form-control" name="introduction_badge_en" value="{{ old('introduction_badge_en', $item->introduction_badge_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Intro Badge AR</label>
                <input class="form-control text-end" dir="rtl" name="introduction_badge_ar" value="{{ old('introduction_badge_ar', $item->introduction_badge_ar) }}">
            </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Detailed Section Title EN</label>
                <input class="form-control" name="detailed_title_en" value="{{ old('detailed_title_en', $item->detailed_title_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Detailed Section Title AR</label>
                <input class="form-control text-end" dir="rtl" name="detailed_title_ar" value="{{ old('detailed_title_ar', $item->detailed_title_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Detailed Explanation EN</label>
                <textarea class="form-control" rows="7" name="detailed_description_en">{{ old('detailed_description_en', $item->detailed_description_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Detailed Explanation AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="7" name="detailed_description_ar">{{ old('detailed_description_ar', $item->detailed_description_ar) }}</textarea>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <div>
                <h3 class="h6 mb-1">Intro Bullet Points</h3>
                <p class="text-muted mb-0">Short bullets shown beside the intro text and image.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="intro-points">Add bullet</button>
        </div>
        <div class="row gy-3" data-repeater-list="intro-points">
            @foreach($introPointsEn as $index => $pointEn)
                <div class="col-12" data-repeater-item>
                    <div class="border rounded-4 p-3">
                        <div class="row g-3">
                            <div class="col-md-5"><label class="form-label">Bullet EN</label><input class="form-control" name="introduction_points_en[]" value="{{ $pointEn }}"></div>
                            <div class="col-md-5"><label class="form-label">Bullet AR</label><input class="form-control text-end" dir="rtl" name="introduction_points_ar[]" value="{{ $introPointsAr[$index] ?? '' }}"></div>
                            <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <div>
                <h3 class="h6 mb-1">Helpful Guidance Points</h3>
                <p class="text-muted mb-0">Optional short bullets under the detailed explanation.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="detail-highlights">Add point</button>
        </div>
        <div class="row gy-3" data-repeater-list="detail-highlights">
            @foreach($detailHighlightsEn as $index => $pointEn)
                <div class="col-12" data-repeater-item>
                    <div class="border rounded-4 p-3">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Point EN</label>
                                <input class="form-control" name="highlights_en[]" value="{{ $pointEn }}">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Point AR</label>
                                <input class="form-control text-end" dir="rtl" name="highlights_ar[]" value="{{ $detailHighlightsAr[$index] ?? '' }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @include('admin.visa-countries.partials.repeater-card', ['title' => 'Why Choose Travel Wave', 'description' => 'Trust-building cards with icons, descriptions, and ordering.', 'sectionFields' => [['label' => 'Section Title EN', 'name' => 'why_choose_title_en', 'value' => old('why_choose_title_en', $item->why_choose_title_en)], ['label' => 'Section Title AR', 'name' => 'why_choose_title_ar', 'value' => old('why_choose_title_ar', $item->why_choose_title_ar), 'rtl' => true]], 'sectionTextareas' => [['label' => 'Section Intro EN', 'name' => 'why_choose_intro_en', 'value' => old('why_choose_intro_en', $item->why_choose_intro_en)], ['label' => 'Section Intro AR', 'name' => 'why_choose_intro_ar', 'value' => old('why_choose_intro_ar', $item->why_choose_intro_ar), 'rtl' => true]], 'repeaterKey' => 'why-choose', 'buttonLabel' => 'Add card', 'items' => $whyChooseItems, 'fields' => [['label' => 'Title EN', 'key' => 'title_en'], ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true], ['label' => 'Description EN', 'key' => 'description_en', 'type' => 'textarea'], ['label' => 'Description AR', 'key' => 'description_ar', 'type' => 'textarea', 'rtl' => true], ['label' => 'Icon Keyword', 'key' => 'icon', 'placeholder' => 'shield, file, calendar, support'], ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'], ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox']], 'inputName' => 'why_choose_items'])
    @include('admin.visa-countries.partials.repeater-card', ['title' => 'Required Documents', 'description' => 'Document checklist items with optional descriptions.', 'sectionFields' => [['label' => 'Section Title EN', 'name' => 'documents_title_en', 'value' => old('documents_title_en', $item->documents_title_en)], ['label' => 'Section Title AR', 'name' => 'documents_title_ar', 'value' => old('documents_title_ar', $item->documents_title_ar), 'rtl' => true]], 'sectionTextareas' => [['label' => 'Section Subtitle EN', 'name' => 'documents_subtitle_en', 'value' => old('documents_subtitle_en', $item->documents_subtitle_en)], ['label' => 'Section Subtitle AR', 'name' => 'documents_subtitle_ar', 'value' => old('documents_subtitle_ar', $item->documents_subtitle_ar), 'rtl' => true]], 'repeaterKey' => 'documents', 'buttonLabel' => 'Add document', 'items' => $documentItems, 'fields' => [['label' => 'Document Name EN', 'key' => 'name_en'], ['label' => 'Document Name AR', 'key' => 'name_ar', 'rtl' => true], ['label' => 'Description EN', 'key' => 'description_en', 'type' => 'textarea'], ['label' => 'Description AR', 'key' => 'description_ar', 'type' => 'textarea', 'rtl' => true], ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'], ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox']], 'inputName' => 'document_items'])
    @include('admin.visa-countries.partials.repeater-card', ['title' => 'Application Steps', 'description' => 'Step-by-step process with descriptions and numbering.', 'sectionFields' => [['label' => 'Section Title EN', 'name' => 'steps_title_en', 'value' => old('steps_title_en', $item->steps_title_en)], ['label' => 'Section Title AR', 'name' => 'steps_title_ar', 'value' => old('steps_title_ar', $item->steps_title_ar), 'rtl' => true]], 'repeaterKey' => 'steps', 'buttonLabel' => 'Add step', 'items' => $stepItems, 'fields' => [['label' => 'Step Title EN', 'key' => 'title_en'], ['label' => 'Step Title AR', 'key' => 'title_ar', 'rtl' => true], ['label' => 'Description EN', 'key' => 'description_en', 'type' => 'textarea'], ['label' => 'Description AR', 'key' => 'description_ar', 'type' => 'textarea', 'rtl' => true], ['label' => 'Step Number', 'key' => 'step_number', 'type' => 'number'], ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'], ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox']], 'inputName' => 'step_items'])
    @include('admin.visa-countries.partials.repeater-card', ['title' => 'FAQ Section', 'description' => 'Accordion items for common visa questions.', 'sectionFields' => [['label' => 'Section Title EN', 'name' => 'faq_title_en', 'value' => old('faq_title_en', $item->faq_title_en)], ['label' => 'Section Title AR', 'name' => 'faq_title_ar', 'value' => old('faq_title_ar', $item->faq_title_ar), 'rtl' => true]], 'repeaterKey' => 'faq', 'buttonLabel' => 'Add FAQ', 'items' => $faqItems, 'fields' => [['label' => 'Question EN', 'key' => 'question_en'], ['label' => 'Question AR', 'key' => 'question_ar', 'rtl' => true], ['label' => 'Answer EN', 'key' => 'answer_en', 'type' => 'textarea'], ['label' => 'Answer AR', 'key' => 'answer_ar', 'type' => 'textarea', 'rtl' => true], ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'], ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox']], 'inputName' => 'faq_items'])

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Fees, Map, Inquiry, and Final CTA</h2>
                <p class="text-muted mb-0">Conversion-focused blocks and practical page settings.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="fees">Add fee item</button>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Fees Section Title EN</label>
                <input class="form-control" name="fees_title_en" value="{{ old('fees_title_en', $item->fees_title_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Fees Section Title AR</label>
                <input class="form-control text-end" dir="rtl" name="fees_title_ar" value="{{ old('fees_title_ar', $item->fees_title_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Fees Notes EN</label>
                <textarea class="form-control" rows="4" name="fees_notes_en">{{ old('fees_notes_en', $item->fees_notes_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Fees Notes AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="4" name="fees_notes_ar">{{ old('fees_notes_ar', $item->fees_notes_ar) }}</textarea>
            </div>
        </div>
        <div class="row gy-3 mb-4" data-repeater-list="fees">
            @foreach($feeItems as $index => $fee)
                <div class="col-12" data-repeater-item>
                    <div class="border rounded-4 p-3">
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">Label EN</label><input class="form-control" data-field="label_en" name="fee_items[{{ $index }}][label_en]" value="{{ $fee['label_en'] ?? '' }}"></div>
                            <div class="col-md-3"><label class="form-label">Label AR</label><input class="form-control text-end" dir="rtl" data-field="label_ar" name="fee_items[{{ $index }}][label_ar]" value="{{ $fee['label_ar'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label">Value EN</label><input class="form-control" data-field="value_en" name="fee_items[{{ $index }}][value_en]" value="{{ $fee['value_en'] ?? '' }}"></div>
                            <div class="col-md-2"><label class="form-label">Value AR</label><input class="form-control text-end" dir="rtl" data-field="value_ar" name="fee_items[{{ $index }}][value_ar]" value="{{ $fee['value_ar'] ?? '' }}"></div>
                            <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" type="number" data-field="sort_order" name="fee_items[{{ $index }}][sort_order]" value="{{ $fee['sort_order'] ?? ($index + 1) }}"></div>
                            <div class="col-md-1 d-flex flex-column justify-content-end">
                                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" data-field="is_active" name="fee_items[{{ $index }}][is_active]" value="1" @checked($fee['is_active'] ?? true)><label class="form-check-label">Active</label></div>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-repeater-remove>Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="map_is_active" value="1" id="map_is_active" @checked(old('map_is_active', $item->map_is_active ?? true))><label class="form-check-label" for="map_is_active">Show map section</label></div>
                <label class="form-label">Map Title EN</label><input class="form-control mb-3" name="map_title_en" value="{{ old('map_title_en', $item->map_title_en) }}">
                <label class="form-label">Map Title AR</label><input class="form-control text-end mb-3" dir="rtl" name="map_title_ar" value="{{ old('map_title_ar', $item->map_title_ar) }}">
                <label class="form-label">Map Description EN</label><textarea class="form-control mb-3" rows="3" name="map_description_en">{{ old('map_description_en', $item->map_description_en) }}</textarea>
                <label class="form-label">Map Description AR</label><textarea class="form-control text-end" dir="rtl" rows="3" name="map_description_ar">{{ old('map_description_ar', $item->map_description_ar) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Map Embed Code</label>
                <textarea class="form-control" rows="12" name="map_embed_code">{{ old('map_embed_code', $item->map_embed_code) }}</textarea>
            </div>
            <div class="col-md-6">
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="inquiry_form_is_active" value="1" id="inquiry_form_is_active" @checked(old('inquiry_form_is_active', $item->inquiry_form_is_active ?? true))><label class="form-check-label" for="inquiry_form_is_active">Show inquiry form</label></div>
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="support_is_active" value="1" id="support_is_active" @checked(old('support_is_active', $item->support_is_active ?? true))><label class="form-check-label" for="support_is_active">Show support CTA block</label></div>
                <label class="form-label">Support Title EN</label><input class="form-control mb-3" name="support_title_en" value="{{ old('support_title_en', $item->support_title_en) }}">
                <label class="form-label">Support Title AR</label><input class="form-control text-end mb-3" dir="rtl" name="support_title_ar" value="{{ old('support_title_ar', $item->support_title_ar) }}">
                <label class="form-label">Support Subtitle EN</label><textarea class="form-control mb-3" rows="3" name="support_subtitle_en">{{ old('support_subtitle_en', $item->support_subtitle_en) }}</textarea>
                <label class="form-label">Support Subtitle AR</label><textarea class="form-control text-end mb-3" dir="rtl" rows="3" name="support_subtitle_ar">{{ old('support_subtitle_ar', $item->support_subtitle_ar) }}</textarea>
                <label class="form-label">Support Button EN</label><input class="form-control mb-3" name="support_button_en" value="{{ old('support_button_en', $item->support_button_en) }}">
                <label class="form-label">Support Button AR</label><input class="form-control text-end mb-3" dir="rtl" name="support_button_ar" value="{{ old('support_button_ar', $item->support_button_ar) }}">
                <label class="form-label">Support Button Link</label><input class="form-control mb-3" name="support_button_link" value="{{ old('support_button_link', $item->support_button_link) }}">
                <label class="form-label">Inquiry Title EN</label><input class="form-control mb-3" name="inquiry_form_title_en" value="{{ old('inquiry_form_title_en', $item->inquiry_form_title_en) }}">
                <label class="form-label">Inquiry Title AR</label><input class="form-control text-end mb-3" dir="rtl" name="inquiry_form_title_ar" value="{{ old('inquiry_form_title_ar', $item->inquiry_form_title_ar) }}">
                <label class="form-label">Inquiry Section Label EN</label><input class="form-control mb-3" name="inquiry_form_label_en" value="{{ old('inquiry_form_label_en', $item->inquiry_form_label_en) }}">
                <label class="form-label">Inquiry Section Label AR</label><input class="form-control text-end mb-3" dir="rtl" name="inquiry_form_label_ar" value="{{ old('inquiry_form_label_ar', $item->inquiry_form_label_ar) }}">
                <label class="form-label">Inquiry Subtitle EN</label><textarea class="form-control mb-3" rows="3" name="inquiry_form_subtitle_en">{{ old('inquiry_form_subtitle_en', $item->inquiry_form_subtitle_en) }}</textarea>
                <label class="form-label">Inquiry Subtitle AR</label><textarea class="form-control text-end mb-3" dir="rtl" rows="3" name="inquiry_form_subtitle_ar">{{ old('inquiry_form_subtitle_ar', $item->inquiry_form_subtitle_ar) }}</textarea>
                <label class="form-label">Default Service Type</label><input class="form-control mb-3" name="inquiry_form_default_service_type" value="{{ old('inquiry_form_default_service_type', $item->inquiry_form_default_service_type) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Submit Button EN</label><input class="form-control mb-3" name="inquiry_form_button_en" value="{{ old('inquiry_form_button_en', $item->inquiry_form_button_en) }}">
                <label class="form-label">Submit Button AR</label><input class="form-control text-end mb-3" dir="rtl" name="inquiry_form_button_ar" value="{{ old('inquiry_form_button_ar', $item->inquiry_form_button_ar) }}">
                <label class="form-label">Success Message EN</label><textarea class="form-control mb-3" rows="3" name="inquiry_form_success_en">{{ old('inquiry_form_success_en', $item->inquiry_form_success_en) }}</textarea>
                <label class="form-label">Success Message AR</label><textarea class="form-control text-end mb-3" dir="rtl" rows="3" name="inquiry_form_success_ar">{{ old('inquiry_form_success_ar', $item->inquiry_form_success_ar) }}</textarea>
                <label class="form-label d-block">Visible Fields</label>
                <div class="row g-2">
                    @foreach(['full_name' => 'Full Name', 'phone' => 'Phone', 'whatsapp_number' => 'WhatsApp Number', 'email' => 'Email', 'service_type' => 'Visa Type', 'destination' => 'Country', 'travel_date' => 'Travel Date', 'message' => 'Notes / Message'] as $field => $label)
                        <div class="col-sm-6">
                            <div class="form-check border rounded-3 p-3">
                                <input class="form-check-input" type="checkbox" name="inquiry_form_visible_fields[]" id="field_{{ $field }}" value="{{ $field }}" @checked(in_array($field, $visibleFields, true))>
                                <label class="form-check-label fw-semibold" for="field_{{ $field }}">{{ $label }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="final_cta_is_active" value="1" id="final_cta_is_active" @checked(old('final_cta_is_active', $item->final_cta_is_active ?? true))><label class="form-check-label" for="final_cta_is_active">Show final CTA</label></div>
                <label class="form-label">CTA Title EN</label><input class="form-control mb-3" name="cta_title_en" value="{{ old('cta_title_en', $item->cta_title_en) }}">
                <label class="form-label">CTA Title AR</label><input class="form-control text-end mb-3" dir="rtl" name="cta_title_ar" value="{{ old('cta_title_ar', $item->cta_title_ar) }}">
                <label class="form-label">CTA Button EN</label><input class="form-control mb-3" name="cta_button_en" value="{{ old('cta_button_en', $item->cta_button_en) }}">
                <label class="form-label">CTA Button AR</label><input class="form-control text-end" dir="rtl" name="cta_button_ar" value="{{ old('cta_button_ar', $item->cta_button_ar) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">CTA Subtitle EN</label><textarea class="form-control mb-3" rows="4" name="cta_text_en">{{ old('cta_text_en', $item->cta_text_en) }}</textarea>
                <label class="form-label">CTA Subtitle AR</label><textarea class="form-control text-end mb-3" dir="rtl" rows="4" name="cta_text_ar">{{ old('cta_text_ar', $item->cta_text_ar) }}</textarea>
                <label class="form-label">CTA URL</label><input class="form-control" name="cta_url" value="{{ old('cta_url', $item->cta_url) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Final CTA Background</label><input type="file" class="form-control mb-3" name="final_cta_background_image" accept="image/*">
                <label class="form-label">OG Image</label><input type="file" class="form-control mb-3" name="og_image" accept="image/*">
                <label class="form-label">Meta Title EN</label><input class="form-control mb-3" name="meta_title_en" value="{{ old('meta_title_en', $item->meta_title_en) }}">
                <label class="form-label">Meta Title AR</label><input class="form-control text-end" dir="rtl" name="meta_title_ar" value="{{ old('meta_title_ar', $item->meta_title_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Description EN</label><textarea class="form-control" rows="4" name="meta_description_en">{{ old('meta_description_en', $item->meta_description_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Description AR</label><textarea class="form-control text-end" dir="rtl" rows="4" name="meta_description_ar">{{ old('meta_description_ar', $item->meta_description_ar) }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button class="btn btn-primary px-4">Save Visa Page</button>
    </div>
</form>

@include('admin.visa-countries.partials.repeater-templates')
@endsection
