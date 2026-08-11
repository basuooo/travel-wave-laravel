@extends('layouts.admin')

@section('page_title', __('admin.website_settings') ?? 'إعدادات الموقع')
@section('page_description', __('admin.website_settings_desc') ?? 'إدارة حالة تشغيل الموقع العام، اختيار تصميم صفحة الصيانة، وإعادة التوجيه إلى روابط خارجية.')

@section('content')
<form method="post" action="{{ route('admin.website-settings.update') }}">
    @csrf
    @method('PUT')

    @if(isset($dbMigrated) && !$dbMigrated)
        <div class="alert alert-warning border-2 border-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
            <div>
                <h5 class="alert-heading mb-1 fw-bold">تنبيه هام: أعمدة قاعدة البيانات غير موجودة حتى الآن!</h5>
                <p class="mb-0 small">لحفظ حالة الموقع والتصاميم بنجاح، يرجى تشغيل أمر التحديث لقاعدة البيانات في Terminal:</p>
                <code class="d-inline-block bg-dark text-warning px-3 py-1 rounded-2 mt-2 fw-bold">php artisan migrate</code>
            </div>
        </div>
    @endif

    <!-- Card 1: Website Status & Control (فصل الموقع، الصيانة، والتحويل) -->
    <div class="card admin-card p-4 mb-4 border-start border-4 border-primary shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <h2 class="h5 mb-1 text-primary"><i class="bi bi-power me-2"></i>حالة الموقع العام والتحكم في التشغيل</h2>
                <p class="text-muted small mb-0">يمكنك التحكم في تشغيل أو إغلاق الموقع العام أو إعادة توجيهه لرابط خارجي دون التأثير على لوحة التحكم أو نظام الـ CRM.</p>
            </div>
            @php
                $status = old('site_status', $setting->site_status ?? 'active');
                $selectedTemplate = old('maintenance_template', $setting->maintenance_template ?? 'glassmorphism');
            @endphp
            <span class="badge {{ $status === 'active' ? 'bg-success' : ($status === 'maintenance' ? 'bg-warning text-dark' : 'bg-info text-dark') }} fs-6 px-3 py-2">
                {{ $status === 'active' ? '🟢 يعمل بشكل طبيعي' : ($status === 'maintenance' ? '🚧 تحت الصيانة' : '🔗 إعادة توجيه خارجي') }}
            </span>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="p-3 border rounded-3 bg-light text-center h-100">
                    <div class="form-check form-check-inline m-0 text-start w-100">
                        <input class="form-check-input" type="radio" name="site_status" id="status_active" value="active" {{ $status === 'active' ? 'checked' : '' }} onchange="toggleStatusSettings()">
                        <label class="form-check-label fw-bold text-success ms-2" for="status_active">
                            <i class="bi bi-check-circle-fill me-1"></i> نشط (Active)
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2">الموقع يعمل بشكل كامل واستقبال الحجوزات والزوار بشكل طبيعي.</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 border rounded-3 bg-light text-center h-100">
                    <div class="form-check form-check-inline m-0 text-start w-100">
                        <input class="form-check-input" type="radio" name="site_status" id="status_maintenance" value="maintenance" {{ $status === 'maintenance' ? 'checked' : '' }} onchange="toggleStatusSettings()">
                        <label class="form-check-label fw-bold text-warning text-dark ms-2" for="status_maintenance">
                            <i class="bi bi-tools me-1"></i> تحت الصيانة (Maintenance)
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2">عرض صفحة "تحت الصيانة" للزوار مع اختيار القالب والتعديل عليه.</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 border rounded-3 bg-light text-center h-100">
                    <div class="form-check form-check-inline m-0 text-start w-100">
                        <input class="form-check-input" type="radio" name="site_status" id="status_redirect" value="redirect" {{ $status === 'redirect' ? 'checked' : '' }} onchange="toggleStatusSettings()">
                        <label class="form-check-label fw-bold text-info text-dark ms-2" for="status_redirect">
                            <i class="bi bi-box-arrow-up-right me-1"></i> تحويل لرابط خارجي (Redirect)
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2">تحويل كافة الزوار تلقائياً لرابط خارجي (مثل صفحة فيسبوك أو موقع آخر).</small>
                </div>
            </div>
        </div>

        <!-- Section for Redirect Settings -->
        <div id="redirect_settings_section" class="p-3 border rounded-3 bg-white mb-3 shadow-sm" style="display: {{ $status === 'redirect' ? 'block' : 'none' }};">
            <h6 class="fw-bold text-info mb-2"><i class="bi bi-link-45deg me-1"></i> إعدادات إعادة التوجيه الخارجي</h6>
            <div class="col-12">
                <label class="form-label fw-semibold">رابط التحويل الخارجي (Redirect URL)</label>
                <input type="url" class="form-control" name="site_redirect_url" placeholder="https://facebook.com/yourpage" value="{{ old('site_redirect_url', $setting->site_redirect_url) }}">
                <small class="text-muted">سيتم تحويل الزائر مباشرة لهذا الرابط فور فتح الموقع.</small>
            </div>
        </div>

        <!-- Section for Maintenance Settings & Template Selector -->
        <div id="maintenance_settings_section" class="p-3 border rounded-3 bg-white mb-3 shadow-sm" style="display: {{ $status === 'maintenance' ? 'block' : 'none' }};">
            
            <!-- Template Selection Cards -->
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-palette-fill me-1 text-primary"></i> اختر تصميم صفحة الصيانة المناسب:</h6>
            <div class="row g-3 mb-4">
                
                <!-- Template 1: Glassmorphism Luxury -->
                <div class="col-md-4">
                    <div class="card h-100 border-2 {{ $selectedTemplate === 'glassmorphism' ? 'border-primary bg-light' : '' }} text-center p-3">
                        <div class="form-check text-start w-100 mb-2">
                            <input class="form-check-input" type="radio" name="maintenance_template" id="tpl_glassmorphism" value="glassmorphism" {{ $selectedTemplate === 'glassmorphism' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="tpl_glassmorphism">
                                🎨 1. زجاجي فاخر (Glassmorphism)
                            </label>
                        </div>
                        <div class="p-3 rounded-3 bg-dark text-white my-2 small">
                            <i class="bi bi-gear-wide-connected text-warning fs-3 d-block mb-1"></i>
                            تصميم زجاجي فاخر مع خلفية داكنة متفاعلة وألوان عصرية.
                        </div>
                        <span class="badge bg-secondary">عربي / إنجليزي</span>
                    </div>
                </div>

                <!-- Template 2: Minimal Countdown -->
                <div class="col-md-4">
                    <div class="card h-100 border-2 {{ $selectedTemplate === 'minimal_countdown' ? 'border-primary bg-light' : '' }} text-center p-3">
                        <div class="form-check text-start w-100 mb-2">
                            <input class="form-check-input" type="radio" name="maintenance_template" id="tpl_minimal" value="minimal_countdown" {{ $selectedTemplate === 'minimal_countdown' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="tpl_minimal">
                                ⏱️ 2. عداد تنازلي عصري (Countdown)
                            </label>
                        </div>
                        <div class="p-3 rounded-3 bg-dark text-white my-2 small">
                            <i class="bi bi-clock-history text-info fs-3 d-block mb-1"></i>
                            شاشة داكنة أنيقة تعرض العداد التنازلي لموعد عودة الموقع.
                        </div>
                        <span class="badge bg-secondary">عربي / إنجليزي</span>
                    </div>
                </div>

                <!-- Template 3: Travel Agency Banner -->
                <div class="col-md-4">
                    <div class="card h-100 border-2 {{ $selectedTemplate === 'agency_hero' ? 'border-primary bg-light' : '' }} text-center p-3">
                        <div class="form-check text-start w-100 mb-2">
                            <input class="form-check-input" type="radio" name="maintenance_template" id="tpl_agency" value="agency_hero" {{ $selectedTemplate === 'agency_hero' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="tpl_agency">
                                ✈️ 3. طيران وسياحة (Travel Hero)
                            </label>
                        </div>
                        <div class="p-3 rounded-3 bg-dark text-white my-2 small">
                            <i class="bi bi-airplane-engines text-danger fs-3 d-block mb-1"></i>
                            تصميم خاص بمنتج السياحة مع خلفية سفر وشارة تحديث الحجوزات.
                        </div>
                        <span class="badge bg-secondary">عربي / إنجليزي</span>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Template Customization Fields -->
            <h6 class="fw-bold text-warning text-dark mb-3"><i class="bi bi-pencil-square me-1"></i> تخصيص النصوص وموعد الانتهاء:</h6>
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">عنوان الصيانة (بالعربية)</label>
                    <input class="form-control text-end" dir="rtl" name="maintenance_title_ar" placeholder="الموقع تحت الصيانة حالياً" value="{{ old('maintenance_title_ar', $setting->maintenance_title_ar ?: 'الموقع تحت الصيانة والتطوير') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Maintenance Title (English)</label>
                    <input class="form-control" name="maintenance_title_en" placeholder="Site Under Maintenance" value="{{ old('maintenance_title_en', $setting->maintenance_title_en ?: 'We will be back soon!') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">رسالة الصيانة (بالعربية)</label>
                    <textarea class="form-control text-end" dir="rtl" name="maintenance_message_ar" rows="3" placeholder="نقوم حالياً بإجراء تحديثات لتحسين تجربتكم...">{{ old('maintenance_message_ar', $setting->maintenance_message_ar ?: 'نقوم حالياً بإجراء تحديثات وتطويرات لتحسين تجربتكم. يسعدنا تواصلكم معنا عبر الواتساب أو شبكات التواصل الاجتماعية.') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Maintenance Message (English)</label>
                    <textarea class="form-control" name="maintenance_message_en" rows="3" placeholder="We are currently performing scheduled maintenance...">{{ old('maintenance_message_en', $setting->maintenance_message_en ?: 'We are currently performing scheduled maintenance. Please get in touch with us via WhatsApp or social media.') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">موعد انتهاء الصيانة المتوقع (لإظهار العداد التنازلي)</label>
                    <input type="datetime-local" class="form-control" name="maintenance_end_time" value="{{ old('maintenance_end_time', $setting->maintenance_end_time ? \Carbon\Carbon::parse($setting->maintenance_end_time)->format('Y-m-d\TH:i') : '') }}">
                    <small class="text-muted">إذا تم تحديده، سيتم تشغيل عداد تنازلي مباشر على التصميم.</small>
                </div>
            </div>

            <div class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" role="switch" id="maintenance_bypass_admin" name="maintenance_bypass_admin" value="1" {{ old('maintenance_bypass_admin', $setting->maintenance_bypass_admin ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold ms-2" for="maintenance_bypass_admin">
                    السماح للمدراء المسجلين بالدخول بمعاينة الموقع العام أثناء الصيانة
                </label>
            </div>
        </div>
    </div>

    <button class="btn btn-primary px-4 shadow-sm"><i class="bi bi-save me-1"></i> حفظ إعدادات وتصميم الموقع</button>
</form>

<script>
    function toggleStatusSettings() {
        const statusRedirect = document.getElementById('status_redirect').checked;
        const statusMaintenance = document.getElementById('status_maintenance').checked;

        document.getElementById('redirect_settings_section').style.display = statusRedirect ? 'block' : 'none';
        document.getElementById('maintenance_settings_section').style.display = statusMaintenance ? 'block' : 'none';
    }
</script>
@endsection
