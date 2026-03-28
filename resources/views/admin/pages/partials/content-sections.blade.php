@php
    $storyPoints = old('story_points', $sections['story']['points'] ?? []);
    $missionItems = old('mission_items', $sections['mission']['items'] ?? []);
    $whyChooseItems = old('why_choose_items', $sections['why_choose']['items'] ?? []);
    $serviceItems = old('services_items', $sections['services']['items'] ?? []);
    $statsItems = old('stats_items', $sections['stats']['items'] ?? []);
    $professionalismPoints = old('professionalism_points', $sections['professionalism']['points'] ?? []);
    $contactInfoItems = old('contact_info_items', $sections['contact_info']['items'] ?? []);
    $quickHelpItems = old('quick_help_items', $sections['quick_help']['items'] ?? []);
    $faqItems = old('content_faq_items', $sections['faq']['items'] ?? []);
    $ctaButtons = old('content_cta_buttons', $sections['cta']['buttons'] ?? []);
@endphp

<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-1">Content Page Sections</h2>
    <p class="text-muted mb-0">Manage the structured sections used on About and Contact pages.</p>
</div>

<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">Story Section</h2>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" value="1" id="story_enabled" name="story_enabled" @checked(old('story_enabled', $sections['story']['enabled'] ?? true))><label class="form-check-label" for="story_enabled">Show story section</label></div>
            <label class="form-label">Eyebrow EN</label>
            <input class="form-control mb-3" name="story_eyebrow_en" value="{{ old('story_eyebrow_en', $sections['story']['eyebrow_en'] ?? '') }}">
            <label class="form-label">Eyebrow AR</label>
            <input class="form-control text-end mb-3" dir="rtl" name="story_eyebrow_ar" value="{{ old('story_eyebrow_ar', $sections['story']['eyebrow_ar'] ?? '') }}">
            <label class="form-label">Title EN</label>
            <input class="form-control mb-3" name="story_title_en" value="{{ old('story_title_en', $sections['story']['title_en'] ?? '') }}">
            <label class="form-label">Title AR</label>
            <input class="form-control text-end mb-3" dir="rtl" name="story_title_ar" value="{{ old('story_title_ar', $sections['story']['title_ar'] ?? '') }}">
            <div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="story_reverse" name="story_reverse" @checked(old('story_reverse', $sections['story']['reverse'] ?? false))><label class="form-check-label" for="story_reverse">Reverse desktop layout</label></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Description EN</label>
            <textarea class="form-control mb-3" rows="4" name="story_description_en">{{ old('story_description_en', $sections['story']['description_en'] ?? '') }}</textarea>
            <label class="form-label">Description AR</label>
            <textarea class="form-control text-end mb-3" dir="rtl" rows="4" name="story_description_ar">{{ old('story_description_ar', $sections['story']['description_ar'] ?? '') }}</textarea>
            <label class="form-label">Image</label>
            <input class="form-control" type="file" name="story_image" accept="image/*">
            @if(!empty($sections['story']['image']))
                <img src="{{ asset('storage/' . $sections['story']['image']) }}" alt="" class="img-fluid rounded mt-3 border">
            @endif
        </div>
    </div>
</div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Story Bullet Points',
    'description' => 'Supporting bullets for the story section.',
    'repeaterKey' => 'content-story-points',
    'buttonLabel' => 'Add point',
    'items' => $storyPoints,
    'fields' => [
        ['label' => 'Text EN', 'key' => 'text_en'],
        ['label' => 'Text AR', 'key' => 'text_ar', 'rtl' => true],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'story_points',
])

@foreach([
    ['prefix' => 'mission', 'title' => 'Mission / Values Cards', 'items' => $missionItems],
    ['prefix' => 'why_choose', 'title' => 'Why Choose Section', 'items' => $whyChooseItems],
    ['prefix' => 'services', 'title' => 'Services Cards', 'items' => $serviceItems],
    ['prefix' => 'contact_info', 'title' => 'Contact Info Cards', 'items' => $contactInfoItems],
    ['prefix' => 'quick_help', 'title' => 'Quick Help Cards', 'items' => $quickHelpItems],
] as $group)
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => $group['title'],
        'description' => 'Editable cards with icon, title, meta, text, and optional link.',
        'sectionFields' => [
            ['label' => 'Eyebrow EN', 'name' => $group['prefix'] . '_eyebrow_en', 'value' => old($group['prefix'] . '_eyebrow_en', $sections[$group['prefix']]['eyebrow_en'] ?? '')],
            ['label' => 'Eyebrow AR', 'name' => $group['prefix'] . '_eyebrow_ar', 'value' => old($group['prefix'] . '_eyebrow_ar', $sections[$group['prefix']]['eyebrow_ar'] ?? ''), 'rtl' => true],
            ['label' => 'Title EN', 'name' => $group['prefix'] . '_title_en', 'value' => old($group['prefix'] . '_title_en', $sections[$group['prefix']]['title_en'] ?? '')],
            ['label' => 'Title AR', 'name' => $group['prefix'] . '_title_ar', 'value' => old($group['prefix'] . '_title_ar', $sections[$group['prefix']]['title_ar'] ?? ''), 'rtl' => true],
        ],
        'sectionTextareas' => [
            ['label' => 'Subtitle EN', 'name' => $group['prefix'] . '_subtitle_en', 'value' => old($group['prefix'] . '_subtitle_en', $sections[$group['prefix']]['subtitle_en'] ?? '')],
            ['label' => 'Subtitle AR', 'name' => $group['prefix'] . '_subtitle_ar', 'value' => old($group['prefix'] . '_subtitle_ar', $sections[$group['prefix']]['subtitle_ar'] ?? ''), 'rtl' => true],
        ],
        'repeaterKey' => 'content-' . str_replace('_', '-', $group['prefix']),
        'buttonLabel' => 'Add card',
        'items' => $group['items'],
        'fields' => [
            ['label' => 'Icon Keyword', 'key' => 'icon', 'placeholder' => 'shield, phone, mail, support, file'],
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Meta EN', 'key' => 'meta_en'],
            ['label' => 'Meta AR', 'key' => 'meta_ar', 'rtl' => true],
            ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
            ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Link Label EN', 'key' => 'link_label_en'],
            ['label' => 'Link Label AR', 'key' => 'link_label_ar', 'rtl' => true],
            ['label' => 'URL', 'key' => 'url'],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => $group['prefix'] . '_items',
    ])
    <div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="{{ $group['prefix'] }}_enabled" name="{{ $group['prefix'] }}_enabled" @checked(old($group['prefix'] . '_enabled', $sections[$group['prefix']]['enabled'] ?? true))><label class="form-check-label" for="{{ $group['prefix'] }}_enabled">Show {{ str_replace('_', ' ', $group['prefix']) }} section</label></div></div>
@endforeach

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Stats Section',
    'description' => 'Headline metrics and supporting labels.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'stats_eyebrow_en', 'value' => old('stats_eyebrow_en', $sections['stats']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'stats_eyebrow_ar', 'value' => old('stats_eyebrow_ar', $sections['stats']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'stats_title_en', 'value' => old('stats_title_en', $sections['stats']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'stats_title_ar', 'value' => old('stats_title_ar', $sections['stats']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'content-stats',
    'buttonLabel' => 'Add stat',
    'items' => $statsItems,
    'fields' => [
        ['label' => 'Value', 'key' => 'value'],
        ['label' => 'Label EN', 'key' => 'label_en'],
        ['label' => 'Label AR', 'key' => 'label_ar', 'rtl' => true],
        ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
        ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'stats_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="stats_enabled" name="stats_enabled" @checked(old('stats_enabled', $sections['stats']['enabled'] ?? true))><label class="form-check-label" for="stats_enabled">Show stats section</label></div></div>

<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">Professionalism Story Section</h2>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" value="1" id="professionalism_enabled" name="professionalism_enabled" @checked(old('professionalism_enabled', $sections['professionalism']['enabled'] ?? true))><label class="form-check-label" for="professionalism_enabled">Show professionalism section</label></div>
            <label class="form-label">Eyebrow EN</label>
            <input class="form-control mb-3" name="professionalism_eyebrow_en" value="{{ old('professionalism_eyebrow_en', $sections['professionalism']['eyebrow_en'] ?? '') }}">
            <label class="form-label">Eyebrow AR</label>
            <input class="form-control text-end mb-3" dir="rtl" name="professionalism_eyebrow_ar" value="{{ old('professionalism_eyebrow_ar', $sections['professionalism']['eyebrow_ar'] ?? '') }}">
            <label class="form-label">Title EN</label>
            <input class="form-control mb-3" name="professionalism_title_en" value="{{ old('professionalism_title_en', $sections['professionalism']['title_en'] ?? '') }}">
            <label class="form-label">Title AR</label>
            <input class="form-control text-end mb-3" dir="rtl" name="professionalism_title_ar" value="{{ old('professionalism_title_ar', $sections['professionalism']['title_ar'] ?? '') }}">
            <div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="professionalism_reverse" name="professionalism_reverse" @checked(old('professionalism_reverse', $sections['professionalism']['reverse'] ?? false))><label class="form-check-label" for="professionalism_reverse">Reverse desktop layout</label></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Description EN</label>
            <textarea class="form-control mb-3" rows="4" name="professionalism_description_en">{{ old('professionalism_description_en', $sections['professionalism']['description_en'] ?? '') }}</textarea>
            <label class="form-label">Description AR</label>
            <textarea class="form-control text-end mb-3" dir="rtl" rows="4" name="professionalism_description_ar">{{ old('professionalism_description_ar', $sections['professionalism']['description_ar'] ?? '') }}</textarea>
            <label class="form-label">Image</label>
            <input class="form-control" type="file" name="professionalism_image" accept="image/*">
            @if(!empty($sections['professionalism']['image']))
                <img src="{{ asset('storage/' . $sections['professionalism']['image']) }}" alt="" class="img-fluid rounded mt-3 border">
            @endif
        </div>
    </div>
</div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Professionalism Points',
    'description' => 'Supporting bullet points for the professionalism section.',
    'repeaterKey' => 'content-professionalism-points',
    'buttonLabel' => 'Add point',
    'items' => $professionalismPoints,
    'fields' => [
        ['label' => 'Text EN', 'key' => 'text_en'],
        ['label' => 'Text AR', 'key' => 'text_ar', 'rtl' => true],
        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
    ],
    'inputName' => 'professionalism_points',
])

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Content FAQ Section',
    'description' => 'FAQ used on about/contact style pages.',
    'sectionFields' => [
        ['label' => 'Eyebrow EN', 'name' => 'content_faq_eyebrow_en', 'value' => old('content_faq_eyebrow_en', $sections['faq']['eyebrow_en'] ?? '')],
        ['label' => 'Eyebrow AR', 'name' => 'content_faq_eyebrow_ar', 'value' => old('content_faq_eyebrow_ar', $sections['faq']['eyebrow_ar'] ?? ''), 'rtl' => true],
        ['label' => 'Title EN', 'name' => 'content_faq_title_en', 'value' => old('content_faq_title_en', $sections['faq']['title_en'] ?? '')],
        ['label' => 'Title AR', 'name' => 'content_faq_title_ar', 'value' => old('content_faq_title_ar', $sections['faq']['title_ar'] ?? ''), 'rtl' => true],
    ],
    'repeaterKey' => 'content-faq',
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
    'inputName' => 'content_faq_items',
])
<div class="card admin-card p-4 mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="content_faq_enabled" name="content_faq_enabled" @checked(old('content_faq_enabled', $sections['faq']['enabled'] ?? true))><label class="form-check-label" for="content_faq_enabled">Show FAQ section</label></div></div>

<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">Content CTA</h2>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" value="1" id="content_cta_enabled" name="content_cta_enabled" @checked(old('content_cta_enabled', $sections['cta']['enabled'] ?? true))><label class="form-check-label" for="content_cta_enabled">Show content CTA</label></div>
            <label class="form-label">Eyebrow EN</label>
            <input class="form-control mb-3" name="content_cta_eyebrow_en" value="{{ old('content_cta_eyebrow_en', $sections['cta']['eyebrow_en'] ?? '') }}">
            <label class="form-label">Eyebrow AR</label>
            <input class="form-control text-end mb-3" dir="rtl" name="content_cta_eyebrow_ar" value="{{ old('content_cta_eyebrow_ar', $sections['cta']['eyebrow_ar'] ?? '') }}">
            <label class="form-label">Title EN</label>
            <input class="form-control mb-3" name="content_cta_title_en" value="{{ old('content_cta_title_en', $sections['cta']['title_en'] ?? '') }}">
            <label class="form-label">Title AR</label>
            <input class="form-control text-end mb-3" dir="rtl" name="content_cta_title_ar" value="{{ old('content_cta_title_ar', $sections['cta']['title_ar'] ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Description EN</label>
            <textarea class="form-control mb-3" rows="4" name="content_cta_description_en">{{ old('content_cta_description_en', $sections['cta']['description_en'] ?? '') }}</textarea>
            <label class="form-label">Description AR</label>
            <textarea class="form-control text-end mb-3" dir="rtl" rows="4" name="content_cta_description_ar">{{ old('content_cta_description_ar', $sections['cta']['description_ar'] ?? '') }}</textarea>
            <label class="form-label">Background Image</label>
            <input class="form-control" type="file" name="content_cta_background_image" accept="image/*">
            @if(!empty($sections['cta']['background_image']))
                <img src="{{ asset('storage/' . $sections['cta']['background_image']) }}" alt="" class="img-fluid rounded mt-3 border">
            @endif
        </div>
    </div>
</div>

@include('admin.visa-countries.partials.repeater-card', [
    'title' => 'Content CTA Buttons',
    'description' => 'Buttons shown in the About / Contact CTA.',
    'repeaterKey' => 'content-cta-buttons',
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
    'inputName' => 'content_cta_buttons',
])
