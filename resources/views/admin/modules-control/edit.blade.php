@extends('layouts.admin')

@section('page_title', 'إدارة موديولات النظام (System Modules Control)')
@section('page_description', 'تحكم في تفعيل أو إخفاء موديولات وأقسام النظام بالكامل لجميع أفراد فريق العمل.')

@section('content')
<form method="post" action="{{ route('admin.modules-control.update') }}">
    @csrf
    @method('PUT')

    @if(isset($dbMigrated) && !$dbMigrated)
        <div class="alert alert-warning border-2 border-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
            <div>
                <h5 class="alert-heading mb-1 fw-bold">تنبيه هام: يتم التخزين التلقائي الآن في ملف الإعدادات السريع</h5>
                <p class="mb-0 small">تغييرات الموديولات تعمل وتُحفظ فوراً على جميع الأقسام. لتشغيل التحديث على مستوى قاعدة البيانات أيضاً، نفذ:</p>
                <code class="d-inline-block bg-dark text-warning px-3 py-1 rounded-2 mt-2 fw-bold">php artisan migrate</code>
            </div>
        </div>
    @endif

    <!-- Header Summary Card -->
    <div class="card admin-card p-4 mb-4 border-start border-4 border-info shadow-sm">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="h5 mb-1 text-info"><i class="bi bi-grid-3x3-gap-fill me-2"></i>مركز التحكم في موديولات النظام الكلية</h2>
                <p class="text-muted small mb-0">يمكنك إتاحة أو إخفاء أي موديول من القائمة الجانبية للنظام بضغطة زر (مثالي جداً لبيع النظام مجزأً كـ CRM فقط أو موقع فقط).</p>
            </div>
            <span class="badge bg-primary px-3 py-2 fs-6"><i class="bi bi-shield-check me-1"></i> تحكم كامل للسوبر أدمن</span>
        </div>
    </div>

    <!-- Grid of System Modules -->
    <div class="row g-4 mb-4">
        
        <!-- 1. Public Website Frontend -->
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card p-4 h-100 border-top border-4 border-primary shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 fs-4"><i class="bi bi-globe"></i></span>
                        <h3 class="h6 mb-0 fw-bold">الموقع العام (Frontend)</h3>
                    </div>
                    <span class="badge {{ ($setting->module_website_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->module_website_enabled ?? true) ? 'مُفعّل' : 'مُعطّل' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">الواجهة الرئيسية للموقع العام والتي يستعرض فيها العملاء والزوار الخدمات والرحلات.</p>
                <div class="form-check form-switch mt-auto pt-2 border-top">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" id="module_website_enabled" name="module_website_enabled" value="1" {{ ($setting->module_website_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="module_website_enabled">
                        تفعيل الواجهة العامة للموقع
                    </label>
                </div>
            </div>
        </div>

        <!-- 2. CRM Module -->
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card p-4 h-100 border-top border-4 border-success shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-success bg-opacity-10 text-success rounded-3 fs-4"><i class="bi bi-briefcase-fill"></i></span>
                        <h3 class="h6 mb-0 fw-bold">إدارة العملاء (CRM)</h3>
                    </div>
                    <span class="badge {{ ($setting->module_crm_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->module_crm_enabled ?? true) ? 'مُفعّل' : 'مُعطّل' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">إدارة طلبات المبيعات، العملاء، المهام، Pipeline، والمتابعات وتقارير أداء أفراد الفريق.</p>
                <div class="form-check form-switch mt-auto pt-2 border-top">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" id="module_crm_enabled" name="module_crm_enabled" value="1" {{ ($setting->module_crm_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="module_crm_enabled">
                        تفعيل موديول الـ CRM
                    </label>
                </div>
            </div>
        </div>

        <!-- 3. Accounting Module -->
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card p-4 h-100 border-top border-4 border-warning shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-warning bg-opacity-10 text-warning rounded-3 fs-4"><i class="bi bi-cash-stack"></i></span>
                        <h3 class="h6 mb-0 fw-bold">المحاسبة والمالية (Accounting)</h3>
                    </div>
                    <span class="badge {{ ($setting->module_accounting_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->module_accounting_enabled ?? true) ? 'مُفعّل' : 'مُعطّل' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">إدارة الحسابات، الخزائن، المصروفات العامة، حسابت الموظفين، والتقارير المالية.</p>
                <div class="form-check form-switch mt-auto pt-2 border-top">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" id="module_accounting_enabled" name="module_accounting_enabled" value="1" {{ ($setting->module_accounting_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="module_accounting_enabled">
                        تفعيل موديول المحاسبة
                    </label>
                </div>
            </div>
        </div>

        <!-- 4. Marketing Module -->
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card p-4 h-100 border-top border-4 border-danger shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-danger bg-opacity-10 text-danger rounded-3 fs-4"><i class="bi bi-rocket-takeoff-fill"></i></span>
                        <h3 class="h6 mb-0 fw-bold">التسويق والحملات (Marketing)</h3>
                    </div>
                    <span class="badge {{ ($setting->module_marketing_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->module_marketing_enabled ?? true) ? 'مُفعّل' : 'مُعطّل' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">صفحات الهبوط Landing Pages، الـ Popups، حملات UTM، وتتبع Meta CAPI.</p>
                <div class="form-check form-switch mt-auto pt-2 border-top">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" id="module_marketing_enabled" name="module_marketing_enabled" value="1" {{ ($setting->module_marketing_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="module_marketing_enabled">
                        تفعيل موديول التسويق
                    </label>
                </div>
            </div>
        </div>

        <!-- 5. AI Chatbot Module -->
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card p-4 h-100 border-top border-4 border-info shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-info bg-opacity-10 text-info rounded-3 fs-4"><i class="bi bi-robot"></i></span>
                        <h3 class="h6 mb-0 fw-bold">الشات بوت والذكاء الاصطناعي</h3>
                    </div>
                    <span class="badge {{ ($setting->module_chatbot_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->module_chatbot_enabled ?? true) ? 'مُفعّل' : 'مُعطّل' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">البوت الذكي للرد التلقائي، وإدارة قاعدة المعرفة الخاصة بإجابة أسئلة العملاء.</p>
                <div class="form-check form-switch mt-auto pt-2 border-top">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" id="module_chatbot_enabled" name="module_chatbot_enabled" value="1" {{ ($setting->module_chatbot_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="module_chatbot_enabled">
                        تفعيل الشات بوت والذكاء الاصطناعي
                    </label>
                </div>
            </div>
        </div>

        <!-- 6. Blog Module -->
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card p-4 h-100 border-top border-4 border-secondary shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-secondary bg-opacity-10 text-secondary rounded-3 fs-4"><i class="bi bi-journal-richtext"></i></span>
                        <h3 class="h6 mb-0 fw-bold">المدونة والمقالات (Blog)</h3>
                    </div>
                    <span class="badge {{ ($setting->module_blog_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->module_blog_enabled ?? true) ? 'مُفعّل' : 'مُعطّل' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">إدارة تصنيفات المقالات ونشر التدوينات والمقالات السياحية على الموقع.</p>
                <div class="form-check form-switch mt-auto pt-2 border-top">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" id="module_blog_enabled" name="module_blog_enabled" value="1" {{ ($setting->module_blog_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="module_blog_enabled">
                        تفعيل موديول المدونة
                    </label>
                </div>
            </div>
        </div>

        <!-- 7. Destinations & Visas Module -->
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card p-4 h-100 border-top border-4 border-primary shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 fs-4"><i class="bi bi-map-fill"></i></span>
                        <h3 class="h6 mb-0 fw-bold">البرامج والوجهات والتأشيرات</h3>
                    </div>
                    <span class="badge {{ ($setting->module_destinations_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->module_destinations_enabled ?? true) ? 'مُفعّل' : 'مُعطّل' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">إدارة وجهات السياحة الداخلية، تصنيفات ودول التأشيرات المتوفرة بالكامل.</p>
                <div class="form-check form-switch mt-auto pt-2 border-top">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" id="module_destinations_enabled" name="module_destinations_enabled" value="1" {{ ($setting->module_destinations_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="module_destinations_enabled">
                        تفعيل موديول الوجهات والتأشيرات
                    </label>
                </div>
            </div>
        </div>

        <!-- 8. Forms & Inquiries Module -->
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card p-4 h-100 border-top border-4 border-success shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-success bg-opacity-10 text-success rounded-3 fs-4"><i class="bi bi-ui-checks"></i></span>
                        <h3 class="h6 mb-0 fw-bold">النماذج والاستفسارات (Forms)</h3>
                    </div>
                    <span class="badge {{ ($setting->module_forms_enabled ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($setting->module_forms_enabled ?? true) ? 'مُفعّل' : 'مُعطّل' }}
                    </span>
                </div>
                <p class="text-muted small mb-3">إدارة منشئ النماذج الديناميكية واستقبال الاستفسارات وتوليد الحقول.</p>
                <div class="form-check form-switch mt-auto pt-2 border-top">
                    <input class="form-check-input fs-5" type="checkbox" role="switch" id="module_forms_enabled" name="module_forms_enabled" value="1" {{ ($setting->module_forms_enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="module_forms_enabled">
                        تفعيل موديول النماذج والاستفسارات
                    </label>
                </div>
            </div>
        </div>

    </div>

    <button class="btn btn-primary px-4 py-2 fs-6 shadow-sm"><i class="bi bi-save me-1"></i> حفظ وتطبيق تفعيل الموديولات</button>
</form>
@endsection
