@php
    $featuredItems = old('featured_items', $sections['featured_section']['items'] ?? []);
    $featureItems = old('feature_items', $sections['features_section']['items'] ?? []);
    $cardItems = old('card_items', $sections['cards_section']['items'] ?? []);
    $stepItems = old('step_section_items', $sections['steps_section']['items'] ?? []);
    $gridItems = old('grid_items', $sections['grid_section']['items'] ?? []);
    $quickInfoItems = old('quick_info_items', $sections['quick_info_section']['items'] ?? []);
    $faqItems = old('service_faq_items', $sections['faq_section']['items'] ?? []);
    $ctaButtons = old('service_cta_buttons', $sections['cta_section']['buttons'] ?? []);
@endphp

<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-1">Service Landing Sections</h2>
    <p class="text-muted mb-0">Manage the hardcoded landing blocks for visas, domestic, flights, and hotels.</p>
</div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Featured / Popular Section',
    'description' => 'Headline, supporting copy, and featured cards.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'featured_eyebrow_en', 'value' => old('featured_eyebrow_en', $sections['featured_section']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'featured_eyebrow_ar', 'value' => old('featured_eyebrow_ar', $sections['featured_section']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'featured_title_en', 'value' => old('featured_title_en', $sections['featured_section']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'featured_title_ar', 'value' => old('featured_title_ar', $sections['featured_section']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'sectionTextareas' => [
        ['label' => 'Subtitle EN', 'name' => 'featured_subtitle_en', 'value' => old('featured_subtitle_en', $sections['featured_section']['subtitle_en'] ?? '')],
        ['label' => 'Subtitle AR', 'name' => 'featured_subtitle_ar', 'value' => old('featured_subtitle_ar', $sections['featured_section']['subtitle_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'service-featured',
    'buttonLabel' => 'Add featured card',
    'items' => $featuredItems,
    'fields' => [
        ['label' => 'Title EN', 'key' => 'title_en'],
        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
        ['label' => 'Subtitle EN', 'key' => 'subtitle_en'],
        ['label' => 'Subtitle AR', 'key' => 'subtitle_ar', 'rtl' => true],
        ['label' => 'Meta EN', 'key' => 'meta_en'],
        ['label' => 'Meta AR', 'key' => 'meta_ar', 'rtl' => true],
        ['label' => 'Badge EN', 'key' => 'badge_en'],
        ['label' => 'Badge AR', 'key' => 'badge_ar', 'rtl' => true],
        ['label' => 'Button EN', 'key' => 'button_en'],
        ['label' => 'Button AR', 'key' => 'button_ar', 'rtl' => true],
        ['label' => 'URL', 'key' => 'url'],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'featured_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="featured_enabled" name="featured_enabled" @checked(old('featured_enabled', $sections['featured_section']['enabled'] ?? true))><label class="form-check-label" for="featured_enabled">Show featured section</label></div></div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Feature Cards / Important Points',
    'description' => 'Cards such as highlights, reasons, or important points.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'features_eyebrow_en', 'value' => old('features_eyebrow_en', $sections['features_section']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'features_eyebrow_ar', 'value' => old('features_eyebrow_ar', $sections['features_section']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'features_title_en', 'value' => old('features_title_en', $sections['features_section']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'features_title_ar', 'value' => old('features_title_ar', $sections['features_section']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'sectionTextareas' => [
        ['label' => 'Subtitle EN', 'name' => 'features_subtitle_en', 'value' => old('features_subtitle_en', $sections['features_section']['subtitle_en'] ?? '')],
        ['label' => 'Subtitle AR', 'name' => 'features_subtitle_ar', 'value' => old('features_subtitle_ar', $sections['features_section']['subtitle_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'service-features',
    'buttonLabel' => 'Add feature card',
    'items' => $featureItems,
    'fields' => [
        ['label' => 'Tag', 'key' => 'tag'],
        ['label' => 'Title EN', 'key' => 'title_en'],
        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
        ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
        ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'feature_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="features_enabled" name="features_enabled" @checked(old('features_enabled', $sections['features_section']['enabled'] ?? true))><label class="form-check-label" for="features_enabled">Show feature cards section</label></div></div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Cards / Packages Section',
    'description' => 'Offer cards with meta, price, highlights, and CTA.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'cards_eyebrow_en', 'value' => old('cards_eyebrow_en', $sections['cards_section']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'cards_eyebrow_ar', 'value' => old('cards_eyebrow_ar', $sections['cards_section']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'cards_title_en', 'value' => old('cards_title_en', $sections['cards_section']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'cards_title_ar', 'value' => old('cards_title_ar', $sections['cards_section']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'sectionTextareas' => [
        ['label' => 'Subtitle EN', 'name' => 'cards_subtitle_en', 'value' => old('cards_subtitle_en', $sections['cards_section']['subtitle_en'] ?? '')],
        ['label' => 'Subtitle AR', 'name' => 'cards_subtitle_ar', 'value' => old('cards_subtitle_ar', $sections['cards_section']['subtitle_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'service-cards',
    'buttonLabel' => 'Add card',
    'items' => $cardItems,
    'fields' => [
        ['label' => 'Title EN', 'key' => 'title_en'],
        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
        ['label' => 'Meta EN', 'key' => 'meta_en'],
        ['label' => 'Meta AR', 'key' => 'meta_ar', 'rtl' => true],
        ['label' => 'Price EN', 'key' => 'price_en'],
        ['label' => 'Price AR', 'key' => 'price_ar', 'rtl' => true],
        ['label' => 'Highlights EN (one per line)', 'key' => 'highlights_en', 'type' => 'textarea'],
        ['label' => 'Highlights AR (one per line)', 'key' => 'highlights_ar', 'type' => 'textarea', 'rtl' => true],
        ['label' => 'Button EN', 'key' => 'button_en'],
        ['label' => 'Button AR', 'key' => 'button_ar', 'rtl' => true],
        ['label' => 'URL', 'key' => 'url'],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'card_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="cards_enabled" name="cards_enabled" @checked(old('cards_enabled', $sections['cards_section']['enabled'] ?? true))><label class="form-check-label" for="cards_enabled">Show cards / packages section</label></div></div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Steps Section',
    'description' => 'Step-by-step timeline or process.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'steps_section_eyebrow_en', 'value' => old('steps_section_eyebrow_en', $sections['steps_section']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'steps_section_eyebrow_ar', 'value' => old('steps_section_eyebrow_ar', $sections['steps_section']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'steps_section_title_en', 'value' => old('steps_section_title_en', $sections['steps_section']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'steps_section_title_ar', 'value' => old('steps_section_title_ar', $sections['steps_section']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'sectionTextareas' => [
        ['label' => 'Subtitle EN', 'name' => 'steps_section_subtitle_en', 'value' => old('steps_section_subtitle_en', $sections['steps_section']['subtitle_en'] ?? '')],
        ['label' => 'Subtitle AR', 'name' => 'steps_section_subtitle_ar', 'value' => old('steps_section_subtitle_ar', $sections['steps_section']['subtitle_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'service-steps',
    'buttonLabel' => 'Add step',
    'items' => $stepItems,
    'fields' => [
        ['label' => 'Step Number', 'key' => 'number', 'type' => 'number'],
        ['label' => 'Title EN', 'key' => 'title_en'],
        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
        ['label' => 'Description EN', 'key' => 'description_en', 'type' => 'textarea'],
        ['label' => 'Description AR', 'key' => 'description_ar', 'type' => 'textarea', 'rtl' => true],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'step_section_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="steps_enabled" name="steps_enabled" @checked(old('steps_enabled', $sections['steps_section']['enabled'] ?? true))><label class="form-check-label" for="steps_enabled">Show steps section</label></div></div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Grid / Category Cards',
    'description' => 'Compact cards with chip, text, and link.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'grid_eyebrow_en', 'value' => old('grid_eyebrow_en', $sections['grid_section']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'grid_eyebrow_ar', 'value' => old('grid_eyebrow_ar', $sections['grid_section']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'grid_title_en', 'value' => old('grid_title_en', $sections['grid_section']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'grid_title_ar', 'value' => old('grid_title_ar', $sections['grid_section']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'sectionTextareas' => [
        ['label' => 'Subtitle EN', 'name' => 'grid_subtitle_en', 'value' => old('grid_subtitle_en', $sections['grid_section']['subtitle_en'] ?? '')],
        ['label' => 'Subtitle AR', 'name' => 'grid_subtitle_ar', 'value' => old('grid_subtitle_ar', $sections['grid_section']['subtitle_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'service-grid',
    'buttonLabel' => 'Add grid card',
    'items' => $gridItems,
    'fields' => [
        ['label' => 'Title EN', 'key' => 'title_en'],
        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
        ['label' => 'Chip EN', 'key' => 'chip_en'],
        ['label' => 'Chip AR', 'key' => 'chip_ar', 'rtl' => true],
        ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
        ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
        ['label' => 'URL', 'key' => 'url'],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'grid_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="grid_enabled" name="grid_enabled" @checked(old('grid_enabled', $sections['grid_section']['enabled'] ?? true))><label class="form-check-label" for="grid_enabled">Show grid section</label></div></div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Info Cards Section',
    'description' => 'Small summary cards such as best time, fees, or processing notes.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'quick_info_eyebrow_en', 'value' => old('quick_info_eyebrow_en', $sections['quick_info_section']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'quick_info_eyebrow_ar', 'value' => old('quick_info_eyebrow_ar', $sections['quick_info_section']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'quick_info_title_en', 'value' => old('quick_info_title_en', $sections['quick_info_section']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'quick_info_title_ar', 'value' => old('quick_info_title_ar', $sections['quick_info_section']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'sectionTextareas' => [
        ['label' => 'Subtitle EN', 'name' => 'quick_info_subtitle_en', 'value' => old('quick_info_subtitle_en', $sections['quick_info_section']['subtitle_en'] ?? '')],
        ['label' => 'Subtitle AR', 'name' => 'quick_info_subtitle_ar', 'value' => old('quick_info_subtitle_ar', $sections['quick_info_section']['subtitle_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'service-info',
    'buttonLabel' => 'Add info card',
    'items' => $quickInfoItems,
    'fields' => [
        ['label' => 'Title EN', 'key' => 'title_en'],
        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
        ['label' => 'Value EN', 'key' => 'value_en'],
        ['label' => 'Value AR', 'key' => 'value_ar', 'rtl' => true],
        ['label' => 'Tone', 'key' => 'tone', 'placeholder' => 'navy, royal, amber, slate'],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'quick_info_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="quick_info_enabled" name="quick_info_enabled" @checked(old('quick_info_enabled', $sections['quick_info_section']['enabled'] ?? true))><label class="form-check-label" for="quick_info_enabled">Show info cards section</label></div></div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'FAQ Section',
    'description' => 'Dynamic FAQ for the service landing page.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'faq_section_eyebrow_en', 'value' => old('faq_section_eyebrow_en', $sections['faq_section']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'faq_section_eyebrow_ar', 'value' => old('faq_section_eyebrow_ar', $sections['faq_section']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'faq_section_title_en', 'value' => old('faq_section_title_en', $sections['faq_section']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'faq_section_title_ar', 'value' => old('faq_section_title_ar', $sections['faq_section']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'service-faq',
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
    'inputName' => 'service_faq_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="faq_enabled" name="faq_enabled" @checked(old('faq_enabled', $sections['faq_section']['enabled'] ?? true))><label class="form-check-label" for="faq_enabled">Show FAQ section</label></div></div>

<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">Call To Action Section</h2>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" value="1" id="service_cta_enabled" name="service_cta_enabled" @checked(old('service_cta_enabled', $sections['cta_section']['enabled'] ?? true))><label class="form-check-label" for="service_cta_enabled">Show CTA section</label></div>
            <label class="form-label">Eyebrow EN</label>
            <input class="form-control mb-3" name="service_cta_eyebrow_en" value="{{ old('service_cta_eyebrow_en', $sections['cta_section']['eyebrow_en'] ?? '') }}">
            <label class="form-label">Eyebrow AR</label>
            <input class="form-control text-end mb-3" dir="rtl" name="service_cta_eyebrow_ar" value="{{ old('service_cta_eyebrow_ar', $sections['cta_section']['eyebrow_ar'] ?? '') }}">
            <label class="form-label">Title EN</label>
            <input class="form-control mb-3" name="service_cta_title_en" value="{{ old('service_cta_title_en', $sections['cta_section']['title_en'] ?? '') }}">
            <label class="form-label">Title AR</label>
            <input class="form-control text-end mb-3" dir="rtl" name="service_cta_title_ar" value="{{ old('service_cta_title_ar', $sections['cta_section']['title_ar'] ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Description EN</label>
            <textarea class="form-control mb-3" rows="4" name="service_cta_description_en">{{ old('service_cta_description_en', $sections['cta_section']['description_en'] ?? '') }}</textarea>
            <label class="form-label">Description AR</label>
            <textarea class="form-control text-end mb-3" dir="rtl" rows="4" name="service_cta_description_ar">{{ old('service_cta_description_ar', $sections['cta_section']['description_ar'] ?? '') }}</textarea>
            <label class="form-label">Background Image</label>
            <input class="form-control" type="file" name="service_cta_background_image" accept="image/*">
            @if(!empty($sections['cta_section']['background_image']))
                <img src="{{ asset('storage/' . $sections['cta_section']['background_image']) }}" alt="" class="img-fluid rounded mt-3 border">
            @endif
        </div>
    </div>
</div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'CTA Buttons',
    'description' => 'Buttons shown in the final CTA block.',
    'repeaterKey' => 'service-cta-buttons',
    'buttonLabel' => 'Add CTA button',
    'items' => $ctaButtons,
    'fields' => [
        ['label' => 'Text EN', 'key' => 'text_en'],
        ['label' => 'Text AR', 'key' => 'text_ar', 'rtl' => true],
        ['label' => 'URL', 'key' => 'url'],
        ['label' => 'Variant', 'key' => 'variant', 'placeholder' => 'primary or outline'],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'service_cta_buttons',
])
