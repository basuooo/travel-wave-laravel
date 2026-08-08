@extends('layouts.admin')

@php
    $sections = $item->sections ?? [];
    $orderedSections = $item->getOrderedSections();
@endphp

@section('page_title', $item->exists ? 'تعديل وجهة التأشيرة: ' . ($item->localized('name') ?: $item->name_en) : 'إنشاء وجهة تأشيرة جديدة')
@section('page_description', 'إدارة محتوى وترتيب أقسام التأشيرة والنصوص والصور الخاصة بها بسهولة.')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.visa-countries.update', $item) : route('admin.visa-countries.store') }}" id="visa-country-edit-form">
    @csrf
    @if($item->exists)
        @method('PUT')
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-navy font-weight-bold">
                {{ $item->exists ? 'تعديل قالب دولة التأشيرة: ' . ($item->localized('name') ?: $item->name_en) : 'إنشاء وجهة تأشيرة جديدة' }}
            </h1>
            <p class="text-muted mb-0">إدارة محتوى وترتيب أقسام التأشيرة والنصوص والصور الخاصة بها بسهولة.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($item->exists && $item->frontendUrl())
                <a href="{{ $item->frontendUrl() }}" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer">
                    🌐 معاينة الصفحة
                </a>
            @endif
            <button type="submit" class="btn btn-primary tw-btn-primary px-4 fw-bold">
                💾 {{ __('admin.save_visa_page') }}
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
    <ul class="nav nav-pills custom-admin-tabs mb-4 bg-light p-2 rounded-3 border" id="visaCountryFormTabs" role="tablist">
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
    <div class="tab-content" id="visaCountryFormTabsContent">

        <!-- TAB 1: SECTION ORDERING & TOGGLING -->
        <div class="tab-pane fade show active" id="tab-ordering" role="tabpanel" style="display: block;">
            <div class="card admin-card p-4 mb-4 border-primary shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div>
                        <h2 class="h5 mb-1 text-primary fw-bold">🔀 ترتيب وإظهار/إخفاء أقسام الصفحة (Page Sections Arrangement)</h2>
                        <p class="text-muted small mb-0">يمكنك تعديل ترتيب الأقسام بالنقر على الأسهم ⬆️ ⬇️ أو كتابة رقم الترتيب، وكذلك تفعيل أو تعطيل أي قسم بسهولة.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tw-visa-sections-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">إظهار</th>
                                <th style="width: 110px;">الترتيب</th>
                                <th>اسم القسم / SECTION NAME</th>
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
                <h2 class="h5 mb-3">{{ __('admin.core_settings') }}</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('admin.visa_category') }}</label>
                        <select class="form-select" name="visa_category_id">
                            <option value="">{{ __('admin.select_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('visa_category_id', $item->visa_category_id) == $category->id)>{{ $category->localized('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><label class="form-label">اسم الدولة (EN)</label><input class="form-control" name="name_en" value="{{ old('name_en', $item->name_en) }}"></div>
                    <div class="col-md-4"><label class="form-label">اسم الدولة (AR)</label><input class="form-control text-end" dir="rtl" name="name_ar" value="{{ old('name_ar', $item->name_ar) }}"></div>
                    <div class="col-md-4"><label class="form-label">الرابط الدائم (Slug)</label><input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}"></div>
                    <div class="col-md-4"><label class="form-label">الترتيب (Sort Order)</label><input class="form-control" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 1) }}"></div>
                    <div class="col-md-4 d-flex align-items-end gap-3 pb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item->is_active ?? true))>
                            <label class="form-check-label" for="is_active">{{ __('admin.published') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $item->is_featured))>
                            <label class="form-check-label" for="is_featured">{{ __('admin.featured') }}</label>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="form-label">الوصف المختصر (EN)</label><textarea class="form-control" name="excerpt_en" rows="3">{{ old('excerpt_en', $item->excerpt_en) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">الوصف المختصر (AR)</label><textarea class="form-control text-end" dir="rtl" name="excerpt_ar" rows="3">{{ old('excerpt_ar', $item->excerpt_ar) }}</textarea></div>
                    <div class="col-lg-4">
                        <label class="form-label">{{ __('admin.desktop_hero_image') }}</label>
                        <input type="file" class="form-control" name="hero_image" accept="image/*">
                        @if($item->hero_image)
                            <img src="{{ asset('storage/' . $item->hero_image) }}" alt="" class="img-fluid rounded mt-2 border" style="max-height: 100px;">
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">{{ __('admin.mobile_hero_image') }}</label>
                        <input type="file" class="form-control" name="hero_mobile_image" accept="image/*">
                        @if($item->hero_mobile_image)
                            <img src="{{ asset('storage/' . $item->hero_mobile_image) }}" alt="" class="img-fluid rounded mt-2 border" style="max-height: 100px;">
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">{{ __('admin.flag_image') }}</label>
                        <input type="file" class="form-control" name="flag_image" accept="image/*">
                        @if($item->flag_image)
                            <img src="{{ asset('storage/' . $item->flag_image) }}" alt="" class="img-fluid rounded mt-2 border" style="max-height: 100px;">
                        @endif
                    </div>
                    <div class="col-md-6"><label class="form-label">{{ __('admin.meta_title_en') }}</label><input class="form-control" name="meta_title_en" value="{{ old('meta_title_en', $item->meta_title_en) }}"></div>
                    <div class="col-md-6"><label class="form-label">{{ __('admin.meta_title_ar') }}</label><input class="form-control text-end" dir="rtl" name="meta_title_ar" value="{{ old('meta_title_ar', $item->meta_title_ar) }}"></div>
                    <div class="col-md-6"><label class="form-label">{{ __('admin.meta_description_en') }}</label><textarea class="form-control" rows="3" name="meta_description_en">{{ old('meta_description_en', $item->meta_description_en) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">{{ __('admin.meta_description_ar') }}</label><textarea class="form-control text-end" dir="rtl" rows="3" name="meta_description_ar">{{ old('meta_description_ar', $item->meta_description_ar) }}</textarea></div>
                </div>
            </div>
        </div>

        <!-- TAB 3: SECTIONS CONTENT -->
        <div class="tab-pane fade" id="tab-sections" role="tabpanel" style="display: none;">
            @include('admin.pages.partials.france3-sections', ['sections' => $sections])
        </div>

    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary tw-btn-primary px-5 py-2 fw-bold">
            💾 {{ __('admin.save_visa_page') }}
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Custom Tab Switcher JS for Admin Tabs
    const tabButtons = document.querySelectorAll('#visaCountryFormTabs .nav-link');
    const tabPanes = document.querySelectorAll('#visaCountryFormTabsContent .tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-bs-target');

            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            tabPanes.forEach(pane => {
                if ('#' + pane.id === targetId) {
                    pane.classList.add('show', 'active');
                    pane.style.display = 'block';
                } else {
                    pane.classList.remove('show', 'active');
                    pane.style.display = 'none';
                }
            });
        });
    });

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
@endsection
