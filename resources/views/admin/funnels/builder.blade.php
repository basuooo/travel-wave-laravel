@php
    $currentLang = session('locale', app()->getLocale());
    $isRtl = $currentLang === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLang }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ Visual Funnel Builder Pro Suite | {{ $funnel->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 & Google Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&family=Cairo:wght@400;600;700;800&family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;600;700&family=Montserrat:wght@400;600;700&family=Almarai:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <!-- SortableJS for Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <style>
        :root {
            --fb-topbar-h: 62px;
            --fb-sidebar-w: 340px;
            --fb-inspector-w: 440px;
            --fb-bg-dark: #090d16;
            --fb-panel-bg: #101726;
            --fb-panel-card: #192237;
            --fb-border: #22304d;
            --fb-accent: #3b82f6;
            --fb-primary: {{ $funnel->design_settings['primary_color'] ?? '#2563eb' }};
        }

        * { box-sizing: border-box; }
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Tajawal', 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--fb-bg-dark);
            color: #f8fafc;
        }

        [dir="ltr"] body, [dir="ltr"] html {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Topbar */
        .fb-topbar {
            height: var(--fb-topbar-h);
            background: #090f1d;
            border-bottom: 1px solid var(--fb-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            z-index: 100;
        }

        /* Workspace */
        .fb-workspace {
            display: flex;
            height: calc(100vh - var(--fb-topbar-h));
            overflow: hidden;
        }

        /* Left Elements Sidebar */
        .fb-sidebar {
            width: var(--fb-sidebar-w);
            background: var(--fb-panel-bg);
            border-right: 1px solid var(--fb-border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        [dir="rtl"] .fb-sidebar {
            border-right: none;
            border-left: 1px solid var(--fb-border);
        }

        /* Center Canvas Area */
        .fb-canvas-container {
            flex: 1;
            background: radial-gradient(circle at top, #151f33 0%, #090d16 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem 1rem;
            overflow-y: auto;
            position: relative;
        }

        .fb-canvas {
            width: 100%;
            max-width: 740px;
            background: #ffffff;
            color: #0f172a;
            border-radius: 20px;
            min-height: 560px;
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
            padding: 1.5rem;
        }

        .drop-zone-highlight {
            border: 2px dashed var(--fb-accent) !important;
            background: rgba(59, 130, 246, 0.08) !important;
        }

        /* Right Inspector Panel */
        .fb-inspector {
            width: var(--fb-inspector-w);
            background: var(--fb-panel-bg);
            border-left: 1px solid var(--fb-border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        [dir="rtl"] .fb-inspector {
            border-left: none;
            border-right: 1px solid var(--fb-border);
        }
        .fb-inspector label,
        .fb-inspector .form-label {
            color: #f8fafc !important;
            font-weight: 600 !important;
        }
        .form-control-dark,
        .form-select-dark {
            background-color: #090d16 !important;
            border-color: #22304d !important;
            color: #ffffff !important;
        }
        .form-control-dark::placeholder {
            color: #94a3b8 !important;
            opacity: 0.85 !important;
        }

        /* Accordion Categories */
        .fb-category-accordion .accordion-item {
            background: transparent;
            border: none;
            margin-bottom: 0.5rem;
        }
        .fb-category-accordion .accordion-button {
            background: #151f33;
            color: #cbd5e1;
            font-size: 13px;
            font-weight: 700;
            padding: 9px 14px;
            border-radius: 10px !important;
            border: 1px solid var(--fb-border);
            box-shadow: none;
        }
        .fb-category-accordion .accordion-button:not(.collapsed) {
            background: #1e2c48;
            color: #60a5fa;
            border-color: var(--fb-accent);
        }
        .fb-category-accordion .accordion-button::after {
            filter: invert(1);
            transform: scale(0.75);
        }
        .fb-category-accordion .accordion-body {
            padding: 8px 2px 4px;
        }

        /* Palette Badges */
        .fb-element-pill {
            background: #ffffff;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: grab;
            user-select: none;
            transition: all 0.18s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
        }
        .fb-element-pill:hover {
            border-color: var(--fb-accent);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.15);
            color: var(--fb-accent);
        }
        .fb-element-pill:active {
            cursor: grabbing;
        }

        /* Canvas Element */
        .canvas-element-item {
            border: 2px dashed #e2e8f0;
            padding: 1.25rem;
            border-radius: 14px;
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
            background: #ffffff;
            box-shadow: 0 4px 18px rgba(59, 130, 246, 0.18);
        }

        /* Inline editable text on canvas */
        .canvas-inline-editable {
            outline: none;
            border-radius: 6px;
            padding: 2px 6px;
            transition: all 0.15s ease;
            cursor: text;
            display: inline-block;
            min-width: 60px;
            border: 1px solid transparent;
            word-break: break-word;
        }
        .canvas-inline-editable:hover {
            border: 1px dashed #93c5fd;
            background: rgba(59, 130, 246, 0.04);
        }
        .canvas-inline-editable:focus,
        .canvas-inline-editable:focus-visible {
            border: 2px dashed #2563eb !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }

        .canvas-element-toolbar {
            position: absolute;
            top: -14px;
            right: 12px;
            background: var(--fb-accent);
            color: #fff;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: none;
            gap: 8px;
            align-items: center;
            z-index: 10;
        }
        [dir="ltr"] .canvas-element-toolbar {
            right: auto;
            left: 12px;
        }
        .canvas-element-item.selected .canvas-element-toolbar,
        .canvas-element-item:hover .canvas-element-toolbar {
            display: flex;
        }

        .fb-step-item {
            padding: 0.7rem 0.9rem;
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

        /* Bottom Page Navigation Bar (Involve.me style) */
        .fb-pages-bar-container {
            width: 100%;
            max-width: 740px;
            margin-top: 1.25rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
            position: relative;
            z-index: 50;
        }
        .mobile-mode + .fb-pages-bar-container {
            max-width: 390px;
        }
        .fb-pages-scroll-area {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .fb-page-pill-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
            white-space: nowrap;
            position: relative;
            user-select: none;
        }
        .fb-page-pill-item:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            color: #2563eb;
        }
        .fb-page-pill-item.active {
            border: 2px solid #2563eb;
            background: #ffffff;
            color: #2563eb;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.18);
        }
        .fb-page-pill-item.is-disabled-step {
            opacity: 0.55;
            text-decoration: line-through;
        }

        .fb-page-dropdown-menu {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 0;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            padding: 6px;
            min-width: 170px;
            z-index: 1050;
            display: none;
        }
        [dir="rtl"] .fb-page-dropdown-menu {
            left: auto;
            right: 0;
        }
        .fb-page-dropdown-menu.show {
            display: block;
        }
        .fb-page-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .fb-page-dropdown-item:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .fb-page-dropdown-item.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }
        .fb-page-dropdown-item.text-danger:hover {
            background: #fef2f2;
            color: #dc2626 !important;
        }

        .fb-page-bar-add-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
        }
        .fb-page-bar-add-btn:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: #eff6ff;
        }

        .fb-logic-connector-icon {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #64748b;
            font-size: 14px;
            font-weight: 700;
            padding: 0 6px;
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

        .lang-switch-btn {
            background: #151f33;
            border: 1px solid var(--fb-border);
            color: #cbd5e1;
            padding: 5px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .lang-switch-btn:hover {
            border-color: var(--fb-accent);
            color: #60a5fa;
        }

        .form-control-dark, .form-select-dark {
            background: #192237;
            border: 1px solid var(--fb-border);
            color: #f8fafc;
            border-radius: 8px;
        }
        .form-control-dark:focus, .form-select-dark:focus {
            background: #192237;
            border-color: var(--fb-accent);
            color: #fff;
            box-shadow: none;
        }

        .nav-pills-custom .nav-link {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            padding: 7px 8px;
            border-radius: 8px;
        }
        .nav-pills-custom .nav-link.active {
            background-color: var(--fb-accent);
            color: #fff;
        }

        /* Sub-Tabs within Element Inspector (Content / Design) */
        .nav-tabs-element {
            border-bottom: 1px solid var(--fb-border);
        }
        .nav-tabs-element .nav-link {
            color: #94a3b8;
            font-weight: 700;
            font-size: 13px;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 8px 16px;
            background: transparent;
        }
        .nav-tabs-element .nav-link.active {
            color: #60a5fa;
            border-bottom: 2px solid var(--fb-accent);
            background: transparent;
        }

        .design-section-card {
            background: #151f33;
            border: 1px solid var(--fb-border);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 12px;
        }
        .design-section-title {
            font-size: 12px;
            font-weight: 800;
            color: #e2e8f0;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Option Card in Canvas */
        .canvas-choice-card {
            transition: all 0.2s;
            cursor: pointer;
        }
        .canvas-choice-card:hover {
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12);
        }

        /* Timer Flip Box */
        .timer-box-digit {
            background: #1e293b;
            color: #f8fafc;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 22px;
            font-weight: 800;
            min-width: 52px;
            text-align: center;
        }

        /* Phone Country Code Select UI */
        .phone-code-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            border-radius: 8px 0 0 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
        }
        [dir="rtl"] .phone-code-btn {
            border-radius: 0 8px 8px 0;
        }

        /* Country Group Preset Active Pill */
        .country-preset-btn {
            transition: all 0.2s;
            border: 1px solid var(--fb-border);
            background: #151f33;
            color: #cbd5e1;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .country-preset-btn:hover {
            border-color: var(--fb-accent);
            color: #fff;
        }
        .country-preset-btn.active {
            background: var(--fb-accent);
            border-color: var(--fb-accent);
            color: #fff;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.4);
        }

        #fb_toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: #fff;
            padding: 12px 28px;
            border-radius: 30px;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            display: none;
            z-index: 9999;
        }
    </style>
</head>
<body>

<!-- TOAST -->
<div id="fb_toast">💾 Saved successfully!</div>

<!-- MEDIA PICKER MODAL -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" id="txt_media_modal_title">🖼️ اختيار صورة من مكتبة الموقع</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-muted" id="txt_media_modal_lbl">إدخال رابط صورة مخصص (Direct Image URL)</label>
                    <div class="input-group">
                        <input type="url" class="form-control form-control-dark" id="modal_custom_img_url" placeholder="https://example.com/image.jpg">
                        <button type="button" class="btn btn-primary" id="txt_media_modal_apply" onclick="applyCustomImageUrl()">تطبيق الرابط</button>
                    </div>
                </div>

                <h6 class="fw-bold small text-muted mb-2" id="txt_media_modal_presets">أو اختر من المعرض المقترح:</h6>
                <div class="row g-2" id="modal_presets_container" style="max-height: 280px; overflow-y: auto;">
                    <div class="col-3"><img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=300" class="img-thumbnail bg-dark border-secondary cursor-pointer" onclick="selectModalImage(this.src)"></div>
                    <div class="col-3"><img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=300" class="img-thumbnail bg-dark border-secondary cursor-pointer" onclick="selectModalImage(this.src)"></div>
                    <div class="col-3"><img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=300" class="img-thumbnail bg-dark border-secondary cursor-pointer" onclick="selectModalImage(this.src)"></div>
                    <div class="col-3"><img src="https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=300" class="img-thumbnail bg-dark border-secondary cursor-pointer" onclick="selectModalImage(this.src)"></div>
                    <div class="col-3"><img src="https://images.unsplash.com/photo-1533105079780-92b9be482077?w=300" class="img-thumbnail bg-dark border-secondary cursor-pointer" onclick="selectModalImage(this.src)"></div>
                    <div class="col-3"><img src="https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=300" class="img-thumbnail bg-dark border-secondary cursor-pointer" onclick="selectModalImage(this.src)"></div>
                    <div class="col-3"><img src="https://images.unsplash.com/photo-1516483638261-f4dbaf036963?w=300" class="img-thumbnail bg-dark border-secondary cursor-pointer" onclick="selectModalImage(this.src)"></div>
                    <div class="col-3"><img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=300" class="img-thumbnail bg-dark border-secondary cursor-pointer" onclick="selectModalImage(this.src)"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOPBAR -->
<header class="fb-topbar">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.funnels.index') }}" class="btn btn-outline-secondary btn-sm rounded-3" id="topbar_btn_back">
            ← الفانلات
        </a>
        <div>
            <h6 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                <span>{{ $funnel->name }}</span>
                @if($funnel->status === 'published')
                    <span class="badge bg-success small" id="topbar_badge_status">Live</span>
                @else
                    <span class="badge bg-secondary small" id="topbar_badge_status">Draft</span>
                @endif
            </h6>
            <small class="text-muted">/f/{{ $funnel->slug }}</small>
        </div>
    </div>

    <!-- Viewport & Language Switcher -->
    <div class="d-flex align-items-center gap-3">
        
        <!-- Language Switcher -->
        <button type="button" class="lang-switch-btn" id="btn_toggle_lang" onclick="toggleLanguage()">
            <iconify-icon icon="solar:global-bold" width="16"></iconify-icon>
            <span id="lang_switch_label">English</span>
        </button>

        <!-- Viewport Switcher -->
        <div class="d-flex align-items-center gap-1 bg-dark p-1 rounded-3 border border-secondary">
            <button type="button" class="device-toggle-btn active" id="btn_device_desktop" onclick="setDevice('desktop')">
                <iconify-icon icon="solar:laptop-minimalistic-bold" width="16"></iconify-icon>
                <span id="txt_device_desktop">كمبيوتر</span>
            </button>
            <button type="button" class="device-toggle-btn" id="btn_device_mobile" onclick="setDevice('mobile')">
                <iconify-icon icon="solar:smartphone-bold" width="16"></iconify-icon>
                <span id="txt_device_mobile">جوال</span>
            </button>
        </div>

    </div>

    <!-- Actions -->
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-light btn-sm rounded-3 d-flex align-items-center gap-1" onclick="openLivePreview()">
            <iconify-icon icon="solar:eye-bold" width="16"></iconify-icon>
            <span id="txt_btn_preview">معاينة حية 👁️</span>
        </button>
        <button type="button" class="btn btn-primary btn-sm fw-bold px-4 rounded-3 d-flex align-items-center gap-1" id="btn_save_funnel" onclick="saveFunnel()">
            <iconify-icon icon="solar:diskette-bold" width="18"></iconify-icon>
            <span id="txt_btn_save">حفظ التعديلات 💾</span>
        </button>
        @if($funnel->status !== 'published')
            <form action="{{ route('admin.funnels.publish', $funnel) }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-success btn-sm fw-bold rounded-3" id="txt_btn_publish">🚀 نشر الفانل</button>
            </form>
        @endif
    </div>
</header>

<!-- WORKSPACE -->
<div class="fb-workspace">
    
    <!-- LEFT PANEL: Steps & Involve.me Palette -->
    <aside class="fb-sidebar p-3">
        
        <!-- Steps Header -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-uppercase text-muted fw-bold small mb-0" id="txt_steps_title">📌 خطوات الفانل (Steps)</h6>
            <button type="button" class="btn btn-primary btn-sm py-1 px-2 rounded-2 fw-bold" id="txt_btn_add_step" onclick="addNewStep()">
                ➕ خطوة
            </button>
        </div>
        <div id="steps_list_wrapper" class="mb-4">
            <!-- Rendered by JS -->
        </div>

        <hr class="border-secondary my-3">

        <!-- Search Bar -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-uppercase text-muted fw-bold small mb-0" id="txt_palette_title">🧩 مكتبة العناصر</h6>
        </div>
        <div class="mb-3">
            <input type="text" class="form-control form-control-sm form-control-dark" id="element_search_input" placeholder="🔍 بحث عن عنصر..." oninput="filterPaletteElements(this.value)">
        </div>

        <!-- ACCORDION PALETTE (DYNAMICALLY LOCALIZED) -->
        <div class="accordion fb-category-accordion" id="palette_accordion">
            <!-- Rendered by JS Localization Engine -->
        </div>
    </aside>

    <!-- CENTER CANVAS -->
    <main class="fb-canvas-container" id="canvas_drop_area">
        <div class="fb-canvas" id="canvas_container">
            <!-- Rendered Live by JS Engine -->
        </div>

        <!-- BOTTOM PAGES BAR (INVOLVE.ME STYLE) -->
        <div class="fb-pages-bar-container" id="fb_pages_bar_container">
            <!-- Rendered by JS Engine -->
        </div>
    </main>

    <!-- RIGHT PANEL: Inspector -->
    <aside class="fb-inspector p-3">
        <!-- TOP LEVEL TABS (WHEN NO ELEMENT SELECTED) -->
        <ul class="nav nav-pills nav-pills-custom nav-justified mb-3" id="inspector_tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_props" id="tab_btn_props">⚙️ الخصائص</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_design" id="tab_btn_design">🎨 التصميم العام</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_results" id="tab_btn_results">🏆 النتائج</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_integrations" id="tab_btn_integrations">🔌 الربط</button>
            </li>
        </ul>

        <div class="tab-content" id="inspector_tab_content">
            
            <!-- TAB 1: Properties / Element Inspector -->
            <div class="tab-pane fade show active" id="tab_props">
                <div id="inspector_element_panel">
                    <div class="text-center text-muted py-5" id="inspector_empty_state">
                        <iconify-icon icon="solar:cursor-bold-duotone" width="48" class="opacity-50 mb-2"></iconify-icon>
                        <p class="small" id="txt_inspector_empty">انقر على أي عنصر داخل الشاشة أو انقر نقراً مزدوجاً (Double Click) لتعديل نصوصه وتصميمه مباشرة.</p>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Design & Scoring Settings -->
            <div class="tab-pane fade" id="tab_design">
                <h6 class="fw-bold mb-3" id="txt_design_header">🎨 المظهر ونظام التقييم</h6>
                
                <!-- Scoring Toggle -->
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="scoring_enabled_checkbox" onchange="toggleScoringMode(this.checked)" {{ ($funnel->design_settings['scoring_enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold small text-white" for="scoring_enabled_checkbox" id="txt_scoring_label">
                            تفعيل نظام الدرجات وحساب الأهلية (Quiz / Scoring)
                        </label>
                    </div>
                    <small class="text-muted d-block" style="font-size: 11px;" id="txt_scoring_sub">
                        إذا تم إيقافه، سيعمل الفانل كنموذج تجميع بيانات وليد جنريشن عادي بدون احتساب درجات أو إظهار نسبة مئوية.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted" id="txt_primary_color_lbl">اللون الأساسي (Primary Color)</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" class="form-control form-control-color border-0 p-0" id="design_primary_color" value="{{ $funnel->design_settings['primary_color'] ?? '#2563eb' }}" onchange="updateDesignSettings()">
                        <input type="text" class="form-control form-control-sm form-control-dark" id="design_primary_color_text" value="{{ $funnel->design_settings['primary_color'] ?? '#2563eb' }}" onchange="document.getElementById('design_primary_color').value=this.value; updateDesignSettings()">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted" id="txt_font_family_lbl">نوع الخط (Font Family)</label>
                    <select class="form-select form-select-sm form-select-dark" id="design_font_family" onchange="updateDesignSettings()">
                        <option value="Tajawal, sans-serif">Tajawal (عربي أنيق)</option>
                        <option value="Cairo, sans-serif">Cairo (عصري)</option>
                        <option value="Inter, sans-serif">Inter (English Standard)</option>
                        <option value="Poppins, sans-serif">Poppins</option>
                        <option value="Montserrat, sans-serif">Montserrat</option>
                        <option value="Almarai, sans-serif">Almarai (المراعي)</option>
                        <option value="System">System Default</option>
                    </select>
                </div>
            </div>

            <!-- TAB 3: Results -->
            <div class="tab-pane fade" id="tab_results">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0" id="txt_results_header">🏆 شاشات النتائج والأهلية</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" id="txt_btn_add_result" onclick="addNewResult()">➕ إضافة نتيجة</button>
                </div>
                <div id="results_list_wrapper">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- TAB 4: Integrations -->
            <div class="tab-pane fade" id="tab_integrations">
                <h6 class="fw-bold mb-2" id="txt_integrations_header">⚡ مزامنة CRM و البكسلات</h6>
                
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="crm_enabled_checkbox" {{ ($funnel->crm_settings['enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold small" for="crm_enabled_checkbox" id="txt_crm_sync_lbl">مزامنة العملاء في CRM Travel Wave</label>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted" id="txt_lead_source_lbl">مصدر العميل (Lead Source)</label>
                        <select class="form-select form-select-sm form-select-dark" id="crm_source_select">
                            <option value="">تلقائي (Interactive Funnel)</option>
                            @foreach($leadSources as $ls)
                                <option value="{{ $ls->id }}">{{ $ls->name_en }} / {{ $ls->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label small text-muted" id="txt_service_type_lbl">نوع الخدمة (Service Type)</label>
                        <select class="form-select form-select-sm form-select-dark" id="crm_service_type_select">
                            <option value="">اختر نوع الخدمة</option>
                            @foreach($serviceTypes as $st)
                                <option value="{{ $st->id }}">{{ $st->name_en }} / {{ $st->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <h6 class="fw-bold small mb-3 text-white d-flex align-items-center gap-1" id="txt_tracking_header">
                        <iconify-icon icon="solar:chart-2-bold" class="text-primary"></iconify-icon>
                        <span>🎯 بكسلات التتبع والـ Analytics</span>
                    </h6>
                    
                    <!-- Meta Pixel -->
                    <div class="mb-3">
                        <label class="form-label small text-white fw-semibold d-flex align-items-center gap-1">
                            <iconify-icon icon="logos:facebook" width="14"></iconify-icon>
                            <span>Meta (Facebook) Pixel</span>
                        </label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="meta_pixel_input" placeholder="Pixel ID (e.g. 1234567890)" value="{{ $funnel->tracking_settings['meta_pixel_id'] ?? '' }}">
                    </div>

                    <!-- Google Analytics -->
                    <div class="mb-3">
                        <label class="form-label small text-white fw-semibold d-flex align-items-center gap-1">
                            <iconify-icon icon="logos:google-analytics" width="14"></iconify-icon>
                            <span>Google Analytics (GA4)</span>
                        </label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="ga4_input" placeholder="G-XXXXXXXXXX" value="{{ $funnel->tracking_settings['ga4_id'] ?? '' }}">
                    </div>

                    <!-- Google Tag Manager -->
                    <div class="mb-3">
                        <label class="form-label small text-white fw-semibold d-flex align-items-center gap-1">
                            <iconify-icon icon="logos:google-tag-manager" width="14"></iconify-icon>
                            <span>Google Tag Manager (GTM)</span>
                        </label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="gtm_input" placeholder="GTM-XXXXXXX" value="{{ $funnel->tracking_settings['gtm_id'] ?? '' }}">
                    </div>

                    <!-- LinkedIn Insight Tag -->
                    <div class="mb-3">
                        <label class="form-label small text-white fw-semibold d-flex align-items-center gap-1">
                            <iconify-icon icon="logos:linkedin-icon" width="14"></iconify-icon>
                            <span>LinkedIn Insight Tag ID</span>
                        </label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="linkedin_input" placeholder="Partner Tag ID" value="{{ $funnel->tracking_settings['linkedin_tag_id'] ?? '' }}">
                    </div>

                    <!-- TikTok Pixel -->
                    <div class="mb-3">
                        <label class="form-label small text-white fw-semibold d-flex align-items-center gap-1">
                            <iconify-icon icon="logos:tiktok-icon" width="14"></iconify-icon>
                            <span>TikTok Pixel</span>
                        </label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="tiktok_input" placeholder="Pixel ID" value="{{ $funnel->tracking_settings['tiktok_pixel_id'] ?? '' }}">
                    </div>

                    <!-- Snapchat Pixel -->
                    <div class="mb-3">
                        <label class="form-label small text-white fw-semibold d-flex align-items-center gap-1">
                            <iconify-icon icon="logos:snapchat" width="14"></iconify-icon>
                            <span>Snapchat Pixel</span>
                        </label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="snap_input" placeholder="Snapchat Pixel ID" value="{{ $funnel->tracking_settings['snap_pixel_id'] ?? '' }}">
                    </div>
                </div>

                <!-- CUSTOM SCRIPTS / CODE SECTION (خانة كاستم لإضافة الأكواد) -->
                <div class="p-3 bg-dark rounded-3 border border-secondary">
                    <h6 class="fw-bold small mb-3 text-white d-flex align-items-center gap-1">
                        <iconify-icon icon="solar:code-square-bold" class="text-warning"></iconify-icon>
                        <span>⚡ أكواد كاستم مخصصة (Custom Scripts & HTML)</span>
                    </h6>

                    <div class="mb-3">
                        <label class="form-label small text-white fw-semibold d-flex align-items-center justify-content-between">
                            <span>كود كاستم في الهيدر (Header Scripts - &lt;head&gt;)</span>
                            <span class="badge bg-primary-subtle text-primary" style="font-size: 10px;">&lt;head&gt;</span>
                        </label>
                        <textarea class="form-control form-control-sm form-control-dark font-monospace" id="custom_head_script_input" rows="4" style="font-size: 11px;" placeholder="<script>
  // Paste custom header code or tracking scripts here
</script>">{{ $funnel->tracking_settings['custom_head_script'] ?? '' }}</textarea>
                        <small class="text-muted" style="font-size: 11px;">يتم تضمين هذا الكود مباشرة داخل عنصر &lt;head&gt; بالفانل.</small>
                    </div>

                    <div>
                        <label class="form-label small text-white fw-semibold d-flex align-items-center justify-content-between">
                            <span>كود كاستم في الفوتر (Footer / Body Scripts)</span>
                            <span class="badge bg-success-subtle text-success" style="font-size: 10px;">&lt;/body&gt;</span>
                        </label>
                        <textarea class="form-control form-control-sm form-control-dark font-monospace" id="custom_body_script_input" rows="4" style="font-size: 11px;" placeholder="<script>
  // Paste custom body or footer scripts here
</script>">{{ $funnel->tracking_settings['custom_body_script'] ?? '' }}</textarea>
                        <small class="text-muted" style="font-size: 11px;">يتم تضمين هذا الكود مباشرة قبل إغلاق عنصر &lt;/body&gt; بالفانل.</small>
                    </div>
                </div>
            </div>

        </div>
    </aside>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- BUILDER CORE ENGINE & I18N LOCALIZATION JAVASCRIPT -->
<script>
    // Current Active Language
    let currentLang = localStorage.getItem('travelwave_fb_lang') || '{{ $currentLang }}' || 'ar';

    // DICTIONARY FOR ARABIC & ENGLISH
    const I18N = {
        ar: {
            switch_to: 'English',
            back_to_funnels: '← الفانلات',
            live: 'نشط',
            draft: 'مسودة',
            desktop: 'كمبيوتر',
            mobile: 'جوال',
            live_preview: 'معاينة حية 👁️',
            save_changes: 'حفظ التعديلات 💾',
            saving: 'جاري الحفظ... ⏳',
            publish_funnel: '🚀 نشر الفانل',
            saved_toast: '💾 تم حفظ التعديلات بنجاح!',
            steps_title: '📌 خطوات الفانل (Steps)',
            btn_add_step: '➕ خطوة',
            palette_title: '🧩 مكتبة العناصر',
            search_placeholder: '🔍 بحث عن عنصر...',
            tab_props: '⚙️ الخصائص',
            tab_design: '🎨 التصميم العام',
            tab_results: '🏆 النتائج',
            tab_integrations: '🔌 الربط',
            inspector_empty: 'انقر على أي عنصر داخل الشاشة أو انقر نقراً مزدوجاً (Double Click) لتعديل نصوصه وتصميمه مباشرة.',
            step_empty_title: 'هذه الخطوة فارغة',
            step_empty_sub: 'اسحب أي عنصر من القائمة على اليمين أو انقر عليه لإضافته هنا فوراً.',
            design_header: '🎨 المظهر ونظام التقييم',
            scoring_label: 'تفعيل نظام الدرجات وحساب الأهلية (Quiz / Scoring)',
            scoring_sub: 'إذا تم إيقافه، سيعمل الفانل كنموذج تجميع بيانات وليد جنريشن عادي بدون احتساب درجات.',
            primary_color_lbl: 'اللون الأساسي (Primary Color)',
            font_family_lbl: 'نوع الخط (Font Family)',
            results_header: '🏆 شاشات النتائج والأهلية',
            btn_add_result: '➕ إضافة نتيجة',
            integrations_header: '⚡ مزامنة CRM و البكسلات',
            crm_sync_lbl: 'مزامنة العملاء في CRM Travel Wave',
            lead_source_lbl: 'مصدر العميل (Lead Source)',
            service_type_lbl: 'نوع الخدمة (Service Type)',
            tracking_header: '🎯 بكسلات التتبع',
            hours: 'الساعات (Hours)',
            minutes: 'الدقائق (Minutes)',
            seconds: 'الثواني (Seconds)',
            timer_header: '⏰ إعدادات المؤقت التنازلي (Countdown Timer):',
            show_in_form: '👁️ إظهار في الفورم',
            hide_from_form: '🚫 إخفاء من الفورم',
            visible_badge: '👁️ ظاهر',
            hidden_badge: '🚫 مخفي',
            required_badge: 'حقل إجباري (Required)',
            required_switch_lbl: 'إجباري (Required) - لا يمكن تخطي السؤال بدونه',
            label_lbl: 'نص السؤال / العنوان (Label)',
            crm_key_lbl: 'مفتاح الحقل في CRM (Field Key)',
            presets_title: '📋 نماذج جاهزة من موقع Travel Wave:',
            custom_fields_title: '🧩 تخصيص حقول النموذج',
            btn_add_field: '➕ حقل جديد',
            btn_add_opt: '➕ خيار',
            dropdown_opts_title: 'خيارات القائمة المنسدلة:',
            opt_text_ph: 'نص الخيار',
            field_name_ph: 'اسم الحقل',
            type_text: 'نص عادي (Text)',
            type_phone: 'واتساب / هاتف (Phone)',
            type_email: 'بريد (Email)',
            type_dropdown: 'قائمة منسدلة (Dropdown)',
            type_date: 'تاريخ (Date)',
            type_textarea: 'نص كبير (Textarea)',
            country_presets_lbl: 'تحديد مجموعات الدول (تفعيل/إلغاء بالنقر):',
            total_active_countries: 'إجمالي الدول المفعلة حالياً:',
            delete_step_confirm: 'هل أنت متأكد من حذف هذه الخطوة بكافة عناصرها؟',
            step_default_title: 'السؤال',
            step_default_sub: 'اختر الإجابة المناسبة للمتابعة',
            media_modal_title: '🖼️ اختيار صورة من مكتبة الموقع',
            media_modal_lbl: 'إدخال رابط صورة مخصص (Direct Image URL)',
            media_modal_apply: 'تطبيق الرابط',
            media_modal_presets: 'أو اختر من المعرض المقترح:',
            tab_content: 'المحتوى (Content)',
            tab_element_design: 'التصميم (Design)',
            heading_text_lbl: 'نص العنوان / السؤال (Heading Text)',
            subline_text_lbl: 'الوصف الفرعي (Subline Text)',
            variables_btn: '{ } المتغيرات',
            visibility_rules: 'قواعد الظهور والتحكم (Visibility Rules)',
            always_hide_lbl: 'إخفاء دائم عن المشاركين (Always hide for participants)',
            customize_element_only: 'تخصيص هذا العنصر فقط (Customize this element only)',
            heading_font_group: 'خط عنوان السؤال (Question Font)',
            subline_font_group: 'خط الوصف الفرعي (Subline Font)',
            options_design_group: 'تصميم خيارات الإجابة (Options Design - لكل الخيارات)',
            font_lbl: 'الخط (Font)',
            size_lbl: 'الحجم (Size)',
            bold_lbl: 'عريض (Bold)',
            italic_lbl: 'مائل (Italic)',
            color_align_group: 'اللون والمحاذاة (Color & Alignment)',
            text_color_lbl: 'لون خط العنوان (Question Color)',
            subline_color_lbl: 'لون الوصف الفرعي (Subline Color)',
            opt_text_color_lbl: 'لون نص الخيارات (Options Text Color)',
            opt_bg_color_lbl: 'لون خلفية الخيارات (Options Background)',
            opt_border_color_lbl: 'لون إطار الخيارات (Options Border)',
            opt_radius_lbl: 'انحناء الحواف (Border Radius)',
            bg_color_lbl: 'لون خلفية العنصر الكامل (Card Background)',
            align_lbl: 'المحاذاة (Align)',
            align_center: 'وسط (Center)',
            align_right: 'يمين (Right)',
            align_left: 'يسار (Left)',
            custom_design_badge: '🎨 تخصيص التصميم',
            move_left: 'تحريك لليسار',
            move_right: 'تحريك لليمين',
            edit_step: 'تعديل اسم الصفحة',
            duplicate_step: 'تكرار الصفحة',
            disable_step: 'تعطيل الصفحة',
            enable_step: 'تفعيل الصفحة',
            delete_step: 'حذف الصفحة',
            thank_you_page: 'Thank you page',
            add_thank_you_page: 'إضافة صفحة الشكر',
            remove_thank_you_page: 'إزالة صفحة الشكر',
            thank_you_settings_title: '🎉 إعدادات صفحة الشكر (Thank You Page)',
            show_thank_you_lbl: 'إظهار صفحة الشكر للعميل عند الانتهاء',
            show_thank_you_sub: 'عند تفعيله، سيتم توجيه العميل فوراً لصفحة الشكر هذه بعد إكمال النموذج.'
        },
        en: {
            switch_to: 'العربية 🇸🇦',
            back_to_funnels: '← Funnels',
            live: 'Live',
            draft: 'Draft',
            desktop: 'Desktop',
            mobile: 'Mobile',
            live_preview: 'Live Preview 👁️',
            save_changes: 'Save Changes 💾',
            saving: 'Saving... ⏳',
            publish_funnel: '🚀 Publish Funnel',
            saved_toast: '💾 Saved successfully!',
            steps_title: '📌 Funnel Steps',
            btn_add_step: '➕ Step',
            palette_title: '🧩 Element Palette',
            search_placeholder: '🔍 Search elements...',
            tab_props: '⚙️ Properties',
            tab_design: '🎨 Global Design',
            tab_results: '🏆 Outcomes',
            tab_integrations: '🔌 Connect',
            inspector_empty: 'Click on any canvas element or double-click text to edit content and styling directly.',
            step_empty_title: 'This step is empty',
            step_empty_sub: 'Drag any element from the palette on the left or click to add it here.',
            design_header: '🎨 Appearance & Scoring Mode',
            scoring_label: 'Enable Quiz Scoring & Eligibility System',
            scoring_sub: 'If disabled, the funnel acts as a clean lead generation form without scores or percentages.',
            primary_color_lbl: 'Primary Brand Color',
            font_family_lbl: 'Font Family',
            results_header: '🏆 Result Screens & Outcomes',
            btn_add_result: '➕ Add Outcome',
            integrations_header: '⚡ CRM Sync & Tracking Pixels',
            crm_sync_lbl: 'Sync Leads to Travel Wave CRM',
            lead_source_lbl: 'Lead Source',
            service_type_lbl: 'Service Type',
            tracking_header: '🎯 Tracking Pixels',
            hours: 'Hours',
            minutes: 'Minutes',
            seconds: 'Seconds',
            timer_header: '⏰ Countdown Timer Settings:',
            show_in_form: '👁️ Show in Form',
            hide_from_form: '🚫 Hidden from Form',
            visible_badge: '👁️ Visible',
            hidden_badge: '🚫 Hidden',
            required_badge: 'Required Field',
            required_switch_lbl: 'Required - User cannot proceed without answering',
            label_lbl: 'Field / Question Label',
            crm_key_lbl: 'CRM Field Key',
            presets_title: '📋 Travel Wave Ready Presets:',
            custom_fields_title: '🧩 Form Fields Customization',
            btn_add_field: '➕ Add Field',
            btn_add_opt: '➕ Option',
            dropdown_opts_title: 'Dropdown Options:',
            opt_text_ph: 'Option text',
            field_name_ph: 'Field name',
            type_text: 'Text Input',
            type_phone: 'WhatsApp / Phone',
            type_email: 'Email Address',
            type_dropdown: 'Dropdown Select',
            type_date: 'Date Picker',
            type_textarea: 'Textarea (Long text)',
            country_presets_lbl: 'Select Country Groups (Click to Toggle):',
            total_active_countries: 'Total Active Countries:',
            delete_step_confirm: 'Are you sure you want to delete this step with all its elements?',
            step_default_title: 'Question',
            step_default_sub: 'Select the best option to proceed',
            media_modal_title: '🖼️ Select Image from Media Library',
            media_modal_lbl: 'Direct Image URL',
            media_modal_apply: 'Apply URL',
            media_modal_presets: 'Or choose from presets gallery:',
            tab_content: 'Content',
            tab_element_design: 'Design',
            move_left: 'Move left',
            move_right: 'Move right',
            edit_step: 'Edit',
            duplicate_step: 'Duplicate',
            disable_step: 'Disable',
            enable_step: 'Enable',
            delete_step: 'Delete',
            thank_you_page: 'Thank you page',
            add_thank_you_page: 'Add Thank You page',
            remove_thank_you_page: 'Remove Thank You page',
            thank_you_settings_title: '🎉 Thank You Page Settings',
            show_thank_you_lbl: 'Show Thank You Page to customers upon completion',
            show_thank_you_sub: 'When enabled, customers will see this custom Thank You page immediately after submitting the form.',
            heading_text_lbl: 'Question / Heading Text',
            subline_text_lbl: 'Subline Text',
            variables_btn: '{ } Variables',
            visibility_rules: 'Visibility Rules & Controls',
            always_hide_lbl: 'Always hide for participants',
            customize_element_only: 'Customize this element only',
            heading_font_group: 'Question Font',
            subline_font_group: 'Subline Font',
            options_design_group: 'Options & Choices Design (All options at once)',
            font_lbl: 'Font',
            size_lbl: 'Size',
            bold_lbl: 'Bold',
            italic_lbl: 'Italic',
            color_align_group: 'Color & Alignment',
            text_color_lbl: 'Question Text Color',
            subline_color_lbl: 'Subline Color',
            opt_text_color_lbl: 'Options Text Color',
            opt_bg_color_lbl: 'Options Background',
            opt_border_color_lbl: 'Options Border',
            opt_radius_lbl: 'Border Radius',
            bg_color_lbl: 'Card Background Color',
            align_lbl: 'Align',
            align_center: 'Center',
            align_right: 'Right',
            align_left: 'Left',
            custom_design_badge: '🎨 Custom Design'
        }
    };

    function t(key) {
        return I18N[currentLang]?.[key] || I18N['en']?.[key] || key;
    }

    // PALETTE CATEGORIES DEFINITION (BILINGUAL)
    const PALETTE_CATEGORIES = [
        {
            id: 'cat_choices',
            icon: 'solar:checklist-minimalistic-bold-duotone',
            iconClass: 'text-primary',
            title_ar: 'Choices (أنواع الاختيار)',
            title_en: 'Choices & Options',
            elements: [
                { type: 'single_choice', icon: 'solar:document-text-bold-duotone', iconClass: 'text-primary', label_ar: 'Single Choice (فردي)', label_en: 'Single Choice' },
                { type: 'multiple_choice', icon: 'solar:list-check-bold-duotone', iconClass: 'text-primary', label_ar: 'Multiple Choice (متعدد)', label_en: 'Multiple Choice' },
                { type: 'radio_choice', icon: 'solar:record-circle-bold-duotone', iconClass: 'text-primary', label_ar: 'Radio Choice (راديو)', label_en: 'Radio Choice' },
                { type: 'checkbox_choice', icon: 'solar:check-square-bold-duotone', iconClass: 'text-primary', label_ar: 'Checkbox (تشيك بوكس)', label_en: 'Checkbox Choice' },
                { type: 'yes_no', icon: 'solar:shield-check-bold-duotone', iconClass: 'text-primary', label_ar: 'Yes / No (نعم أو لا)', label_en: 'Yes / No' },
                { type: 'image_choice', icon: 'solar:gallery-bold-duotone', iconClass: 'text-primary', label_ar: 'Image Cards (بطاقات صور)', label_en: 'Image Cards' },
                { type: 'dropdown', icon: 'solar:menu-dots-square-bold-duotone', iconClass: 'text-primary', label_ar: 'Dropdown (قائمة منسدلة)', label_en: 'Dropdown Select' },
            ]
        },
        {
            id: 'cat_rating',
            icon: 'solar:star-fall-bold-duotone',
            iconClass: 'text-warning',
            title_ar: 'Rating & Ranking (التقييم والسلايدر)',
            title_en: 'Rating & Ranking',
            elements: [
                { type: 'rating', icon: 'solar:star-bold-duotone', iconClass: 'text-warning', label_ar: 'Rating Stars (نجوم)', label_en: 'Star Rating' },
                { type: 'slider', icon: 'solar:tuning-square-2-bold-duotone', iconClass: 'text-warning', label_ar: 'Slider (شريط التمرير)', label_en: 'Slider' },
                { type: 'nps', icon: 'solar:like-shapes-bold-duotone', iconClass: 'text-warning', label_ar: 'NPS Score (0-10)', label_en: 'NPS Score (0-10)', col12: true },
            ]
        },
        {
            id: 'cat_collecting',
            icon: 'solar:chat-round-line-bold-duotone',
            iconClass: 'text-info',
            title_ar: 'Collecting Data (جمع البيانات)',
            title_en: 'Collecting Data',
            elements: [
                { type: 'short_answer', icon: 'solar:text-field-bold-duotone', iconClass: 'text-info', label_ar: 'Short Answer (نص قصير)', label_en: 'Short Answer' },
                { type: 'long_answer', icon: 'solar:chat-square-bold-duotone', iconClass: 'text-info', label_ar: 'Long Answer (نص طويل)', label_en: 'Long Answer' },
                { type: 'number_input', icon: 'solar:calculator-bold-duotone', iconClass: 'text-info', label_ar: 'Number (رقم)', label_en: 'Number Input' },
                { type: 'currency', icon: 'solar:dollar-bold-duotone', iconClass: 'text-info', label_ar: 'Currency (عملة وسعر)', label_en: 'Currency' },
                { type: 'file_upload', icon: 'solar:upload-track-bold-duotone', iconClass: 'text-info', label_ar: 'File Upload (رفع ملف)', label_en: 'File Upload', col12: true },
            ]
        },
        {
            id: 'cat_contact',
            icon: 'solar:user-id-bold-duotone',
            iconClass: 'text-success',
            title_ar: 'Contact Info (بيانات الاتصال)',
            title_en: 'Contact Info',
            elements: [
                { type: 'contact_form', icon: 'solar:card-2-bold-duotone', iconClass: 'text-success', label_ar: 'Contact Form (نموذج كامل)', label_en: 'Contact Form' },
                { type: 'email', icon: 'solar:letter-bold-duotone', iconClass: 'text-success', label_ar: 'Email (بريد)', label_en: 'Email' },
                { type: 'phone', icon: 'solar:phone-calling-rounded-bold-duotone', iconClass: 'text-success', label_ar: 'Phone (واتساب/هاتف)', label_en: 'Phone / WhatsApp' },
                { type: 'address', icon: 'solar:map-point-bold-duotone', iconClass: 'text-success', label_ar: 'Address (العنوان)', label_en: 'Address' },
                { type: 'country', icon: 'solar:flag-bold-duotone', iconClass: 'text-success', label_ar: 'Country (الدولة)', label_en: 'Country' },
                { type: 'website', icon: 'solar:global-bold-duotone', iconClass: 'text-success', label_ar: 'Website (موقع)', label_en: 'Website' },
            ]
        },
        {
            id: 'cat_time',
            icon: 'solar:calendar-bold-duotone',
            iconClass: 'text-danger',
            title_ar: 'Time & Scheduling (المواعيد والمؤقت)',
            title_en: 'Time & Scheduling',
            elements: [
                { type: 'date_picker', icon: 'solar:calendar-date-bold-duotone', iconClass: 'text-danger', label_ar: 'Date (تاريخ)', label_en: 'Date Picker' },
                { type: 'schedule', icon: 'solar:calendar-mark-bold-duotone', iconClass: 'text-danger', label_ar: 'Schedule (موعد)', label_en: 'Appointments' },
                { type: 'timer', icon: 'solar:clock-circle-bold-duotone', iconClass: 'text-danger', label_ar: 'Countdown Timer (مؤقت تنازلي)', label_en: 'Countdown Timer', col12: true },
            ]
        },
        {
            id: 'cat_static',
            icon: 'solar:text-bold-duotone',
            iconClass: 'text-warning',
            title_ar: 'Static Elements (العناصر الثابتة)',
            title_en: 'Static Elements',
            elements: [
                { type: 'heading', icon: 'solar:text-bold-duotone', iconClass: 'text-warning', label_ar: 'Heading (عنوان)', label_en: 'Heading' },
                { type: 'paragraph', icon: 'solar:notes-bold-duotone', iconClass: 'text-warning', label_ar: 'Paragraph (فقرة)', label_en: 'Paragraph' },
                { type: 'table', icon: 'solar:table-bold-duotone', iconClass: 'text-warning', label_ar: 'Table (جدول مقارنة)', label_en: 'Table' },
                { type: 'testimonials', icon: 'solar:chat-dots-bold-duotone', iconClass: 'text-warning', label_ar: 'Testimonials (آراء)', label_en: 'Testimonials' },
                { type: 'faqs', icon: 'solar:question-circle-bold-duotone', iconClass: 'text-warning', label_ar: 'FAQs (الأسئلة الشائعة)', label_en: 'FAQs' },
                { type: 'coupon_code', icon: 'solar:ticket-bold-duotone', iconClass: 'text-warning', label_ar: 'Coupon (كود خصم)', label_en: 'Coupon Code' },
            ]
        }
    ];

    const AVAILABLE_FONTS = [
        { name: 'Tajawal (عربي)', val: "'Tajawal', sans-serif" },
        { name: 'Cairo (كايرو)', val: "'Cairo', sans-serif" },
        { name: 'Almarai (المراعي)', val: "'Almarai', sans-serif" },
        { name: 'Inter', val: "'Inter', sans-serif" },
        { name: 'Poppins', val: "'Poppins', sans-serif" },
        { name: 'Montserrat', val: "'Montserrat', sans-serif" },
        { name: 'System Default', val: 'system-ui, -apple-system, sans-serif' },
    ];

    const AVAILABLE_SIZES = ['12px', '14px', '15px', '16px', '18px', '20px', '22px', '24px', '28px', '30px', '32px', '36px', '40px', '48px'];
    const AVAILABLE_RADIUS = [
        { label: '0px (حواف حادة)', val: '0px' },
        { label: '6px (خفيف)', val: '6px' },
        { label: '10px (متوسط)', val: '10px' },
        { label: '14px (دائري أنيق)', val: '14px' },
        { label: '20px (كبير)', val: '20px' },
        { label: '50px (شكل كبسولة Pill)', val: '50px' }
    ];

    const funnelData = @json($funnel);
    if (!funnelData.steps) funnelData.steps = [];
    if (!funnelData.results) funnelData.results = [];
    if (!funnelData.design_settings) funnelData.design_settings = { primary_color: '#2563eb', scoring_enabled: true };

    // Default design initialization
    funnelData.steps.forEach(step => {
        (step.elements || []).forEach(el => {
            if (el.is_required === undefined) {
                el.is_required = el.properties?.is_required || false;
            }
            if (!el.properties) el.properties = {};
            if (!el.properties.design) {
                el.properties.design = {
                    custom_enabled: false,
                    heading_font: "'Tajawal', sans-serif",
                    heading_size: '22px',
                    heading_bold: true,
                    heading_italic: false,
                    text_color: '#0f172a',
                    align: 'start',
                    subline_font: "'Tajawal', sans-serif",
                    subline_size: '14px',
                    subline_bold: false,
                    subline_italic: false,
                    subline_color: '#64748b',
                    options_font: "'Tajawal', sans-serif",
                    options_size: '15px',
                    options_bold: true,
                    options_italic: false,
                    options_text_color: '#1e293b',
                    options_bg_color: '#ffffff',
                    options_border_color: '#e2e8f0',
                    options_border_radius: '12px',
                    options_align: 'start',
                    bg_color: ''
                };
            }
        });
    });

    let activeStepIndex = 0;
    let selectedElementIndex = null;
    let currentElementActiveSubTab = 'content'; // 'content' or 'design'
    let currentEditingOptionIdx = null;

    // COUNTRY GROUP PRESETS
    const COUNTRY_GROUPS = {
        arab: {
            id: 'arab',
            label_ar: '🌍 الدول العربية',
            label_en: '🌍 Arab Countries',
            countries: [
                { label: '🇸🇦 المملكة العربية السعودية', value: 'Saudi Arabia' },
                { label: '🇦🇪 الإمارات العربية المتحدة', value: 'UAE' },
                { label: '🇪🇬 جمهورية مصر العربية', value: 'Egypt' },
                { label: '🇰🇼 دولة الكويت', value: 'Kuwait' },
                { label: '🇶🇦 دولة قطر', value: 'Qatar' },
                { label: '🇧🇭 مملكة البحرين', value: 'Bahrain' },
                { label: '🇴🇲 سلطنة عمان', value: 'Oman' },
                { label: '🇯🇴 المملكة الأردنية الهاشمية', value: 'Jordan' },
                { label: '🇮🇶 جمهورية العراق', value: 'Iraq' },
                { label: '🇲🇦 المملكة المغربية', value: 'Morocco' },
                { label: '🇩🇿 الجمهورية الجزائرية', value: 'Algeria' },
                { label: '🇹🇳 الجمهورية التونسية', value: 'Tunisia' },
                { label: '🇱🇧 الجمهورية اللبنانية', value: 'Lebanon' },
                { label: '🇸🇩 جمهورية السودان', value: 'Sudan' },
                { label: '🇾🇪 الجمهورية اليمنية', value: 'Yemen' },
                { label: '🇵🇸 دولة فلسطين', value: 'Palestine' },
                { label: '🇱🇾 دولة ليبيا', value: 'Libya' },
            ]
        },
        eu: {
            id: 'eu',
            label_ar: '🇪🇺 الاتحاد الأوروبي (شنغن)',
            label_en: '🇪🇺 EU (Schengen)',
            countries: [
                { label: '🇩🇪 ألمانيا (Germany)', value: 'Germany' },
                { label: '🇫🇷 فرنسا (France)', value: 'France' },
                { label: '🇮🇹 إيطاليا (Italy)', value: 'Italy' },
                { label: '🇪🇸 إسبانيا (Spain)', value: 'Spain' },
                { label: '🇨🇭 سويسرا (Switzerland)', value: 'Switzerland' },
                { label: '🇳🇱 هولندا (Netherlands)', value: 'Netherlands' },
                { label: '🇦🇹 النمسا (Austria)', value: 'Austria' },
                { label: '🇵🇹 البرتغال (Portugal)', value: 'Portugal' },
                { label: '🇬🇷 اليونان (Greece)', value: 'Greece' },
                { label: '🇸🇪 السويد (Sweden)', value: 'Sweden' },
                { label: '🇧🇪 بلجيكا (Belgium)', value: 'Belgium' },
                { label: '🇵🇱 بولندا (Poland)', value: 'Poland' },
                { label: '🇳🇴 النرويج (Norway)', value: 'Norway' },
                { label: '🇩🇰 الدنمارك (Denmark)', value: 'Denmark' },
                { label: '🇫🇮 فنلندا (Finland)', value: 'Finland' },
                { label: '🇨🇿 التشيك (Czech Republic)', value: 'Czech Republic' },
                { label: '🇭🇺 المجر (Hungary)', value: 'Hungary' },
            ]
        },
        asia: {
            id: 'asia',
            label_ar: '🌏 دول آسيا',
            label_en: '🌏 Asian Countries',
            countries: [
                { label: '🇲🇾 ماليزيا (Malaysia)', value: 'Malaysia' },
                { label: '🇹🇭 تايلاند (Thailand)', value: 'Thailand' },
                { label: '🇮🇩 إندونيسيا (Indonesia)', value: 'Indonesia' },
                { label: '🇸🇬 سنغافورة (Singapore)', value: 'Singapore' },
                { label: '🇯🇵 اليابان (Japan)', value: 'Japan' },
                { label: '🇰🇷 كوريا الجنوبية (South Korea)', value: 'South Korea' },
                { label: '🇨🇳 الصين (China)', value: 'China' },
                { label: '🇹🇷 تركيا (Turkey)', value: 'Turkey' },
                { label: '🇬🇪 جورجيا (Georgia)', value: 'Georgia' },
                { label: '🇦🇿 أذربيجان (Azerbaijan)', value: 'Azerbaijan' },
                { label: '🇲🇻 جزر المالديف (Maldives)', value: 'Maldives' },
                { label: '🇵🇭 الفلبين (Philippines)', value: 'Philippines' },
                { label: '🇻🇳 فيتنام (Vietnam)', value: 'Vietnam' },
                { label: '🇮🇳 الهند (India)', value: 'India' },
            ]
        },
        americas: {
            id: 'americas',
            label_ar: '🌎 دول أمريكا وكندا',
            label_en: '🌎 Americas & Canada',
            countries: [
                { label: '🇺🇸 الولايات المتحدة الأمريكية (USA)', value: 'USA' },
                { label: '🇨🇦 كندا (Canada)', value: 'Canada' },
                { label: '🇬🇧 المملكة المتحدة (بريطانيا)', value: 'United Kingdom' },
                { label: '🇦🇺 أستراليا (Australia)', value: 'Australia' },
                { label: '🇧🇷 البرازيل (Brazil)', value: 'Brazil' },
            ]
        }
    };

    const WORLD_CURRENCIES = [
        { code: 'SAR', label: '🇸🇦 SAR (ريال سعودي)' },
        { code: 'USD', label: '🇺🇸 USD (US Dollar)' },
        { code: 'EUR', label: '🇪🇺 EUR (Euro)' },
        { code: 'AED', label: '🇦🇪 AED (درهم إماراتي)' },
        { code: 'EGP', label: '🇪🇬 EGP (جنيه مصري)' },
        { code: 'KWD', label: '🇰🇼 KWD (دينار كويتي)' },
        { code: 'QAR', label: '🇶🇦 QAR (ريال قطري)' },
        { code: 'BHD', label: '🇧🇭 BHD (دينار بحريني)' },
        { code: 'OMR', label: '🇴🇲 OMR (ريال عماني)' },
        { code: 'GBP', label: '🇬🇧 GBP (British Pound)' },
        { code: 'CAD', label: '🇨🇦 CAD (Canadian Dollar)' },
        { code: 'AUD', label: '🇦🇺 AUD (Australian Dollar)' },
        { code: 'TRY', label: '🇹🇷 TRY (Turkish Lira)' },
        { code: 'MAD', label: '🇲🇦 MAD (Moroccan Dirham)' },
        { code: 'JOD', label: '🇯🇴 JOD (Jordanian Dinar)' },
    ];

    document.addEventListener('DOMContentLoaded', () => {
        applyLanguageToUI();
        renderStepsList();
        renderPagesBar();
        renderCanvas();
        renderResultsList();
        setupDragAndDrop();
    });

    // ── 🌍 DYNAMIC I18N SWITCHER ─────────────────────────────────────────────
    function toggleLanguage() {
        currentLang = currentLang === 'ar' ? 'en' : 'ar';
        localStorage.setItem('travelwave_fb_lang', currentLang);
        fetch(`/locale/${currentLang}`).catch(() => {});

        applyLanguageToUI();
        renderStepsList();
        renderPagesBar();
        renderCanvas();
        if (selectedElementIndex !== null) {
            inspectElement(selectedElementIndex, currentElementActiveSubTab);
        } else if (activeStepIndex === 'thank_you') {
            inspectThankYouPage();
        } else {
            inspectStepProperties();
        }
    }

    function applyLanguageToUI() {
        const isRtl = currentLang === 'ar';
        document.documentElement.setAttribute('lang', currentLang);
        document.documentElement.setAttribute('dir', isRtl ? 'rtl' : 'ltr');

        document.getElementById('lang_switch_label').innerText = t('switch_to');
        document.getElementById('topbar_btn_back').innerText = t('back_to_funnels');
        document.getElementById('txt_device_desktop').innerText = t('desktop');
        document.getElementById('txt_device_mobile').innerText = t('mobile');
        document.getElementById('txt_btn_preview').innerText = t('live_preview');
        document.getElementById('txt_btn_save').innerText = t('save_changes');
        if (document.getElementById('txt_btn_publish')) {
            document.getElementById('txt_btn_publish').innerText = t('publish_funnel');
        }

        document.getElementById('txt_steps_title').innerText = t('steps_title');
        document.getElementById('txt_btn_add_step').innerText = t('btn_add_step');
        document.getElementById('txt_palette_title').innerText = t('palette_title');
        document.getElementById('element_search_input').placeholder = t('search_placeholder');

        document.getElementById('tab_btn_props').innerText = t('tab_props');
        document.getElementById('tab_btn_design').innerText = t('tab_design');
        document.getElementById('tab_btn_results').innerText = t('tab_results');
        document.getElementById('tab_btn_integrations').innerText = t('tab_integrations');

        document.getElementById('txt_design_header').innerText = t('design_header');
        document.getElementById('txt_scoring_label').innerText = t('scoring_label');
        document.getElementById('txt_scoring_sub').innerText = t('scoring_sub');
        document.getElementById('txt_primary_color_lbl').innerText = t('primary_color_lbl');
        document.getElementById('txt_font_family_lbl').innerText = t('font_family_lbl');

        document.getElementById('txt_results_header').innerText = t('results_header');
        document.getElementById('txt_btn_add_result').innerText = t('btn_add_result');

        document.getElementById('txt_integrations_header').innerText = t('integrations_header');
        document.getElementById('txt_crm_sync_lbl').innerText = t('crm_sync_lbl');
        document.getElementById('txt_lead_source_lbl').innerText = t('lead_source_lbl');
        document.getElementById('txt_service_type_lbl').innerText = t('service_type_lbl');
        document.getElementById('txt_tracking_header').innerText = t('tracking_header');

        document.getElementById('txt_media_modal_title').innerText = t('media_modal_title');
        document.getElementById('txt_media_modal_lbl').innerText = t('media_modal_lbl');
        document.getElementById('txt_media_modal_apply').innerText = t('media_modal_apply');
        document.getElementById('txt_media_modal_presets').innerText = t('media_modal_presets');

        renderPaletteCategories();
        renderPagesBar();
    }

    function renderPaletteCategories() {
        const accordion = document.getElementById('palette_accordion');
        let html = '';

        PALETTE_CATEGORIES.forEach((cat, cIdx) => {
            const catTitle = currentLang === 'ar' ? cat.title_ar : cat.title_en;
            const isOpen = cIdx === 0;

            html += `
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button ${isOpen ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${cat.id}">
                            <iconify-icon icon="${cat.icon}" class="${cat.iconClass} me-2" width="18"></iconify-icon>
                            <span>${catTitle}</span>
                        </button>
                    </h2>
                    <div id="${cat.id}" class="accordion-collapse collapse ${isOpen ? 'show' : ''}">
                        <div class="accordion-body">
                            <div class="row g-2">
            `;

            cat.elements.forEach(item => {
                const itemLabel = currentLang === 'ar' ? item.label_ar : item.label_en;
                const colClass = item.col12 ? 'col-12' : 'col-6';
                html += `
                    <div class="${colClass}">
                        <div class="fb-element-pill" draggable="true" data-type="${item.type}" onclick="addElementToCurrentStep('${item.type}')">
                            <iconify-icon icon="${item.icon}" class="${item.iconClass}"></iconify-icon>
                            <span>${itemLabel}</span>
                        </div>
                    </div>
                `;
            });

            html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        accordion.innerHTML = html;
        setupDragAndDrop();
    }

    function filterPaletteElements(query) {
        query = query.toLowerCase().trim();
        document.querySelectorAll('.fb-element-pill').forEach(pill => {
            const text = pill.innerText.toLowerCase();
            pill.parentElement.style.display = (!query || text.includes(query)) ? 'block' : 'none';
        });
    }

    // ── 1. STEPS & PAGE NAVIGATION MANAGEMENT ────────────────────────────────
    function renderStepsList() {
        const wrapper = document.getElementById('steps_list_wrapper');
        if (!wrapper) return;
        let html = '';

        funnelData.steps.forEach((step, idx) => {
            const isActive = idx === activeStepIndex;
            html += `
                <div class="fb-step-item ${isActive ? 'active' : ''}" onclick="selectStep(${idx})">
                    <div class="d-flex align-items-center gap-2 text-truncate">
                        <iconify-icon icon="solar:menu-dots-bold" class="text-muted" style="cursor: grab;"></iconify-icon>
                        <span class="fw-bold small text-truncate">${idx + 1}. ${step.title || (t('step_default_title') + ' ' + (idx + 1))}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-dark text-muted small">${step.step_type || 'question'}</span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1" onclick="event.stopPropagation(); deleteStep(${idx})" title="Delete Step">
                            <iconify-icon icon="solar:trash-bin-minimalistic-bold" width="14"></iconify-icon>
                        </button>
                    </div>
                </div>
            `;
        });

        wrapper.innerHTML = html;

        new Sortable(wrapper, {
            animation: 150,
            ghostClass: 'bg-primary-subtle',
            onEnd: function (evt) {
                if (evt.oldIndex !== undefined && evt.newIndex !== undefined) {
                    const movedItem = funnelData.steps.splice(evt.oldIndex, 1)[0];
                    funnelData.steps.splice(evt.newIndex, 0, movedItem);
                    activeStepIndex = evt.newIndex;
                    renderStepsList();
                    renderPagesBar();
                    renderCanvas();
                }
            }
        });
    }

    function renderPagesBar() {
        const container = document.getElementById('fb_pages_bar_container');
        if (!container) return;

        if (!funnelData.design_settings) funnelData.design_settings = {};
        if (!funnelData.design_settings.thank_you_page) {
            funnelData.design_settings.thank_you_page = {
                enabled: true,
                title: currentLang === 'ar' ? 'شكراً لإكمال البيانات!' : 'Thanks for your submission!',
                subtitle: currentLang === 'ar' ? 'تم استلام معلوماتك بنجاح، وسنواصل التواصل معك في أقرب وقت.' : 'Made with involve.me, the quickest way to create interactive lead funnels.',
                icon_type: 'checkmark',
                button_text: currentLang === 'ar' ? 'العودة للموقع' : 'Create your own',
                button_action: 'restart',
                button_url: '',
                button_whatsapp: ''
            };
        }

        const tySettings = funnelData.design_settings.thank_you_page;
        const totalSteps = funnelData.steps.length;

        let html = `
            <div class="d-flex align-items-center gap-2" style="overflow-x: auto; max-width: 75%;">
                <div class="fb-pages-scroll-area" id="fb_step_pills_wrapper">
        `;

        funnelData.steps.forEach((step, idx) => {
            const isActive = activeStepIndex === idx;
            const isDisabled = !!step.is_hidden;
            const stepNum = idx + 1;
            const stepName = step.title ? `${stepNum}. ${step.title}` : `${stepNum}. page`;

            html += `
                <div class="fb-page-pill-item ${isActive ? 'active' : ''} ${isDisabled ? 'is-disabled-step' : ''}" onclick="selectStep(${idx})">
                    <span>${escapeHtml(stepName)}</span>
                    <span class="ms-1 opacity-75" onclick="event.stopPropagation(); togglePageMenu(event, ${idx})">
                        <iconify-icon icon="solar:alt-arrow-down-bold" width="12"></iconify-icon>
                    </span>
                    
                    <!-- Page Dropdown Menu -->
                    <div class="fb-page-dropdown-menu" id="page_menu_${idx}">
                        <div class="fb-page-dropdown-item ${idx === 0 ? 'disabled' : ''}" onclick="event.stopPropagation(); moveStepLeft(${idx})">
                            <iconify-icon icon="solar:alt-arrow-left-linear" width="16"></iconify-icon>
                            <span>${t('move_left')}</span>
                        </div>
                        <div class="fb-page-dropdown-item ${idx === totalSteps - 1 ? 'disabled' : ''}" onclick="event.stopPropagation(); moveStepRight(${idx})">
                            <iconify-icon icon="solar:alt-arrow-right-linear" width="16"></iconify-icon>
                            <span>${t('move_right')}</span>
                        </div>
                        <hr class="my-1 border-secondary opacity-25">
                        <div class="fb-page-dropdown-item" onclick="event.stopPropagation(); editStepTitle(${idx})">
                            <iconify-icon icon="solar:pen-bold" width="16"></iconify-icon>
                            <span>${t('edit_step')}</span>
                        </div>
                        <div class="fb-page-dropdown-item" onclick="event.stopPropagation(); duplicateStep(${idx})">
                            <iconify-icon icon="solar:copy-bold" width="16"></iconify-icon>
                            <span>${t('duplicate_step')}</span>
                        </div>
                        <div class="fb-page-dropdown-item" onclick="event.stopPropagation(); toggleDisableStep(${idx})">
                            <iconify-icon icon="${step.is_hidden ? 'solar:eye-bold' : 'solar:eye-closed-bold'}" width="16"></iconify-icon>
                            <span>${step.is_hidden ? t('enable_step') : t('disable_step')}</span>
                        </div>
                        <hr class="my-1 border-secondary opacity-25">
                        <div class="fb-page-dropdown-item text-danger" onclick="event.stopPropagation(); deleteStep(${idx})">
                            <iconify-icon icon="solar:trash-bin-trash-bold" width="16"></iconify-icon>
                            <span>${t('delete_step')}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
                <button type="button" class="fb-page-bar-add-btn" onclick="addNewStep()" title="${t('btn_add_step')}">+</button>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Logic Connector Icon -->
                <div class="fb-logic-connector-icon" title="Logic Routing">
                    <iconify-icon icon="solar:routing-2-bold" width="20"></iconify-icon>
                </div>

                <!-- Thank You Page Pill -->
                <div class="position-relative">
                    <div class="fb-page-pill-item ${activeStepIndex === 'thank_you' ? 'active' : ''} ${!tySettings.enabled ? 'is-disabled-step' : ''}" onclick="selectThankYouPage()">
                        <span>${t('thank_you_page')}</span>
                        <span class="ms-1 opacity-75" onclick="event.stopPropagation(); toggleThankYouMenu(event)">
                            <iconify-icon icon="solar:alt-arrow-down-bold" width="12"></iconify-icon>
                        </span>

                        <div class="fb-page-dropdown-menu" id="thank_you_menu">
                            <div class="fb-page-dropdown-item" onclick="event.stopPropagation(); selectThankYouPage()">
                                <iconify-icon icon="solar:pen-bold" width="16"></iconify-icon>
                                <span>${t('edit_step')}</span>
                            </div>
                            <div class="fb-page-dropdown-item" onclick="event.stopPropagation(); toggleThankYouPage(!${tySettings.enabled})">
                                <iconify-icon icon="${tySettings.enabled ? 'solar:eye-closed-bold' : 'solar:eye-bold'}" width="16"></iconify-icon>
                                <span>${tySettings.enabled ? t('disable_step') : t('enable_step')}</span>
                            </div>
                            <hr class="my-1 border-secondary opacity-25">
                            <div class="fb-page-dropdown-item text-danger" onclick="event.stopPropagation(); toggleThankYouPage(false)">
                                <iconify-icon icon="solar:trash-bin-trash-bold" width="16"></iconify-icon>
                                <span>${t('remove_thank_you_page')}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="fb-page-bar-add-btn" onclick="toggleThankYouPage(true); selectThankYouPage();" title="${t('add_thank_you_page')}">+</button>
            </div>
        `;

        container.innerHTML = html;

        const wrapperEl = document.getElementById('fb_step_pills_wrapper');
        if (wrapperEl) {
            new Sortable(wrapperEl, {
                animation: 150,
                ghostClass: 'bg-primary-subtle',
                onEnd: function (evt) {
                    if (evt.oldIndex !== undefined && evt.newIndex !== undefined && evt.oldIndex !== evt.newIndex) {
                        const movedItem = funnelData.steps.splice(evt.oldIndex, 1)[0];
                        funnelData.steps.splice(evt.newIndex, 0, movedItem);
                        if (typeof activeStepIndex === 'number') {
                            activeStepIndex = evt.newIndex;
                        }
                        renderStepsList();
                        renderPagesBar();
                        renderCanvas();
                    }
                }
            });
        }
    }

    function togglePageMenu(evt, idx) {
        closeAllPageMenus();
        const menu = document.getElementById(`page_menu_${idx}`);
        if (menu) menu.classList.toggle('show');
    }

    function toggleThankYouMenu(evt) {
        closeAllPageMenus();
        const menu = document.getElementById('thank_you_menu');
        if (menu) menu.classList.toggle('show');
    }

    function closeAllPageMenus() {
        document.querySelectorAll('.fb-page-dropdown-menu').forEach(m => m.classList.remove('show'));
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.fb-page-pill-item')) {
            closeAllPageMenus();
        }
    });

    function moveStepLeft(index) {
        if (index > 0) {
            const temp = funnelData.steps[index];
            funnelData.steps[index] = funnelData.steps[index - 1];
            funnelData.steps[index - 1] = temp;
            activeStepIndex = index - 1;
            closeAllPageMenus();
            renderStepsList();
            renderPagesBar();
            renderCanvas();
        }
    }

    function moveStepRight(index) {
        if (index < funnelData.steps.length - 1) {
            const temp = funnelData.steps[index];
            funnelData.steps[index] = funnelData.steps[index + 1];
            funnelData.steps[index + 1] = temp;
            activeStepIndex = index + 1;
            closeAllPageMenus();
            renderStepsList();
            renderPagesBar();
            renderCanvas();
        }
    }

    function editStepTitle(index) {
        const currentTitle = funnelData.steps[index].title || '';
        const newTitle = prompt(currentLang === 'ar' ? 'تعديل اسم الصفحة:' : 'Edit Page Title:', currentTitle);
        if (newTitle !== null && newTitle.trim() !== '') {
            funnelData.steps[index].title = newTitle.trim();
            closeAllPageMenus();
            renderStepsList();
            renderPagesBar();
            renderCanvas();
        }
    }

    function duplicateStep(index) {
        const original = funnelData.steps[index];
        const cloned = JSON.parse(JSON.stringify(original));
        delete cloned.id;
        cloned.title = (cloned.title || 'Step') + ' (Copy)';
        (cloned.elements || []).forEach(el => {
            delete el.id;
            el.question_key = 'q_' + Math.random().toString(36).substr(2, 6);
        });
        funnelData.steps.splice(index + 1, 0, cloned);
        activeStepIndex = index + 1;
        closeAllPageMenus();
        renderStepsList();
        renderPagesBar();
        renderCanvas();
    }

    function toggleDisableStep(index) {
        funnelData.steps[index].is_hidden = !funnelData.steps[index].is_hidden;
        closeAllPageMenus();
        renderStepsList();
        renderPagesBar();
        renderCanvas();
    }

    function selectStep(index) {
        if (index >= 0 && index < funnelData.steps.length) {
            activeStepIndex = index;
            selectedElementIndex = null;
            renderStepsList();
            renderPagesBar();
            renderCanvas();
            inspectStepProperties();
        }
    }

    function selectThankYouPage() {
        activeStepIndex = 'thank_you';
        selectedElementIndex = null;
        closeAllPageMenus();
        renderStepsList();
        renderPagesBar();
        renderCanvas();
        inspectThankYouPage();
    }

    function toggleThankYouPage(enabled) {
        if (!funnelData.design_settings) funnelData.design_settings = {};
        if (!funnelData.design_settings.thank_you_page) {
            funnelData.design_settings.thank_you_page = {};
        }
        funnelData.design_settings.thank_you_page.enabled = !!enabled;
        closeAllPageMenus();
        renderPagesBar();
        if (activeStepIndex === 'thank_you') {
            inspectThankYouPage();
        }
    }

    function addNewStep() {
        const newStepNumber = funnelData.steps.length + 1;
        const newStep = {
            title: `${t('step_default_title')} ${newStepNumber}`,
            subtitle: t('step_default_sub'),
            step_type: 'question',
            sort_order: newStepNumber,
            elements: [
                {
                    element_type: 'single_choice',
                    label: currentLang === 'ar' ? 'السؤال المطروح؟' : 'What is your choice?',
                    is_required: false,
                    question_key: `q_${Math.random().toString(36).substr(2, 6)}`,
                    properties: {
                        subline: currentLang === 'ar' ? 'اختر الإجابة المناسبة للمتابعة' : 'Select an option to proceed',
                        design: {
                            custom_enabled: true,
                            heading_font: "'Tajawal', sans-serif",
                            heading_size: '22px',
                            heading_bold: true,
                            heading_italic: false,
                            text_color: '#0f172a',
                            align: 'start',
                            subline_font: "'Tajawal', sans-serif",
                            subline_size: '14px',
                            subline_bold: false,
                            subline_italic: false,
                            subline_color: '#64748b',
                            options_font: "'Tajawal', sans-serif",
                            options_size: '15px',
                            options_bold: true,
                            options_italic: false,
                            options_text_color: '#1e293b',
                            options_bg_color: '#ffffff',
                            options_border_color: '#cbd5e1',
                            options_border_radius: '12px',
                            options_align: 'start',
                            bg_color: ''
                        },
                        options: [
                            { label: currentLang === 'ar' ? 'الخيار الأول' : 'Option 1', value: 'Option 1', score: 10, image_url: '' },
                            { label: currentLang === 'ar' ? 'الخيار الثاني' : 'Option 2', value: 'Option 2', score: 20, image_url: '' },
                        ]
                    }
                }
            ]
        };

        funnelData.steps.push(newStep);
        activeStepIndex = funnelData.steps.length - 1;
        renderStepsList();
        renderPagesBar();
        renderCanvas();
    }

    function deleteStep(index) {
        if (funnelData.steps.length <= 1) {
            alert(currentLang === 'ar' ? 'يجب أن يحتوي الفانل على خطوة واحدة على الأقل!' : 'Funnel must contain at least one step!');
            return;
        }
        if (confirm(t('delete_step_confirm'))) {
            funnelData.steps.splice(index, 1);
            activeStepIndex = Math.max(0, typeof activeStepIndex === 'number' ? activeStepIndex - 1 : 0);
            renderStepsList();
            renderPagesBar();
            renderCanvas();
        }
    }

    // ── HELPER: GET ELEMENT CUSTOM STYLES ────────────────────────────────────
    function getElementStyles(el) {
        const d = el.properties?.design || {};

        const headingStyle = `
            font-family: ${d.heading_font || 'inherit'} !important;
            font-size: ${d.heading_size || '22px'} !important;
            font-weight: ${d.heading_bold ? '800' : '500'} !important;
            font-style: ${d.heading_italic ? 'italic' : 'normal'} !important;
            color: ${d.text_color || '#0f172a'} !important;
            text-align: ${d.align || 'start'} !important;
        `;

        const sublineStyle = `
            font-family: ${d.subline_font || 'inherit'} !important;
            font-size: ${d.subline_size || '14px'} !important;
            font-weight: ${d.subline_bold ? '700' : '400'} !important;
            font-style: ${d.subline_italic ? 'italic' : 'normal'} !important;
            color: ${d.subline_color || '#64748b'} !important;
            text-align: ${d.align || 'start'} !important;
        `;

        const optCardStyle = `
            font-family: ${d.options_font || 'inherit'} !important;
            font-size: ${d.options_size || '15px'} !important;
            font-weight: ${d.options_bold ? '700' : '500'} !important;
            font-style: ${d.options_italic ? 'italic' : 'normal'} !important;
            color: ${d.options_text_color || '#1e293b'} !important;
            background-color: ${d.options_bg_color || '#ffffff'} !important;
            border: 1.5px solid ${d.options_border_color || '#e2e8f0'} !important;
            border-radius: ${d.options_border_radius || '12px'} !important;
            text-align: ${d.options_align || 'start'} !important;
        `;

        const containerStyle = `
            text-align: ${d.align || 'start'};
            ${d.bg_color ? `background-color: ${d.bg_color} !important;` : ''}
        `;

        return { headingStyle, sublineStyle, optCardStyle, containerStyle };
    }

    // ── 2. CANVAS RENDERING WITH INLINE EDITING ──────────────────────────────
    function renderCanvas() {
        const canvas = document.getElementById('canvas_container');

        if (activeStepIndex === 'thank_you') {
            renderThankYouCanvas();
            return;
        }

        const currentStep = funnelData.steps[activeStepIndex];

        if (!currentStep) {
            canvas.innerHTML = `<div class="text-center text-muted py-5">${t('step_empty_title')}</div>`;
            return;
        }

        let html = `
            <div class="mb-4 pb-2 border-bottom">
                <input type="text" class="form-control form-control-lg fw-bold border-0 p-0 text-dark bg-transparent mb-1" value="${escapeHtml(currentStep.title || '')}" placeholder="${currentLang==='ar'?'اكتب عنوان الخطوة هنا...':'Enter step title here...'}" oninput="updateStepTitle(this.value)">
                <input type="text" class="form-control form-control-sm text-muted border-0 p-0 bg-transparent" value="${escapeHtml(currentStep.subtitle || '')}" placeholder="${currentLang==='ar'?'اكتب وصفاً فرعياً اختيارياً...':'Enter optional step subtitle...'}" oninput="updateStepSubtitle(this.value)">
            </div>
            <div id="canvas_elements_wrapper">
        `;

        if (currentStep.elements && currentStep.elements.length > 0) {
            currentStep.elements.forEach((el, eIdx) => {
                const isSelected = eIdx === selectedElementIndex;
                const reqStar = el.is_required ? '<span class="text-danger ms-1 fw-bold">*</span>' : '';
                const hasScoring = funnelData.design_settings?.scoring_enabled !== false;
                const { headingStyle, sublineStyle, optCardStyle, containerStyle } = getElementStyles(el);
                const subline = el.properties?.subline || '';

                html += `
                    <div class="canvas-element-item ${isSelected ? 'selected' : ''}" data-el-index="${eIdx}" style="${containerStyle}" onclick="selectCanvasElement(event, ${eIdx})">
                        <div class="canvas-element-toolbar">
                            <span style="cursor: pointer;" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design')">${t('custom_design_badge')}</span>
                            <span>${el.element_type} ${el.is_required ? '(' + (currentLang==='ar'?'إجباري':'Required') + ')' : ''}</span>
                            <button type="button" class="btn btn-sm text-white p-0" onclick="event.stopPropagation(); duplicateElement(${eIdx})" title="Duplicate">
                                <iconify-icon icon="solar:copy-bold" width="13"></iconify-icon>
                            </button>
                            <button type="button" class="btn btn-sm text-white p-0" onclick="event.stopPropagation(); deleteElement(${eIdx})" title="Delete">
                                <iconify-icon icon="solar:trash-bin-trash-bold" width="13"></iconify-icon>
                            </button>
                        </div>
                `;

                // 1. Heading
                if (el.element_type === 'heading') {
                    html += `
                        <div class="mb-1" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'عنوان رئيسي جذاب':'Engaging Headline'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                    `;
                
                // 2. Paragraph / Text
                } else if (el.element_type === 'text' || el.element_type === 'paragraph') {
                    html += `
                        <div contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'اكتب نص الفقرة هنا...':'Enter paragraph text here...'))}</div>
                    `;

                // 3. Radio Choice
                } else if (el.element_type === 'radio_choice') {
                    html += `
                        <div class="mb-2" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'اختر إجابة واحدة:':'Select one option:'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable mb-2 w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                    `;
                    html += '<div class="d-flex flex-column gap-2" onclick="event.stopPropagation(); inspectElement(' + eIdx + ', \'design\', \'options\');">';
                    (el.properties?.options || []).forEach((opt, oIdx) => {
                        html += `
                            <div class="p-3 canvas-choice-card d-flex justify-content-between align-items-center" style="${optCardStyle}">
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <iconify-icon icon="solar:record-circle-bold" class="text-primary fs-5"></iconify-icon>
                                    <span contenteditable="true" class="canvas-inline-editable" style="color: inherit;" onclick="event.stopPropagation();" oninput="updateInlineOptionText(${eIdx}, ${oIdx}, this.innerText)">${escapeHtml(opt.label || opt.value || '')}</span>
                                </div>
                                ${hasScoring && (opt.score || 0) > 0 ? `<span class="badge bg-primary-subtle text-primary small">+${opt.score} ${currentLang==='ar'?'نقطة':'pts'}</span>` : ''}
                            </div>
                        `;
                    });
                    html += '</div>';

                // 4. Checkbox Choice
                } else if (el.element_type === 'checkbox_choice') {
                    html += `
                        <div class="mb-2" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'حدد جميع الخيارات:':'Select all that apply:'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable mb-2 w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                    `;
                    html += '<div class="d-flex flex-column gap-2" onclick="event.stopPropagation(); inspectElement(' + eIdx + ', \'design\', \'options\');">';
                    (el.properties?.options || []).forEach((opt, oIdx) => {
                        html += `
                            <div class="p-3 canvas-choice-card d-flex justify-content-between align-items-center" style="${optCardStyle}">
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <iconify-icon icon="solar:check-square-bold" class="text-primary fs-5"></iconify-icon>
                                    <span contenteditable="true" class="canvas-inline-editable" style="color: inherit;" onclick="event.stopPropagation();" oninput="updateInlineOptionText(${eIdx}, ${oIdx}, this.innerText)">${escapeHtml(opt.label || opt.value || '')}</span>
                                </div>
                                ${hasScoring && (opt.score || 0) > 0 ? `<span class="badge bg-primary-subtle text-primary small">+${opt.score} ${currentLang==='ar'?'نقطة':'pts'}</span>` : ''}
                            </div>
                        `;
                    });
                    html += '</div>';

                // 5. Single / Multiple Choice
                } else if (el.element_type === 'single_choice' || el.element_type === 'multiple_choice' || el.element_type === 'yes_no') {
                    html += `
                        <div class="mb-2" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'السؤال المطروح؟':'Select Option:'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable mb-2 w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                    `;
                    html += '<div class="d-flex flex-column gap-2" onclick="event.stopPropagation(); inspectElement(' + eIdx + ', \'design\', \'options\');">';
                    (el.properties?.options || []).forEach((opt, oIdx) => {
                        html += `
                            <div class="p-3 canvas-choice-card d-flex justify-content-between align-items-center" style="${optCardStyle}">
                                <span contenteditable="true" class="canvas-inline-editable flex-grow-1" style="color: inherit;" onclick="event.stopPropagation();" oninput="updateInlineOptionText(${eIdx}, ${oIdx}, this.innerText)">${escapeHtml(opt.label || opt.value || '')}</span>
                                ${hasScoring && (opt.score || 0) > 0 ? `<span class="badge bg-primary-subtle text-primary small">+${opt.score} ${currentLang==='ar'?'نقطة':'pts'}</span>` : ''}
                            </div>
                        `;
                    });
                    html += '</div>';

                // 6. Image Choice
                } else if (['image_choice', 'single_image_choice', 'multiple_image_choice'].includes(el.element_type)) {
                    html += `
                        <div class="mb-2" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'اختر بطاقة صورة:':'Select Image Card:'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable mb-2 w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                    `;
                    html += '<div class="row g-2" onclick="event.stopPropagation(); inspectElement(' + eIdx + ', \'design\', \'options\');">';
                    (el.properties?.options || []).forEach((opt, oIdx) => {
                        html += `
                            <div class="col-6">
                                <div class="p-3 canvas-choice-card text-center" style="${optCardStyle}">
                                    ${opt.image_url ? `<img src="${opt.image_url}" class="rounded-3 mb-2" style="max-height: 80px; width: 100%; object-fit: cover;">` : '<iconify-icon icon="solar:gallery-bold-duotone" width="36" class="text-primary mb-1"></iconify-icon>'}
                                    <div contenteditable="true" class="canvas-inline-editable fw-bold small" style="color: inherit;" onclick="event.stopPropagation();" oninput="updateInlineOptionText(${eIdx}, ${oIdx}, this.innerText)">${escapeHtml(opt.label || '')}</div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';

                // 7. CUSTOMIZABLE CONTACT FORM
                } else if (el.element_type === 'contact_form') {
                    const allFields = el.properties?.fields || [
                        { key: 'full_name', label: currentLang==='ar'?'الاسم الكريم':'Full Name', type: 'text', required: true, visible: true, placeholder: currentLang==='ar'?'أدخل اسمك الكريم':'Enter your full name' },
                        { key: 'phone', label: currentLang==='ar'?'رقم الواتساب':'WhatsApp / Phone', type: 'tel', required: true, visible: true, placeholder: '05XXXXXXXX' },
                        { key: 'email', label: currentLang==='ar'?'البريد الإلكتروني':'Email Address', type: 'email', required: false, visible: true, placeholder: 'example@domain.com' }
                    ];
                    const fields = allFields.filter(f => f.visible !== false);

                    html += `
                        <div class="mb-2" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'نموذج بيانات التواصل (CRM):':'Contact Information (CRM):'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable mb-2 w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                        <div class="p-3 rounded-4 border" style="background-color: #f8fafc;">
                    `;
                    if (fields.length === 0) {
                        html += `<div class="text-center text-muted small py-3">${currentLang==='ar'?'⚠️ جميع حقول النموذج مخفية حالياً.':'⚠️ All form fields are currently hidden.'}</div>`;
                    } else {
                        fields.forEach(f => {
                            const fReq = f.required ? '<span class="text-danger">*</span>' : '';
                            if (f.type === 'tel' || f.type === 'phone') {
                                html += `
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark mb-1">${escapeHtml(f.label)} ${fReq}</label>
                                        <div class="input-group">
                                            <button class="phone-code-btn" type="button">🇸🇦 +966 ▾</button>
                                            <input type="tel" class="form-control" placeholder="${escapeHtml(f.placeholder || '05XXXXXXXX')}" disabled>
                                        </div>
                                    </div>
                                `;
                            } else if (f.type === 'select' || f.type === 'dropdown') {
                                html += `
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark mb-1">${escapeHtml(f.label)} ${fReq}</label>
                                        <select class="form-select" disabled>
                                            <option value="">${escapeHtml(f.placeholder || (currentLang==='ar'?'اختر من القائمة المنسدلة...':'Select option...'))}</option>
                                            ${(f.options || []).map(o => `<option>${escapeHtml(o.label || o.value)}</option>`).join('')}
                                        </select>
                                    </div>
                                `;
                            } else if (f.type === 'textarea') {
                                html += `
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark mb-1">${escapeHtml(f.label)} ${fReq}</label>
                                        <textarea class="form-control" rows="2" placeholder="${escapeHtml(f.placeholder || '')}" disabled></textarea>
                                    </div>
                                `;
                            } else {
                                html += `
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark mb-1">${escapeHtml(f.label)} ${fReq}</label>
                                        <input type="${f.type || 'text'}" class="form-control" placeholder="${escapeHtml(f.placeholder || '')}" disabled>
                                    </div>
                                `;
                            }
                        });
                    }
                    html += `</div>`;

                // 8. Phone
                } else if (el.element_type === 'phone') {
                    html += `
                        <div class="mb-2" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'رقم الواتساب / الجوال:':'WhatsApp / Phone:'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable mb-2 w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                        <div class="input-group input-group-lg">
                            <button class="phone-code-btn" type="button">🇸🇦 +966 ▾</button>
                            <input type="tel" class="form-control" placeholder="05XXXXXXXX" disabled>
                        </div>
                    `;

                // 9. Slider
                } else if (el.element_type === 'slider' || el.element_type === 'currency') {
                    const min = el.properties?.min || 0;
                    const max = el.properties?.max || 50000;
                    const unit = el.properties?.show_currency !== false ? (el.properties?.currency_code || 'SAR') : (el.properties?.custom_unit || '');
                    html += `
                        <div class="mb-2" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'حدد القيمة المطلوبة:':'Select Value:'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable mb-2 w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <h4 class="fw-bold text-primary mb-1">${(min + max)/2} ${unit}</h4>
                            <input type="range" class="form-range" disabled>
                            <div class="d-flex justify-content-between text-muted small"><span>${min} ${unit}</span><span>${max} ${unit}</span></div>
                        </div>
                    `;

                // 10. Countdown Timer
                } else if (el.element_type === 'timer' || el.element_type === 'page_timer') {
                    const hrs = el.properties?.duration_hours || 0;
                    const mins = el.properties?.duration_minutes || 15;
                    const secs = el.properties?.duration_seconds || 0;
                    html += `
                        <div class="p-3 bg-dark text-white rounded-4 border text-center">
                            <span contenteditable="true" class="canvas-inline-editable small text-warning fw-bold d-block mb-3" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">⏰ ${escapeHtml(el.label || (currentLang==='ar'?'احجز الآن! هذا العرض ساري لمدة:':'Special Offer Expires In:'))}</span>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="small text-white-50 fw-bold mb-1" style="font-size: 11px;">${t('hours')}</span>
                                    <div class="timer-box-digit">${String(hrs).padStart(2, '0')}</div>
                                </div>
                                <span class="fs-3 fw-bold text-warning mt-3">:</span>
                                <div class="d-flex flex-column align-items-center">
                                    <span class="small text-white-50 fw-bold mb-1" style="font-size: 11px;">${t('minutes')}</span>
                                    <div class="timer-box-digit">${String(mins).padStart(2, '0')}</div>
                                </div>
                                <span class="fs-3 fw-bold text-warning mt-3">:</span>
                                <div class="d-flex flex-column align-items-center">
                                    <span class="small text-white-50 fw-bold mb-1" style="font-size: 11px;">${t('seconds')}</span>
                                    <div class="timer-box-digit">${String(secs).padStart(2, '0')}</div>
                                </div>
                            </div>
                        </div>
                    `;

                // 11. Generic fallback
                } else {
                    html += `
                        <div class="mb-2" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'design', 'heading');">
                            <span contenteditable="true" class="canvas-inline-editable w-100" style="${headingStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'label', this.innerText)">${escapeHtml(el.label || (currentLang==='ar'?'حقل إدخال:':'Input field:'))}</span>
                            ${reqStar}
                        </div>
                        ${subline ? `<div contenteditable="true" class="canvas-inline-editable mb-2 w-100" style="${sublineStyle}" onclick="event.stopPropagation(); inspectElement(${eIdx}, 'content');" oninput="updateInlineText(${eIdx}, 'subline', this.innerText)">${escapeHtml(subline)}</div>` : ''}
                        <input type="text" class="form-control" placeholder="${currentLang==='ar'?'اكتب هنا...':'Enter text...'}" disabled>
                    `;
                }

                html += '</div>';
            });
        } else {
            html += `
                <div class="alert alert-light border border-dashed text-center py-5 text-muted rounded-4 my-3">
                    <iconify-icon icon="solar:add-circle-bold-duotone" width="44" class="text-primary opacity-50 mb-2"></iconify-icon>
                    <p class="mb-0 fw-bold">${t('step_empty_title')}</p>
                    <small>${t('step_empty_sub')}</small>
                </div>
            `;
        }

        html += '</div>';
        canvas.innerHTML = html;

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

    function selectCanvasElement(e, eIdx) {
        selectedElementIndex = eIdx;
        // Highlight in DOM
        document.querySelectorAll('.canvas-element-item').forEach((item, idx) => {
            if (idx === eIdx) item.classList.add('selected');
            else item.classList.remove('selected');
        });
        inspectElement(eIdx, currentElementActiveSubTab);
    }

    function updateInlineText(eIdx, field, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[eIdx]) return;
        const el = currentStep.elements[eIdx];

        if (field === 'label') {
            el.label = val;
            const headingInput = document.getElementById('inspector_heading_text');
            if (headingInput) headingInput.value = val;
        } else if (field === 'subline') {
            if (!el.properties) el.properties = {};
            el.properties.subline = val;
            const sublineInput = document.getElementById('inspector_subline_text');
            if (sublineInput) sublineInput.value = val;
        }
    }

    function updateInlineOptionText(eIdx, oIdx, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[eIdx]?.properties?.options?.[oIdx]) return;
        currentStep.elements[eIdx].properties.options[oIdx].label = val;
        currentStep.elements[eIdx].properties.options[oIdx].value = val;

        const optInput = document.getElementById(`opt_input_${oIdx}`);
        if (optInput) optInput.value = val;
    }

    // ── 3. DRAG & DROP HANDLERS ──────────────────────────────────────────────
    function setupDragAndDrop() {
        const dropArea = document.getElementById('canvas_drop_area');
        const pills = document.querySelectorAll('.fb-element-pill');

        pills.forEach(pill => {
            pill.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', pill.dataset.type);
                dropArea.classList.add('drop-zone-highlight');
            });

            pill.addEventListener('dragend', () => {
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
            is_required: false,
            question_key: `q_${Math.random().toString(36).substr(2, 6)}`,
            properties: {
                subline: '',
                design: {
                    custom_enabled: true,
                    heading_font: "'Tajawal', sans-serif",
                    heading_size: '22px',
                    heading_bold: true,
                    heading_italic: false,
                    text_color: '#0f172a',
                    align: 'start',
                    subline_font: "'Tajawal', sans-serif",
                    subline_size: '14px',
                    subline_bold: false,
                    subline_italic: false,
                    subline_color: '#64748b',
                    options_font: "'Tajawal', sans-serif",
                    options_size: '15px',
                    options_bold: true,
                    options_italic: false,
                    options_text_color: '#1e293b',
                    options_bg_color: '#ffffff',
                    options_border_color: '#cbd5e1',
                    options_border_radius: '12px',
                    options_align: 'start',
                    bg_color: ''
                }
            }
        };

        if (['single_choice', 'multiple_choice', 'radio_choice', 'checkbox_choice', 'image_choice', 'single_image_choice', 'multiple_image_choice', 'dropdown'].includes(type)) {
            newElement.properties.options = [
                { label: currentLang === 'ar' ? 'الخيار الأول' : 'Option 1', value: 'Option 1', score: 10, image_url: '' },
                { label: currentLang === 'ar' ? 'الخيار الثاني' : 'Option 2', value: 'Option 2', score: 20, image_url: '' },
                { label: currentLang === 'ar' ? 'الخيار الثالث' : 'Option 3', value: 'Option 3', score: 30, image_url: '' },
            ];
        } else if (type === 'yes_no') {
            newElement.properties.options = [
                { label: currentLang === 'ar' ? 'نعم (Yes)' : 'Yes', value: 'Yes', score: 20 },
                { label: currentLang === 'ar' ? 'لا (No)' : 'No', value: 'No', score: 0 },
            ];
        } else if (type === 'country') {
            newElement.properties.active_presets = ['arab'];
            newElement.properties.options = [...COUNTRY_GROUPS.arab.countries];
        } else if (type === 'contact_form') {
            newElement.properties.fields = [
                { key: 'full_name', label: currentLang==='ar'?'الاسم الكريم':'Full Name', type: 'text', required: true, visible: true, placeholder: currentLang==='ar'?'أدخل اسمك بالكامل':'Enter your name' },
                { key: 'phone', label: currentLang==='ar'?'رقم الواتساب':'WhatsApp / Phone', type: 'tel', required: true, visible: true, placeholder: '05XXXXXXXX' },
                { key: 'email', label: currentLang==='ar'?'البريد الإلكتروني':'Email Address', type: 'email', required: false, visible: true, placeholder: 'example@domain.com' },
            ];
        } else if (type === 'slider' || type === 'currency') {
            newElement.properties.min = 0;
            newElement.properties.max = 50000;
            newElement.properties.step = 1000;
            newElement.properties.show_currency = true;
            newElement.properties.currency_code = 'SAR';
            newElement.properties.custom_unit = currentLang === 'ar' ? 'ريال' : 'SAR';
        } else if (type === 'timer' || type === 'page_timer') {
            newElement.properties.duration_hours = 0;
            newElement.properties.duration_minutes = 15;
            newElement.properties.duration_seconds = 0;
        }

        currentStep.elements.push(newElement);
        selectedElementIndex = currentStep.elements.length - 1;
        renderCanvas();
        inspectElement(selectedElementIndex, 'content');
    }

    function getDefaultLabelForType(type) {
        if (currentLang === 'ar') {
            switch(type) {
                case 'single_choice': return 'السؤال المطروح؟';
                case 'radio_choice': return 'اختر إجابة واحدة:';
                case 'multiple_choice': case 'checkbox_choice': return 'اختر جميع الخيارات المناسبة:';
                case 'yes_no': return 'هل ينطبق عليك هذا الشرط؟';
                case 'image_choice': case 'single_image_choice': case 'multiple_image_choice': return 'اختر البطاقة الأنسب لك:';
                case 'dropdown': return 'اختر من القائمة المنسدلة:';
                case 'contact_form': return 'بيانات التواصل لاستلام التقرير وخطة المتابعة:';
                case 'email': return 'البريد الإلكتروني:';
                case 'phone': return 'رقم الواتساب / الجوال:';
                case 'address': return 'العنوان ومقر الإقامة:';
                case 'country': return 'الدولة / الجنسية:';
                case 'website': return 'الموقع الإلكتروني:';
                case 'file_upload': return 'تحميل المستند أو المرفق:';
                case 'slider': case 'currency': return 'حدد الميزانية التقديرية:';
                case 'date_picker': case 'date_time': return 'تاريخ السفر أو الموعد المرغوب:';
                case 'schedule': return 'حجز وجدولة الموعد:';
                case 'timer': case 'page_timer': return 'احجز الآن! هذا العرض متاح لمدة:';
                case 'rating': return 'ما هو تقييمك لمستوى الخدمة؟';
                case 'nps': return 'ما مدى ترشيحك لنا لأصدقائك (0-10)؟';
                case 'heading': return 'Welcome to involve.me';
                case 'text': case 'paragraph': return 'اكتب هنا تفاصيل إضافية لتوضيح السؤال.';
                case 'table': return 'جدول مقارنة الأسعار والباقات';
                case 'testimonials': return 'آراء وتجارب العملاء السابقين';
                case 'faqs': return 'الأسئلة الشائعة وتفاصيل الخدمة';
                case 'coupon_code': return 'كود خصم فوري مخصص لك';
                default: return 'سؤال جديد';
            }
        } else {
            switch(type) {
                case 'single_choice': return 'What is your choice?';
                case 'radio_choice': return 'Select one answer:';
                case 'multiple_choice': case 'checkbox_choice': return 'Select all that apply:';
                case 'yes_no': return 'Does this condition apply to you?';
                case 'image_choice': case 'single_image_choice': case 'multiple_image_choice': return 'Choose your preferred card:';
                case 'dropdown': return 'Select from dropdown:';
                case 'contact_form': return 'Contact information for consultation & report:';
                case 'email': return 'Email Address:';
                case 'phone': return 'WhatsApp / Phone:';
                case 'address': return 'Address & City:';
                case 'country': return 'Country / Nationality:';
                case 'website': return 'Website URL:';
                case 'file_upload': return 'Upload Document / Passport Copy:';
                case 'slider': case 'currency': return 'Select Estimated Budget:';
                case 'date_picker': case 'date_time': return 'Desired Travel / Appointment Date:';
                case 'schedule': return 'Schedule Your Appointment:';
                case 'timer': case 'page_timer': return 'Special Offer Expires In:';
                case 'rating': return 'How would you rate our service?';
                case 'nps': return 'How likely are you to recommend us (0-10)?';
                case 'heading': return 'Welcome to involve.me';
                case 'text': case 'paragraph': return 'Enter additional explanation text.';
                case 'table': return 'Price & Package Comparison Table';
                case 'testimonials': return 'Customer Testimonials & Reviews';
                case 'faqs': return 'Frequently Asked Questions (FAQs)';
                case 'coupon_code': return 'Exclusive Discount Voucher Code';
                default: return 'New Question';
            }
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
        copy.label = copy.label + (currentLang==='ar'?' (نسخة)':' (Copy)');
        currentStep.elements.splice(eIdx + 1, 0, copy);
        selectedElementIndex = eIdx + 1;
        renderCanvas();
        inspectElement(selectedElementIndex);
    }

    // ── 4. INSPECTOR: DEDICATED CONTENT & DESIGN TABS ─────────────────────────
    function inspectElement(eIdx, subTab = 'content') {
        selectedElementIndex = eIdx;
        currentElementActiveSubTab = subTab;

        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[eIdx]) return;
        const el = currentStep.elements[eIdx];

        if (!el.properties) el.properties = {};
        if (!el.properties.design) {
            el.properties.design = {
                custom_enabled: true,
                heading_font: "'Tajawal', sans-serif",
                heading_size: '22px',
                heading_bold: true,
                heading_italic: false,
                text_color: '#0f172a',
                align: 'start',
                subline_font: "'Tajawal', sans-serif",
                subline_size: '14px',
                subline_bold: false,
                subline_italic: false,
                subline_color: '#64748b',
                options_font: "'Tajawal', sans-serif",
                options_size: '15px',
                options_bold: true,
                options_italic: false,
                options_text_color: '#1e293b',
                options_bg_color: '#ffffff',
                options_border_color: '#cbd5e1',
                options_border_radius: '12px',
                options_align: 'start',
                bg_color: ''
            };
        }
        const d = el.properties.design;

        const propsTabBtn = document.querySelector('#inspector_tabs button[data-bs-target="#tab_props"]');
        if (propsTabBtn) {
            bootstrap.Tab.getOrCreateInstance(propsTabBtn).show();
        }

        const panel = document.getElementById('inspector_element_panel');
        let html = `
            <!-- Element Header with Close / Deselect Button -->
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
                <h6 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-uppercase">${el.element_type}</span>
                    <span class="text-truncate" style="max-width: 220px;">${escapeHtml(el.label || '')}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-link text-white-50 p-0 fs-5" onclick="deselectElement()" title="Close">✕</button>
            </div>

            <!-- Involve.me Style Sub-Tabs: Content | Design -->
            <ul class="nav nav-tabs nav-tabs-element mb-3">
                <li class="nav-item">
                    <button class="nav-link ${subTab === 'content' ? 'active' : ''}" type="button" onclick="inspectElement(${eIdx}, 'content')">
                        ${t('tab_content')}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link ${subTab === 'design' ? 'active' : ''}" type="button" onclick="inspectElement(${eIdx}, 'design')">
                        ${t('tab_element_design')}
                    </button>
                </li>
            </ul>
        `;

        // ═════════════════════════════════════════════════════════════════════
        // TAB 1: CONTENT (المحتوى)
        // ═════════════════════════════════════════════════════════════════════
        if (subTab === 'content') {
            html += `
                <!-- Heading Text -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small text-muted mb-0 fw-bold">${t('heading_text_lbl')}</label>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 small" onclick="insertVariable('inspector_heading_text', 'first_name')">
                            ${t('variables_btn')}
                        </button>
                    </div>
                    <textarea class="form-control form-control-dark" id="inspector_heading_text" rows="2" placeholder="${t('heading_text_lbl')}" oninput="updateCurrentElementProp('label', this.value)">${escapeHtml(el.label || '')}</textarea>
                </div>

                <!-- Subline Text -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small text-muted mb-0 fw-bold">${t('subline_text_lbl')}</label>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 small" onclick="insertVariable('inspector_subline_text', 'first_name')">
                            ${t('variables_btn')}
                        </button>
                    </div>
                    <textarea class="form-control form-control-dark" id="inspector_subline_text" rows="2" placeholder="${t('subline_text_lbl')}" oninput="updateSublineText(this.value)">${escapeHtml(el.properties.subline || '')}</textarea>
                </div>
            `;

            // Element Choices / Options in Content Tab
            if (el.properties?.options && el.element_type !== 'contact_form') {
                const hasScoring = funnelData.design_settings?.scoring_enabled !== false;
                html += `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small text-muted mb-0 fw-bold">${currentLang==='ar'?'خيارات السؤال':'Question Options'} (${el.properties.options.length}):</label>
                            <button type="button" class="btn btn-sm btn-primary py-0 px-2 small" onclick="addOptionToCurrentElement()">${t('btn_add_opt')}</button>
                        </div>
                        <div class="d-flex flex-column gap-2" style="max-height: 280px; overflow-y: auto;">
                `;

                el.properties.options.forEach((opt, oIdx) => {
                    html += `
                        <div class="p-2 bg-dark rounded-3 border border-secondary">
                            <div class="d-flex gap-1 mb-1">
                                <input type="text" id="opt_input_${oIdx}" class="form-control form-control-sm form-control-dark" placeholder="${t('opt_text_ph')}" value="${escapeHtml(opt.label || '')}" oninput="updateOptionProp(${oIdx}, 'label', this.value)">
                                <button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="deleteOptionFromCurrentElement(${oIdx})">✕</button>
                            </div>
                            
                            ${['image_choice', 'single_image_choice', 'multiple_image_choice'].includes(el.element_type) ? `
                                <div class="mb-1">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm form-control-dark" placeholder="${currentLang==='ar'?'رابط الصورة (Image URL)':'Image URL'}" value="${escapeHtml(opt.image_url || '')}" oninput="updateOptionProp(${oIdx}, 'image_url', this.value)">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openMediaPickerForOption(${oIdx})">🖼️</button>
                                    </div>
                                </div>
                            ` : ''}

                            ${hasScoring ? `
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="small text-muted">${currentLang==='ar'?'النقاط (Score):':'Score (pts):'}</span>
                                    <input type="number" class="form-control form-control-sm form-control-dark" style="width: 80px;" value="${opt.score || 0}" oninput="updateOptionProp(${oIdx}, 'score', parseInt(this.value)||0)">
                                </div>
                            ` : ''}
                        </div>
                    `;
                });

                html += `</div></div>`;
            }

            // Contact Form Fields
            if (el.element_type === 'contact_form') {
                if (!el.properties.fields) el.properties.fields = [
                    { key: 'full_name', label: currentLang==='ar'?'الاسم الكريم':'Full Name', type: 'text', required: true, visible: true, placeholder: currentLang==='ar'?'أدخل اسمك بالكامل':'Enter your full name' },
                    { key: 'phone', label: currentLang==='ar'?'رقم الواتساب':'WhatsApp / Phone', type: 'tel', required: true, visible: true, placeholder: '05XXXXXXXX' },
                    { key: 'email', label: currentLang==='ar'?'البريد الإلكتروني':'Email Address', type: 'email', required: false, visible: true, placeholder: 'example@domain.com' }
                ];

                html += `
                    <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                        <h6 class="fw-bold small text-white mb-2">${t('presets_title')}</h6>
                        <div class="d-flex flex-column gap-2 mb-3">
                            <button type="button" class="btn btn-outline-info btn-sm text-start" onclick="applyContactFormPreset('visa')">
                                🛂 ${currentLang==='ar'?'فورم استخراج التأشيرات':'Visa Application Form'}
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm text-start" onclick="applyContactFormPreset('flight_hotel')">
                                ✈️ ${currentLang==='ar'?'فورم حجز الطيران والفنادق':'Flight & Hotel Booking Form'}
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm text-start" onclick="applyContactFormPreset('support')">
                                💬 ${currentLang==='ar'?'فورم خدمة العملاء والاستفسارات':'Customer Support Form'}
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm text-start" onclick="applyContactFormPreset('quick')">
                                ⚡ ${currentLang==='ar'?'نموذج تسجيل بيانات سريع':'Quick Lead Form'}
                            </button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold small text-white mb-0">${t('custom_fields_title')} (${el.properties.fields.length}):</h6>
                            <button type="button" class="btn btn-primary btn-sm py-0 px-2 small" onclick="addCustomFieldToContactForm()">${t('btn_add_field')}</button>
                        </div>

                        <div class="d-flex flex-column gap-2" id="contact_fields_list">
                `;

                el.properties.fields.forEach((f, fIdx) => {
                    const isVisible = f.visible !== false;
                    const isSelect = f.type === 'select' || f.type === 'dropdown';
                    if (isSelect && !f.options) {
                        f.options = [{ label: currentLang==='ar'?'الخيار الأول':'Option 1', value: 'Option 1' }, { label: currentLang==='ar'?'الخيار الثاني':'Option 2', value: 'Option 2' }];
                    }

                    html += `
                        <div class="p-2 rounded-3 border ${isVisible ? 'bg-secondary bg-opacity-10 border-secondary' : 'bg-dark border-danger opacity-75'} mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-1 flex-grow-1 me-2">
                                    <span class="badge ${isVisible ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} small" style="font-size: 10px;">
                                        ${isVisible ? t('visible_badge') : t('hidden_badge')}
                                    </span>
                                    <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(f.label)}" placeholder="${t('field_name_ph')}" oninput="updateContactFieldProp(${fIdx}, 'label', this.value)">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-0" onclick="deleteContactField(${fIdx})" title="Delete Field">✕</button>
                            </div>
                            <div class="row g-1 mb-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm form-select-dark" onchange="updateContactFieldProp(${fIdx}, 'type', this.value); inspectElement(${eIdx});">
                                        <option value="text" ${f.type==='text'?'selected':''}>${t('type_text')}</option>
                                        <option value="tel" ${f.type==='tel'?'selected':''}>${t('type_phone')}</option>
                                        <option value="email" ${f.type==='email'?'selected':''}>${t('type_email')}</option>
                                        <option value="select" ${isSelect?'selected':''}>${t('type_dropdown')}</option>
                                        <option value="date" ${f.type==='date'?'selected':''}>${t('type_date')}</option>
                                        <option value="textarea" ${f.type==='textarea'?'selected':''}>${t('type_textarea')}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(f.placeholder||'')}" placeholder="${isSelect?(currentLang==='ar'?'نص الاختيار الافتراضي':'Default placeholder'):(currentLang==='ar'?'النص التوضيحي':'Placeholder')}" oninput="updateContactFieldProp(${fIdx}, 'placeholder', this.value)">
                                </div>
                            </div>

                            ${isSelect ? `
                                <div class="p-2 bg-dark rounded-2 border border-secondary mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-info">${t('dropdown_opts_title')}</span>
                                        <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 small" onclick="addOptionToContactSelectField(${fIdx})">${t('btn_add_opt')}</button>
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        ${(f.options || []).map((opt, oIdx) => `
                                            <div class="d-flex gap-1">
                                                <input type="text" class="form-control form-control-sm form-control-dark" placeholder="${t('opt_text_ph')}" value="${escapeHtml(opt.label || opt.value || '')}" oninput="updateContactSelectOption(${fIdx}, ${oIdx}, this.value)">
                                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-0" onclick="deleteContactSelectOption(${fIdx}, ${oIdx})">✕</button>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            ` : ''}

                            <div class="d-flex justify-content-between align-items-center pt-1 border-top border-secondary">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="cf_vis_${fIdx}" ${isVisible?'checked':''} onchange="updateContactFieldProp(${fIdx}, 'visible', this.checked); inspectElement(${eIdx});">
                                    <label class="form-check-label small ${isVisible?'text-white':'text-danger'}" for="cf_vis_${fIdx}">
                                        ${isVisible ? t('show_in_form') : t('hide_from_form')}
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="cf_req_${fIdx}" ${f.required?'checked':''} onchange="updateContactFieldProp(${fIdx}, 'required', this.checked)">
                                    <label class="form-check-label text-muted small" for="cf_req_${fIdx}">${t('required_badge')}</label>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += `</div></div>`;
            }

            // Visibility & Required Rules
            html += `
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <h6 class="fw-bold small text-white mb-2">${t('visibility_rules')}</h6>
                    
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="el_required_switch" ${el.is_required ? 'checked' : ''} onchange="updateCurrentElementProp('is_required', this.checked)">
                        <label class="form-check-label small text-white" for="el_required_switch">
                            ${t('required_switch_lbl')}
                        </label>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="el_always_hide_switch" ${el.properties.always_hide ? 'checked' : ''} onchange="updateElementDesignProp('always_hide', this.checked)">
                        <label class="form-check-label small text-white" for="el_always_hide_switch">
                            ${t('always_hide_lbl')}
                        </label>
                    </div>

                    <div>
                        <label class="form-label small text-muted mb-1">${t('crm_key_lbl')}</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(el.question_key || '')}" oninput="updateCurrentElementProp('question_key', this.value)">
                    </div>
                </div>
            `;
        }

        // ═════════════════════════════════════════════════════════════════════
        // TAB 2: DESIGN (التصميم) — QUESTION + OPTIONS + COLORS
        // ═════════════════════════════════════════════════════════════════════
        else if (subTab === 'design') {
            html += `
                <!-- 1. QUESTION TITLE FONT & COLOR -->
                <div class="design-section-card">
                    <div class="design-section-title">
                        <iconify-icon icon="solar:text-bold" class="text-primary fs-5"></iconify-icon>
                        <span>${t('heading_font_group')}</span>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">${t('font_lbl')}</label>
                        <select class="form-select form-select-sm form-select-dark" onchange="updateElementDesignProp('heading_font', this.value)">
                            ${AVAILABLE_FONTS.map(f => `<option value="${f.val}" ${d.heading_font === f.val ? 'selected' : ''}>${f.name}</option>`).join('')}
                        </select>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">${t('size_lbl')}</label>
                            <select class="form-select form-select-sm form-select-dark" onchange="updateElementDesignProp('heading_size', this.value)">
                                ${AVAILABLE_SIZES.map(s => `<option value="${s}" ${d.heading_size === s ? 'selected' : ''}>${s}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">${t('text_color_lbl')}</label>
                            <div class="d-flex align-items-center gap-1">
                                <input type="color" class="form-control form-control-color border-0 p-0" style="width: 38px;" value="${d.text_color || '#0f172a'}" oninput="updateElementDesignProp('text_color', this.value); document.getElementById('el_txt_color_val').value=this.value;">
                                <input type="text" class="form-control form-control-sm form-control-dark" id="el_txt_color_val" value="${d.text_color || '#0f172a'}" oninput="updateElementDesignProp('text_color', this.value)">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="el_h_bold" ${d.heading_bold ? 'checked' : ''} onchange="updateElementDesignProp('heading_bold', this.checked)">
                            <label class="form-check-label small text-white" for="el_h_bold">${t('bold_lbl')}</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="el_h_italic" ${d.heading_italic ? 'checked' : ''} onchange="updateElementDesignProp('heading_italic', this.checked)">
                            <label class="form-check-label small text-white" for="el_h_italic">${t('italic_lbl')}</label>
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label small text-muted mb-1">${t('align_lbl')}</label>
                        <select class="form-select form-select-sm form-select-dark" onchange="updateElementDesignProp('align', this.value)">
                            <option value="start" ${d.align === 'start' ? 'selected' : ''}>${currentLang==='ar'?t('align_right'):t('align_left')}</option>
                            <option value="center" ${d.align === 'center' ? 'selected' : ''}>${t('align_center')}</option>
                            <option value="end" ${d.align === 'end' ? 'selected' : ''}>${currentLang==='ar'?t('align_left'):t('align_right')}</option>
                        </select>
                    </div>
                </div>

                <!-- 2. OPTIONS & ANSWERS CARDS DESIGN (FOR ALL CHOICES AT ONCE) -->
                ${['single_choice', 'multiple_choice', 'radio_choice', 'checkbox_choice', 'yes_no', 'image_choice'].includes(el.element_type) ? `
                    <div class="design-section-card border-primary">
                        <div class="design-section-title text-primary">
                            <iconify-icon icon="solar:checklist-minimalistic-bold" class="fs-5"></iconify-icon>
                            <span>${t('options_design_group')}</span>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small text-muted mb-1">${t('font_lbl')}</label>
                            <select class="form-select form-select-sm form-select-dark" onchange="updateElementDesignProp('options_font', this.value)">
                                ${AVAILABLE_FONTS.map(f => `<option value="${f.val}" ${d.options_font === f.val ? 'selected' : ''}>${f.name}</option>`).join('')}
                            </select>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">${t('size_lbl')}</label>
                                <select class="form-select form-select-sm form-select-dark" onchange="updateElementDesignProp('options_size', this.value)">
                                    ${AVAILABLE_SIZES.map(s => `<option value="${s}" ${d.options_size === s ? 'selected' : ''}>${s}</option>`).join('')}
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">${t('opt_radius_lbl')}</label>
                                <select class="form-select form-select-sm form-select-dark" onchange="updateElementDesignProp('options_border_radius', this.value)">
                                    ${AVAILABLE_RADIUS.map(r => `<option value="${r.val}" ${d.options_border_radius === r.val ? 'selected' : ''}>${r.label}</option>`).join('')}
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">${t('opt_text_color_lbl')}</label>
                                <div class="d-flex align-items-center gap-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" style="width: 38px;" value="${d.options_text_color || '#1e293b'}" oninput="updateElementDesignProp('options_text_color', this.value); document.getElementById('el_opt_color_val').value=this.value;">
                                    <input type="text" class="form-control form-control-sm form-control-dark" id="el_opt_color_val" value="${d.options_text_color || '#1e293b'}" oninput="updateElementDesignProp('options_text_color', this.value)">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">${t('opt_bg_color_lbl')}</label>
                                <div class="d-flex align-items-center gap-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" style="width: 38px;" value="${d.options_bg_color || '#ffffff'}" oninput="updateElementDesignProp('options_bg_color', this.value); document.getElementById('el_opt_bg_val').value=this.value;">
                                    <input type="text" class="form-control form-control-sm form-control-dark" id="el_opt_bg_val" value="${d.options_bg_color || '#ffffff'}" oninput="updateElementDesignProp('options_bg_color', this.value)">
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">${t('opt_border_color_lbl')}</label>
                                <div class="d-flex align-items-center gap-1">
                                    <input type="color" class="form-control form-control-color border-0 p-0" style="width: 38px;" value="${d.options_border_color || '#cbd5e1'}" oninput="updateElementDesignProp('options_border_color', this.value); document.getElementById('el_opt_border_val').value=this.value;">
                                    <input type="text" class="form-control form-control-sm form-control-dark" id="el_opt_border_val" value="${d.options_border_color || '#cbd5e1'}" oninput="updateElementDesignProp('options_border_color', this.value)">
                                </div>
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="el_opt_bold" ${d.options_bold ? 'checked' : ''} onchange="updateElementDesignProp('options_bold', this.checked)">
                                    <label class="form-check-label small text-white" for="el_opt_bold">${t('bold_lbl')}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                ` : ''}

                <!-- 3. SUBLINE FONT -->
                <div class="design-section-card">
                    <div class="design-section-title">
                        <iconify-icon icon="solar:notes-bold" class="text-info fs-5"></iconify-icon>
                        <span>${t('subline_font_group')}</span>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">${t('size_lbl')}</label>
                            <select class="form-select form-select-sm form-select-dark" onchange="updateElementDesignProp('subline_size', this.value)">
                                ${AVAILABLE_SIZES.map(s => `<option value="${s}" ${d.subline_size === s ? 'selected' : ''}>${s}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">${t('subline_color_lbl')}</label>
                            <div class="d-flex align-items-center gap-1">
                                <input type="color" class="form-control form-control-color border-0 p-0" style="width: 38px;" value="${d.subline_color || '#64748b'}" oninput="updateElementDesignProp('subline_color', this.value); document.getElementById('el_sub_color_val').value=this.value;">
                                <input type="text" class="form-control form-control-sm form-control-dark" id="el_sub_color_val" value="${d.subline_color || '#64748b'}" oninput="updateElementDesignProp('subline_color', this.value)">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        panel.innerHTML = html;
    }

    function deselectElement() {
        selectedElementIndex = null;
        document.querySelectorAll('.canvas-element-item').forEach(item => item.classList.remove('selected'));
        inspectStepProperties();
    }

    function inspectStepProperties() {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep) return;

        const panel = document.getElementById('inspector_element_panel');
        panel.innerHTML = `
            <h6 class="fw-bold mb-3">⚙️ ${currentLang==='ar'?'خصائص الخطوة':'Step Properties'} (${activeStepIndex + 1})</h6>
            <div class="mb-3">
                <label class="form-label small text-muted">${currentLang==='ar'?'عنوان الخطوة (Title)':'Step Title'}</label>
                <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(currentStep.title || '')}" oninput="updateStepTitle(this.value)">
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted">${currentLang==='ar'?'الوصف الفرعي (Subtitle)':'Step Subtitle'}</label>
                <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(currentStep.subtitle || '')}" oninput="updateStepSubtitle(this.value)">
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted">${currentLang==='ar'?'نوع الخطوة (Step Type)':'Step Type'}</label>
                <select class="form-select form-select-sm form-select-dark" onchange="currentStep.step_type = this.value; renderStepsList();">
                    <option value="welcome" ${currentStep.step_type === 'welcome' ? 'selected' : ''}>Welcome (ترحيب وبداية)</option>
                    <option value="question" ${currentStep.step_type === 'question' ? 'selected' : ''}>Question (سؤال تفاعلي)</option>
                    <option value="lead_form" ${currentStep.step_type === 'lead_form' ? 'selected' : ''}>Lead Form (نموذج تواصل)</option>
                </select>
            </div>
            <div class="mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="deleteStep(${activeStepIndex})">
                    🗑️ ${currentLang==='ar'?'حذف هذه الخطوة':'Delete Step'}
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
            if (!currentStep.elements[selectedElementIndex].properties) currentStep.elements[selectedElementIndex].properties = {};
            currentStep.elements[selectedElementIndex].properties[key] = val;
            renderCanvas();
        }
    }

    function updateSublineText(val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]) {
            if (!currentStep.elements[selectedElementIndex].properties) currentStep.elements[selectedElementIndex].properties = {};
            currentStep.elements[selectedElementIndex].properties.subline = val;
            renderCanvas();
        }
    }

    function updateElementDesignProp(key, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]) {
            if (!currentStep.elements[selectedElementIndex].properties) currentStep.elements[selectedElementIndex].properties = {};
            if (!currentStep.elements[selectedElementIndex].properties.design) currentStep.elements[selectedElementIndex].properties.design = {};
            currentStep.elements[selectedElementIndex].properties.design[key] = val;
            currentStep.elements[selectedElementIndex].properties.design.custom_enabled = true;
            renderCanvas();
        }
    }

    function insertVariable(inputId, varKey) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const varText = `@{{${varKey}}}`;
        const start = input.selectionStart || 0;
        const end = input.selectionEnd || 0;
        const val = input.value;
        input.value = val.substring(0, start) + varText + val.substring(end);
        input.focus();
        input.setSelectionRange(start + varText.length, start + varText.length);
        input.dispatchEvent(new Event('input'));
    }

    function updateSliderProp(key, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]) {
            if (!currentStep.elements[selectedElementIndex].properties) currentStep.elements[selectedElementIndex].properties = {};
            currentStep.elements[selectedElementIndex].properties[key] = val;
            renderCanvas();
        }
    }

    function updateTimerProp(key, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]) {
            if (!currentStep.elements[selectedElementIndex].properties) currentStep.elements[selectedElementIndex].properties = {};
            currentStep.elements[selectedElementIndex].properties[key] = val;
            renderCanvas();
        }
    }

    // ── CONTACT FORM BUILDER HELPERS ──
    function applyContactFormPreset(preset) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[selectedElementIndex]) return;
        const el = currentStep.elements[selectedElementIndex];

        if (preset === 'visa') {
            el.properties.fields = [
                { key: 'full_name', label: currentLang==='ar'?'الاسم الكريم بالكامل':'Full Name (As in Passport)', type: 'text', required: true, visible: true, placeholder: currentLang==='ar'?'أدخل الاسم كما في الجواز':'Enter full name' },
                { key: 'phone', label: currentLang==='ar'?'رقم الواتساب':'WhatsApp / Phone', type: 'tel', required: true, visible: true, placeholder: '05XXXXXXXX' },
                { key: 'email', label: currentLang==='ar'?'البريد الإلكتروني':'Email Address', type: 'email', required: false, visible: true, placeholder: 'name@example.com' },
                { 
                    key: 'destination', 
                    label: currentLang==='ar'?'وجهة السفر / الدولة':'Destination Country', 
                    type: 'select', 
                    required: true, 
                    visible: true,
                    placeholder: currentLang==='ar'?'اختر وجهة السفر...':'Select Destination...',
                    options: [
                        { label: '🇪🇺 Schengen (Europe)', value: 'Schengen' },
                        { label: '🇬🇧 United Kingdom (UK)', value: 'UK' },
                        { label: '🇺🇸 United States (USA)', value: 'USA' },
                        { label: '🇨🇦 Canada', value: 'Canada' },
                        { label: '🇯🇵 Japan', value: 'Japan' },
                        { label: '🇹🇷 Turkey', value: 'Turkey' }
                    ]
                },
                { 
                    key: 'visa_type', 
                    label: currentLang==='ar'?'نوع التأشيرة':'Visa Type', 
                    type: 'select', 
                    required: true, 
                    visible: true,
                    placeholder: currentLang==='ar'?'اختر نوع التأشيرة...':'Select Visa Type...',
                    options: [
                        { label: currentLang==='ar'?'سياحية (Tourist)':'Tourist Visa', value: 'Tourist' },
                        { label: currentLang==='ar'?'تجارة وأعمال (Business)':'Business Visa', value: 'Business' },
                        { label: currentLang==='ar'?'علاجية (Medical)':'Medical Visa', value: 'Medical' },
                        { label: currentLang==='ar'?'دراسية (Student)':'Student Visa', value: 'Student' }
                    ]
                },
                { key: 'travel_date', label: currentLang==='ar'?'تاريخ السفر المتوقع':'Estimated Travel Date', type: 'date', required: false, visible: true, placeholder: '' }
            ];
        } else if (preset === 'flight_hotel') {
            el.properties.fields = [
                { key: 'full_name', label: currentLang==='ar'?'الاسم الكريم':'Client Full Name', type: 'text', required: true, visible: true, placeholder: '' },
                { key: 'phone', label: currentLang==='ar'?'رقم الواتساب':'WhatsApp / Phone', type: 'tel', required: true, visible: true, placeholder: '05XXXXXXXX' },
                { key: 'departure', label: currentLang==='ar'?'مدينة المغادرة':'Departure City', type: 'text', required: true, visible: true, placeholder: 'Riyadh / Jeddah' },
                { key: 'destination', label: currentLang==='ar'?'مدينة الوصول (الوجهة)':'Destination City', type: 'text', required: true, visible: true, placeholder: 'London / Paris' },
                { key: 'passengers', label: currentLang==='ar'?'عدد المسافرين':'Passengers Count', type: 'text', required: true, visible: true, placeholder: '2 Adults, 1 Child' },
                { key: 'departure_date', label: currentLang==='ar'?'تاريخ الذهاب':'Departure Date', type: 'date', required: true, visible: true, placeholder: '' },
                { key: 'return_date', label: currentLang==='ar'?'تاريخ العودة':'Return Date', type: 'date', required: false, visible: true, placeholder: '' }
            ];
        } else if (preset === 'support') {
            el.properties.fields = [
                { key: 'full_name', label: currentLang==='ar'?'الاسم الكريم':'Your Name', type: 'text', required: true, visible: true, placeholder: '' },
                { key: 'phone', label: currentLang==='ar'?'رقم الواتساب / الجوال':'WhatsApp / Phone', type: 'tel', required: true, visible: true, placeholder: '05XXXXXXXX' },
                { key: 'email', label: currentLang==='ar'?'البريد الإلكتروني':'Email Address', type: 'email', required: false, visible: true, placeholder: 'name@example.com' },
                { 
                    key: 'subject', 
                    label: currentLang==='ar'?'موضوع الاستفسار':'Inquiry Subject', 
                    type: 'select', 
                    required: true, 
                    visible: true,
                    placeholder: currentLang==='ar'?'اختر موضوع الاستفسار...':'Select Subject...',
                    options: [
                        { label: currentLang==='ar'?'استفسار عن تأشيرات السفر':'Visa Services Inquiry', value: 'Visa' },
                        { label: currentLang==='ar'?'حجوزات الطيران والفنادق':'Flight & Hotel Bookings', value: 'Bookings' },
                        { label: currentLang==='ar'?'متابعة طلب سابق':'Order Status Tracking', value: 'Status' },
                        { label: currentLang==='ar'?'شكوى أو اقتراح':'Feedback & Suggestions', value: 'Feedback' }
                    ]
                },
                { key: 'message', label: currentLang==='ar'?'تفاصيل الرسالة':'Message Details', type: 'textarea', required: true, visible: true, placeholder: '' }
            ];
        } else if (preset === 'quick') {
            el.properties.fields = [
                { key: 'full_name', label: currentLang==='ar'?'الاسم الكريم':'Full Name', type: 'text', required: true, visible: true, placeholder: '' },
                { key: 'phone', label: currentLang==='ar'?'رقم الواتساب':'WhatsApp / Phone', type: 'tel', required: true, visible: true, placeholder: '05XXXXXXXX' },
                { key: 'city', label: currentLang==='ar'?'المدينة الحالية':'City', type: 'text', required: false, visible: true, placeholder: '' }
            ];
        }

        renderCanvas();
        inspectElement(selectedElementIndex, currentElementActiveSubTab);
    }

    function addCustomFieldToContactForm() {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[selectedElementIndex]) return;
        const el = currentStep.elements[selectedElementIndex];
        if (!el.properties) el.properties = {};
        if (!el.properties.fields) el.properties.fields = [];

        el.properties.fields.push({
            key: `f_${Math.random().toString(36).substr(2, 5)}`,
            label: `${currentLang==='ar'?'حقل جديد':'New Field'} ${el.properties.fields.length + 1}`,
            type: 'text',
            required: false,
            visible: true,
            placeholder: ''
        });

        renderCanvas();
        inspectElement(selectedElementIndex, currentElementActiveSubTab);
    }

    function updateContactFieldProp(fIdx, key, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]?.properties?.fields?.[fIdx]) {
            currentStep.elements[selectedElementIndex].properties.fields[fIdx][key] = val;
            renderCanvas();
        }
    }

    function addOptionToContactSelectField(fIdx) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[selectedElementIndex]?.properties?.fields?.[fIdx]) return;
        const field = currentStep.elements[selectedElementIndex].properties.fields[fIdx];
        if (!field.options) field.options = [];
        field.options.push({ label: `${currentLang==='ar'?'خيار':'Option'} ${field.options.length + 1}`, value: `Option ${field.options.length + 1}` });
        renderCanvas();
        inspectElement(selectedElementIndex, currentElementActiveSubTab);
    }

    function updateContactSelectOption(fIdx, oIdx, val) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]?.properties?.fields?.[fIdx]?.options?.[oIdx]) {
            currentStep.elements[selectedElementIndex].properties.fields[fIdx].options[oIdx].label = val;
            currentStep.elements[selectedElementIndex].properties.fields[fIdx].options[oIdx].value = val;
            renderCanvas();
        }
    }

    function deleteContactSelectOption(fIdx, oIdx) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]?.properties?.fields?.[fIdx]?.options) {
            currentStep.elements[selectedElementIndex].properties.fields[fIdx].options.splice(oIdx, 1);
            renderCanvas();
            inspectElement(selectedElementIndex, currentElementActiveSubTab);
        }
    }

    function deleteContactField(fIdx) {
        const currentStep = funnelData.steps[activeStepIndex];
        if (currentStep && currentStep.elements[selectedElementIndex]?.properties?.fields) {
            currentStep.elements[selectedElementIndex].properties.fields.splice(fIdx, 1);
            renderCanvas();
            inspectElement(selectedElementIndex, currentElementActiveSubTab);
        }
    }

    function addOptionToCurrentElement() {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[selectedElementIndex]) return;
        const el = currentStep.elements[selectedElementIndex];
        if (!el.properties) el.properties = {};
        if (!el.properties.options) el.properties.options = [];

        el.properties.options.push({
            label: `${currentLang==='ar'?'الخيار':'Option'} ${el.properties.options.length + 1}`,
            value: `Option ${el.properties.options.length + 1}`,
            score: 10,
            image_url: ''
        });

        renderCanvas();
        inspectElement(selectedElementIndex, currentElementActiveSubTab);
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
            renderCanvas();
            inspectElement(selectedElementIndex, currentElementActiveSubTab);
        }
    }

    // Media Picker Modal for Options
    function openMediaPickerForOption(oIdx) {
        currentEditingOptionIdx = oIdx;
        const modal = new bootstrap.Modal(document.getElementById('mediaPickerModal'));
        modal.show();
    }

    function selectModalImage(url) {
        if (currentEditingOptionIdx !== null) {
            updateOptionProp(currentEditingOptionIdx, 'image_url', url);
            inspectElement(selectedElementIndex, currentElementActiveSubTab);
        }
        bootstrap.Modal.getInstance(document.getElementById('mediaPickerModal'))?.hide();
    }

    function applyCustomImageUrl() {
        const url = document.getElementById('modal_custom_img_url').value;
        if (url) {
            selectModalImage(url);
        }
    }

    function toggleScoringMode(enabled) {
        if (!funnelData.design_settings) funnelData.design_settings = {};
        funnelData.design_settings.scoring_enabled = enabled;
        renderCanvas();
        if (selectedElementIndex !== null) inspectElement(selectedElementIndex, currentElementActiveSubTab);
    }

    // ── 5. RESULTS MANAGEMENT ────────────────────────────────────────────────
    function renderResultsList() {
        const wrapper = document.getElementById('results_list_wrapper');
        let html = '';

        (funnelData.results || []).forEach((res, rIdx) => {
            html += `
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success small">${currentLang==='ar'?'نتيجة':'Outcome'} #${rIdx + 1}</span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="deleteResult(${rIdx})">✕</button>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">${currentLang==='ar'?'عنوان النتيجة':'Outcome Title'}</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(res.title || '')}" oninput="funnelData.results[${rIdx}].title = this.value">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">${currentLang==='ar'?'الوصف التفصيلي':'Description'}</label>
                        <textarea class="form-control form-control-sm form-control-dark" rows="2" oninput="funnelData.results[${rIdx}].description = this.value">${escapeHtml(res.description || '')}</textarea>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small text-muted">${currentLang==='ar'?'أدنى نقاط (Min)':'Min Score'}</label>
                            <input type="number" class="form-control form-control-sm form-control-dark" value="${res.min_score ?? 0}" oninput="funnelData.results[${rIdx}].min_score = parseInt(this.value)||0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">${currentLang==='ar'?'أقصى نقاط (Max)':'Max Score'}</label>
                            <input type="number" class="form-control form-control-sm form-control-dark" value="${res.max_score ?? 100}" oninput="funnelData.results[${rIdx}].max_score = parseInt(this.value)||100">
                        </div>
                    </div>
                    <div>
                        <label class="form-label small text-muted">${currentLang==='ar'?'نص زر الواتساب / التحويل':'CTA Button Label'}</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(res.cta_label || (currentLang==='ar'?'تواصل معنا الآن':'Contact Us Now'))}" oninput="funnelData.results[${rIdx}].cta_label = this.value">
                    </div>
                </div>
            `;
        });

        wrapper.innerHTML = html;
    }

    function addNewResult() {
        if (!funnelData.results) funnelData.results = [];
        funnelData.results.push({
            title: currentLang === 'ar' ? 'نتيجة جديدة 🎉' : 'New Outcome 🎉',
            description: currentLang === 'ar' ? 'وصف نتيجة التقييم وخطة المتابعة للعميل.' : 'Outcome description and customer follow-up plan.',
            min_score: 50,
            max_score: 100,
            cta_type: 'whatsapp',
            cta_label: currentLang === 'ar' ? 'تحدث مع المستشار عبر الواتساب' : 'Chat with Consultant on WhatsApp',
            cta_whatsapp_number: '966500000000',
            sort_order: funnelData.results.length + 1
        });
        renderResultsList();
    }

    function deleteResult(rIdx) {
        if (confirm(currentLang === 'ar' ? 'حذف هذه النتيجة؟' : 'Delete this outcome?')) {
            funnelData.results.splice(rIdx, 1);
            renderResultsList();
        }
    }

    function updateDesignSettings() {
        const color = document.getElementById('design_primary_color').value;
        const font = document.getElementById('design_font_family').value;
        const scoring = document.getElementById('scoring_enabled_checkbox').checked;

        funnelData.design_settings = {
            primary_color: color,
            font_family: font,
            scoring_enabled: scoring,
        };
        document.documentElement.style.setProperty('--fb-primary', color);
    }

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

    function openLivePreview() {
        showToast(currentLang === 'ar' ? 'جاري حفظ التعديلات وفتح المعاينة الحية... ⏳' : 'Saving and launching live preview... ⏳');
        saveFunnel(true);
    }

    function saveFunnel(openAfterSave = false) {
        const saveBtn = document.getElementById('btn_save_funnel');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = `<span>${t('saving')}</span>`;
        }

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
                meta_pixel_id: document.getElementById('meta_pixel_input')?.value || '',
                ga4_id: document.getElementById('ga4_input')?.value || '',
                gtm_id: document.getElementById('gtm_input')?.value || '',
                linkedin_tag_id: document.getElementById('linkedin_input')?.value || '',
                tiktok_pixel_id: document.getElementById('tiktok_input')?.value || '',
                snap_pixel_id: document.getElementById('snap_input')?.value || '',
                custom_head_script: document.getElementById('custom_head_script_input')?.value || '',
                custom_body_script: document.getElementById('custom_body_script_input')?.value || '',
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
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = `<span>${t('save_changes')}</span>`;
            }
            if (data.success) {
                showToast(t('saved_toast'));
                if (openAfterSave) {
                    window.open(`{{ route('funnels.public.show', $funnel->slug) }}?preview=1&t=` + Date.now(), '_blank');
                }
            } else {
                alert('Error: ' + (data.message || 'Error'));
            }
        })
        .catch(err => {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = `<span>${t('save_changes')}</span>`;
            }
            alert('Error: ' + err.message);
        });
    }

    function showToast(msg) {
        const toast = document.getElementById('fb_toast');
        toast.innerText = msg;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 2500);
    }

    // ── 3. THANK YOU PAGE RENDERER & INSPECTOR ───────────────────────────────
    function renderThankYouCanvas() {
        const canvas = document.getElementById('canvas_container');
        const ty = funnelData.design_settings?.thank_you_page || {};

        const title = ty.title || (currentLang === 'ar' ? 'شكراً لإكمال البيانات!' : 'Thanks for your submission!');
        const subtitle = ty.subtitle || (currentLang === 'ar' ? 'تم استلام معلوماتك بنجاح، وسنواصل التواصل معك في أقرب وقت.' : 'Made with involve.me, the quickest way to create interactive lead funnels.');
        const btnText = ty.button_text || (currentLang === 'ar' ? 'العودة للموقع' : 'Create your own');

        canvas.innerHTML = `
            <div class="text-center py-5" onclick="inspectThankYouPage()">
                <div class="mb-4 d-flex justify-content-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; background: #dcfce7; color: #22c55e;">
                        <iconify-icon icon="solar:check-circle-bold" width="56"></iconify-icon>
                    </div>
                </div>

                <h2 class="fw-bold mb-3 text-dark canvas-inline-editable" contenteditable="true" onblur="updateThankYouTitle(this.innerText)">
                    ${escapeHtml(title)}
                </h2>

                <p class="text-muted fs-6 mb-4 px-4 canvas-inline-editable" contenteditable="true" style="max-width: 520px; margin: 0 auto;" onblur="updateThankYouSubtitle(this.innerText)">
                    ${escapeHtml(subtitle)}
                </p>

                <div class="mt-4">
                    <button type="button" class="btn btn-primary btn-lg rounded-3 fw-bold px-5 py-3 shadow-sm" onclick="event.stopPropagation(); inspectThankYouPage()">
                        ${escapeHtml(btnText)}
                    </button>
                </div>
            </div>
        `;
    }

    function inspectThankYouPage() {
        const panel = document.getElementById('inspector_element_panel');
        if (!panel) return;
        const ty = funnelData.design_settings?.thank_you_page || {};

        const tabBtn = document.getElementById('tab_btn_props');
        if (tabBtn) {
            const bsTab = new bootstrap.Tab(tabBtn);
            bsTab.show();
        }

        panel.innerHTML = `
            <div class="p-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-white">${t('thank_you_settings_title')}</h6>
                    <span class="badge ${ty.enabled !== false ? 'bg-success' : 'bg-danger'}">${ty.enabled !== false ? t('visible_badge') : t('hidden_badge')}</span>
                </div>

                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="ty_enabled_toggle" onchange="toggleThankYouPage(this.checked)" ${ty.enabled !== false ? 'checked' : ''}>
                        <label class="form-check-label fw-bold small text-white" for="ty_enabled_toggle">
                            ${t('show_thank_you_lbl')}
                        </label>
                    </div>
                    <small class="text-muted d-block" style="font-size: 11px;">
                        ${t('show_thank_you_sub')}
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">${currentLang==='ar'?'عنوان صفحة الشكر (Heading Title)':'Thank You Heading'}</label>
                    <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(ty.title || '')}" oninput="updateThankYouTitle(this.value)">
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">${currentLang==='ar'?'نص الوصف (Subtitle Description)':'Subtitle Description'}</label>
                    <textarea class="form-control form-control-sm form-control-dark" rows="3" oninput="updateThankYouSubtitle(this.value)">${escapeHtml(ty.subtitle || '')}</textarea>
                </div>

                <hr class="border-secondary my-3">

                <h6 class="fw-bold small text-white mb-2">${currentLang==='ar'?'إعدادات زر الإجراء (CTA Button Settings)':'CTA Button Settings'}</h6>

                <div class="mb-3">
                    <label class="form-label small text-muted">${currentLang==='ar'?'نص الزر (Button Label)':'Button Text'}</label>
                    <input type="text" class="form-control form-control-sm form-control-dark" value="${escapeHtml(ty.button_text || '')}" oninput="updateThankYouButtonText(this.value)">
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">${currentLang==='ar'?'نوع الإجراء (Action Type)':'Button Action'}</label>
                    <select class="form-select form-select-sm form-select-dark" onchange="updateThankYouButtonAction(this.value)">
                        <option value="restart" ${ty.button_action === 'restart' ? 'selected' : ''}>${currentLang==='ar'?'إعادة تشغيل الفانل (Restart Funnel)':'Restart Funnel'}</option>
                        <option value="url" ${ty.button_action === 'url' ? 'selected' : ''}>${currentLang==='ar'?'التحويل لرابط آخر (Redirect URL)':'Redirect to URL'}</option>
                        <option value="whatsapp" ${ty.button_action === 'whatsapp' ? 'selected' : ''}>${currentLang==='ar'?'فتح الواتساب (Open WhatsApp)':'Open WhatsApp'}</option>
                    </select>
                </div>

                ${ty.button_action === 'url' ? `
                    <div class="mb-3">
                        <label class="form-label small text-muted">${currentLang==='ar'?'رابط التحويل (Redirect URL)':'Target URL'}</label>
                        <input type="url" class="form-control form-control-sm form-control-dark" placeholder="https://example.com" value="${escapeHtml(ty.button_url || '')}" oninput="updateThankYouButtonUrl(this.value)">
                    </div>
                ` : ''}

                ${ty.button_action === 'whatsapp' ? `
                    <div class="mb-3">
                        <label class="form-label small text-muted">${currentLang==='ar'?'رقم الواتساب (WhatsApp Number)':'WhatsApp Number'}</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" placeholder="966500000000" value="${escapeHtml(ty.button_whatsapp || '')}" oninput="updateThankYouButtonWhatsapp(this.value)">
                    </div>
                ` : ''}

                <hr class="border-secondary my-3">

                <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-bold" onclick="toggleThankYouPage(false); selectStep(0);">
                    🗑️ ${t('remove_thank_you_page')}
                </button>
            </div>
        `;
    }

    function updateThankYouTitle(val) {
        if (!funnelData.design_settings.thank_you_page) funnelData.design_settings.thank_you_page = {};
        funnelData.design_settings.thank_you_page.title = val;
        if (activeStepIndex === 'thank_you') renderThankYouCanvas();
    }

    function updateThankYouSubtitle(val) {
        if (!funnelData.design_settings.thank_you_page) funnelData.design_settings.thank_you_page = {};
        funnelData.design_settings.thank_you_page.subtitle = val;
        if (activeStepIndex === 'thank_you') renderThankYouCanvas();
    }

    function updateThankYouButtonText(val) {
        if (!funnelData.design_settings.thank_you_page) funnelData.design_settings.thank_you_page = {};
        funnelData.design_settings.thank_you_page.button_text = val;
        if (activeStepIndex === 'thank_you') renderThankYouCanvas();
    }

    function updateThankYouButtonAction(val) {
        if (!funnelData.design_settings.thank_you_page) funnelData.design_settings.thank_you_page = {};
        funnelData.design_settings.thank_you_page.button_action = val;
        inspectThankYouPage();
        if (activeStepIndex === 'thank_you') renderThankYouCanvas();
    }

    function updateThankYouButtonUrl(val) {
        if (!funnelData.design_settings.thank_you_page) funnelData.design_settings.thank_you_page = {};
        funnelData.design_settings.thank_you_page.button_url = val;
    }

    function updateThankYouButtonWhatsapp(val) {
        if (!funnelData.design_settings.thank_you_page) funnelData.design_settings.thank_you_page = {};
        funnelData.design_settings.thank_you_page.button_whatsapp = val;
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
