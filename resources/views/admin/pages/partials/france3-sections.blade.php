@php($hero = data_get($sections, 'hero', []))
@php($quickSummary = data_get($sections, 'quick_summary', []))
@php($qsItems = old('france3_qs_items', data_get($quickSummary, 'items', [])))
@php($intro = data_get($sections, 'intro', []))
@php($bestTime = data_get($sections, 'best_time', []))
@php($requirements = data_get($sections, 'requirements', []))
@php($reqItems = old('france3_req_items', data_get($requirements, 'items', [])))
@php($servicesSec = data_get($sections, 'services', []))
@php($serviceItems = old('france3_service_items', data_get($servicesSec, 'items', [])))
@php($steps = data_get($sections, 'steps', []))
@php($stepItems = old('france3_step_items', data_get($steps, 'items', [])))
@php($why = data_get($sections, 'why_choose', []))
@php($whyItems = old('france3_why_items', data_get($why, 'items', [])))
@php($suitability = data_get($sections, 'suitability', []))
@php($pricingDuration = data_get($sections, 'pricing_duration', []))
@php($faq = data_get($sections, 'faq', []))
@php($faqItems = old('france3_faq_items', data_get($faq, 'items', [])))
@php($notice = data_get($sections, 'notice', []))
@php($cta = data_get($sections, 'cta', []))

<div class="d-flex flex-column gap-4">

    <!-- 1. HERO SECTION SETTINGS -->
    <div class="card admin-card p-4 border-primary shadow-sm">
        <h2 class="h5 mb-3 text-primary fw-bold">1️⃣ البانر الهيرو (Hero Section)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Eyebrow EN</label><input class="form-control" name="france3_hero_eyebrow_en" value="{{ old('france3_hero_eyebrow_en', data_get($hero, 'eyebrow_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Eyebrow AR (الشارة بالعربية)</label><input class="form-control text-end" dir="rtl" name="france3_hero_eyebrow_ar" value="{{ old('france3_hero_eyebrow_ar', data_get($hero, 'eyebrow_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Hero Title EN</label><input class="form-control" name="france3_hero_title_en" value="{{ old('france3_hero_title_en', data_get($hero, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Hero Title AR (العنوان الرئيسي)</label><input class="form-control text-end" dir="rtl" name="france3_hero_title_ar" value="{{ old('france3_hero_title_ar', data_get($hero, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Hero Subtitle EN</label><textarea class="form-control" name="france3_hero_subtitle_en" rows="2">{{ old('france3_hero_subtitle_en', data_get($hero, 'subtitle_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Hero Subtitle AR (الوصف الفرعي)</label><textarea class="form-control text-end" dir="rtl" name="france3_hero_subtitle_ar" rows="2">{{ old('france3_hero_subtitle_ar', data_get($hero, 'subtitle_ar')) }}</textarea></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button Text EN</label><input class="form-control" name="france3_hero_cta_text_en" value="{{ old('france3_hero_cta_text_en', data_get($hero, 'primary_cta_text_en')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button Text AR (نص الزر)</label><input class="form-control text-end" dir="rtl" name="france3_hero_cta_text_ar" value="{{ old('france3_hero_cta_text_ar', data_get($hero, 'primary_cta_text_ar')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button URL (رابط الزر)</label><input class="form-control" name="france3_hero_cta_url" value="{{ old('france3_hero_cta_url', data_get($hero, 'primary_cta_url')) }}"></div>
        </div>
    </div>

    <!-- 2. QUICK SUMMARY REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '2️⃣ ملخص سريع عن فيزا فرنسا (Quick Summary)',
        'description' => 'إضافة كروت الملخص السريع (الدولة، نوع التأشيرة، مدة الإقامة، مدة الإجراءات، الرسوم).',
        'sectionFields' => [
            ['label' => 'Summary Section Title EN', 'name' => 'france3_qs_title_en', 'value' => old('france3_qs_title_en', data_get($quickSummary, 'title_en'))],
            ['label' => 'Summary Section Title AR (عنوان قسم الملخص السريع)', 'name' => 'france3_qs_title_ar', 'value' => old('france3_qs_title_ar', data_get($quickSummary, 'title_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france3-qs-items',
        'buttonLabel' => '➕ إضافة عنصر ملخص جديد',
        'items' => $qsItems,
        'fields' => [
            ['label' => 'Icon / أيقونة', 'key' => 'icon'],
            ['label' => 'Label EN', 'key' => 'label_en'],
            ['label' => 'Label AR (اسم الحقل)', 'key' => 'label_ar', 'rtl' => true],
            ['label' => 'Value EN', 'key' => 'value_en'],
            ['label' => 'Value AR (القيمة/الوصف)', 'key' => 'value_ar', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'france3_qs_items',
    ])

    <!-- 3. INTRO SECTION -->
    <div class="card admin-card p-4 shadow-sm">
        <h2 class="h5 mb-3 text-navy fw-bold">3️⃣ نبذة عن فيزا فرنسا (About France Visa)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Title EN</label><input class="form-control" name="france3_intro_title_en" value="{{ old('france3_intro_title_en', data_get($intro, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Title AR (عنوان القسم)</label><input class="form-control text-end" dir="rtl" name="france3_intro_title_ar" value="{{ old('france3_intro_title_ar', data_get($intro, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Description EN</label><textarea class="form-control" name="france3_intro_desc_en" rows="3">{{ old('france3_intro_desc_en', data_get($intro, 'description_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Description AR (الفقرة التعريفية)</label><textarea class="form-control text-end" dir="rtl" name="france3_intro_desc_ar" rows="3">{{ old('france3_intro_desc_ar', data_get($intro, 'description_ar')) }}</textarea></div>
        </div>
    </div>

    <!-- 4. BEST TIME TO APPLY SECTION -->
    <div class="card admin-card p-4 shadow-sm border-info border-opacity-50">
        <h2 class="h5 mb-3 text-info fw-bold">4️⃣ متى تبدأ إجراءات فيزا فرنسا؟ (Best Time to Apply)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Title EN</label><input class="form-control" name="france3_bt_title_en" value="{{ old('france3_bt_title_en', data_get($bestTime, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Title AR (عنوان السؤال)</label><input class="form-control text-end" dir="rtl" name="france3_bt_title_ar" value="{{ old('france3_bt_title_ar', data_get($bestTime, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Text EN</label><textarea class="form-control" name="france3_bt_text_en" rows="2">{{ old('france3_bt_text_en', data_get($bestTime, 'text_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Text AR (فقرة الميعاد الأفضل)</label><textarea class="form-control text-end" dir="rtl" name="france3_bt_text_ar" rows="2">{{ old('france3_bt_text_ar', data_get($bestTime, 'text_ar')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Note EN</label><textarea class="form-control" name="france3_bt_note_en" rows="2">{{ old('france3_bt_note_en', data_get($bestTime, 'note_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Note AR (الملاحظة الذهبية)</label><textarea class="form-control text-end" dir="rtl" name="france3_bt_note_ar" rows="2">{{ old('france3_bt_note_ar', data_get($bestTime, 'note_ar')) }}</textarea></div>
        </div>
    </div>

    <!-- 5. REQUIREMENTS REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '5️⃣ المتطلبات الأساسية (Key Requirements)',
        'description' => 'إضافة وتعديل وحذف عناصر المتطلبات والملاحظة المرفقة.',
        'sectionFields' => [
            ['label' => 'Requirements Title EN', 'name' => 'france3_req_title_en', 'value' => old('france3_req_title_en', data_get($requirements, 'title_en'))],
            ['label' => 'Requirements Title AR (عنوان قسم المتطلبات)', 'name' => 'france3_req_title_ar', 'value' => old('france3_req_title_ar', data_get($requirements, 'title_ar')), 'rtl' => true],
        ],
        'sectionTextareas' => [
            ['label' => 'Note EN', 'name' => 'france3_req_note_en', 'value' => old('france3_req_note_en', data_get($requirements, 'note_en'))],
            ['label' => 'Note AR (ملاحظة التنبيه أسفل المتطلبات)', 'name' => 'france3_req_note_ar', 'value' => old('france3_req_note_ar', data_get($requirements, 'note_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france3-req-items',
        'buttonLabel' => '➕ إضافة شرط / متطلب جديد',
        'items' => $reqItems,
        'fields' => [
            ['label' => 'Icon / أيقونة', 'key' => 'icon'],
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR (عنوان المتطلب)', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
            ['label' => 'Text AR (شرح المتطلب)', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'france3_req_items',
    ])

    <!-- 6. SERVICES REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '6️⃣ خدمات ترافل ويف (Travel Wave Services)',
        'description' => 'إضافة وتعديل وحذف كروت خدمات الشركة لفيزا فرنسا (تجهيز الملف، المراجعة، الحجز، إلخ).',
        'sectionFields' => [
            ['label' => 'Services Title EN', 'name' => 'france3_serv_title_en', 'value' => old('france3_serv_title_en', data_get($servicesSec, 'title_en'))],
            ['label' => 'Services Title AR (عنوان قسم الخدمات)', 'name' => 'france3_serv_title_ar', 'value' => old('france3_serv_title_ar', data_get($servicesSec, 'title_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france3-serv-items',
        'buttonLabel' => '➕ إضافة خدمة جديدة',
        'items' => $serviceItems,
        'fields' => [
            ['label' => 'Icon / أيقونة', 'key' => 'icon'],
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR (اسم الخدمة)', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
            ['label' => 'Text AR (وصف الخدمة)', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'france3_service_items',
    ])

    <!-- 7. APPLICATION STEPS REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '7️⃣ خطوات التقديم الـ 5 (Application Steps)',
        'description' => 'إضافة وتعديل وترتيب خطوات التقديم الخمس الممنهجة.',
        'sectionFields' => [
            ['label' => 'Steps Title EN', 'name' => 'france3_steps_title_en', 'value' => old('france3_steps_title_en', data_get($steps, 'title_en'))],
            ['label' => 'Steps Title AR (عنوان خطوات التقديم)', 'name' => 'france3_steps_title_ar', 'value' => old('france3_steps_title_ar', data_get($steps, 'title_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france3-steps-items',
        'buttonLabel' => '➕ إضافة خطوة جديدة',
        'items' => $stepItems,
        'fields' => [
            ['label' => 'Step Number (01)', 'key' => 'step_number'],
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR (عنوان الخطوة)', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
            ['label' => 'Text AR (شرح الخطوة)', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'france3_step_items',
    ])

    <!-- 8. WHY TRAVEL WAVE REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '8️⃣ ليه تختار Travel Wave؟ (Why Choose Travel Wave?)',
        'description' => 'إضافة وتعديل كروت مميزات وأسباب تفضيل ترافل ويف.',
        'sectionFields' => [
            ['label' => 'Why Title EN', 'name' => 'france3_why_title_en', 'value' => old('france3_why_title_en', data_get($why, 'title_en'))],
            ['label' => 'Why Title AR (عنوان لماذا تختارنا)', 'name' => 'france3_why_title_ar', 'value' => old('france3_why_title_ar', data_get($why, 'title_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france3-why-items',
        'buttonLabel' => '➕ إضافة ميزة جديدة',
        'items' => $whyItems,
        'fields' => [
            ['label' => 'Icon / أيقونة', 'key' => 'icon'],
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR (اسم الميزة)', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
            ['label' => 'Text AR (شرح الميزة)', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'france3_why_items',
    ])

    <!-- 9. SUITABILITY ASSESSMENT SECTION -->
    <div class="card admin-card p-4 shadow-sm">
        <h2 class="h5 mb-3 text-navy fw-bold">9️⃣ هل حالتك مناسبة للتقديم؟ (Suitability Assessment)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Title EN</label><input class="form-control" name="france3_suit_title_en" value="{{ old('france3_suit_title_en', data_get($suitability, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Title AR (عنوان السؤال)</label><input class="form-control text-end" dir="rtl" name="france3_suit_title_ar" value="{{ old('france3_suit_title_ar', data_get($suitability, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Description EN</label><textarea class="form-control" name="france3_suit_desc_en" rows="2">{{ old('france3_suit_desc_en', data_get($suitability, 'description_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Description AR (الشرح)</label><textarea class="form-control text-end" dir="rtl" name="france3_suit_desc_ar" rows="2">{{ old('france3_suit_desc_ar', data_get($suitability, 'description_ar')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Call Note EN</label><textarea class="form-control" name="france3_suit_note_en" rows="2">{{ old('france3_suit_note_en', data_get($suitability, 'note_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Call Note AR (فقرة التوجيه للتواصل)</label><textarea class="form-control text-end" dir="rtl" name="france3_suit_note_ar" rows="2">{{ old('france3_suit_note_ar', data_get($suitability, 'note_ar')) }}</textarea></div>
            <div class="col-md-4"><label class="form-label fw-bold">Button Text EN</label><input class="form-control" name="france3_suit_btn_en" value="{{ old('france3_suit_btn_en', data_get($suitability, 'button_text_en')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">Button Text AR (نص زر التواصل)</label><input class="form-control text-end" dir="rtl" name="france3_suit_btn_ar" value="{{ old('france3_suit_btn_ar', data_get($suitability, 'button_text_ar')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">Button URL (رابط الزر)</label><input class="form-control" name="france3_suit_btn_url" value="{{ old('france3_suit_btn_url', data_get($suitability, 'button_url')) }}"></div>
        </div>
    </div>

    <!-- 10. PRICING & DURATION SECTION -->
    <div class="card admin-card p-4 shadow-sm">
        <h2 class="h5 mb-3 text-navy fw-bold">🔟 مدة الإجراءات والرسوم (Duration & Fees)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Duration Box Title EN</label><input class="form-control" name="france3_dur_title_en" value="{{ old('france3_dur_title_en', data_get($pricingDuration, 'duration_title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Duration Box Title AR (عنوان مدة الإجراءات)</label><input class="form-control text-end" dir="rtl" name="france3_dur_title_ar" value="{{ old('france3_dur_title_ar', data_get($pricingDuration, 'duration_title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Duration Text EN</label><textarea class="form-control" name="france3_dur_text_en" rows="2">{{ old('france3_dur_text_en', data_get($pricingDuration, 'duration_text_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Duration Text AR (نص مدة الإجراءات)</label><textarea class="form-control text-end" dir="rtl" name="france3_dur_text_ar" rows="2">{{ old('france3_dur_text_ar', data_get($pricingDuration, 'duration_text_ar')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Fees Box Title EN</label><input class="form-control" name="france3_fees_title_en" value="{{ old('france3_fees_title_en', data_get($pricingDuration, 'fees_title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Fees Box Title AR (عنوان الرسوم)</label><input class="form-control text-end" dir="rtl" name="france3_fees_title_ar" value="{{ old('france3_fees_title_ar', data_get($pricingDuration, 'fees_title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Fees Text EN</label><textarea class="form-control" name="france3_fees_text_en" rows="2">{{ old('france3_fees_text_en', data_get($pricingDuration, 'fees_text_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Fees Text AR (نص الرسوم)</label><textarea class="form-control text-end" dir="rtl" name="france3_fees_text_ar" rows="2">{{ old('france3_fees_text_ar', data_get($pricingDuration, 'fees_text_ar')) }}</textarea></div>
        </div>
    </div>

    <!-- 11. FAQ REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '11️⃣ الأسئلة الشائعة (Frequently Asked Questions)',
        'description' => 'إضافة وتعديل وحذف وتحديد ترتيب الأسئلة الشائعة وتفعيلها أو إخفائها.',
        'sectionFields' => [
            ['label' => 'FAQ Section Title EN', 'name' => 'france3_faq_title_en', 'value' => old('france3_faq_title_en', data_get($faq, 'title_en'))],
            ['label' => 'FAQ Section Title AR (عنوان قسم الأسئلة الشائعة)', 'name' => 'france3_faq_title_ar', 'value' => old('france3_faq_title_ar', data_get($faq, 'title_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france3-faq-items',
        'buttonLabel' => '➕ إضافة سؤال شائع جديد',
        'items' => $faqItems,
        'fields' => [
            ['label' => 'Question EN', 'key' => 'question_en'],
            ['label' => 'Question AR (السؤال)', 'key' => 'question_ar', 'rtl' => true],
            ['label' => 'Answer EN', 'key' => 'answer_en', 'type' => 'textarea'],
            ['label' => 'Answer AR (الإجابة)', 'key' => 'answer_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'france3_faq_items',
    ])

    <!-- 12. IMPORTANT NOTICE SECTION -->
    <div class="card admin-card p-4 shadow-sm border-warning border-opacity-50">
        <h2 class="h5 mb-3 text-warning fw-bold">12️⃣ تنبيه مهم (Important Notice)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Notice Title EN</label><input class="form-control" name="france3_notice_title_en" value="{{ old('france3_notice_title_en', data_get($notice, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Notice Title AR (عنوان التنبيه)</label><input class="form-control text-end" dir="rtl" name="france3_notice_title_ar" value="{{ old('france3_notice_title_ar', data_get($notice, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Notice Text EN</label><textarea class="form-control" name="france3_notice_text_en" rows="3">{{ old('france3_notice_text_en', data_get($notice, 'text_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Notice Text AR (نص التنبيه الرسمي)</label><textarea class="form-control text-end" dir="rtl" name="france3_notice_text_ar" rows="3">{{ old('france3_notice_text_ar', data_get($notice, 'text_ar')) }}</textarea></div>
        </div>
    </div>

    <!-- 13. FINAL CTA SECTION -->
    <div class="card admin-card p-4 shadow-sm border-success border-opacity-50">
        <h2 class="h5 mb-3 text-success fw-bold">13️⃣ شريط CTA النهائي (Final CTA Banner)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">CTA Title EN</label><input class="form-control" name="france3_cta_title_en" value="{{ old('france3_cta_title_en', data_get($cta, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">CTA Title AR (العنوان النهائي)</label><input class="form-control text-end" dir="rtl" name="france3_cta_title_ar" value="{{ old('france3_cta_title_ar', data_get($cta, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">CTA Description EN</label><textarea class="form-control" name="france3_cta_desc_en" rows="2">{{ old('france3_cta_desc_en', data_get($cta, 'description_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">CTA Description AR (الوصف النهائي)</label><textarea class="form-control text-end" dir="rtl" name="france3_cta_desc_ar" rows="2">{{ old('france3_cta_desc_ar', data_get($cta, 'description_ar')) }}</textarea></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button Text EN</label><input class="form-control" name="france3_cta_btn_en" value="{{ old('france3_cta_btn_en', data_get($cta, 'button_text_en')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button Text AR (نص الزر النهائي)</label><input class="form-control text-end" dir="rtl" name="france3_cta_btn_ar" value="{{ old('france3_cta_btn_ar', data_get($cta, 'button_text_ar')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button URL (رابط الزر)</label><input class="form-control" name="france3_cta_btn_url" value="{{ old('france3_cta_btn_url', data_get($cta, 'button_url')) }}"></div>
        </div>
    </div>

</div>
