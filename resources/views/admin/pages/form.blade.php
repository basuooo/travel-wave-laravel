@php($sections = $page->sections ?? [])
<form method="post" enctype="multipart/form-data" action="{{ $formAction }}" id="page-edit-form">
    @csrf
    @if(($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-navy font-weight-bold">
                {{ !empty($page->id) ? 'تعديل الصفحة: ' . ($page->localized('title') ?: $page->key) : 'إنشاء صفحة جديدة' }}
            </h1>
            <p class="text-muted mb-0">إدارة محتوى وترتيب أقسام الصفحة والنصوص والصور الخاصة بها بسهولة.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(!empty($page->id) && $page->frontendUrl())
                <a href="{{ $page->frontendUrl() }}" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer">
                    🌐 معاينة الصفحة
                </a>
            @endif
            <button type="submit" class="btn btn-primary tw-btn-primary px-4 fw-bold">
                💾 {{ $submitLabel ?? 'حفظ الصفحة' }}
            </button>
        </div>
    </div>

    <style>
        .custom-admin-tabs .nav-link {
            color: #495057;
            padding: 0.65rem 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 0;
            background: transparent;
        }
        .custom-admin-tabs .nav-link.active {
            background-color: #0d6efd !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
    </style>

    <!-- TAB NAVIGATION HEADER -->
    <ul class="nav nav-pills custom-admin-tabs mb-4 bg-light p-2 rounded-3 border" id="pageFormTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold d-flex align-items-center gap-2" id="tab-ordering-tab" data-bs-toggle="pill" data-bs-target="#tab-ordering" type="button" role="tab">
                <span>🔀</span> <span>ترتيب وإظهار الأقسام</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold d-flex align-items-center gap-2" id="tab-core-tab" data-bs-toggle="pill" data-bs-target="#tab-core" type="button" role="tab">
                <span>⚙️</span> <span>البيانات الأساسية والـ SEO</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold d-flex align-items-center gap-2" id="tab-sections-tab" data-bs-toggle="pill" data-bs-target="#tab-sections" type="button" role="tab">
                <span>🧩</span> <span>محتوى العناصر والأقسام</span>
            </button>
        </li>
    </ul>

    <!-- TAB CONTENT -->
    <div class="tab-content" id="pageFormTabsContent">

        <!-- TAB 1: SECTION ORDERING -->
        <div class="tab-pane fade show active" id="tab-ordering" role="tabpanel" style="display: block;">
            @php($orderedSections = $page->getOrderedSections())
            <div class="card admin-card p-4 mb-4 border-primary shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h2 class="h5 mb-1 text-primary fw-bold">🔀 ترتيب وإظهار/إخفاء أقسام الصفحة (Page Sections Arrangement)</h2>
                        <p class="text-muted small mb-0">يمكنك تعديل ترتيب الأقسام بالنقر على الأسهم ⬆️ ⬇️ أو كتابة رقم الترتيب، وكذلك تفعيل أو تعطيل أي قسم بسهولة.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tw-page-sections-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">إظهار</th>
                                <th style="width: 110px;">الترتيب</th>
                                <th>اسم القسم / Section Name</th>
                                <th style="width: 120px;" class="text-center">تحريك</th>
                            </tr>
                        </thead>
                        <tbody class="js-section-order-body">
                            @foreach($orderedSections as $secKey => $secMeta)
                                <tr class="js-section-order-row" data-key="{{ $secKey }}">
                                    <td>
                                        <div class="form-check form-switch mb-0">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   name="page_section_enabled[{{ $secKey }}]"
                                                   value="1"
                                                   @checked($secMeta['enabled'])>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number"
                                               class="form-control form-control-sm js-section-order-input text-center fw-bold"
                                               name="page_section_order[{{ $secKey }}]"
                                               value="{{ $secMeta['sort_order'] }}"
                                               min="1"
                                               max="99">
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block mb-0">{{ $secMeta['name_ar'] }}</span>
                                        <span class="text-muted small">{{ $secMeta['name_en'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary js-move-up" title="للأعلى">▲</button>
                                            <button type="button" class="btn btn-outline-secondary js-move-down" title="للأسفل">▼</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: CORE INFO & SEO -->
        <div class="tab-pane fade" id="tab-core" role="tabpanel" style="display: none;">
            <div class="card admin-card p-4 mb-4">
                <h2 class="h5 mb-3">Core Page Info & Hero Settings</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Key</label>
                        <input class="form-control" name="key" value="{{ old('key', $page->key) }}" {{ !empty($page->id) && $page->isCorePage() ? 'readonly' : '' }}>
                        <div class="form-text">Used internally. Core page keys stay locked.</div>
                    </div>
                    <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" name="title_en" value="{{ old('title_en', $page->title_en) }}"></div>
                    <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" name="title_ar" value="{{ old('title_ar', $page->title_ar) }}"></div>
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
                    <div class="col-md-3 d-flex align-items-end"><div class="form-check pb-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="page_is_active" @checked(old('is_active', $page->is_active))><label class="form-check-label" for="page_is_active">Active / Published</label></div></div>
                </div>
            </div>
        </div>

        <!-- TAB 3: SECTIONS CONTENT -->
        <div class="tab-pane fade" id="tab-sections" role="tabpanel" style="display: none;">
            @php($pageKey = old('key', $page->key))

            @if($pageKey === 'home')
                @php($homepageServicesItems = old('services_items', $sections['services'] ?? []))
                @include('admin.visa-countries.partials.repeater-card', [
                    'title' => 'Homepage Services / خدمات الصفحة الرئيسية',
                    'description' => 'إضافة وتعديل الخدمات المعروضة بالصفحة الرئيسية مع أزرار الإضافة والتنسيق الحُر.',
                    'repeaterKey' => 'home-services-items',
                    'buttonLabel' => '➕ إضافة خدمة جديدة',
                    'items' => $homepageServicesItems,
                    'fields' => [
                        ['label' => 'Icon / الإيقونة', 'key' => 'icon'],
                        ['label' => 'Title EN', 'key' => 'title_en'],
                        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
                        ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
                        ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
                        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
                        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
                    ],
                    'inputName' => 'services_items',
                ])

                @php($homeWhyChooseSection = data_get($sections, 'why_choose_travel_wave', []))
                @php($whyChooseItems = old('why_choose_travel_wave_items', data_get($homeWhyChooseSection, 'items', [])))
                @include('admin.visa-countries.partials.repeater-card', [
                    'title' => 'Why Choose Travel Wave / لماذا تختار ترافل ويف',
                    'description' => 'إضافة كروت المميزات وأسباب اختيار ترافل ويف مع أزرار الإضافة والتنسيق الحُر.',
                    'sectionFields' => [
                        ['label' => 'Section Title EN', 'name' => 'why_choose_travel_wave_title_en', 'value' => old('why_choose_travel_wave_title_en', data_get($homeWhyChooseSection, 'title_en', ''))],
                        ['label' => 'Section Title AR', 'name' => 'why_choose_travel_wave_title_ar', 'value' => old('why_choose_travel_wave_title_ar', data_get($homeWhyChooseSection, 'title_ar', '')), 'rtl' => true],
                    ],
                    'sectionTextareas' => [
                        ['label' => 'Section Subtitle EN', 'name' => 'why_choose_travel_wave_subtitle_en', 'value' => old('why_choose_travel_wave_subtitle_en', data_get($homeWhyChooseSection, 'subtitle_en', ''))],
                        ['label' => 'Section Subtitle AR', 'name' => 'why_choose_travel_wave_subtitle_ar', 'value' => old('why_choose_travel_wave_subtitle_ar', data_get($homeWhyChooseSection, 'subtitle_ar', '')), 'rtl' => true],
                    ],
                    'repeaterKey' => 'home-why-choose-items',
                    'buttonLabel' => '➕ إضافة ميزة جديدة',
                    'items' => $whyChooseItems,
                    'fields' => [
                        ['label' => 'Icon / الإيقونة', 'key' => 'icon'],
                        ['label' => 'Title EN', 'key' => 'title_en'],
                        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
                        ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
                        ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
                        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
                        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
                    ],
                    'inputName' => 'why_choose_travel_wave_items',
                ])

                @php($whyUsItems = old('why_choose_us_items', $sections['why_choose_us'] ?? []))
                @include('admin.visa-countries.partials.repeater-card', [
                    'title' => 'Why Choose Us / لماذا نحن',
                    'description' => 'عناصر ولماذا يفضل العملاء التعامل معنا مع زر الإضافة السريع.',
                    'repeaterKey' => 'home-why-us-items',
                    'buttonLabel' => '➕ إضافة عنصر جديد',
                    'items' => $whyUsItems,
                    'fields' => [
                        ['label' => 'Title EN', 'key' => 'title_en'],
                        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
                        ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
                        ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
                        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
                        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
                    ],
                    'inputName' => 'why_choose_us_items',
                ])

                @php($howItWorksItems = old('how_it_works_items', $sections['how_it_works'] ?? []))
                @include('admin.visa-countries.partials.repeater-card', [
                    'title' => 'How It Works / خطوات ونظام العمل',
                    'description' => 'خطوات العمل والحصول على الخدمة بالتفصيل مع زر الإضافة السريع.',
                    'repeaterKey' => 'home-how-it-works-items',
                    'buttonLabel' => '➕ إضافة خطوة جديدة',
                    'items' => $howItWorksItems,
                    'fields' => [
                        ['label' => 'Title EN', 'key' => 'title_en'],
                        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
                        ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
                        ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
                        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
                        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
                    ],
                    'inputName' => 'how_it_works_items',
                ])

                <div class="card admin-card p-4 mb-4">
                    <h2 class="h5 mb-3">Promo, Inquiry, and Final CTA Settings</h2>
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

            @elseif(in_array($pageKey, ['visas', 'domestic', 'flights', 'hotels'], true))
                @include('admin.pages.partials.service-sections', ['sections' => $sections])
            @elseif(in_array($pageKey, ['about', 'contact'], true))
                @include('admin.pages.partials.content-sections', ['sections' => $sections])
            @else
                @php($featureBlocksItems = old('feature_blocks_items', $sections['feature_blocks'] ?? []))
                @include('admin.visa-countries.partials.repeater-card', [
                    'title' => 'Feature Blocks / مميزات الصفحة',
                    'description' => 'إضافة كروت الخدمات والمميزات الخاصة بالصفحة مع أزرار الإضافة التفاعلية.',
                    'repeaterKey' => 'page-feature-blocks',
                    'buttonLabel' => '➕ إضافة ميزة جديدة',
                    'items' => $featureBlocksItems,
                    'fields' => [
                        ['label' => 'Icon / الإيقونة', 'key' => 'icon'],
                        ['label' => 'Title EN', 'key' => 'title_en'],
                        ['label' => 'Title AR', 'key' => 'title_ar', 'rtl' => true],
                        ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
                        ['label' => 'Text AR', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
                        ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
                        ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
                    ],
                    'inputName' => 'feature_blocks_items',
                ])

                @php($pageFaqsItems = old('faq_items', $sections['faqs'] ?? []))
                @include('admin.visa-countries.partials.repeater-card', [
                    'title' => 'FAQs / الأسئلة الشائعة',
                    'description' => 'إضافة أسئلة وإجابات شائعة خاصة بهذه الصفحة.',
                    'repeaterKey' => 'page-faq-items',
                    'buttonLabel' => '➕ إضافة سؤال جديد',
                    'items' => $pageFaqsItems,
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
                    <h2 class="h5 mb-3">CTA Banner Settings</h2>
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
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
        <button type="submit" class="btn btn-primary tw-btn-primary btn-lg px-5 font-weight-bold">
            💾 {{ $submitLabel ?? 'حفظ الصفحة' }}
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // TAB SWITCHER
    const pageTabButtons = document.querySelectorAll('#pageFormTabs button');
    const pageTabPanes = document.querySelectorAll('#pageFormTabsContent > .tab-pane');

    pageTabButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            pageTabButtons.forEach((b) => {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            });

            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');

            const targetSelector = btn.getAttribute('data-bs-target') || btn.getAttribute('data-target');
            pageTabPanes.forEach((pane) => {
                if ('#' + pane.id === targetSelector) {
                    pane.classList.add('show', 'active');
                    pane.style.display = 'block';
                } else {
                    pane.classList.remove('show', 'active');
                    pane.style.display = 'none';
                }
            });
        });
    });

    // Dynamic Repeater Add / Remove logic
    document.addEventListener('click', function (event) {
        const addButton = event.target.closest('[data-repeater-add]');
        const removeButton = event.target.closest('[data-repeater-remove]');

        if (addButton) {
            const key = addButton.getAttribute('data-repeater-add');
            const list = document.querySelector('[data-repeater-list="' + key + '"]');
            const lastItem = list?.querySelector('[data-repeater-item]:last-child');

            if (!list || !lastItem) {
                return;
            }

            const clone = lastItem.cloneNode(true);
            clone.querySelectorAll('input, textarea, select').forEach((input) => {
                if (input.type === 'checkbox') {
                    input.checked = input.defaultChecked;
                } else {
                    input.value = '';
                }
            });
            list.appendChild(clone);
            syncPageRepeaterNames(list);
        }

        if (removeButton) {
            const list = removeButton.closest('[data-repeater-list]');
            const item = removeButton.closest('[data-repeater-item]');

            if (item && list) {
                if (list.querySelectorAll('[data-repeater-item]').length > 1) {
                    item.remove();
                } else {
                    item.querySelectorAll('input, textarea, select').forEach((input) => {
                        if (input.type === 'checkbox') {
                            input.checked = false;
                        } else {
                            input.value = '';
                        }
                    });
                }

                syncPageRepeaterNames(list);
            }
        }
    });

    function syncPageRepeaterNames(list) {
        if (!list) {
            return;
        }

        list.querySelectorAll('[data-repeater-item]').forEach((item, index) => {
            item.querySelectorAll('input, textarea, select').forEach((input) => {
                if (!input.name) {
                    return;
                }

                input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
            });
        });
    }

    document.querySelectorAll('[data-repeater-list]').forEach(syncPageRepeaterNames);

    // Section Ordering Table Up/Down Move Logic
    const tableBody = document.querySelector('.js-section-order-body');
    if (tableBody) {
        const updateInputs = () => {
            const rows = Array.from(tableBody.querySelectorAll('.js-section-order-row'));
            rows.forEach((row, index) => {
                const input = row.querySelector('.js-section-order-input');
                if (input) {
                    input.value = index + 1;
                }
            });
        };

        tableBody.addEventListener('click', (e) => {
            const row = e.target.closest('.js-section-order-row');
            if (!row) return;

            if (e.target.classList.contains('js-move-up')) {
                const prev = row.previousElementSibling;
                if (prev) {
                    tableBody.insertBefore(row, prev);
                    updateInputs();
                }
            } else if (e.target.classList.contains('js-move-down')) {
                const next = row.nextElementSibling;
                if (next) {
                    tableBody.insertBefore(next, row);
                    updateInputs();
                }
            }
        });
    }
});
</script>
