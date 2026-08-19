<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ Visual Funnel Builder | {{ $funnel->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5, FontAwesome & Iconify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <!-- SortableJS for flawless Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <style>
        :root {
            --fb-topbar-h: 62px;
            --fb-sidebar-w: 320px;
            --fb-inspector-w: 380px;
            --fb-bg-dark: #090d16;
            --fb-panel-bg: #131b2e;
            --fb-panel-card: #1e293b;
            --fb-border: #26334d;
            --fb-accent: #3b82f6;
            --fb-primary: {{ $funnel->design_settings['primary_color'] ?? '#2563eb' }};
        }

        * { box-sizing: border-box; }
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Tajawal', system-ui, -apple-system, sans-serif;
            background: var(--fb-bg-dark);
            color: #f8fafc;
        }

        /* Topbar */
        .fb-topbar {
            height: var(--fb-topbar-h);
            background: #0b1120;
            border-bottom: 1px solid var(--fb-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            z-index: 100;
        }

        /* Main Layout */
        .fb-workspace {
            display: flex;
            height: calc(100vh - var(--fb-topbar-h));
            overflow: hidden;
        }

        /* Left Panel (Steps & Elements) */
        .fb-sidebar {
            width: var(--fb-sidebar-w);
            background: var(--fb-panel-bg);
            border-right: 1px solid var(--fb-border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Center Canvas */
        .fb-canvas-container {
            flex: 1;
            background: radial-gradient(circle at top, #172033 0%, #090d16 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2.5rem 1rem;
            overflow-y: auto;
            position: relative;
        }

        .fb-canvas {
            width: 100%;
            max-width: 680px;
            background: #ffffff;
            color: #0f172a;
            border-radius: 20px;
            min-height: 540px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .fb-canvas.mobile-mode {
            max-width: 390px;
            min-height: 680px;
            border: 12px solid #1e293b;
            border-radius: 36px;
            padding: 1.75rem;
        }

        /* Drop Zone */
        .drop-zone-highlight {
            border: 2px dashed var(--fb-accent) !important;
            background: rgba(59, 130, 246, 0.08) !important;
        }

        /* Right Panel Inspector */
        .fb-inspector {
            width: var(--fb-inspector-w);
            background: var(--fb-panel-bg);
            border-left: 1px solid var(--fb-border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Step Item */
        .fb-step-item {
            padding: 0.75rem 1rem;
            background: var(--fb-panel-card);
            border: 1px solid var(--fb-border);
            border-radius: 10px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
        }
        .fb-step-item:hover {
            border-color: var(--fb-accent);
            background: #1e3a8a33;
        }
        .fb-step-item.active {
            border-color: var(--fb-accent);
            background: #2563eb33;
            box-shadow: 0 0 0 1px var(--fb-accent);
        }

        /* Element Catalog Badge */
        .fb-element-badge {
            background: var(--fb-panel-card);
            border: 1px solid var(--fb-border);
            padding: 0.65rem 0.75rem;
            border-radius: 10px;
            cursor: grab;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: #cbd5e1;
            transition: all 0.2s;
            user-select: none;
        }
        .fb-element-badge:hover {
            background: #27354f;
            border-color: var(--fb-accent);
            color: #fff;
            transform: translateY(-2px);
        }
        .fb-element-badge:active {
            cursor: grabbing;
        }

        /* Canvas Element */
        .canvas-element-item {
            border: 2px dashed #e2e8f0;
            padding: 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.25rem;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
            background: #ffffff;
        }
        .canvas-element-item:hover {
            border-color: var(--fb-accent);
            background: #f8fafc;
        }
        .canvas-element-item.selected {
            border-color: var(--fb-accent);
            border-style: solid;
            background: #eff6ff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .canvas-element-toolbar {
            position: absolute;
            top: -14px;
            right: 12px;
            background: var(--fb-accent);
            color: #fff;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: none;
            gap: 6px;
            align-items: center;
            z-index: 10;
        }
        .canvas-element-item.selected .canvas-element-toolbar,
        .canvas-element-item:hover .canvas-element-toolbar {
            display: flex;
        }

        /* Custom UI Pills & Tabs */
        .nav-pills-custom .nav-link {
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            padding: 7px 12px;
            border-radius: 8px;
        }
        .nav-pills-custom .nav-link.active {
            background-color: var(--fb-accent);
            color: #fff;
        }

        .device-toggle-btn {
            background: #1e293b;
            border: 1px solid var(--fb-border);
            color: #cbd5e1;
            padding: 5px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .device-toggle-btn.active {
            background: var(--fb-accent);
            border-color: var(--fb-accent);
            color: #fff;
        }

        .form-control-dark, .form-select-dark {
            background: #1e293b;
            border: 1px solid var(--fb-border);
            color: #f8fafc;
            border-radius: 8px;
        }
        .form-control-dark:focus, .form-select-dark:focus {
            background: #1e293b;
            border-color: var(--fb-accent);
            color: #fff;
            box-shadow: none;
        }

        /* Notification Toast */
        #fb_toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: #fff;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            display: none;
            z-index: 9999;
            animation: fadeIn 0.2s;
        }
    </style>
</head>
<body>

<!-- NOTIFICATION TOAST -->
<div id="fb_toast">💾 تم حفظ الفانل بنجاح!</div>

<!-- TOPBAR -->
<header class="fb-topbar">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.funnels.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
            ← الفانلات
        </a>
        <div>
            <h6 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                <span>{{ $funnel->name }}</span>
                @if($funnel->status === 'published')
                    <span class="badge bg-success small">منشور (Live)</span>
                @else
                    <span class="badge bg-secondary small">مسودة (Draft)</span>
                @endif
            </h6>
            <small class="text-muted">/f/{{ $funnel->slug }}</small>
        </div>
    </div>

    <!-- Device Viewport Switcher -->
    <div class="d-flex align-items-center gap-1 bg-dark p-1 rounded-3 border border-secondary">
        <button type="button" class="device-toggle-btn active" id="btn_device_desktop" onclick="setDevice('desktop')">
            <iconify-icon icon="solar:laptop-minimalistic-bold" width="16"></iconify-icon>
            <span>كمبيوتر</span>
        </button>
        <button type="button" class="device-toggle-btn" id="btn_device_mobile" onclick="setDevice('mobile')">
            <iconify-icon icon="solar:smartphone-bold" width="16"></iconify-icon>
            <span>جوال</span>
        </button>
    </div>

    <!-- Actions -->
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('funnels.public.show', $funnel->slug) }}" target="_blank" class="btn btn-outline-light btn-sm rounded-3 d-flex align-items-center gap-1">
            <iconify-icon icon="solar:eye-bold" width="16"></iconify-icon>
            <span>معاينة حية</span>
        </a>
        <button type="button" class="btn btn-primary btn-sm fw-bold px-4 rounded-3 d-flex align-items-center gap-1" id="btn_save_funnel" onclick="saveFunnel()">
            <iconify-icon icon="solar:diskette-bold" width="18"></iconify-icon>
            <span>حفظ التعديلات 💾</span>
        </button>
        @if($funnel->status !== 'published')
            <form action="{{ route('admin.funnels.publish', $funnel) }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-success btn-sm fw-bold rounded-3">🚀 نشر الفانل</button>
            </form>
        @endif
    </div>
</header>

<!-- WORKSPACE -->
<div class="fb-workspace">
    <!-- LEFT PANEL: Steps List & Elements Catalog -->
    <aside class="fb-sidebar p-3">
        <!-- Steps Header -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-uppercase text-muted fw-bold small mb-0">📌 خطوات الفانل (Steps)</h6>
            <button type="button" class="btn btn-primary btn-sm py-1 px-2 rounded-2 small fw-bold" onclick="addNewStep()">
                ➕ إضافة خطوة
            </button>
        </div>
        <p class="text-muted" style="font-size: 11px;">اسحب لإعادة ترتيب الخطوات أو انقر للتعديل</p>

        <!-- Steps List (Sortable) -->
        <div id="steps_list_wrapper" class="mb-4">
            <!-- Rendered by JS -->
        </div>

        <!-- Element Catalog (Click or Drag to Canvas) -->
        <h6 class="text-uppercase text-muted fw-bold small mb-2">🧩 عناصر وأسئلة الفانل</h6>
        <p class="text-muted" style="font-size: 11px;">اسحب العنصر إلى الشاشة أو انقر عليه للإضافة فوراً</p>

        <div class="row g-2" id="elements_palette">
            <!-- Choice Elements -->
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="single_choice" onclick="addElementToCurrentStep('single_choice')">
                    <iconify-icon icon="solar:check-circle-bold-duotone" class="text-primary" width="18"></iconify-icon>
                    <span>اختيار فردي</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="multiple_choice" onclick="addElementToCurrentStep('multiple_choice')">
                    <iconify-icon icon="solar:checklist-bold-duotone" class="text-success" width="18"></iconify-icon>
                    <span>اختيار متعدد</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="image_choice" onclick="addElementToCurrentStep('image_choice')">
                    <iconify-icon icon="solar:gallery-bold-duotone" class="text-warning" width="18"></iconify-icon>
                    <span>بطاقات صور</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="dropdown" onclick="addElementToCurrentStep('dropdown')">
                    <iconify-icon icon="solar:menu-dots-square-bold-duotone" class="text-info" width="18"></iconify-icon>
                    <span>قائمة منسدلة</span>
                </div>
            </div>

            <!-- Inputs & Form -->
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="text_input" onclick="addElementToCurrentStep('text_input')">
                    <iconify-icon icon="solar:text-field-bold-duotone" class="text-light" width="18"></iconify-icon>
                    <span>حقل نصي قصير</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="contact_form" onclick="addElementToCurrentStep('contact_form')">
                    <iconify-icon icon="solar:user-id-bold-duotone" class="text-danger" width="18"></iconify-icon>
                    <span>نموذج اتصال CRM</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="slider" onclick="addElementToCurrentStep('slider')">
                    <iconify-icon icon="solar:slider-minimalistic-horizontal-bold-duotone" class="text-warning" width="18"></iconify-icon>
                    <span>سلايدر ميزانية</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="rating" onclick="addElementToCurrentStep('rating')">
                    <iconify-icon icon="solar:star-bold-duotone" class="text-warning" width="18"></iconify-icon>
                    <span>تقييم نجوم</span>
                </div>
            </div>

            <!-- Content Elements -->
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="heading" onclick="addElementToCurrentStep('heading')">
                    <iconify-icon icon="solar:text-bold-duotone" class="text-info" width="18"></iconify-icon>
                    <span>عنوان / Header</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="text" onclick="addElementToCurrentStep('text')">
                    <iconify-icon icon="solar:notes-bold-duotone" class="text-secondary" width="18"></iconify-icon>
                    <span>فقرة توضيحية</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="button" onclick="addElementToCurrentStep('button')">
                    <iconify-icon icon="solar:cursor-square-bold-duotone" class="text-primary" width="18"></iconify-icon>
                    <span>زر مخصص</span>
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" draggable="true" data-type="date_picker" onclick="addElementToCurrentStep('date_picker')">
                    <iconify-icon icon="solar:calendar-bold-duotone" class="text-success" width="18"></iconify-icon>
                    <span>تحديد تاريخ</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- CENTER CANVAS -->
    <main class="fb-canvas-container" id="canvas_drop_area">
        <div class="fb-canvas" id="canvas_container">
            <!-- Rendered Live by JS Engine -->
        </div>
    </main>

    <!-- RIGHT PANEL: Inspector (Properties / Design / Logic / Results / Tracking / CRM) -->
    <aside class="fb-inspector p-3">
        <!-- Navigation Tabs -->
        <ul class="nav nav-pills nav-pills-custom nav-justified mb-3" id="inspector_tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_props">⚙️ الخصائص</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_design">🎨 التصميم</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_results">🏆 النتائج</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_integrations">🔌 الربط</button>
            </li>
        </ul>

        <div class="tab-content" id="inspector_tab_content">
            
            <!-- TAB 1: Element / Step Properties -->
            <div class="tab-pane fade show active" id="tab_props">
                <div id="inspector_element_panel">
                    <div class="text-center text-muted py-5">
                        <iconify-icon icon="solar:cursor-bold-duotone" width="48" class="opacity-50 mb-2"></iconify-icon>
                        <p class="small">اختر عنصراً من الشاشة لتعديل أسئلته، خياراته، ونقاط حسابه.</p>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Design & Theme Customizer -->
            <div class="tab-pane fade" id="tab_design">
                <h6 class="fw-bold mb-3">🎨 تخصيص التصميم والمظهر</h6>
                
                <div class="mb-3">
                    <label class="form-label small text-muted">اللون الرئيسي (Primary Brand Color)</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" class="form-control form-control-color border-0 p-0" id="design_primary_color" value="{{ $funnel->design_settings['primary_color'] ?? '#2563eb' }}" onchange="updateDesignSettings()">
                        <input type="text" class="form-control form-control-sm form-control-dark" id="design_primary_color_text" value="{{ $funnel->design_settings['primary_color'] ?? '#2563eb' }}" onchange="document.getElementById('design_primary_color').value=this.value; updateDesignSettings()">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">نوع الخط (Font Family)</label>
                    <select class="form-select form-select-sm form-select-dark" id="design_font_family" onchange="updateDesignSettings()">
                        <option value="Tajawal, sans-serif">Tajawal (عربي احترافي)</option>
                        <option value="Cairo, sans-serif">Cairo (كايرو عصري)</option>
                        <option value="System">System Default</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">شكل الأزرار والبطاقات</label>
                    <select class="form-select form-select-sm form-select-dark" id="design_button_style" onchange="updateDesignSettings()">
                        <option value="rounded-lg">Rounded Large (دائري ناعم)</option>
                        <option value="rounded-pill">Rounded Pill (كبسولة بيضاوية)</option>
                        <option value="rounded-none">Sharp Square (حواف حادة)</option>
                    </select>
                </div>
            </div>

            <!-- TAB 3: Scoring & Results Builder -->
            <div class="tab-pane fade" id="tab_results">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">🏆 شاشات النتائج والأهلية</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" onclick="addNewResult()">➕ إضافة نتيجة</button>
                </div>
                <p class="text-muted small">حدد النتائج التي تظهر للعميل بناءً على مجموع نقاط إجاباته.</p>

                <div id="results_list_wrapper">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- TAB 4: CRM & Tracking Integrations -->
            <div class="tab-pane fade" id="tab_integrations">
                <h6 class="fw-bold mb-2">⚡ مزامنة CRM و البكسلات</h6>
                
                <!-- CRM -->
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="crm_enabled_checkbox" {{ ($funnel->crm_settings['enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold small" for="crm_enabled_checkbox">مزامنة العملاء في CRM Travel Wave</label>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">مصدر العميل (Lead Source)</label>
                        <select class="form-select form-select-sm form-select-dark" id="crm_source_select">
                            <option value="">تلقائي (Interactive Funnel)</option>
                            @foreach($leadSources as $ls)
                                <option value="{{ $ls->id }}">{{ $ls->name_en }} / {{ $ls->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label small text-muted">نوع الخدمة (Service Type)</label>
                        <select class="form-select form-select-sm form-select-dark" id="crm_service_type_select">
                            <option value="">اختر نوع الخدمة</option>
                            @foreach($serviceTypes as $st)
                                <option value="{{ $st->id }}">{{ $st->name_en }} / {{ $st->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Pixels -->
                <div class="p-3 bg-dark rounded-3 border border-secondary">
                    <h6 class="fw-bold small mb-2">🎯 بكسلات التتبع (Tracking Pixels)</h6>
                    <div class="mb-2">
                        <label class="form-label small text-muted">Meta (Facebook) Pixel ID</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="meta_pixel_input" value="{{ $funnel->tracking_settings['meta_pixel_id'] ?? '' }}" placeholder="e.g. 1234567890">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">Google Analytics (GA4) ID</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="ga4_input" value="{{ $funnel->tracking_settings['ga4_id'] ?? '' }}" placeholder="e.g. G-XXXXXXXXXX">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">TikTok Pixel ID</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="tiktok_input" value="{{ $funnel->tracking_settings['tiktok_pixel_id'] ?? '' }}" placeholder="e.g. CXXXXXXXXXX">
                    </div>
                    <div>
                        <label class="form-label small text-muted">Snapchat Pixel ID</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="snap_input" value="{{ $funnel->tracking_settings['snap_pixel_id'] ?? '' }}" placeholder="e.g. xxxxxxxx-xxxx">
                    </div>
                </div>
            </div>

        </div>
    </aside>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- BUILDER CORE ENGINE JAVASCRIPT -->
<script>
    // Funnel State Data
    const funnelData = @json($funnel);
    if (!funnelData.steps) funnelData.steps = [];
    if (!funnelData.results) funnelData.results = [];
    if (!funnelData.design_settings) funnelData.design_settings = { primary_color: '#2563eb' };

    let activeStepIndex = 0;
    let selectedElementIndex = null;

    // Initialize Builder
    document.addEventListener('DOMContentLoaded', () => {
        renderStepsList();
        renderCanvas();
        renderResultsList();
        setupDragAndDrop();
    });

    // ── 1. STEPS MANAGEMENT ──────────────────────────────────────────────────
    function renderStepsList() {
        const wrapper = document.getElementById('steps_list_wrapper');
        let html = '';

        funnelData.steps.forEach((step, idx) => {
            const isActive = idx === activeStepIndex;
            html += `
                <div class="fb-step-item ${isActive ? 'active' : ''}" onclick="selectStep(${idx})">
                    <div class="d-flex align-items-center gap-2 text-truncate">
                        <iconify-icon icon="solar:menu-dots-bold" class="text-muted" style="cursor: grab;"></iconify-icon>
                        <span class="fw-bold small text-truncate">${idx + 1}. ${step.title || 'خطوة بدون عنوان'}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-dark text-muted small">${step.step_type || 'question'}</span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1" onclick="event.stopPropagation(); deleteStep(${idx})" title="حذف الخطوة">
                            <iconify-icon icon="solar:trash-bin-minimalistic-bold" width="14"></iconify-icon>
                        </button>
                    </div>
                </div>
            `;
        });

        wrapper.innerHTML = html;

        // Enable Drag & Drop Reordering of Steps via SortableJS
        new Sortable(wrapper, {
            animation: 150,
            ghostClass: 'bg-primary-subtle',
            onEnd: function (evt) {
                const movedItem = funnelData.steps.splice(evt.oldIndex, 1)[0];
                funnelData.steps.splice(evt.newIndex, 0, movedItem);
                activeStepIndex = evt.newIndex;
                renderStepsList();
                renderCanvas();
            }
        });
    }

    function selectStep(index) {
        if (index >= 0 && index < funnelData.steps.length) {
            activeStepIndex = index;
            selectedElementIndex = null;
            renderStepsList();
            renderCanvas();
            inspectStepProperties();
        }
    }

    function addNewStep() {
        const newStepNumber = funnelData.steps.length + 1;
        const newStep = {
            title: `السؤال ${newStepNumber}`,
            subtitle: 'اختر الإجابة المناسبة للمتابعة',
            step_type: 'question',
            sort_order: newStepNumber,
            elements: [
                {
                    element_type: 'single_choice',
                    label: 'السؤال المطروح؟',
                    question_key: `q_${Math.random().toString(36).substr(2, 6)}`,
                    properties: {
                        options: [
                            { label: 'الخيار الأول', value: 'Option 1', score: 10 },
                            { label: 'الخيار الثاني', value: 'Option 2', score: 20 },
                        ]
                    }
                }
            ]
        };

        funnelData.steps.push(newStep);
        activeStepIndex = funnelData.steps.length - 1;
        renderStepsList();
        renderCanvas();
    }

    function deleteStep(index) {
        if (funnelData.steps.length <= 1) {
            alert('يجب أن يحتوي الفانل على خطوة واحدة على الأقل!');
            return;
        }
        if (confirm('هل أنت متأكد من حذف هذه الخطوة بكافة عناصرها؟')) {
            funnelData.steps.splice(index, 1);
            activeStepIndex = Math.max(0, activeStepIndex - 1);
            renderStepsList();
            renderCanvas();
        }
    }

    // ── 2. CANVAS RENDERING ──────────────────────────────────────────────────
    function renderCanvas() {
        const canvas = document.getElementById('canvas_container');
        const currentStep = funnelData.steps[activeStepIndex];

        if (!currentStep) {
            canvas.innerHTML = '<div class="text-center text-muted py-5">لا توجد خطوات حالياً. اضغط على إضافة خطوة.</div>';
            return;
        }

        let html = `
            <div class="mb-4 pb-2 border-bottom">
                <input type="text" class="form-control form-control-lg fw-bold border-0 p-0 text-dark bg-transparent mb-1" value="${escapeHtml(currentStep.title || '')}" placeholder="اكتب عنوان الخطوة هنا..." onchange="updateStepTitle(this.value)">
                <input type="text" class="form-control form-control-sm text-muted border-0 p-0 bg-transparent" value="${escapeHtml(currentStep.subtitle || '')}" placeholder="اكتب وصفاً فرعياً اختيارياً..." onchange="updateStepSubtitle(this.value)">
            </div>
            <div id="canvas_elements_wrapper">
        `;

        if (currentStep.elements && currentStep.elements.length > 0) {
            currentStep.elements.forEach((el, eIdx) => {
                const isSelected = eIdx === selectedElementIndex;
                html += `
                    <div class="canvas-element-item ${isSelected ? 'selected' : ''}" data-el-index="${eIdx}" onclick="inspectElement(${eIdx})">
                        <div class="canvas-element-toolbar">
                            <span>${el.element_type}</span>
                            <button type="button" class="btn btn-sm text-white p-0" onclick="event.stopPropagation(); duplicateElement(${eIdx})" title="تكرار">
                                <iconify-icon icon="solar:copy-bold" width="13"></iconify-icon>
                            </button>
                            <button type="button" class="btn btn-sm text-white p-0" onclick="event.stopPropagation(); deleteElement(${eIdx})" title="حذف">
                                <iconify-icon icon="solar:trash-bin-trash-bold" width="13"></iconify-icon>
                            </button>
                        </div>
                `;

                // Render element preview according to type
                if (el.element_type === 'heading') {
                    html += `<h4 class="fw-bold mb-0 text-primary">${escapeHtml(el.label || 'عنوان توضيحي')}</h4>`;
                } else if (el.element_type === 'text') {
                    html += `<p class="text-muted mb-0">${escapeHtml(el.label || 'نص فقرة توضيحية...')}</p>`;
                } else if (el.element_type === 'single_choice' || el.element_type === 'multiple_choice') {
                    html += `<label class="fw-bold mb-2 d-block">${escapeHtml(el.label || 'سؤال الاختيار:')}</label>`;
                    html += '<div class="d-flex flex-column gap-2">';
                    (el.properties?.options || []).forEach(opt => {
                        html += `
                            <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">${escapeHtml(opt.label || opt.value || '')}</span>
                                <span class="badge bg-primary-subtle text-primary small">+${opt.score || 0} نقطة</span>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else if (el.element_type === 'image_choice') {
                    html += `<label class="fw-bold mb-2 d-block">${escapeHtml(el.label || 'اختر بطاقة:')}</label>`;
                    html += '<div class="row g-2">';
                    (el.properties?.options || []).forEach(opt => {
                        html += `
                            <div class="col-6">
                                <div class="p-3 border rounded-3 text-center bg-light">
                                    <iconify-icon icon="solar:gallery-bold-duotone" width="32" class="text-muted mb-1"></iconify-icon>
                                    <div class="fw-bold small">${escapeHtml(opt.label || '')}</div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else if (el.element_type === 'contact_form') {
                    html += `
                        <label class="fw-bold mb-2 text-primary d-block">${escapeHtml(el.label || 'نموذج بيانات التواصل:')}</label>
                        <div class="bg-light p-3 rounded-3 border">
                            <input type="text" class="form-control mb-2" placeholder="الاسم الكريم *" disabled>
                            <input type="tel" class="form-control mb-2" placeholder="رقم الواتساب *" disabled>
                            <input type="email" class="form-control" placeholder="البريد الإلكتروني" disabled>
                        </div>
                    `;
                } else if (el.element_type === 'slider') {
                    html += `
                        <label class="fw-bold mb-2 d-block">${escapeHtml(el.label || 'الميزانية / القيمة:')}</label>
                        <input type="range" class="form-range" disabled>
                    `;
                } else if (el.element_type === 'rating') {
                    html += `
                        <label class="fw-bold mb-2 d-block">${escapeHtml(el.label || 'التقييم:')}</label>
                        <div class="d-flex gap-2 text-warning fs-4">⭐⭐⭐⭐⭐</div>
                    `;
                } else {
                    html += `
                        <label class="fw-bold mb-2 d-block">${escapeHtml(el.label || 'الحقل المطلوب:')}</label>
                        <input type="text" class="form-control" placeholder="اكتب الإجابة هنا..." disabled>
                    `;
                }

                html += '</div>';
            });
        } else {
            html += `
                <div class="alert alert-light border border-dashed text-center py-5 text-muted rounded-4 my-3">
                    <iconify-icon icon="solar:add-circle-bold-duotone" width="40" class="text-primary opacity-50 mb-2"></iconify-icon>
                    <p class="mb-0 fw-bold">هذه الخطوة فارغة حالياً</p>
                    <small>اسحب أي عنصر من القائمة الجانبية أو انقر عليه لإضافته هنا فوراً.</small>
                </div>
            `;
        }

        html += '</div>';
        canvas.innerHTML = html;

        // Re-enable Sortable on elements in the canvas
        const elementsWrapper = document.getElementById('canvas_elements_wrapper');
        if (elementsWrapper && currentStep.elements.length > 0) {
            new Sortable(elementsWrapper, {
                animation: 150,
                ghostClass: 'bg-primary-subtle',
                onEnd: function (evt) {
                    const moved = currentStep.elements.splice(evt.oldIndex, 1)[0];
                    currentStep.elements.splice(evt.newIndex, 0, moved);
                    selectedElementIndex = evt.newIndex;
                    renderCanvas();
                    inspectElement(evt.newIndex);
                }
            });
        }
    }

    // ── 3. DRAG & DROP FROM PALETTE TO CANVAS ────────────────────────────────
    function setupDragAndDrop() {
        const dropArea = document.getElementById('canvas_drop_area');
        const badges = document.querySelectorAll('.fb-element-badge');

        badges.forEach(badge => {
            badge.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', badge.dataset.type);
                dropArea.classList.add('drop-zone-highlight');
            });

            badge.addEventListener('dragend', () => {
                dropArea.classList.remove('drop-zone-highlight');
            });
        });

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('drop-zone-highlight');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('drop-zone-highlight');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('drop-zone-highlight');
            const elementType = e.dataTransfer.getData('text/plain');
            if (elementType) {
                addElementToCurrentStep(elementType);
            }
        });
    }

    function addElementToCurrentStep(type) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep) return;
        if (!currentStep.elements) currentStep.elements = [];

        let newElement = {
            element_type: type,
            label: getDefaultLabelForType(type),
            question_key: `q_${Math.random().toString(36).substr(2, 6)}`,
            properties: {}
        };

        if (type === 'single_choice' || type === 'multiple_choice' || type === 'image_choice' || type === 'dropdown') {
            newElement.properties.options = [
                { label: 'الخيار الأول', value: 'Option 1', score: 10 },
                { label: 'الخيار الثاني', value: 'Option 2', score: 20 },
                { label: 'الخيار الثالث', value: 'Option 3', score: 30 },
            ];
        } else if (type === 'contact_form') {
            newElement.properties.fields = ['full_name', 'phone', 'email'];
        }

        currentStep.elements.push(newElement);
        selectedElementIndex = currentStep.elements.length - 1;
        renderCanvas();
        inspectElement(selectedElementIndex);
    }

    function getDefaultLabelForType(type) {
        switch(type) {
            case 'single_choice': return 'ما هو اختيارك المفضل؟';
            case 'multiple_choice': return 'اختر جميع الإجابات التي تنطبق عليك:';
            case 'image_choice': return 'اختر البطاقة الأنسب:';
            case 'contact_form': return 'سجّل بياناتك لاستلام التقرير والتواصل:';
            case 'heading': return 'عنوان توضيحي جذاب';
            case 'text': return 'اكتب هنا تفاصيل إضافية لمساعدة العميل في فهم السؤال.';
            case 'slider': return 'حدد الميزانية التقديرية:';
            case 'rating': return 'ما هو تقييمك لخدماتنا؟';
            case 'date_picker': return 'تاريخ السفر أو الموعد المرغوب:';
            case 'button': return 'متابعة الخطوة التالية ➔';
            default: return 'سؤال جديد';
        }
    }

    function deleteElement(eIdx) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep) return;
        currentStep.elements.splice(eIdx, 1);
        selectedElementIndex = null;
        renderCanvas();
        inspectStepProperties();
    }

    function duplicateElement(eIdx) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep) return;
        const copy = JSON.parse(JSON.stringify(currentStep.elements[eIdx]));
        copy.question_key = `q_${Math.random().toString(36).substr(2, 6)}`;
        copy.label = copy.label + ' (نسخة)';
        currentStep.elements.splice(eIdx + 1, 0, copy);
        selectedElementIndex = eIdx + 1;
        renderCanvas();
        inspectElement(selectedElementIndex);
    }

    // ── 4. INSPECTOR: ELEMENT & STEP PROPERTIES ──────────────────────────────
    function inspectElement(eIdx) {
        selectedElementIndex = eIdx;
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[eIdx]) return;
        const el = currentStep.elements[eIdx];

        // Switch to properties tab
        const propsTabBtn = document.querySelector('#inspector_tabs button[data-bs-target="#tab_props"]');
        if (propsTabBtn) {
            const tabInstance = bootstrap.Tab.getOrCreateInstance(propsTabBtn);
            tabInstance.show();
        }

        const panel = document.getElementById('inspector_element_panel');
        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-primary text-uppercase">${el.element_type}</span>
                <button type="button" class="btn btn-sm btn-outline-danger py-0" onclick="deleteElement(${eIdx})">
                    <iconify-icon icon="solar:trash-bin-bold"></iconify-icon> حذف العنصر
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">نص السؤال / العنوان (Label)</label>
                <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(el.label || '')}" oninput="updateCurrentElementProp('label', this.value)">
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">مفتاح الحقل في CRM (Field Key)</label>
                <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(el.question_key || '')}" oninput="updateCurrentElementProp('question_key', this.value)">
            </div>
        `;

        // If choices/options exist
        if (el.element_type === 'single_choice' || el.element_type === 'multiple_choice' || el.element_type === 'image_choice' || el.element_type === 'dropdown') {
            html += `
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small text-muted mb-0">خيارات السؤال والنقاط (Options & Scores)</label>
                        <button type="button" class="btn btn-sm btn-primary py-0 px-2 small" onclick="addOptionToCurrentElement()">➕ خيار</button>
                    </div>
                    <div class="d-flex flex-column gap-2" id="options_editor_wrapper">
            `;

            (el.properties?.options || []).forEach((opt, oIdx) => {
                html += `
                    <div class="p-2 bg-dark rounded-3 border border-secondary">
                        <div class="d-flex gap-1 mb-1">
                            <input type="text" class="form-control form-control-sm form-control-dark" placeholder="نص الخيار" value="${escapeHtml(opt.label || '')}" oninput="updateOptionProp(${oIdx}, 'label', this.value)">
                            <button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="deleteOptionFromCurrentElement(${oIdx})">✕</button>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="small text-muted">النقاط:</span>
                            <input type="number" class="form-control form-control-sm form-control-dark" style="width: 80px;" value="${opt.score || 0}" oninput="updateOptionProp(${oIdx}, 'score', parseInt(this.value)||0)">
                        </div>
                    </div>
                `;
            });

            html += `</div></div>`;
        }

        panel.innerHTML = html;
        renderCanvas();
    }

    function inspectStepProperties() {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep) return;

        const panel = document.getElementById('inspector_element_panel');
        panel.innerHTML = `
            <h6 class="fw-bold mb-3">⚙️ خصائص الخطوة الحالية (${activeStepIndex + 1})</h6>
            <div class="mb-3">
                <label class="form-label small text-muted">عنوان الخطوة (Title)</label>
                <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(currentStep.title || '')}" oninput="updateStepTitle(this.value)">
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted">الوصف الفرعي (Subtitle)</label>
                <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(currentStep.subtitle || '')}" oninput="updateStepSubtitle(this.value)">
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted">نوع الخطوة (Step Type)</label>
                <select class="form-select form-select-sm form-select-dark" onchange="currentStep.step_type = this.value; renderStepsList();">
                    <option value="welcome" ${currentStep.step_type === 'welcome' ? 'selected' : ''}>Welcome (ترحيب وبداية)</option>
                    <option value="question" ${currentStep.step_type === 'question' ? 'selected' : ''}>Question (سؤال تفاعلي)</option>
                    <option value="lead_form" ${currentStep.step_type === 'lead_form' ? 'selected' : ''}>Lead Form (نموذج تواصل)</option>
                </select>
            </div>
            <div class="mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="deleteStep(${activeStepIndex})">
                    🗑️ حذف هذه الخطوة
                </button>
            </div>
        `;
    }

    function updateStepTitle(val) {
        if (funnelData.steps[activeStepIndex]) {
            funnelData.steps[activeStepIndex].title = val;
            renderStepsList();
        }
    }

    function updateStepSubtitle(val) {
        if (funnelData.steps[activeStepIndex]) {
            funnelData.steps[activeStepIndex].subtitle = val;
        }
    }

    function updateCurrentElementProp(key, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]) {
            currentStep.elements[selectedElementIndex][key] = val;
            renderCanvas();
        }
    }

    function addOptionToCurrentElement() {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[selectedElementIndex]) return;
        const el = currentStep.elements[selectedElementIndex];
        if (!el.properties) el.properties = {};
        if (!el.properties.options) el.properties.options = [];

        el.properties.options.push({
            label: `خيار ${el.properties.options.length + 1}`,
            value: `Option ${el.properties.options.length + 1}`,
            score: 10
        });

        inspectElement(selectedElementIndex);
    }

    function updateOptionProp(oIdx, key, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]?.properties?.options[oIdx]) {
            currentStep.elements[selectedElementIndex].properties.options[oIdx][key] = val;
            renderCanvas();
        }
    }

    function deleteOptionFromCurrentElement(oIdx) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]?.properties?.options) {
            currentStep.elements[selectedElementIndex].properties.options.splice(oIdx, 1);
            inspectElement(selectedElementIndex);
        }
    }

    // ── 5. RESULTS TAB ───────────────────────────────────────────────────────
    function renderResultsList() {
        const wrapper = document.getElementById('results_list_wrapper');
        let html = '';

        (funnelData.results || []).forEach((res, rIdx) => {
            html += `
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success small">نتيجة #${rIdx + 1}</span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="deleteResult(${rIdx})">✕</button>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">عنوان النتيجة</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(res.title || '')}" oninput="funnelData.results[${rIdx}].title = this.value">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">الوصف التفصيلي</label>
                        <textarea class="form-control form-control-sm form-control-dark" rows="2" oninput="funnelData.results[${rIdx}].description = this.value">${escapeHtml(res.description || '')}</textarea>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small text-muted">أدنى نقاط (Min)</label>
                            <input type="number" class="form-control form-control-sm form-control-dark" value="${res.min_score ?? 0}" oninput="funnelData.results[${rIdx}].min_score = parseInt(this.value)||0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">أقصى نقاط (Max)</label>
                            <input type="number" class="form-control form-control-sm form-control-dark" value="${res.max_score ?? 100}" oninput="funnelData.results[${rIdx}].max_score = parseInt(this.value)||100">
                        </div>
                    </div>
                    <div>
                        <label class="form-label small text-muted">نص زر الواتساب / التحويل</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(res.cta_label || 'تواصل معنا الآن')}" oninput="funnelData.results[${rIdx}].cta_label = this.value">
                    </div>
                </div>
            `;
        });

        wrapper.innerHTML = html;
    }

    function addNewResult() {
        if (!funnelData.results) funnelData.results = [];
        funnelData.results.push({
            title: 'نتيجة جديدة 🎉',
            description: 'وصف نتيجة التقييم والأهلية المقترحة للعميل.',
            min_score: 50,
            max_score: 100,
            cta_type: 'whatsapp',
            cta_label: 'تحدث مع المستشار عبر الواتساب',
            cta_whatsapp_number: '966500000000',
            sort_order: funnelData.results.length + 1
        });
        renderResultsList();
    }

    function deleteResult(rIdx) {
        if (confirm('حذف هذه النتيجة؟')) {
            funnelData.results.splice(rIdx, 1);
            renderResultsList();
        }
    }

    // ── 6. DESIGN TAB ────────────────────────────────────────────────────────
    function updateDesignSettings() {
        const color = document.getElementById('design_primary_color').value;
        const font = document.getElementById('design_font_family').value;
        const btnStyle = document.getElementById('design_button_style').value;

        funnelData.design_settings = {
            primary_color: color,
            font_family: font,
            button_style: btnStyle
        };
        document.documentElement.style.setProperty('--fb-primary', color);
    }

    // ── 7. VIEWPORT TOGGLE ───────────────────────────────────────────────────
    function setDevice(type) {
        const canvas = document.getElementById('canvas_container');
        const btnDesk = document.getElementById('btn_device_desktop');
        const btnMob = document.getElementById('btn_device_mobile');

        if (type === 'mobile') {
            canvas.classList.add('mobile-mode');
            btnMob.classList.add('active');
            btnDesk.classList.remove('active');
        } else {
            canvas.classList.remove('mobile-mode');
            btnDesk.classList.add('active');
            btnMob.classList.remove('active');
        }
    }

    // ── 8. SAVE FUNNEL TO SERVER ─────────────────────────────────────────────
    function saveFunnel() {
        const saveBtn = document.getElementById('btn_save_funnel');
        saveBtn.disabled = true;
        saveBtn.innerHTML = 'جاري الحفظ... ⏳';

        const payload = {
            name: funnelData.name,
            slug: funnelData.slug,
            design_settings: funnelData.design_settings,
            crm_settings: {
                enabled: document.getElementById('crm_enabled_checkbox').checked,
                source_id: document.getElementById('crm_source_select').value,
                service_type_id: document.getElementById('crm_service_type_select').value,
            },
            tracking_settings: {
                meta_pixel_id: document.getElementById('meta_pixel_input').value,
                ga4_id: document.getElementById('ga4_input').value,
                tiktok_pixel_id: document.getElementById('tiktok_input').value,
                snap_pixel_id: document.getElementById('snap_input').value,
            },
            steps: funnelData.steps,
            results: funnelData.results,
        };

        fetch(`{{ route('admin.funnels.builder.update', $funnel) }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<span>حفظ التعديلات 💾</span>';
            if (data.success) {
                showToast('💾 تم حفظ التعديلات في الفانل بنجاح!');
            } else {
                alert('حدث خطأ أثناء الحفظ: ' + (data.message || 'Error'));
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<span>حفظ التعديلات 💾</span>';
            alert('تعذر الحفظ: ' + err.message);
        });
    }

    function showToast(msg) {
        const toast = document.getElementById('fb_toast');
        toast.innerText = msg;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 2500);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
</body>
</html>
