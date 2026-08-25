@extends('layouts.admin')

@section('title', 'إعدادات الدليل العام ومعاينة التأشيرة / Public Catalog Settings')

@section('content')
<div class="container-fluid px-3 py-2">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-sliders text-primary me-2"></i>مركز تحكم الدليل العام ومعاينة التأشيرة</h3>
            <p class="text-muted small mb-0">تخصيص كامل لعناصر العرض، رسائل الواتساب الذكية، الأزرار المخصصة، وربط النماذج والخرائط القائمة بالنظام.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.visa-database.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-right me-1"></i> العودة لقاعدة التأشيرات
            </a>
            <a href="{{ route('visa-database.public-catalog') }}" target="_blank" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-globe me-1"></i> معاينة الدليل العام الحية
            </a>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.visa-database.update-catalog-settings') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- 0. Custom Logo Settings -->
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-2">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-image text-primary me-2"></i>إدارة لوجو وهيدر الدليل العام والمعاينة (Independent Logo)
                        </h5>
                        <span class="badge bg-light text-secondary border">لوجو مخصص للدليل</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center g-4">
                            <div class="col-md-3 text-center border-end">
                                <div class="p-3 bg-light rounded border d-flex flex-column align-items-center justify-content-center" style="min-height: 140px;">
                                    @if($setting->logo_url)
                                        <img src="{{ $setting->logo_url }}" alt="Logo Preview" style="max-width: {{ $setting->logo_width ?: 180 }}px; max-height: {{ $setting->logo_height ?: 50 }}px; object-fit: {{ $setting->logo_keep_aspect_ratio ? 'contain' : 'fill' }};" class="mb-2">
                                        <span class="small text-success fw-bold"><i class="bi bi-check-circle me-1"></i>اللوجو الحالي للمعاينة</span>
                                    @else
                                        <i class="bi bi-image text-muted display-4 mb-2"></i>
                                        <span class="small text-muted">لا يوجد لوجو مخصص (يُستخدم لوجو الموقع)</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">رفع لوجو جديد</label>
                                        <input type="file" name="logo_file" class="form-control" accept="image/*">
                                        <div class="form-text fs-8">صيغ الصور المسموحة: PNG, JPG, WEBP, SVG.</div>
                                        @if($setting->logo_path)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogoCheck">
                                                <label class="form-check-label small text-danger fw-bold" for="removeLogoCheck">
                                                    حذف اللوجو المخصص والعودة للوجو الرئيسي
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold small">العرض (px)</label>
                                        <input type="number" name="logo_width" class="form-control" value="{{ $setting->logo_width ?: 180 }}" min="30" max="500">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold small">الارتفاع (px)</label>
                                        <input type="number" name="logo_height" class="form-control" value="{{ $setting->logo_height ?: 50 }}" min="15" max="200">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="logo_keep_aspect_ratio" value="1" id="aspectRatioCheck" {{ $setting->logo_keep_aspect_ratio ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-bold" for="aspectRatioCheck">
                                                الحفاظ على الأبعاد
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1. Section Visibility Toggles -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-eye text-primary me-2"></i>إظهار وإخفاء عناصر الدليل والمعاينة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-4">تتحكم هذه المفاتيح في ظهور أو إخفاء التفاصيل داخل كروت الدليل العام وصفحة معاينة الشروط للعميل:</p>

                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                                <div>
                                    <div class="fw-bold small text-dark">سعر الخدمة للعميل</div>
                                    <div class="text-muted small fs-8">إظهار قيمة السعر بالجنيه على الكروت وفي التفاصيل</div>
                                </div>
                                <div class="form-check form-switch fs-5 m-0">
                                    <input class="form-check-input" type="checkbox" name="show_price" value="1" {{ $setting->show_price ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                                <div>
                                    <div class="fw-bold small text-dark">رسوم السفارة وطريقة الدفع</div>
                                    <div class="text-muted small fs-8">إظهار قيمة رسوم السفارة والعملة المحددة</div>
                                </div>
                                <div class="form-check form-switch fs-5 m-0">
                                    <input class="form-check-input" type="checkbox" name="show_embassy_fee" value="1" {{ $setting->show_embassy_fee ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                                <div>
                                    <div class="fw-bold small text-dark">أيام العمل ومدة التأشيرة</div>
                                    <div class="text-muted small fs-8">إظهار المدة المتوقعة واستلام التأشيرة</div>
                                </div>
                                <div class="form-check form-switch fs-5 m-0">
                                    <input class="form-check-input" type="checkbox" name="show_working_days" value="1" {{ $setting->show_working_days ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                                <div>
                                    <div class="fw-bold small text-dark">شروط البصمة والمقابلة</div>
                                    <div class="text-muted small fs-8">إظهار إذا كانت البصمة أو المقابلة مطلوبة بالسفارة</div>
                                </div>
                                <div class="form-check form-switch fs-5 m-0">
                                    <input class="form-check-input" type="checkbox" name="show_biometrics" value="1" {{ $setting->show_biometrics ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                                <div>
                                    <div class="fw-bold small text-dark">الملاحظات والاستثناءات الهامة</div>
                                    <div class="text-muted small fs-8">إظهار التنبيهات المخصصة باللون الأصفر في المعاينة</div>
                                </div>
                                <div class="form-check form-switch fs-5 m-0">
                                    <input class="form-check-input" type="checkbox" name="show_notes" value="1" {{ $setting->show_notes ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                                <div>
                                    <div class="fw-bold small text-dark">زر معاينة الشروط والأوراق</div>
                                    <div class="text-muted small fs-8">إظهار زر المعايرة على كروت الدليل العام</div>
                                </div>
                                <div class="form-check form-switch fs-5 m-0">
                                    <input class="form-check-input" type="checkbox" name="show_preview_button" value="1" {{ $setting->show_preview_button ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                                <div>
                                    <div class="fw-bold small text-dark">زر الواتساب العائم (Floating Button)</div>
                                    <div class="text-muted small fs-8">زر يتأرجح أسفل الشاشة للتواصل المباشر</div>
                                </div>
                                <div class="form-check form-switch fs-5 m-0">
                                    <input class="form-check-input" type="checkbox" name="floating_whatsapp_enabled" value="1" {{ $setting->floating_whatsapp_enabled ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. WhatsApp Settings & Builders -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-whatsapp text-success me-2"></i>منشئ إعدادات الواتساب التفاعلي
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small">رقم الواتساب المستلم للتواصل</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-telephone text-success"></i></span>
                                <input type="text" name="whatsapp_phone" class="form-control" placeholder="201000000000" value="{{ $setting->whatsapp_phone }}">
                            </div>
                            <div class="form-text fs-8">اكتب الرقم بالكود الدولي بدون علامة + (مثال: 201000000000).</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">صياغة الرسالة التلقائية للعميل</label>
                            <textarea name="whatsapp_message_template" class="form-control" rows="4" placeholder="مرحباً ترافيل ويف، أود الاستفسار والتقديم على تأشيرة {country_name} ({visa_type})">{{ $setting->whatsapp_message_template }}</textarea>
                            <div class="p-2 bg-light rounded border mt-2 fs-8">
                                <strong>المتغيرات الذكية المتاحة للاستخدام:</strong><br>
                                <code>{country_name}</code> : يُستبدل تلقائياً باسم الدولة<br>
                                <code>{visa_type}</code> : يُستبدل بنوع التأشيرة (سياحة / عمل)<br>
                                <code>{price}</code> : يُستبدل بسعر التأشيرة في العرض
                            </div>
                        </div>

                        <!-- System Manager Integrations -->
                        <div class="border-top pt-4">
                            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-diagram-3 text-primary me-2"></i>الربط مع أنظمة النماذج والخرائط بالسيستم</h6>

                            <!-- Form Manager Select -->
                            <div class="mb-3 p-3 bg-primary-subtle rounded border border-primary-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold small mb-0 text-primary"><i class="bi bi-file-earmark-person me-1"></i>نموذج تسجيل العملاء (Lead Form)</label>
                                    @if(Route::has('admin.lead-forms.index'))
                                        <a href="{{ route('admin.lead-forms.index') }}" target="_blank" class="small fw-bold text-decoration-none">
                                            <i class="bi bi-plus-circle me-1"></i>إدارة النماذج والعملاء
                                        </a>
                                    @endif
                                </div>
                                <select name="selected_lead_form_id" class="form-select">
                                    <option value="">-- التلقائي بحسب الدولة (LeadFormManager) --</option>
                                    @foreach($leadForms as $form)
                                        <option value="{{ $form->id }}" {{ $setting->selected_lead_form_id == $form->id ? 'selected' : '' }}>
                                            {{ $form->name }} ({{ $form->fields_count ?? $form->fields()->count() }} حقل)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text fs-8 text-dark-50">اختر نموذجاً مخصصاً ليظهر للعميل بصفحة المعايرة والدليل. يمكنك تعديل حقوله من قسم (إدارة النماذج والعملاء).</div>
                            </div>

                            <!-- Map Manager Select -->
                            <div class="p-3 bg-info-subtle rounded border border-info-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold small mb-0 text-info-emphasis"><i class="bi bi-geo-alt me-1"></i>خريطة موقع السفارة / المركز (Map Section)</label>
                                    @if(Route::has('admin.map-sections.index'))
                                        <a href="{{ route('admin.map-sections.index') }}" target="_blank" class="small fw-bold text-decoration-none">
                                            <i class="bi bi-plus-circle me-1"></i>إدارة الخرائط
                                        </a>
                                    @endif
                                </div>
                                <select name="selected_map_section_id" class="form-select">
                                    <option value="">-- التلقائي بحسب الدولة (MapSectionManager) --</option>
                                    @foreach($mapSections as $map)
                                        <option value="{{ $map->id }}" {{ $setting->selected_map_section_id == $map->id ? 'selected' : '' }}>
                                            {{ $map->title_ar }} ({{ $map->location_name_ar ?: 'Google Maps' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text fs-8 text-dark-50">اختر خريطة تفاعلية لتظهر للعميل. يمكنك تعديل كود التضمين وعنوان الجوجل ماب من قسم (إدارة الخرائط).</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Custom Action Buttons Builder -->
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-menu-button-wide text-primary me-2"></i>منشئ الأزرار المخصصة (Custom Action Buttons)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustomButtonRow()">
                            <i class="bi bi-plus-circle me-1"></i> إضافة زر جديد
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">تتيح لك إضافة أزرار تفاعلية مخصصة تظهر للعملاء في كروت الدليل العام وصفحة التفاصيل (مثال: زر تحميل ملف PDF، رابط خارجي، أو زر اتصال فوري).</p>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="customButtonsTable">
                                <thead class="table-light small">
                                    <tr>
                                        <th style="width: 25%;">اسم الزر (عربي)</th>
                                        <th style="width: 35%;">الرابط أو الملف (URL)</th>
                                        <th style="width: 15%;">الأيقونة (Bootstrap Icon)</th>
                                        <th style="width: 15%;">لون وشكل الزر</th>
                                        <th style="width: 5%;" class="text-center">تفعيل</th>
                                        <th style="width: 5%;" class="text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($buttons = $setting->custom_buttons ?? [])
                                    @forelse($buttons as $index => $btn)
                                        <tr>
                                            <td>
                                                <input type="text" name="custom_buttons[{{ $index }}][title_ar]" class="form-control form-control-sm" value="{{ $btn['title_ar'] ?? '' }}" placeholder="مثال: تحميل قائمة المستندات" required>
                                            </td>
                                            <td>
                                                <input type="text" name="custom_buttons[{{ $index }}][url]" class="form-control form-control-sm" value="{{ $btn['url'] ?? '' }}" placeholder="https://..." required>
                                            </td>
                                            <td>
                                                <input type="text" name="custom_buttons[{{ $index }}][icon]" class="form-control form-control-sm" value="{{ $btn['icon'] ?? 'bi-file-pdf' }}" placeholder="bi-file-pdf">
                                            </td>
                                            <td>
                                                <select name="custom_buttons[{{ $index }}][button_class]" class="form-select form-select-sm">
                                                    <option value="btn-outline-primary" {{ ($btn['button_class'] ?? '') == 'btn-outline-primary' ? 'selected' : '' }}>أزرق (Outline)</option>
                                                    <option value="btn-success" {{ ($btn['button_class'] ?? '') == 'btn-success' ? 'selected' : '' }}>أخضر (Success)</option>
                                                    <option value="btn-outline-success" {{ ($btn['button_class'] ?? '') == 'btn-outline-success' ? 'selected' : '' }}>أخضر مفرغ</option>
                                                    <option value="btn-warning" {{ ($btn['button_class'] ?? '') == 'btn-warning' ? 'selected' : '' }}>أصفر (Warning)</option>
                                                    <option value="btn-danger" {{ ($btn['button_class'] ?? '') == 'btn-danger' ? 'selected' : '' }}>أحمر (Danger)</option>
                                                    <option value="btn-dark" {{ ($btn['button_class'] ?? '') == 'btn-dark' ? 'selected' : '' }}>أسود (Dark)</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="custom_buttons[{{ $index }}][is_active]" value="1" class="form-check-input" {{ !empty($btn['is_active']) ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="emptyButtonsRow">
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                لا توجد أزرار مخصصة مضافة حالياً. اضغط على "إضافة زر جديد" لإضافة أزرار تفاعلية.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                <i class="bi bi-save me-2"></i>حفظ إعدادات الدليل العام
            </button>
        </div>
    </form>
</div>

<script>
let customButtonIndex = {{ count($setting->custom_buttons ?? []) }};

function addCustomButtonRow() {
    const emptyRow = document.getElementById('emptyButtonsRow');
    if (emptyRow) emptyRow.remove();

    const tbody = document.querySelector('#customButtonsTable tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="text" name="custom_buttons[\${customButtonIndex}][title_ar]" class="form-control form-control-sm" placeholder="مثال: تحميل نموذج الطلب" required>
        </td>
        <td>
            <input type="text" name="custom_buttons[\${customButtonIndex}][url]" class="form-control form-control-sm" placeholder="https://..." required>
        </td>
        <td>
            <input type="text" name="custom_buttons[\${customButtonIndex}][icon]" class="form-control form-control-sm" value="bi-file-pdf" placeholder="bi-file-pdf">
        </td>
        <td>
            <select name="custom_buttons[\${customButtonIndex}][button_class]" class="form-select form-select-sm">
                <option value="btn-outline-primary" selected>أزرق (Outline)</option>
                <option value="btn-success">أخضر (Success)</option>
                <option value="btn-outline-success">أخضر مفرغ</option>
                <option value="btn-warning">أصفر (Warning)</option>
                <option value="btn-danger">أحمر (Danger)</option>
                <option value="btn-dark">أسود (Dark)</option>
            </select>
        </td>
        <td class="text-center">
            <input type="checkbox" name="custom_buttons[\${customButtonIndex}][is_active]" value="1" class="form-check-input" checked>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    customButtonIndex++;
}
</script>
@endsection
