@extends('layouts.admin')

@section('page_title', $item->exists ? 'تعديل عنصر القائمة' : 'إضافة عنصر قائمة جديد')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-navy font-weight-bold">
            {{ $item->exists ? 'تعديل عنصر القائمة: ' . $item->title_ar : 'إضافة عنصر قائمة جديد' }}
        </h1>
        <p class="text-muted mb-0">تحديد نوع الرابط، الصفحة المرتبطة، العنوان، الأيقونة وموقع العرض.</p>
    </div>
    <a href="{{ route('admin.menu-items.index', ['location' => $item->location ?: 'header']) }}" class="btn btn-outline-secondary">
        ← العودة لإدارة القوائم
    </a>
</div>

<form method="post" action="{{ $item->exists ? route('admin.menu-items.update', $item) : route('admin.menu-items.store') }}" id="menu-item-form">
    @csrf
    @if($item->exists)
        @method('PUT')
    @endif

    <div class="card admin-card p-4 mb-4 shadow-sm">
        <h2 class="h5 mb-3 text-primary border-bottom pb-2">تفاصيل عنصر القائمة (Menu Item Details)</h2>

        <div class="row g-3">
            <!-- TYPE SELECTOR -->
            <div class="col-md-6">
                <label class="form-label fw-bold">نوع العنصر / Item Type <span class="text-danger">*</span></label>
                <select class="form-select form-select-lg" name="type" id="menu-item-type" required>
                    <option value="page" @selected(old('type', $item->type ?: 'page') === 'page')>📄 صفحة من الموقع (Existing Page)</option>
                    <option value="custom" @selected(old('type', $item->type) === 'custom')>🔗 رابط مخصص (Custom URL)</option>
                    <option value="section" @selected(old('type', $item->type) === 'section')>⚓ قسم داخل صفحة (Page Section / Anchor)</option>
                    <option value="submenu" @selected(old('type', $item->type) === 'submenu')>📁 رأس قائمة فرعية (Submenu Container)</option>
                </select>
                <div class="form-text">اختر كيفية توجيه الزائر عند النقر على هذا العنصر.</div>
            </div>

            <!-- LOCATION -->
            <div class="col-md-6">
                <label class="form-label fw-bold">موقع العرض / Location <span class="text-danger">*</span></label>
                <select class="form-select form-select-lg" name="location" required>
                    <option value="header" @selected(old('location', $item->location ?: 'header') === 'header')>🔝 القائمة الرئيسية بالهيدر (Main Header Menu)</option>
                    <option value="footer" @selected(old('location', $item->location) === 'footer')>🔻 قائمة التذييل (Footer Menu)</option>
                </select>
            </div>

            <!-- PAGE SELECTION FIELD -->
            <div class="col-md-6 js-type-field js-type-page js-type-section">
                <label class="form-label fw-bold">اختر الصفحة المرتبطة / Select Page</label>
                <select class="form-select" name="page_id" id="menu-item-page">
                    <option value="">-- اختر صفحة --</option>
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}"
                                data-title-ar="{{ $page->title_ar }}"
                                data-title-en="{{ $page->title_en }}"
                                @selected(old('page_id', $item->page_id) == $page->id)>
                            {{ $page->title_ar }} ({{ $page->title_en }}) [slug: {{ $page->slug ?: $page->key }}]
                        </option>
                    @endforeach
                </select>
                <div class="form-text">يتم توليد الرابط تلقائياً وتحديثه أوتوماتيكياً في حال تغير الـ Slug لاحقاً.</div>
            </div>

            <!-- CUSTOM URL / SECTION ANCHOR FIELD -->
            <div class="col-md-6 js-type-field js-type-custom js-type-section">
                <label class="form-label fw-bold" id="url-label">الرابط / URL</label>
                <input type="text" class="form-control" name="url" id="menu-item-url" value="{{ old('url', $item->url) }}" placeholder="https://example.com أو #services">
                <div class="form-text" id="url-hint">يمكنك إدخال رابط خارجي كامل أو اسم قسم مسبوقاً بـ #.</div>
            </div>

            <!-- PARENT MENU ITEM -->
            <div class="col-md-6">
                <label class="form-label fw-bold">العنصر الأب / Parent Menu Item</label>
                <select class="form-select" name="parent_id">
                    <option value="">عنصر رئيسي (مستوى أول / Root Item)</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $item->parent_id) == $parent->id)>
                            📁 {{ $parent->title_ar }} ({{ $parent->title_en }}) - [{{ strtoupper($parent->location) }}]
                        </option>
                    @endforeach
                </select>
                <div class="form-text">اختر العنصر الأب في حال رغبتك في جعل هذا العنصر منسدلاً داخل قائمة فرعية.</div>
            </div>

            <!-- TARGET WINDOW -->
            <div class="col-md-6">
                <label class="form-label fw-bold">فتح الرابط في / Target Window</label>
                <select class="form-select" name="target">
                    <option value="_self" @selected(old('target', $item->target ?: '_self') === '_self')>نفس النافذة (Same Window - _self)</option>
                    <option value="_blank" @selected(old('target', $item->target) === '_blank')>نافذة جديدة (New Window - _blank)</option>
                </select>
            </div>

            <!-- BILINGUAL TITLES -->
            <div class="col-md-6">
                <label class="form-label fw-bold">العنوان بالعربية / Title (Arabic) <span class="text-danger">*</span></label>
                <input type="text" class="form-control text-end" dir="rtl" name="title_ar" id="menu-title-ar" value="{{ old('title_ar', $item->title_ar) }}" placeholder="مثال: التأشيرات الخارجية">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">العنوان بالإنجليزية / Title (English) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title_en" id="menu-title-en" value="{{ old('title_en', $item->title_en) }}" placeholder="e.g. Overseas Visas">
            </div>

            <!-- ICON PICKER -->
            <div class="col-md-6">
                @include('admin.partials.icon-picker', [
                    'name' => 'icon',
                    'value' => old('icon', $item->icon),
                    'label' => 'الأيقونة (اختياري / Optional Icon)'
                ])
            </div>

            <!-- SORT ORDER -->
            <div class="col-md-3">
                <label class="form-label fw-bold">الترتيب / Sort Order</label>
                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order ?: 1) }}" min="1">
            </div>

            <!-- ACTIVE STATUS SWITCH -->
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check form-switch pb-2">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="item_is_active" @checked(old('is_active', $item->is_active ?? true))>
                    <label class="form-check-label fw-bold ms-2" for="item_is_active">مفعل / Active</label>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3">
        <a href="{{ route('admin.menu-items.index', ['location' => $item->location ?: 'header']) }}" class="btn btn-secondary px-4">إلغاء</a>
        <button type="submit" class="btn btn-primary tw-btn-primary px-5 fw-bold">
            💾 {{ $item->exists ? 'حفظ التعديلات' : 'إضافة إلى القائمة' }}
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('menu-item-type');
    const pageSelect = document.getElementById('menu-item-page');
    const titleArInput = document.getElementById('menu-title-ar');
    const titleEnInput = document.getElementById('menu-title-en');
    const urlLabel = document.getElementById('url-label');
    const urlHint = document.getElementById('url-hint');
    const urlInput = document.getElementById('menu-item-url');

    const updateFieldsVisibility = () => {
        const selectedType = typeSelect.value;

        // Hide all conditional type fields
        document.querySelectorAll('.js-type-field').forEach(el => el.style.display = 'none');

        if (selectedType === 'page') {
            document.querySelectorAll('.js-type-page').forEach(el => el.style.display = 'block');
        } else if (selectedType === 'custom') {
            document.querySelectorAll('.js-type-custom').forEach(el => el.style.display = 'block');
            urlLabel.textContent = 'الرابط المخصص / Custom URL';
            urlHint.textContent = 'أدخل رابط كامل مثل https://google.com';
        } else if (selectedType === 'section') {
            document.querySelectorAll('.js-type-section').forEach(el => el.style.display = 'block');
            urlLabel.textContent = 'اسم القسم / Section Anchor';
            urlHint.textContent = 'أدخل اسم القسم مسبوقاً بـ # مثل #services أو #faq';
        }
    };

    typeSelect.addEventListener('change', updateFieldsVisibility);
    updateFieldsVisibility();

    // Auto fill titles when page is selected if empty
    pageSelect?.addEventListener('change', () => {
        const option = pageSelect.options[pageSelect.selectedIndex];
        if (option && option.value) {
            if (!titleArInput.value.trim()) {
                titleArInput.value = option.dataset.titleAr || '';
            }
            if (!titleEnInput.value.trim()) {
                titleEnInput.value = option.dataset.titleEn || '';
            }
        }
    });
});
</script>
@endsection
