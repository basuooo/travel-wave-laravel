@extends('layouts.admin')

@php
    $quickInfoItems = old('quick_info_items', $item->quick_info_items ?: []);
    $aboutPointsEn = old('about_points_en', collect($item->about_points ?? [])->pluck('text_en')->all());
    $aboutPointsAr = old('about_points_ar', collect($item->about_points ?? [])->pluck('text_ar')->all());
    $highlightItems = old('highlight_items', $item->highlight_items ?: []);
    $serviceItems = old('service_items', $item->service_items ?: []);
    $documentItems = old('document_items', $item->document_items ?: []);
    $stepItems = old('step_items', $item->step_items ?: []);
    $pricingItems = old('pricing_items', $item->pricing_items ?: []);
    $faqItems = old('faq_items', $item->faqs ?: []);
    $visibleFields = old('form_visible_fields', $item->form_visible_fields ?: ['email', 'travel_date', 'travelers_count', 'message']);
@endphp

@section('page_title', $item->exists ? 'Edit Destination Page' : 'Create Destination Page')
@section('page_description', 'Full dynamic destination page editor for domestic tourism and destination-style service pages.')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.destinations.update', $item) : route('admin.destinations.store') }}">
    @csrf
    @if($item->exists)
        @method('PUT')
    @endif

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">Core Settings</h2>
                <p class="text-muted mb-0">Destination identity, type, publish state, slug, and SEO basics.</p>
            </div>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Published</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $item->is_featured))>
                    <label class="form-check-label" for="is_featured">Featured</label>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Destination Type</label>
                <select class="form-select" name="destination_type" required>
                    <option value="domestic" @selected(old('destination_type', $item->destination_type ?: 'domestic') === 'domestic')>Domestic Tourism</option>
                    <option value="visa" @selected(old('destination_type', $item->destination_type) === 'visa')>External Visa Style Page</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Title EN</label>
                <input class="form-control" name="title_en" value="{{ old('title_en', $item->title_en) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Title AR</label>
                <input class="form-control text-end" dir="rtl" name="title_ar" value="{{ old('title_ar', $item->title_ar) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Slug</label>
                <input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle EN</label>
                <input class="form-control" name="subtitle_en" value="{{ old('subtitle_en', $item->subtitle_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle AR</label>
                <input class="form-control text-end" dir="rtl" name="subtitle_ar" value="{{ old('subtitle_ar', $item->subtitle_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Excerpt EN</label>
                <textarea class="form-control" rows="4" name="excerpt_en">{{ old('excerpt_en', $item->excerpt_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Excerpt AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="4" name="excerpt_ar">{{ old('excerpt_ar', $item->excerpt_ar) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Meta Title EN</label>
                <input class="form-control" name="meta_title_en" value="{{ old('meta_title_en', $item->meta_title_en) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Meta Title AR</label>
                <input class="form-control text-end" dir="rtl" name="meta_title_ar" value="{{ old('meta_title_ar', $item->meta_title_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Description EN</label>
                <textarea class="form-control" rows="3" name="meta_description_en">{{ old('meta_description_en', $item->meta_description_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Description AR</label>
                <textarea class="form-control text-end" dir="rtl" rows="3" name="meta_description_ar">{{ old('meta_description_ar', $item->meta_description_ar) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Hero and Media</h2>
        <div class="row g-3">
            <div class="col-lg-4">
                <label class="form-label">Hero Image</label>
                <input type="file" class="form-control" name="hero_image" accept="image/*">
                @if($item->hero_image)
                    <img src="{{ asset('storage/' . $item->hero_image) }}" alt="" class="img-fluid rounded mt-3 border">
                @endif
            </div>
            <div class="col-lg-4">
                <label class="form-label">Hero Mobile Image</label>
                <input type="file" class="form-control" name="hero_mobile_image" accept="image/*">
                @if($item->hero_mobile_image)
                    <img src="{{ asset('storage/' . $item->hero_mobile_image) }}" alt="" class="img-fluid rounded mt-3 border">
                @endif
            </div>
            <div class="col-lg-4">
                <label class="form-label">Flag / Icon Image</label>
                <input type="file" class="form-control" name="flag_image" accept="image/*">
                @if($item->flag_image)
                    <img src="{{ asset('storage/' . $item->flag_image) }}" alt="" class="img-fluid rounded mt-3 border" style="max-height: 140px;">
                @endif
            </div>
            <div class="col-lg-4">
                <label class="form-label">Featured Destination Image</label>
                <input type="file" class="form-control" name="featured_image" accept="image/*">
                @if($item->featured_image)
                    <img src="{{ asset('storage/' . $item->featured_image) }}" alt="" class="img-fluid rounded mt-3 border">
                @endif
            </div>
            <div class="col-lg-4">
                <label class="form-label">About Section Image</label>
                <input type="file" class="form-control" name="about_image" accept="image/*">
                @if($item->about_image)
                    <img src="{{ asset('storage/' . $item->about_image) }}" alt="" class="img-fluid rounded mt-3 border">
                @endif
            </div>
            <div class="col-lg-4">
                <label class="form-label">CTA Background Image</label>
                <input type="file" class="form-control" name="cta_background_image" accept="image/*">
                @if($item->cta_background_image)
                    <img src="{{ asset('storage/' . $item->cta_background_image) }}" alt="" class="img-fluid rounded mt-3 border">
                @endif
            </div>
            <div class="col-md-4">
                <label class="form-label">Hero Overlay Opacity</label>
                <input class="form-control" type="number" step="0.05" min="0" max="0.95" name="hero_overlay_opacity" value="{{ old('hero_overlay_opacity', $item->hero_overlay_opacity ?? 0.45) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Hero Badge EN</label>
                <input class="form-control" name="hero_badge_en" value="{{ old('hero_badge_en', $item->hero_badge_en) }}">
            </div>
            <div class="col-md-4">
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
                <label class="form-label">Primary Button EN</label>
                <input class="form-control" name="hero_cta_text_en" value="{{ old('hero_cta_text_en', $item->hero_cta_text_en) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Primary Button AR</label>
                <input class="form-control text-end" dir="rtl" name="hero_cta_text_ar" value="{{ old('hero_cta_text_ar', $item->hero_cta_text_ar) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Primary Button Link</label>
                <input class="form-control" name="hero_cta_url" value="{{ old('hero_cta_url', $item->hero_cta_url) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Secondary Button EN</label>
                <input class="form-control" name="hero_secondary_cta_text_en" value="{{ old('hero_secondary_cta_text_en', $item->hero_secondary_cta_text_en) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Secondary Button AR</label>
                <input class="form-control text-end" dir="rtl" name="hero_secondary_cta_text_ar" value="{{ old('hero_secondary_cta_text_ar', $item->hero_secondary_cta_text_ar) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Secondary Button Link</label>
                <input class="form-control" name="hero_secondary_cta_url" value="{{ old('hero_secondary_cta_url', $item->hero_secondary_cta_url) }}">
            </div>
        </div>
    </div>

    @include('admin.visa-countries.partials.repeater-card', [
        'title' => 'Quick Info Cards',
        'description' => 'Compact cards for service type, duration, fees, best time, and other key info.',
        'sectionFields' => [
            ['label' => 'Section Title EN', 'name' => 'quick_info_title_en', 'value' => old('quick_info_title_en', $item->quick_info_title_en)],
            ['label' => 'Section Title AR', 'name' => 'quick_info_title_ar', 'value' => old('quick_info_title_ar', $item->quick_info_title_ar), 'rtl' => true],
            ['label' => 'Destination Card Label EN', 'name' => 'quick_summary_destination_label_en', 'value' => old('quick_summary_destination_label_en', $item->quick_summary_destination_label_en)],
            ['label' => 'Destination Card Label AR', 'name' => 'quick_summary_destination_label_ar', 'value' => old('quick_summary_destination_label_ar', $item->quick_summary_destination_label_ar), 'rtl' => true],
            ['label' => 'Destination Card Icon', 'name' => 'quick_summary_destination_icon', 'value' => old('quick_summary_destination_icon', $item->quick_summary_destination_icon), 'placeholder' => 'material-symbols:globe-location-pin-outline'],
        ],
        'repeaterKey' => 'quick-info',
        'buttonLabel' => 'Add info card',
        'items' => $quickInfoItems,
        'fields' => [
            ['label' => 'Label EN', 'key' => 'label_en'],
            ['label' => 'Label AR', 'key' => 'label_ar', 'rtl' => true],
            ['label' => 'Value EN', 'key' => 'value_en'],
            ['label' => 'Value AR', 'key' => 'value_ar', 'rtl' => true],
            ['label' => 'Icon', 'key' => 'icon'],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'quick_info_items',
    ])

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">About, Details, and Best Time</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">About Title EN</label><input class="form-control" name="about_title_en" value="{{ old('about_title_en', $item->about_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">About Title AR</label><input class="form-control text-end" dir="rtl" name="about_title_ar" value="{{ old('about_title_ar', $item->about_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">About Description EN</label><textarea class="form-control" rows="5" name="about_description_en">{{ old('about_description_en', $item->about_description_en ?: $item->overview_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">About Description AR</label><textarea class="form-control text-end" dir="rtl" rows="5" name="about_description_ar">{{ old('about_description_ar', $item->about_description_ar ?: $item->overview_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Detailed Section Title EN</label><input class="form-control" name="detailed_title_en" value="{{ old('detailed_title_en', $item->detailed_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Detailed Section Title AR</label><input class="form-control text-end" dir="rtl" name="detailed_title_ar" value="{{ old('detailed_title_ar', $item->detailed_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Detailed Description EN</label><textarea class="form-control" rows="6" name="detailed_description_en">{{ old('detailed_description_en', $item->detailed_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Detailed Description AR</label><textarea class="form-control text-end" dir="rtl" rows="6" name="detailed_description_ar">{{ old('detailed_description_ar', $item->detailed_description_ar) }}</textarea></div>
            <div class="col-12 pt-2">
                <h3 class="h6 mb-1">Best Time Section / قسم أفضل وقت للزيارة</h3>
                <p class="text-muted mb-0">This side note matches the live page card and is fully editable per destination.</p>
            </div>
            <div class="col-md-6"><label class="form-label">Best Time Badge EN</label><input class="form-control" name="best_time_badge_en" value="{{ old('best_time_badge_en', $item->best_time_badge_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Best Time Badge AR</label><input class="form-control text-end" dir="rtl" name="best_time_badge_ar" value="{{ old('best_time_badge_ar', $item->best_time_badge_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Best Time Title EN</label><input class="form-control" name="best_time_title_en" value="{{ old('best_time_title_en', $item->best_time_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Best Time Title AR</label><input class="form-control text-end" dir="rtl" name="best_time_title_ar" value="{{ old('best_time_title_ar', $item->best_time_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Best Time Description EN</label><textarea class="form-control" rows="4" name="best_time_description_en">{{ old('best_time_description_en', $item->best_time_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Best Time Description AR</label><textarea class="form-control text-end" dir="rtl" rows="4" name="best_time_description_ar">{{ old('best_time_description_ar', $item->best_time_description_ar) }}</textarea></div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <div>
                <h3 class="h6 mb-1">About Bullet Points</h3>
                <p class="text-muted mb-0">Short bullets to support the overview section.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="about-points">Add bullet</button>
        </div>
        <div class="row gy-3" data-repeater-list="about-points">
            @foreach($aboutPointsEn as $index => $pointEn)
                <div class="col-12" data-repeater-item>
                    <div class="border rounded-4 p-3">
                        <div class="row g-3">
                            <div class="col-md-5"><label class="form-label">Point EN</label><input class="form-control" name="about_points_en[]" value="{{ $pointEn }}"></div>
                            <div class="col-md-5"><label class="form-label">Point AR</label><input class="form-control text-end" dir="rtl" name="about_points_ar[]" value="{{ $aboutPointsAr[$index] ?? '' }}"></div>
                            <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Helpful Guidance Points / Highlights</h2>
                <p class="text-muted mb-0">Image cards shown in the highlights section on the live destination page.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="highlights">Add card</button>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Section Label EN</label>
                <input class="form-control" name="highlights_section_label_en" value="{{ old('highlights_section_label_en', $item->highlights_section_label_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Section Label AR</label>
                <input class="form-control text-end" dir="rtl" name="highlights_section_label_ar" value="{{ old('highlights_section_label_ar', $item->highlights_section_label_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Section Title EN</label>
                <input class="form-control" name="highlights_title_en" value="{{ old('highlights_title_en', $item->highlights_title_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Section Title AR</label>
                <input class="form-control text-end" dir="rtl" name="highlights_title_ar" value="{{ old('highlights_title_ar', $item->highlights_title_ar) }}">
            </div>
        </div>

        @php($renderHighlightRows = !empty($highlightItems) ? $highlightItems : [[]])

        <div class="row gy-3" data-repeater-list="highlights">
            @foreach($renderHighlightRows as $index => $row)
                @php($currentImage = $row['image'] ?? '')
                <div class="col-12" data-repeater-item>
                    <div class="border rounded-4 p-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Title EN</label>
                                <input class="form-control" data-field="title_en" name="highlight_items[{{ $index }}][title_en]" value="{{ $row['title_en'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Title AR</label>
                                <input class="form-control text-end" dir="rtl" data-field="title_ar" name="highlight_items[{{ $index }}][title_ar]" value="{{ $row['title_ar'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label d-flex align-items-center gap-2">
                                    <span>Icon</span>
                                    <a href="https://icon-sets.iconify.design/" target="_blank" rel="noopener noreferrer" class="small text-decoration-none" aria-label="Browse Iconify icons"><span aria-hidden="true">&#127760;</span></a>
                                </label>
                                <input class="form-control" data-field="icon" name="highlight_items[{{ $index }}][icon]" value="{{ $row['icon'] ?? '' }}" placeholder="material-symbols:travel-explore-rounded">
                                <div class="form-text">Example: material-symbols:travel-explore-rounded</div>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Order</label>
                                <input class="form-control" data-field="sort_order" type="number" name="highlight_items[{{ $index }}][sort_order]" value="{{ $row['sort_order'] ?? ($index + 1) }}">
                            </div>
                            <div class="col-md-2">
                                <div class="form-check mt-4 pt-2">
                                    <input class="form-check-input" data-field="is_active" type="checkbox" name="highlight_items[{{ $index }}][is_active]" value="1" @checked($row['is_active'] ?? true)>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description EN</label>
                                <textarea class="form-control" data-field="description_en" name="highlight_items[{{ $index }}][description_en]" rows="3">{{ $row['description_en'] ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description AR</label>
                                <textarea class="form-control text-end" dir="rtl" data-field="description_ar" name="highlight_items[{{ $index }}][description_ar]" rows="3">{{ $row['description_ar'] ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Card Image</label>
                                <input class="form-control" data-field="image_file" data-highlight-image-input data-media-target-field="existing_image" data-media-enhanced="1" type="file" name="highlight_items[{{ $index }}][image_file]" accept="image/*">
                                <div class="admin-media-picker">
                                    <div class="admin-media-picker__actions">
                                        <button type="button" class="btn btn-outline-secondary btn-sm js-open-media-library">Select from Library</button>
                                        <span class="admin-media-picker__hint">or upload new</span>
                                    </div>
                                    <div class="admin-media-picker__selected"></div>
                                </div>
                                <input data-field="existing_image" type="hidden" name="highlight_items[{{ $index }}][existing_image]" value="{{ $currentImage }}">
                                <img src="{{ $currentImage ? asset('storage/' . ltrim($currentImage, '/')) : '' }}" alt="" class="img-fluid rounded border mt-3 js-highlight-preview {{ $currentImage ? '' : 'd-none' }}" style="max-height: 160px; object-fit: cover;">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" data-field="remove_image" type="checkbox" name="highlight_items[{{ $index }}][remove_image]" value="1">
                                    <label class="form-check-label">Remove current image</label>
                                </div>
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

    @include('admin.visa-countries.partials.repeater-card', [
        'title' => 'Included Services',
        'description' => 'Travel Wave services included for this destination or visa support page.',
        'sectionFields' => [
            ['label' => 'Section Title EN', 'name' => 'services_title_en', 'value' => old('services_title_en', $item->services_title_en)],
            ['label' => 'Section Title AR', 'name' => 'services_title_ar', 'value' => old('services_title_ar', $item->services_title_ar), 'rtl' => true],
        ],
        'sectionTextareas' => [
            ['label' => 'Section Intro EN', 'name' => 'services_intro_en', 'value' => old('services_intro_en', $item->services_intro_en)],
            ['label' => 'Section Intro AR', 'name' => 'services_intro_ar', 'value' => old('services_intro_ar', $item->services_intro_ar), 'rtl' => true],
        ],
        'repeaterKey' => 'services',
        'buttonLabel' => 'Add service item',
        'items' => $serviceItems,
        'fields' => [
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Description EN', 'key' => 'description_en', 'type' => 'textarea'],
            ['label' => 'Description AR', 'key' => 'description_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Icon', 'key' => 'icon'],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'service_items',
    ])

    @include('admin.visa-countries.partials.repeater-card', [
        'title' => 'Required Documents / Needed Items',
        'description' => 'Use this for visa documents or trip preparation requirements.',
        'sectionFields' => [
            ['label' => 'Section Title EN', 'name' => 'documents_title_en', 'value' => old('documents_title_en', $item->documents_title_en)],
            ['label' => 'Section Title AR', 'name' => 'documents_title_ar', 'value' => old('documents_title_ar', $item->documents_title_ar), 'rtl' => true],
        ],
        'sectionTextareas' => [
            ['label' => 'Section Subtitle EN', 'name' => 'documents_subtitle_en', 'value' => old('documents_subtitle_en', $item->documents_subtitle_en)],
            ['label' => 'Section Subtitle AR', 'name' => 'documents_subtitle_ar', 'value' => old('documents_subtitle_ar', $item->documents_subtitle_ar), 'rtl' => true],
        ],
        'repeaterKey' => 'documents',
        'buttonLabel' => 'Add item',
        'items' => $documentItems,
        'fields' => [
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Description EN', 'key' => 'description_en', 'type' => 'textarea'],
            ['label' => 'Description AR', 'key' => 'description_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Icon', 'key' => 'icon'],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'document_items',
    ])

    @include('admin.visa-countries.partials.repeater-card', [
        'title' => 'Steps Section',
        'description' => 'Step-by-step process for booking, visa support, or program confirmation.',
        'sectionFields' => [
            ['label' => 'Section Title EN', 'name' => 'steps_title_en', 'value' => old('steps_title_en', $item->steps_title_en)],
            ['label' => 'Section Title AR', 'name' => 'steps_title_ar', 'value' => old('steps_title_ar', $item->steps_title_ar), 'rtl' => true],
        ],
        'repeaterKey' => 'steps',
        'buttonLabel' => 'Add step',
        'items' => $stepItems,
        'fields' => [
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Description EN', 'key' => 'description_en', 'type' => 'textarea'],
            ['label' => 'Description AR', 'key' => 'description_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Step Number', 'key' => 'step_number', 'type' => 'number'],
            ['label' => 'Icon', 'key' => 'icon'],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'step_items',
    ])

    @include('admin.visa-countries.partials.repeater-card', [
        'title' => 'Pricing / Fees',
        'description' => 'Visa fees, package notes, or pricing hints.',
        'sectionFields' => [
            ['label' => 'Section Title EN', 'name' => 'pricing_title_en', 'value' => old('pricing_title_en', $item->pricing_title_en)],
            ['label' => 'Section Title AR', 'name' => 'pricing_title_ar', 'value' => old('pricing_title_ar', $item->pricing_title_ar), 'rtl' => true],
        ],
        'sectionTextareas' => [
            ['label' => 'Section Notes EN', 'name' => 'pricing_notes_en', 'value' => old('pricing_notes_en', $item->pricing_notes_en)],
            ['label' => 'Section Notes AR', 'name' => 'pricing_notes_ar', 'value' => old('pricing_notes_ar', $item->pricing_notes_ar), 'rtl' => true],
        ],
        'repeaterKey' => 'pricing',
        'buttonLabel' => 'Add pricing item',
        'items' => $pricingItems,
        'fields' => [
            ['label' => 'Label EN', 'key' => 'label_en'],
            ['label' => 'Label AR', 'key' => 'label_ar', 'rtl' => true],
            ['label' => 'Value EN', 'key' => 'value_en'],
            ['label' => 'Value AR', 'key' => 'value_ar', 'rtl' => true],
            ['label' => 'Note EN', 'key' => 'note_en', 'type' => 'textarea'],
            ['label' => 'Note AR', 'key' => 'note_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'pricing_items',
    ])

    @include('admin.visa-countries.partials.repeater-card', [
        'title' => 'FAQ Section',
        'description' => 'Dynamic frequently asked questions for this destination page.',
        'sectionFields' => [
            ['label' => 'Section Title EN', 'name' => 'faq_title_en', 'value' => old('faq_title_en', $item->faq_title_en)],
            ['label' => 'Section Title AR', 'name' => 'faq_title_ar', 'value' => old('faq_title_ar', $item->faq_title_ar), 'rtl' => true],
        ],
        'repeaterKey' => 'faq',
        'buttonLabel' => 'Add FAQ',
        'items' => $faqItems,
        'fields' => [
            ['label' => 'Question EN', 'key' => 'question_en'],
            ['label' => 'Question AR', 'key' => 'question_ar', 'rtl' => true],
            ['label' => 'Answer EN', 'key' => 'answer_en', 'type' => 'textarea'],
            ['label' => 'Answer AR', 'key' => 'answer_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'faq_items',
    ])

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">CTA and Form Section</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">CTA Title EN</label><input class="form-control" name="cta_title_en" value="{{ old('cta_title_en', $item->cta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">CTA Title AR</label><input class="form-control text-end" dir="rtl" name="cta_title_ar" value="{{ old('cta_title_ar', $item->cta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">CTA Description EN</label><textarea class="form-control" rows="4" name="cta_text_en">{{ old('cta_text_en', $item->cta_text_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">CTA Description AR</label><textarea class="form-control text-end" dir="rtl" rows="4" name="cta_text_ar">{{ old('cta_text_ar', $item->cta_text_ar) }}</textarea></div>
            <div class="col-md-4"><label class="form-label">CTA Button 1 EN</label><input class="form-control" name="cta_button_en" value="{{ old('cta_button_en', $item->cta_button_en) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA Button 1 AR</label><input class="form-control text-end" dir="rtl" name="cta_button_ar" value="{{ old('cta_button_ar', $item->cta_button_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA Button 1 Link</label><input class="form-control" name="cta_url" value="{{ old('cta_url', $item->cta_url) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA Button 2 EN</label><input class="form-control" name="cta_secondary_button_en" value="{{ old('cta_secondary_button_en', $item->cta_secondary_button_en) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA Button 2 AR</label><input class="form-control text-end" dir="rtl" name="cta_secondary_button_ar" value="{{ old('cta_secondary_button_ar', $item->cta_secondary_button_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA Button 2 Link</label><input class="form-control" name="cta_secondary_url" value="{{ old('cta_secondary_url', $item->cta_secondary_url) }}"></div>
            <div class="col-md-6"><label class="form-label">Form Title EN</label><input class="form-control" name="form_title_en" value="{{ old('form_title_en', $item->form_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Form Title AR</label><input class="form-control text-end" dir="rtl" name="form_title_ar" value="{{ old('form_title_ar', $item->form_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Form Subtitle EN</label><textarea class="form-control" rows="3" name="form_subtitle_en">{{ old('form_subtitle_en', $item->form_subtitle_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Form Subtitle AR</label><textarea class="form-control text-end" dir="rtl" rows="3" name="form_subtitle_ar">{{ old('form_subtitle_ar', $item->form_subtitle_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Submit Button EN</label><input class="form-control" name="form_submit_text_en" value="{{ old('form_submit_text_en', $item->form_submit_text_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Submit Button AR</label><input class="form-control text-end" dir="rtl" name="form_submit_text_ar" value="{{ old('form_submit_text_ar', $item->form_submit_text_ar) }}"></div>
        </div>
        <div class="mt-4">
            <label class="form-label d-block">Visible Form Fields</label>
            <div class="row g-2">
                @foreach(['email' => 'Email', 'travel_date' => 'Travel Date', 'return_date' => 'Return Date', 'travelers_count' => 'Travelers Count', 'message' => 'Message / Notes'] as $field => $label)
                    <div class="col-md-4">
                        <div class="form-check border rounded-3 p-3">
                            <input class="form-check-input" type="checkbox" name="form_visible_fields[]" id="form_field_{{ $field }}" value="{{ $field }}" @checked(in_array($field, $visibleFields, true))>
                            <label class="form-check-label fw-semibold" for="form_field_{{ $field }}">{{ $label }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Gallery and Section Visibility</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Gallery Images</label>
                <input class="form-control" type="file" name="gallery_files[]" multiple accept="image/*">
                @if(!empty($item->gallery))
                    <div class="row g-2 mt-3">
                        @foreach($item->gallery as $image)
                            <div class="col-6 col-lg-4">
                                <img src="{{ asset('storage/' . $image) }}" alt="" class="img-fluid rounded border">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label d-block">Section Visibility</label>
                <div class="row g-2">
                    @foreach([
                        'show_hero' => 'Hero',
                        'show_quick_info' => 'Quick Info',
                        'show_about' => 'About',
                        'show_detailed' => 'Detailed Explanation',
                        'show_best_time' => 'Best Time',
                        'show_highlights' => 'Highlights',
                        'show_services' => 'Services',
                        'show_documents' => 'Documents',
                        'show_steps' => 'Steps',
                        'show_pricing' => 'Pricing',
                        'show_faq' => 'FAQ',
                        'show_cta' => 'CTA',
                        'show_form' => 'Contact Form',
                    ] as $field => $label)
                        <div class="col-sm-6">
                            <div class="form-check border rounded-3 p-3">
                                <input class="form-check-input" type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1" @checked(old($field, $item->{$field} ?? true))>
                                <label class="form-check-label fw-semibold" for="{{ $field }}">{{ $label }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button class="btn btn-primary px-4">Save Destination Page</button>
    </div>
</form>

@include('admin.destinations.partials.repeater-templates')
@endsection
