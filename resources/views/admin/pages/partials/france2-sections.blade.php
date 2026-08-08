@php($hero = data_get($sections, 'hero', []))
@php($intro = data_get($sections, 'intro', []))
@php($requirements = data_get($sections, 'requirements', []))
@php($reqItems = old('france2_req_items', data_get($requirements, 'items', [])))
@php($steps = data_get($sections, 'steps', []))
@php($stepItems = old('france2_step_items', data_get($steps, 'items', [])))
@php($servicesSec = data_get($sections, 'services', []))
@php($serviceItems = old('france2_service_items', data_get($servicesSec, 'items', [])))
@php($suitability = data_get($sections, 'suitability', []))
@php($pricingDuration = data_get($sections, 'pricing_duration', []))
@php($faq = data_get($sections, 'faq', []))
@php($faqItems = old('france2_faq_items', data_get($faq, 'items', [])))
@php($notice = data_get($sections, 'notice', []))
@php($cta = data_get($sections, 'cta', []))

<div class="d-flex flex-column gap-4">

    <!-- 1. HERO SECTION SETTINGS -->
    <div class="card admin-card p-4 border-primary shadow-sm">
        <h2 class="h5 mb-3 text-primary fw-bold">1️⃣ البانر الهيرو (Hero Section)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Eyebrow EN</label><input class="form-control" name="france2_hero_eyebrow_en" value="{{ old('france2_hero_eyebrow_en', data_get($hero, 'eyebrow_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Eyebrow AR (الشارة بالعربية)</label><input class="form-control text-end" dir="rtl" name="france2_hero_eyebrow_ar" value="{{ old('france2_hero_eyebrow_ar', data_get($hero, 'eyebrow_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Hero Title EN</label><input class="form-control" name="france2_hero_title_en" value="{{ old('france2_hero_title_en', data_get($hero, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Hero Title AR (العنوان الرئيسي)</label><input class="form-control text-end" dir="rtl" name="france2_hero_title_ar" value="{{ old('france2_hero_title_ar', data_get($hero, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Hero Subtitle EN</label><textarea class="form-control" name="france2_hero_subtitle_en" rows="2">{{ old('france2_hero_subtitle_en', data_get($hero, 'subtitle_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Hero Subtitle AR (الوصف الفرعي)</label><textarea class="form-control text-end" dir="rtl" name="france2_hero_subtitle_ar" rows="2">{{ old('france2_hero_subtitle_ar', data_get($hero, 'subtitle_ar')) }}</textarea></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button Text EN</label><input class="form-control" name="france2_hero_cta_text_en" value="{{ old('france2_hero_cta_text_en', data_get($hero, 'primary_cta_text_en')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button Text AR (نص الزر)</label><input class="form-control text-end" dir="rtl" name="france2_hero_cta_text_ar" value="{{ old('france2_hero_cta_text_ar', data_get($hero, 'primary_cta_text_ar')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button URL (رابط الزر)</label><input class="form-control" name="france2_hero_cta_url" value="{{ old('france2_hero_cta_url', data_get($hero, 'primary_cta_url')) }}"></div>
        </div>
    </div>

    <!-- 2. INTRO SECTION -->
    <div class="card admin-card p-4 shadow-sm">
        <h2 class="h5 mb-3 text-navy fw-bold">2️⃣ نبذة بسيطة عن فيزا فرنسا (Intro)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Title EN</label><input class="form-control" name="france2_intro_title_en" value="{{ old('france2_intro_title_en', data_get($intro, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Title AR (عنوان القسم)</label><input class="form-control text-end" dir="rtl" name="france2_intro_title_ar" value="{{ old('france2_intro_title_ar', data_get($intro, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Description EN</label><textarea class="form-control" name="france2_intro_desc_en" rows="3">{{ old('france2_intro_desc_en', data_get($intro, 'description_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Description AR (الفقرة القصيرة)</label><textarea class="form-control text-end" dir="rtl" name="france2_intro_desc_ar" rows="3">{{ old('france2_intro_desc_ar', data_get($intro, 'description_ar')) }}</textarea></div>
        </div>
    </div>

    <!-- 3. REQUIREMENTS REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '3️⃣ المتطلبات الأساسية (Key Requirements)',
        'description' => 'إضافة وتعديل وحذف عناصر المتطلبات والملاحظة المرفقة.',
        'sectionFields' => [
            ['label' => 'Requirements Title EN', 'name' => 'france2_req_title_en', 'value' => old('france2_req_title_en', data_get($requirements, 'title_en'))],
            ['label' => 'Requirements Title AR (عنوان قسم المتطلبات)', 'name' => 'france2_req_title_ar', 'value' => old('france2_req_title_ar', data_get($requirements, 'title_ar')), 'rtl' => true],
        ],
        'sectionTextareas' => [
            ['label' => 'Note EN', 'name' => 'france2_req_note_en', 'value' => old('france2_req_note_en', data_get($requirements, 'note_en'))],
            ['label' => 'Note AR (ملاحظة التنبيه أسفل المتطلبات)', 'name' => 'france2_req_note_ar', 'value' => old('france2_req_note_ar', data_get($requirements, 'note_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france2-req-items',
        'buttonLabel' => '➕ إضافة شرط / متطلب جديد',
        'items' => $reqItems,
        'fields' => [
            ['label' => 'Icon / أيقونة (Emoji/FA)', 'key' => 'icon'],
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR (عنوان المتطلب)', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
            ['label' => 'Text AR (شرح المتطلب)', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'france2_req_items',
    ])

    <!-- 4. APPLICATION STEPS REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '4️⃣ خطوات التقديم (Application Steps)',
        'description' => 'إضافة وتعديل وترتيب خطوات التقديم الـ 4.',
        'sectionFields' => [
            ['label' => 'Steps Title EN', 'name' => 'france2_steps_title_en', 'value' => old('france2_steps_title_en', data_get($steps, 'title_en'))],
            ['label' => 'Steps Title AR (عنوان إجراءات التقديم)', 'name' => 'france2_steps_title_ar', 'value' => old('france2_steps_title_ar', data_get($steps, 'title_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france2-steps-items',
        'buttonLabel' => '➕ إضافة خطوة تقديم جديدة',
        'items' => $stepItems,
        'fields' => [
            ['label' => 'Step Number / رقم الخطوة (01)', 'key' => 'step_number'],
            ['label' => 'Title EN', 'key' => 'title_en'],
            ['label' => 'Title AR (عنوان الخطوة)', 'key' => 'title_ar', 'rtl' => true],
            ['label' => 'Text EN', 'key' => 'text_en', 'type' => 'textarea'],
            ['label' => 'Text AR (شرح الخطوة)', 'key' => 'text_ar', 'type' => 'textarea', 'rtl' => true],
            ['label' => 'Sort Order', 'key' => 'sort_order', 'type' => 'number'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'checkbox'],
        ],
        'inputName' => 'france2_step_items',
    ])

    <!-- 5. SERVICES REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '5️⃣ خدمات ترافل ويف (Travel Wave Services)',
        'description' => 'إضافة وتعديل وحذف كروت خدمات الشركة لفيزا فرنسا.',
        'sectionFields' => [
            ['label' => 'Services Title EN', 'name' => 'france2_serv_title_en', 'value' => old('france2_serv_title_en', data_get($servicesSec, 'title_en'))],
            ['label' => 'Services Title AR (عنوان قسم الخدمات)', 'name' => 'france2_serv_title_ar', 'value' => old('france2_serv_title_ar', data_get($servicesSec, 'title_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france2-serv-items',
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
        'inputName' => 'france2_service_items',
    ])

    <!-- 6. SUITABILITY ASSESSMENT SECTION -->
    <div class="card admin-card p-4 shadow-sm">
        <h2 class="h5 mb-3 text-navy fw-bold">6️⃣ هل حالتك مناسبة للتقديم؟ (Suitability Assessment)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Title EN</label><input class="form-control" name="france2_suit_title_en" value="{{ old('france2_suit_title_en', data_get($suitability, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Title AR (عنوان السؤال)</label><input class="form-control text-end" dir="rtl" name="france2_suit_title_ar" value="{{ old('france2_suit_title_ar', data_get($suitability, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Description EN</label><textarea class="form-control" name="france2_suit_desc_en" rows="2">{{ old('france2_suit_desc_en', data_get($suitability, 'description_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Description AR (الشرح)</label><textarea class="form-control text-end" dir="rtl" name="france2_suit_desc_ar" rows="2">{{ old('france2_suit_desc_ar', data_get($suitability, 'description_ar')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Call Note EN</label><textarea class="form-control" name="france2_suit_note_en" rows="2">{{ old('france2_suit_note_en', data_get($suitability, 'note_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Call Note AR (فقرة التوجيه للتواصل)</label><textarea class="form-control text-end" dir="rtl" name="france2_suit_note_ar" rows="2">{{ old('france2_suit_note_ar', data_get($suitability, 'note_ar')) }}</textarea></div>
            <div class="col-md-4"><label class="form-label fw-bold">Button Text EN</label><input class="form-control" name="france2_suit_btn_en" value="{{ old('france2_suit_btn_en', data_get($suitability, 'button_text_en')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">Button Text AR (نص زر التواصل)</label><input class="form-control text-end" dir="rtl" name="france2_suit_btn_ar" value="{{ old('france2_suit_btn_ar', data_get($suitability, 'button_text_ar')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">Button URL (رابط الزر)</label><input class="form-control" name="france2_suit_btn_url" value="{{ old('france2_suit_btn_url', data_get($suitability, 'button_url')) }}"></div>
        </div>
    </div>

    <!-- 7. PRICING & DURATION SECTION -->
    <div class="card admin-card p-4 shadow-sm">
        <h2 class="h5 mb-3 text-navy fw-bold">7️⃣ مدة الإجراءات والرسوم (Duration & Fees)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Duration Box Title EN</label><input class="form-control" name="france2_dur_title_en" value="{{ old('france2_dur_title_en', data_get($pricingDuration, 'duration_title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Duration Box Title AR (عنوان مدة الإجراءات)</label><input class="form-control text-end" dir="rtl" name="france2_dur_title_ar" value="{{ old('france2_dur_title_ar', data_get($pricingDuration, 'duration_title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Duration Text EN</label><textarea class="form-control" name="france2_dur_text_en" rows="2">{{ old('france2_dur_text_en', data_get($pricingDuration, 'duration_text_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Duration Text AR (نص مدة الإجراءات)</label><textarea class="form-control text-end" dir="rtl" name="france2_dur_text_ar" rows="2">{{ old('france2_dur_text_ar', data_get($pricingDuration, 'duration_text_ar')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Fees Box Title EN</label><input class="form-control" name="france2_fees_title_en" value="{{ old('france2_fees_title_en', data_get($pricingDuration, 'fees_title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Fees Box Title AR (عنوان الرسوم)</label><input class="form-control text-end" dir="rtl" name="france2_fees_title_ar" value="{{ old('france2_fees_title_ar', data_get($pricingDuration, 'fees_title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Fees Text EN</label><textarea class="form-control" name="france2_fees_text_en" rows="2">{{ old('france2_fees_text_en', data_get($pricingDuration, 'fees_text_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Fees Text AR (نص الرسوم)</label><textarea class="form-control text-end" dir="rtl" name="france2_fees_text_ar" rows="2">{{ old('france2_fees_text_ar', data_get($pricingDuration, 'fees_text_ar')) }}</textarea></div>
        </div>
    </div>

    <!-- 8. FAQ REPEATER -->
    @include('admin.visa-countries.partials.repeater-card', [
        'title' => '8️⃣ الأسئلة الشائعة (Frequently Asked Questions)',
        'description' => 'إضافة وتعديل وحذف وتحديد ترتيب الأسئلة الشائعة وتفعيلها أو إخفائها.',
        'sectionFields' => [
            ['label' => 'FAQ Section Title EN', 'name' => 'france2_faq_title_en', 'value' => old('france2_faq_title_en', data_get($faq, 'title_en'))],
            ['label' => 'FAQ Section Title AR (عنوان قسم الأسئلة الشائعة)', 'name' => 'france2_faq_title_ar', 'value' => old('france2_faq_title_ar', data_get($faq, 'title_ar')), 'rtl' => true],
        ],
        'repeaterKey' => 'france2-faq-items',
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
        'inputName' => 'france2_faq_items',
    ])

    <!-- 9. IMPORTANT NOTICE SECTION -->
    <div class="card admin-card p-4 shadow-sm border-warning border-opacity-50">
        <h2 class="h5 mb-3 text-warning fw-bold">9️⃣ تنبيه مهم (Important Notice)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">Notice Title EN</label><input class="form-control" name="france2_notice_title_en" value="{{ old('france2_notice_title_en', data_get($notice, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Notice Title AR (عنوان التنبيه)</label><input class="form-control text-end" dir="rtl" name="france2_notice_title_ar" value="{{ old('france2_notice_title_ar', data_get($notice, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">Notice Text EN</label><textarea class="form-control" name="france2_notice_text_en" rows="3">{{ old('france2_notice_text_en', data_get($notice, 'text_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">Notice Text AR (نص التنبيه الرسمي)</label><textarea class="form-control text-end" dir="rtl" name="france2_notice_text_ar" rows="3">{{ old('france2_notice_text_ar', data_get($notice, 'text_ar')) }}</textarea></div>
        </div>
    </div>

    <!-- 10. FINAL CTA SECTION -->
    <div class="card admin-card p-4 shadow-sm border-success border-opacity-50">
        <h2 class="h5 mb-3 text-success fw-bold">🔟 شريط CTA النهائي (Final CTA Banner)</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold">CTA Title EN</label><input class="form-control" name="france2_cta_title_en" value="{{ old('france2_cta_title_en', data_get($cta, 'title_en')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">CTA Title AR (العنوان النهائي)</label><input class="form-control text-end" dir="rtl" name="france2_cta_title_ar" value="{{ old('france2_cta_title_ar', data_get($cta, 'title_ar')) }}"></div>
            <div class="col-md-6"><label class="form-label fw-bold">CTA Description EN</label><textarea class="form-control" name="france2_cta_desc_en" rows="2">{{ old('france2_cta_desc_en', data_get($cta, 'description_en')) }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-bold">CTA Description AR (الوصف النهائي)</label><textarea class="form-control text-end" dir="rtl" name="france2_cta_desc_ar" rows="2">{{ old('france2_cta_desc_ar', data_get($cta, 'description_ar')) }}</textarea></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button Text EN</label><input class="form-control" name="france2_cta_btn_en" value="{{ old('france2_cta_btn_en', data_get($cta, 'button_text_en')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button Text AR (نص الزر النهائي)</label><input class="form-control text-end" dir="rtl" name="france2_cta_btn_ar" value="{{ old('france2_cta_btn_ar', data_get($cta, 'button_text_ar')) }}"></div>
            <div class="col-md-4"><label class="form-label fw-bold">CTA Button URL (رابط الزر)</label><input class="form-control" name="france2_cta_btn_url" value="{{ old('france2_cta_btn_url', data_get($cta, 'button_url')) }}"></div>
        </div>
    </div>

</div>
