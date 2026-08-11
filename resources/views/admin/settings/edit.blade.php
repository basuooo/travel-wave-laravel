@extends('layouts.admin')

@section('page_title', __('admin.brand_settings'))
@section('page_description', __('admin.brand_settings_desc'))

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <!-- Website Status & Control Panel Card -->
    <div class="card admin-card p-4 mb-4 border-start border-4 border-primary">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <h2 class="h5 mb-1 text-primary"><i class="bi bi-power me-2"></i>حالة الموقع الإلكتروني (Website Status)</h2>
                <p class="text-muted small mb-0">يمكنك التحكم في تشغيل أو إغلاق الموقع العام أو إعادة توجيهه لرابط خارجي دون التأثير على لوحة التحكم.</p>
            </div>
            @php
                $status = old('site_status', $setting->site_status ?? 'active');
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
                    <small class="text-muted d-block mt-2">عرض صفحة "تحت الصيانة" للزوار مع احتفاظك بإمكانية تصفح الموقع كأدمن.</small>
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

        <!-- Section for Maintenance Settings -->
        <div id="maintenance_settings_section" class="p-3 border rounded-3 bg-white mb-3 shadow-sm" style="display: {{ $status === 'maintenance' ? 'block' : 'none' }};">
            <h6 class="fw-bold text-warning text-dark mb-3"><i class="bi bi-gear-fill me-1"></i> إعدادات صفحة الصيانة</h6>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">عنوان الصيانة (بالعربية)</label>
                    <input class="form-control text-end" dir="rtl" name="maintenance_title_ar" placeholder="الموقع تحت الصيانة حالياً" value="{{ old('maintenance_title_ar', $setting->maintenance_title_ar ?: 'الموقع تحت الصيانة والتطوير') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maintenance Title (English)</label>
                    <input class="form-control" name="maintenance_title_en" placeholder="Site Under Maintenance" value="{{ old('maintenance_title_en', $setting->maintenance_title_en ?: 'We will be back soon!') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">رسالة الصيانة (بالعربية)</label>
                    <textarea class="form-control text-end" dir="rtl" name="maintenance_message_ar" rows="3" placeholder="نقوم حالياً بإجراء تحديثات لتحسين تجربتكم...">{{ old('maintenance_message_ar', $setting->maintenance_message_ar ?: 'نقوم حالياً بإجراء تحديثات وتطويرات لتحسين تجربتكم. يسعدنا تواصلكم معنا عبر الواتساب أو شبكات التواصل الاجتماعية.') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Maintenance Message (English)</label>
                    <textarea class="form-control" name="maintenance_message_en" rows="3" placeholder="We are currently performing scheduled maintenance...">{{ old('maintenance_message_en', $setting->maintenance_message_en ?: 'We are currently performing scheduled maintenance. Please get in touch with us via WhatsApp or social media.') }}</textarea>
                </div>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="maintenance_bypass_admin" name="maintenance_bypass_admin" value="1" {{ old('maintenance_bypass_admin', $setting->maintenance_bypass_admin ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold ms-2" for="maintenance_bypass_admin">
                    السماح للمدراء المسجلين بالدخول بمعاينة الموقع العام أثناء الصيانة
                </label>
            </div>
        </div>
    </div>

    <script>
        function toggleStatusSettings() {
            const statusRedirect = document.getElementById('status_redirect').checked;
            const statusMaintenance = document.getElementById('status_maintenance').checked;

            document.getElementById('redirect_settings_section').style.display = statusRedirect ? 'block' : 'none';
            document.getElementById('maintenance_settings_section').style.display = statusMaintenance ? 'block' : 'none';
        }
    </script>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.brand_identity') }}</h2>
        <div class="row g-4">
            <div class="col-md-6"><label class="form-label">{{ __('admin.site_name_en') }}</label><input class="form-control" name="site_name_en" value="{{ old('site_name_en', $setting->site_name_en) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.site_name_ar') }}</label><input class="form-control text-end" dir="rtl" name="site_name_ar" value="{{ old('site_name_ar', $setting->site_name_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.tagline_en') }}</label><input class="form-control" name="site_tagline_en" value="{{ old('site_tagline_en', $setting->site_tagline_en) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.tagline_ar') }}</label><input class="form-control text-end" dir="rtl" name="site_tagline_ar" value="{{ old('site_tagline_ar', $setting->site_tagline_ar) }}"></div>
            <div class="col-lg-4"><label class="form-label">{{ __('admin.favicon') }}</label><input type="file" class="form-control" name="favicon" accept="image/*">@if($setting->favicon_path)<div class="mt-3 p-3 border rounded-4 bg-light d-inline-flex"><img src="{{ asset('storage/' . $setting->favicon_path) }}" alt="" style="width: 48px; height: 48px; object-fit: contain;"></div>@endif</div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.global_brand_colors') }}</h2>
        <div class="row g-3">
            <div class="col-md-2"><label class="form-label">{{ __('admin.primary_brand') }}</label><input class="form-control form-control-color w-100" type="color" name="primary_color" value="{{ old('primary_color', $setting->primary_color ?: '#12395b') }}"></div>
            <div class="col-md-2"><label class="form-label">{{ __('admin.secondary_brand') }}</label><input class="form-control form-control-color w-100" type="color" name="secondary_color" value="{{ old('secondary_color', $setting->secondary_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">{{ __('admin.accent_cta') }}</label><input class="form-control form-control-color w-100" type="color" name="accent_color" value="{{ old('accent_color', $setting->accent_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">{{ __('admin.button_color') }}</label><input class="form-control form-control-color w-100" type="color" name="button_color" value="{{ old('button_color', $setting->button_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">{{ __('admin.button_hover') }}</label><input class="form-control form-control-color w-100" type="color" name="button_hover_color" value="{{ old('button_hover_color', $setting->button_hover_color ?: '#ef5c00') }}"></div>
            <div class="col-md-2"><label class="form-label">{{ __('admin.link_hover') }}</label><input class="form-control form-control-color w-100" type="color" name="link_hover_color" value="{{ old('link_hover_color', $setting->link_hover_color ?: '#ff8c32') }}"></div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.seo_global_cta') }}</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">{{ __('admin.meta_title_en') }}</label><input class="form-control" name="default_meta_title_en" value="{{ old('default_meta_title_en', $setting->default_meta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.meta_title_ar') }}</label><input class="form-control text-end" dir="rtl" name="default_meta_title_ar" value="{{ old('default_meta_title_ar', $setting->default_meta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.meta_description_en') }}</label><textarea class="form-control" name="default_meta_description_en" rows="3">{{ old('default_meta_description_en', $setting->default_meta_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.meta_description_ar') }}</label><textarea class="form-control text-end" dir="rtl" name="default_meta_description_ar" rows="3">{{ old('default_meta_description_ar', $setting->default_meta_description_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.cta_title_en') }}</label><input class="form-control" name="global_cta_title_en" value="{{ old('global_cta_title_en', $setting->global_cta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.cta_title_ar') }}</label><input class="form-control text-end" dir="rtl" name="global_cta_title_ar" value="{{ old('global_cta_title_ar', $setting->global_cta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.cta_text_en') }}</label><textarea class="form-control" name="global_cta_text_en" rows="3">{{ old('global_cta_text_en', $setting->global_cta_text_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.cta_text_ar') }}</label><textarea class="form-control text-end" dir="rtl" name="global_cta_text_ar" rows="3">{{ old('global_cta_text_ar', $setting->global_cta_text_ar) }}</textarea></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.cta_button_en') }}</label><input class="form-control" name="global_cta_button_en" value="{{ old('global_cta_button_en', $setting->global_cta_button_en) }}"></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.cta_button_ar') }}</label><input class="form-control text-end" dir="rtl" name="global_cta_button_ar" value="{{ old('global_cta_button_ar', $setting->global_cta_button_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.cta_url') }}</label><input class="form-control" name="global_cta_url" value="{{ old('global_cta_url', $setting->global_cta_url) }}"></div>
        </div>
    </div>

    <button class="btn btn-primary px-4">{{ __('admin.save_brand_settings') }}</button>
</form>
@endsection
