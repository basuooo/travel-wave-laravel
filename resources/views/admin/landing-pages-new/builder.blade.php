<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎨 محرر صفحات الهبوط المتقدم (GrapesJS Visual Builder) | {{ $page->internal_name }}</title>

    <!-- Bootstrap 5 RTL & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- GrapesJS Core CSS & Preset -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <link rel="stylesheet" href="https://unpkg.com/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --gjs-topbar-h: 62px;
            --gjs-sidebar-w: 320px;
            --gjs-bg-dark: #0f172a;
            --gjs-panel-bg: #1e293b;
            --gjs-border-color: #334155;
            --gjs-accent: #3b82f6;
            --gjs-success: #10b981;
        }

        * {
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: var(--gjs-bg-dark);
            color: #f8fafc;
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* TOPBAR CONTAINER */
        .gjs-top-bar {
            height: var(--gjs-topbar-h);
            background: #020617;
            border-bottom: 1px solid var(--gjs-border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: 1000;
            position: relative;
        }

        /* GREEN & BLUE ACTION BUTTONS (Matching Reference Image) */
        .btn-top-green {
            background-color: #10b981;
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 0.45rem 1.1rem;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        }
        .btn-top-green:hover {
            background-color: #059669;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-top-blue {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 0.45rem 1.25rem;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }
        .btn-top-blue:hover {
            background-color: #1d4ed8;
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* LOGO & BRANDING */
        .gjs-brand-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #10b981, #059669);
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: white;
            font-size: 1.1rem;
        }

        /* VIEWPORT SWITCHER CONTROLS */
        .gjs-viewport-btns {
            background: #0f172a;
            border: 1px solid var(--gjs-border-color);
            border-radius: 8px;
            padding: 2px;
            display: flex;
            gap: 2px;
        }
        .gjs-viewport-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .gjs-viewport-btn:hover {
            color: #ffffff;
            background: #1e293b;
        }
        .gjs-viewport-btn.active {
            background: var(--gjs-accent);
            color: #ffffff;
            font-weight: 600;
        }

        /* ACTION TOOL ICONS */
        .gjs-tool-btn {
            background: transparent;
            border: 1px solid var(--gjs-border-color);
            color: #cbd5e1;
            width: 34px;
            height: 34px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        .gjs-tool-btn:hover {
            background: #1e293b;
            color: #ffffff;
            border-color: var(--gjs-accent);
        }
        .gjs-tool-btn.active {
            background: var(--gjs-accent);
            color: white;
            border-color: var(--gjs-accent);
        }

        /* WORKSPACE LAYOUT */
        .gjs-workspace {
            display: flex;
            height: calc(100vh - var(--gjs-topbar-h));
            position: relative;
        }

        /* LEFT SIDEBAR PANELS */
        .gjs-sidebar-panel {
            width: var(--gjs-sidebar-w);
            background-color: var(--gjs-panel-bg);
            border-left: 1px solid var(--gjs-border-color);
            display: flex;
            flex-direction: column;
            height: 100%;
            z-index: 10;
        }

        /* SIDEBAR TAB BUTTONS */
        .gjs-panel-tabs {
            display: flex;
            background: #0f172a;
            border-bottom: 1px solid var(--gjs-border-color);
        }
        .gjs-panel-tab {
            flex: 1;
            padding: 0.75rem 0.25rem;
            text-align: center;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
        .gjs-panel-tab i {
            font-size: 1.1rem;
        }
        .gjs-panel-tab:hover {
            color: #f1f5f9;
        }
        .gjs-panel-tab.active {
            color: var(--gjs-accent);
            border-bottom-color: var(--gjs-accent);
            background: #1e293b;
        }

        .gjs-panel-content {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .gjs-tab-pane {
            display: none;
        }
        .gjs-tab-pane.active {
            display: block;
        }

        /* CANVAS CONTAINER */
        .gjs-editor-canvas {
            flex: 1;
            position: relative;
            background-color: #020617;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        #gjs {
            height: 100%;
            width: 100%;
        }

        /* FLOATING NAVIGATOR PANEL */
        .gjs-floating-navigator {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 280px;
            max-height: 400px;
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid var(--gjs-border-color);
            border-radius: 8px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            z-index: 500;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: opacity 0.3s ease;
        }
        .gjs-floating-header {
            padding: 0.6rem 1rem;
            background: #020617;
            border-bottom: 1px solid var(--gjs-border-color);
            font-size: 0.85rem;
            font-weight: 700;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: move;
        }
        .gjs-floating-body {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem;
        }

        /* BOTTOM BREADCRUMBS BAR */
        .gjs-breadcrumbs-bar {
            height: 36px;
            background: #020617;
            border-top: 1px solid var(--gjs-border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            font-size: 0.8rem;
            color: #94a3b8;
            font-family: monospace;
            z-index: 100;
        }
        .gjs-breadcrumbs-trail {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .gjs-breadcrumb-item {
            color: var(--gjs-accent);
            cursor: pointer;
        }
        .gjs-breadcrumb-item:hover {
            text-decoration: underline;
        }

        /* GRAPESJS OVERRIDES FOR DARK THEME & RTL */
        .gjs-one-bg {
            background-color: var(--gjs-panel-bg);
        }
        .gjs-two-color {
            color: #cbd5e1;
        }
        .gjs-three-bg {
            background-color: #0f172a;
        }
        .gjs-four-color, .gjs-four-color-h:hover {
            color: var(--gjs-accent);
        }
        .gjs-block {
            background: #0f172a;
            border: 1px solid var(--gjs-border-color);
            border-radius: 6px;
            color: #f8fafc;
            box-shadow: none;
            transition: all 0.2s ease;
        }
        .gjs-block:hover {
            border-color: var(--gjs-accent);
            color: var(--gjs-accent);
            transform: translateY(-2px);
        }
        .gjs-block-label {
            font-size: 0.8rem;
            color: #e2e8f0;
        }
        .gjs-sm-sector .gjs-sm-title {
            background: #0f172a;
            border-bottom: 1px solid var(--gjs-border-color);
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        .gjs-sm-field input, .gjs-sm-field select, .gjs-clm-tags input {
            background: #0f172a !important;
            border: 1px solid var(--gjs-border-color) !important;
            color: #f8fafc !important;
            border-radius: 4px;
        }
        .gjs-trt-trait {
            padding: 8px 0;
            border-bottom: 1px solid var(--gjs-border-color);
        }
        .gjs-trt-trait .gjs-label {
            color: #94a3b8;
            font-size: 0.8rem;
        }
        .gjs-layer {
            background: #0f172a;
            border-bottom: 1px solid var(--gjs-border-color);
            color: #e2e8f0;
        }
        .gjs-layer.gjs-selected {
            background: #1e3a8a;
            color: #ffffff;
        }

        /* CUSTOM MODAL OVERLAYS */
        .modal-content {
            background-color: #0f172a;
            border: 1px solid var(--gjs-border-color);
            color: #f8fafc;
        }
        .form-control, .form-select {
            background-color: #1e293b;
            border-color: var(--gjs-border-color);
            color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            background-color: #1e293b;
            border-color: var(--gjs-accent);
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
    </style>
</head>
<body>

    <!-- TOPBAR -->
    <header class="gjs-top-bar">
        <!-- RIGHT TOP ACTIONS (RTL) -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.landing-pages-new.index') }}" class="btn-top-green">
                <i class="bi bi-arrow-right-short fs-5"></i> العودة للقائمة
            </a>

            <button type="button" class="btn-top-green" data-bs-toggle="modal" data-bs-target="#funnelSettingsModal">
                <i class="bi bi-gear-fill"></i> إعدادات الـ Funnel
            </button>
        </div>

        <!-- CENTER VIEWPORT SWITCHER -->
        <div class="gjs-viewport-btns">
            <button class="gjs-viewport-btn active" id="viewDesktop" onclick="setDevice('Desktop')" title="عرض الشاشة">
                <i class="bi bi-display"></i> Desktop
            </button>
            <button class="gjs-viewport-btn" id="viewTablet" onclick="setDevice('Tablet')" title="عرض الأيباد والتابلت">
                <i class="bi bi-tablet"></i> Tablet
            </button>
            <button class="gjs-viewport-btn" id="viewMobile" onclick="setDevice('Mobile')" title="عرض الجوال">
                <i class="bi bi-phone"></i> Mobile
            </button>
        </div>

        <!-- LEFT TOP ACTIONS & SAVE BUTTON -->
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="gjs-tool-btn" id="btnOutlines" onclick="toggleOutlines()" title="إظهار/إخفاء حدود العناصر">
                <i class="bi bi-border-outer"></i>
            </button>
            <button type="button" class="gjs-tool-btn" onclick="editor.UndoManager.undo()" title="تراجع Undo">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
            <button type="button" class="gjs-tool-btn" onclick="editor.UndoManager.redo()" title="إعادة Redo">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button type="button" class="gjs-tool-btn" onclick="togglePreview()" title="معاينة الصفحة Preview">
                <i class="bi bi-eye"></i>
            </button>
            <button type="button" class="gjs-tool-btn" onclick="toggleFullscreen()" title="ملء الشاشة Fullscreen">
                <i class="bi bi-arrows-fullscreen"></i>
            </button>
            <button type="button" class="gjs-tool-btn" onclick="clearCanvas()" title="مسح اللوحة Clear">
                <i class="bi bi-trash text-danger"></i>
            </button>
            <button type="button" class="gjs-tool-btn" data-bs-toggle="modal" data-bs-target="#codeEditorModal" title="محرر الكود">
                <i class="bi bi-code-slash text-info"></i>
            </button>

            <span id="saveStatusBadge" class="badge bg-secondary">جاهز</span>

            <button type="button" class="btn-top-blue" onclick="savePageData()">
                <i class="bi bi-floppy-fill"></i> Save Page
            </button>
        </div>
    </header>

    <!-- WORKSPACE -->
    <div class="gjs-workspace">

        <!-- LEFT SIDEBAR: CONTENT, STYLE, ADVANCED, NAVIGATOR -->
        <aside class="gjs-sidebar-panel">
            <div class="gjs-panel-tabs">
                <button class="gjs-panel-tab active" onclick="switchTab('contentTab', this)">
                    <i class="bi bi-box-seam"></i> Content
                </button>
                <button class="gjs-panel-tab" onclick="switchTab('styleTab', this)">
                    <i class="bi bi-palette"></i> Style
                </button>
                <button class="gjs-panel-tab" onclick="switchTab('advancedTab', this)">
                    <i class="bi bi-sliders"></i> Advanced
                </button>
                <button class="gjs-panel-tab" onclick="switchTab('navigatorTab', this)">
                    <i class="bi bi-diagram-3"></i> Navigator
                </button>
            </div>

            <div class="gjs-panel-content">
                <!-- TAB 1: CONTENT (BLOCK MANAGER) -->
                <div id="contentTab" class="gjs-tab-pane active">
                    <div class="text-white-50 small mb-2 fw-bold uppercase">🧩 عناصر وأقسام جاهزة</div>
                    <div id="blocks-container"></div>
                </div>

                <!-- TAB 2: STYLE (STYLE MANAGER) -->
                <div id="styleTab" class="gjs-tab-pane">
                    <div class="text-white-50 small mb-2 fw-bold uppercase">🎨 خصائص التنسيق والمظهر</div>
                    <div id="style-container"></div>
                </div>

                <!-- TAB 3: ADVANCED (TRAIT MANAGER) -->
                <div id="advancedTab" class="gjs-tab-pane">
                    <div class="text-white-50 small mb-2 fw-bold uppercase">⚙️ الخصائص والمعرفات (Id & Class & Attributes)</div>
                    <div id="traits-container"></div>
                </div>

                <!-- TAB 4: NAVIGATOR (LAYER MANAGER) -->
                <div id="navigatorTab" class="gjs-tab-pane">
                    <div class="text-white-50 small mb-2 fw-bold uppercase">🌲 شجرة الطبقات والمكونات</div>
                    <div id="layers-container"></div>
                </div>
            </div>
        </aside>

        <!-- CENTER EDITOR CANVAS -->
        <main class="gjs-editor-canvas">
            <div id="gjs"></div>

            <!-- FLOATING NAVIGATOR PANEL (TOGGLEABLE LIKE SCREENSHOT) -->
            <div class="gjs-floating-navigator" id="floatingNavigator">
                <div class="gjs-floating-header">
                    <span>:: Navigator</span>
                    <button type="button" class="btn-close btn-close-white btn-sm" onclick="document.getElementById('floatingNavigator').style.display='none'"></button>
                </div>
                <div class="gjs-floating-body" id="floating-layers-container"></div>
            </div>

            <!-- BOTTOM BREADCRUMBS & CODE EDITOR BAR -->
            <div class="gjs-breadcrumbs-bar">
                <div class="d-flex align-items-center gap-3">
                    <div class="gjs-breadcrumbs-trail" id="breadcrumbsTrail">
                        <span class="text-white-50">body</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-warning border-0 text-warning px-2 fw-bold" onclick="forceUnlockSelected()" title="إلغاء قفل العنصر المحدد وجعله قابل للتعديل بالكامل">
                        <i class="bi bi-unlock-fill"></i> 🔓 إلغاء قفل العنصر (Unlock Element)
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-info border-0 text-white-50 px-2" data-bs-toggle="modal" data-bs-target="#codeEditorModal">
                        <i class="bi bi-code-slash"></i> &lt;/&gt; Code editor
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- FUNNEL & PAGE SETTINGS MODAL -->
    <div class="modal fade" id="funnelSettingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-success"><i class="bi bi-gear-fill"></i> إعدادات الـ Funnel والصفحة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="funnelSettingsForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 fw-bold">الاسم الداخلي (Internal Name)</label>
                                <input type="text" class="form-control" name="internal_name" value="{{ $page->internal_name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 fw-bold">رابط الصفحة (Slug)</label>
                                <div class="input-group" dir="ltr">
                                    <span class="input-group-text bg-dark border-secondary text-white-50">/lp-new/</span>
                                    <input type="text" class="form-control" name="slug" value="{{ $page->slug }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 fw-bold">العنوان بالعربية (Title AR)</label>
                                <input type="text" class="form-control" name="title_ar" value="{{ $page->title_ar }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 fw-bold">العنوان بالإنجليزية (Title EN)</label>
                                <input type="text" class="form-control" name="title_en" value="{{ $page->title_en }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 fw-bold">البراند (Brand)</label>
                                <select class="form-select" name="brand_id">
                                    <option value="">-- بدون براند --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $page->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 fw-bold">نموذج تجميع البيانات (Assigned Lead Form)</label>
                                <select class="form-select" name="assigned_lead_form_id">
                                    <option value="">-- بدون نموذج --</option>
                                    @foreach($forms as $form)
                                        <option value="{{ $form->id }}" {{ $page->assigned_lead_form_id == $form->id ? 'selected' : '' }}>{{ $form->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 fw-bold">حالة الصفحة (Status)</label>
                                <select class="form-select" name="status">
                                    <option value="draft" {{ $page->status === 'draft' ? 'selected' : '' }}>مسودة (Draft)</option>
                                    <option value="published" {{ $page->status === 'published' ? 'selected' : '' }}>منشورة (Published)</option>
                                    <option value="archived" {{ $page->status === 'archived' ? 'selected' : '' }}>أرشيف (Archived)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-white-50 fw-bold">تفعيل الصفحة</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ $page->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="isActiveSwitch">مفعلة وتستقبل الزيارات</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-success fw-bold px-4" onclick="saveFunnelSettings()">تحديث الإعدادات</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CODE EDITOR MODAL -->
    <div class="modal fade" id="codeEditorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-info"><i class="bi bi-code-slash"></i> محرر الأكواد المباشر (&lt;/&gt; Code Editor)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white fw-bold">Custom CSS (تنسيقات CSS المخصصة)</label>
                            <textarea id="modalCustomCss" class="form-control bg-dark text-success font-monospace" rows="12" dir="ltr">{{ $page->custom_css }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white fw-bold">Custom JS (أكواد JavaScript المخصصة)</label>
                            <textarea id="modalCustomJs" class="form-control bg-dark text-warning font-monospace" rows="12" dir="ltr">{{ $page->custom_js }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="applyCustomCodeModal()">تطبيق وتأكيد الكود</button>
                </div>
            </div>
        </div>
    </div>

    <!-- GrapesJS Scripts & Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/grapesjs"></script>
    <script src="https://unpkg.com/grapesjs-preset-webpage"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const updateUrl = "{{ route('admin.landing-pages-new.builder.update', $page) }}";
        const pageUpdateUrl = "{{ route('admin.landing-pages-new.update', $page) }}";

        @php
            $structure = is_string($page->structure) ? json_decode($page->structure, true) : ($page->structure ?? []);
            $gjsProject = $structure['gjs_project'] ?? null;
            $initialHtml = $structure['html'] ?? '';

            if (empty($initialHtml) && !empty($structure['elements'])) {
                foreach ($structure['elements'] as $el) {
                    $initialHtml .= ($el['html'] ?? '');
                }
            }

            if (empty($initialHtml)) {
                $initialHtml = '<div class="container py-5 text-center"><h1 class="display-4 fw-bold text-dark mb-3">مرحباً بك في صفحة الهبوط الجديدة</h1><p class="lead text-muted mb-4">انقر على أي نص للتعديل المباشر، أو اسحب عناصر جديدة من القائمة الجانبية.</p><a href="#lead-form" class="btn btn-primary btn-lg rounded-pill px-5">قدم طلبك الآن</a></div>';
            }

            // Automatically convert slide placeholders and non-editable placeholders to native editable components
            $gallerySliderSnippet = '<div class="product-gallery-slider my-4 text-center"><div class="main-image-preview mb-3 bg-light p-2 rounded-4 shadow-sm"><img src="https://via.placeholder.com/800x600" id="mainGalleryImage" class="img-fluid rounded-3 max-h-500 w-100 object-fit-cover" alt="معرض صور المنتج"></div><div class="row g-2 justify-content-center thumbnails-row"><div class="col-2"><img src="https://via.placeholder.com/150?text=1" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 1"></div><div class="col-2"><img src="https://via.placeholder.com/150?text=2" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 2"></div><div class="col-2"><img src="https://via.placeholder.com/150?text=3" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 3"></div><div class="col-2"><img src="https://via.placeholder.com/150?text=4" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 4"></div><div class="col-2"><img src="https://via.placeholder.com/150?text=5" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 5"></div></div></div>';

            $initialHtml = preg_replace('/\{\{\s*slides\s*#?\s*\}\}/i', $gallerySliderSnippet, $initialHtml);
            $initialHtml = preg_replace('/contenteditable=["\']false["\']/i', 'contenteditable="true"', $initialHtml);
            $initialHtml = preg_replace('/\bnon-editable-area\b/i', 'editable-area', $initialHtml);
            $initialHtml = preg_replace('/\bnon-editable\b/i', 'is-editable', $initialHtml);
        @endphp

        const initialGjsProject = @json($gjsProject);
        const initialHtmlContent = @json($initialHtml);
        const initialCustomCss = @json($page->custom_css ?? '');

        // INITIALIZE GRAPESJS BUILDER
        const editor = grapesjs.init({
            container: '#gjs',
            height: '100%',
            width: 'auto',
            storageManager: false,
            fromElement: false,
            blockManager: {
                appendTo: '#blocks-container'
            },
            styleManager: {
                appendTo: '#style-container',
                sectors: [{
                    name: 'General / العام',
                    open: true,
                    buildProps: ['display', 'position', 'top', 'right', 'left', 'bottom']
                }, {
                    name: 'Dimension / الأحجام والهوامش',
                    open: false,
                    buildProps: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding']
                }, {
                    name: 'Typography / الخطوط والنصوص',
                    open: false,
                    buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-shadow']
                }, {
                    name: 'Decorator / المظهر والخلفية',
                    open: false,
                    buildProps: ['background-color', 'background-image', 'border-radius', 'border', 'box-shadow']
                }, {
                    name: 'Flexbox / مرونة العناصر',
                    open: false,
                    buildProps: ['flex-direction', 'flex-wrap', 'justify-content', 'align-items', 'align-content', 'order', 'flex-grow']
                }]
            },
            traitManager: {
                appendTo: '#traits-container'
            },
            layerManager: {
                appendTo: '#layers-container'
            },
            deviceManager: {
                devices: [
                    { name: 'Desktop', width: '' },
                    { name: 'Tablet', width: '768px', widthMedia: '992px' },
                    { name: 'Mobile', width: '375px', widthMedia: '480px' }
                ]
            },
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css',
                    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css'
                ],
                scripts: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'
                ]
            }
        });

        // LOAD INITIAL CONTENT INTO GRAPESJS
        if (initialGjsProject) {
            try {
                editor.loadProjectData(initialGjsProject);
            } catch (e) {
                editor.setComponents(initialHtmlContent);
            }
        } else {
            editor.setComponents(initialHtmlContent);
            if (initialCustomCss) {
                editor.setStyle(initialCustomCss);
            }
        }

        const systemFormsList = @json($forms->map(fn($f) => ['id' => (string)$f->id, 'name' => $f->name]));

        // 1. CUSTOM COUNTDOWN TIMER COMPONENT WITH LIVE SIDEBAR TRAITS
        editor.DomComponents.addType('countdown-timer', {
            model: {
                defaults: {
                    traits: [
                        { type: 'number', label: 'الساعات', name: 'data-hours', changeProp: 1 },
                        { type: 'number', label: 'الدقائق', name: 'data-minutes', changeProp: 1 },
                        { type: 'number', label: 'الثواني', name: 'data-seconds', changeProp: 1 },
                        { type: 'text', label: 'عنوان العرض', name: 'data-title', changeProp: 1 }
                    ]
                },
                init() {
                    this.on('change:data-hours change:data-minutes change:data-seconds change:data-title', this.updateCountdownDisplay);
                },
                updateCountdownDisplay() {
                    const hrs = this.get('data-hours') || '02';
                    const mins = this.get('data-minutes') || '45';
                    const secs = this.get('data-seconds') || '30';
                    const title = this.get('data-title') || '⚠️ ينتهي الخصم والعرض خلال:';

                    const el = this.getEl();
                    if (el) {
                        const h6 = el.querySelector('h6');
                        if (h6) h6.innerText = title;
                        const spans = el.querySelectorAll('.fw-bold.fs-4 > div');
                        if (spans.length >= 3) {
                            spans[0].childNodes[0].nodeValue = (hrs < 10 ? '0' + parseInt(hrs) : hrs) + ' ';
                            spans[1].childNodes[0].nodeValue = (mins < 10 ? '0' + parseInt(mins) : mins) + ' ';
                            spans[2].childNodes[0].nodeValue = (secs < 10 ? '0' + parseInt(secs) : secs) + ' ';
                        }
                    }
                }
            }
        });

        // 2. CUSTOM SYSTEM LEAD FORM COMPONENT WITH FORM SELECTOR TRAIT
        editor.DomComponents.addType('system-lead-form', {
            model: {
                defaults: {
                    traits: [
                        {
                            type: 'select',
                            label: 'اختيار نموذج من السيستم',
                            name: 'data-lead-form-id',
                            options: [
                                { id: '', name: '-- اختر نموذج السيستم --' },
                                ...systemFormsList
                            ],
                            changeProp: 1
                        },
                        {
                            type: 'select',
                            label: 'مصدر النموذج',
                            name: 'data-form-source',
                            options: [
                                { id: 'existing', name: 'نموذج سيستم مسبق (System Form)' },
                                { id: 'custom', name: 'نموذج HTML مخصص (Custom Form)' }
                            ],
                            changeProp: 1
                        },
                        { type: 'text', label: 'عنوان النموذج', name: 'data-form-title', changeProp: 1 },
                        { type: 'text', label: 'نص زر الطلب', name: 'data-submit-text', changeProp: 1 }
                    ]
                },
                init() {
                    this.on('change:data-lead-form-id change:data-form-title change:data-submit-text', this.updateFormDisplay);
                },
                updateFormDisplay() {
                    const formId = this.get('data-lead-form-id');
                    const title = this.get('data-form-title');
                    const btnText = this.get('data-submit-text');

                    const el = this.getEl();
                    if (el) {
                        if (formId) {
                            const hiddenInput = el.querySelector('input[name="lead_form_id"]');
                            if (hiddenInput) hiddenInput.value = formId;
                        }
                        if (title) {
                            const h4 = el.querySelector('h4');
                            if (h4) h4.innerText = title;
                        }
                        if (btnText) {
                            const btn = el.querySelector('button[type="submit"]');
                            if (btn) btn.innerText = btnText;
                        }
                    }
                }
            }
        });

        // 3. IMAGE COMPONENT ENHANCEMENT WITH REPLACE IMAGE TOOLBAR BUTTON
        editor.on('component:selected', (component) => {
            if (component && component.is('image')) {
                const tb = component.get('toolbar') || [];
                const hasReplaceBtn = tb.some(item => item.command === 'custom-image-replace');

                if (!hasReplaceBtn) {
                    component.set('toolbar', [
                        ...tb,
                        {
                            attributes: { class: 'fa fa-picture-o', title: '🖼️ استبدال الصورة (Replace Image)' },
                            command: 'custom-image-replace'
                        }
                    ]);
                }
            }
        });

        editor.Commands.add('custom-image-replace', {
            run(ed, sender) {
                const selected = ed.getSelected();
                if (selected && selected.is('image')) {
                    const currentSrc = selected.get('src') || (selected.getAttributes ? selected.getAttributes().src : '') || '';
                    const newUrl = prompt('أدخل رابط الصورة الجديدة لاستبدالها (Image URL):', currentSrc);
                    if (newUrl && newUrl.trim() !== '') {
                        selected.set('src', newUrl.trim());
                        const el = selected.getEl();
                        if (el) el.setAttribute('src', newUrl.trim());
                    }
                }
            }
        });

        // ADD CUSTOM READY BLOCKS
        const bm = editor.BlockManager;

        bm.add('hero-section', {
            label: '🌟 قسم الهيرو (Hero)',
            category: 'أقسام جاهزة',
            content: `
                <section class="py-5 bg-light text-center border-bottom">
                    <div class="container py-4">
                        <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">نمط الحياة الجديد</span>
                        <h1 class="display-3 fw-bold text-dark mb-3">الواقع الجديد أمتع مما تتصور</h1>
                        <p class="lead text-muted max-w-700 mx-auto mb-4">لتوفير رحلة وسفر مثالي، قمنا بصناعة واختيار كل عنصر بعناية فائقة.</p>
                        <a href="#order" class="btn btn-success btn-lg px-5 rounded-pill fw-bold">اطلب الآن 🛒</a>
                    </div>
                </section>
            `
        });

        bm.add('features-grid', {
            label: '⚡ شبكة المميزات (Features)',
            category: 'أقسام جاهزة',
            content: `
                <section class="py-5 bg-white">
                    <div class="container">
                        <h2 class="text-center fw-bold mb-5">لماذا تختار خدماتنا؟</h2>
                        <div class="row g-4 text-center">
                            <div class="col-md-4">
                                <div class="p-4 border rounded-4 bg-light shadow-sm">
                                    <i class="bi bi-lightning-charge-fill text-warning fs-1 mb-3"></i>
                                    <h4 class="fw-bold">سرعة وإنجاز</h4>
                                    <p class="text-muted">استخراج التأشيرات والخدمات بأقصى سرعة ممكنة.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 border rounded-4 bg-light shadow-sm">
                                    <i class="bi bi-shield-check text-success fs-1 mb-3"></i>
                                    <h4 class="fw-bold">ضمان وموثوقية</h4>
                                    <p class="text-muted">نضمن لك أعلى معدلات القبول والدقة في الأوراق.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 border rounded-4 bg-light shadow-sm">
                                    <i class="bi bi-headset text-primary fs-1 mb-3"></i>
                                    <h4 class="fw-bold">دعم متواصل 24/7</h4>
                                    <p class="text-muted">فريق خدمة العملاء متواجد دائماً للإجابة على أسئلتك.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            `
        });

        bm.add('lead-form-section', {
            label: '📝 نموذج الطلب (Lead Form)',
            category: 'أقسام جاهزة',
            content: `
                <section class="py-5 bg-dark text-white" id="order">
                    <div class="container">
                        <div class="max-w-600 mx-auto bg-secondary bg-opacity-25 p-4 p-md-5 rounded-4 border border-secondary shadow">
                            <h3 class="fw-bold text-center mb-3">احجز استشارتك المجانية الآن</h3>
                            <p class="text-center text-white-50 mb-4">سجل بياناتك وسيقوم أحد مستشارينا بالتواصل معك فوراً.</p>
                            <form onsubmit="event.preventDefault(); alert('تم استلام طلبك بنجاح');">
                                <div class="mb-3">
                                    <label class="form-label">الاسم الكامل</label>
                                    <input type="text" class="form-control form-control-lg" placeholder="أدخل اسمك هنا" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">رقم الجوال / الواتساب</label>
                                    <input type="tel" class="form-control form-control-lg" placeholder="0500000000" required>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg w-100 fw-bold rounded-pill">تأكيد وإرسال الطلب</button>
                            </form>
                        </div>
                    </div>
                </section>
            `
        });

        bm.add('basic-heading', {
            label: '📌 عنوان (Heading)',
            category: 'عناصر بسيطة',
            content: '<h2 class="fw-bold">اكتب عنوانك هنا</h2>'
        });

        bm.add('basic-paragraph', {
            label: '📄 فقرة نصية (Text)',
            category: 'عناصر بسيطة',
            content: '<p class="lead">اكتب الفقرة النصية والتفاصيل هنا...</p>'
        });

        bm.add('basic-button', {
            label: '🔘 زر إجراء (Button)',
            category: 'عناصر بسيطة',
            content: '<a href="#" class="btn btn-primary rounded-pill px-4">اضغط هنا</a>'
        });

        bm.add('basic-image', {
            label: '🖼️ صورة (Image)',
            category: 'عناصر بسيطة',
            content: '<img src="https://via.placeholder.com/600x400" class="img-fluid rounded-3" alt="صورة توضيحية">'
        });

        // DYNAMIC PRODUCT & E-COMMERCE BLOCKS (100% EDITABLE)
        bm.add('sticky-buy-bar', {
            label: '🛒 شريط الشراء السفلي (Sticky Bar)',
            category: 'عناصر المنتجات والخصومات',
            content: `
                <div class="position-fixed bottom-0 start-0 w-100 bg-dark text-white p-3 border-top border-secondary shadow-lg z-3" id="stickyBuyContainer">
                    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://via.placeholder.com/60" class="rounded-3 border border-secondary" alt="صورة المنتج" width="50" height="50">
                            <div>
                                <h6 class="fw-bold mb-0 text-white">ساعة watcha الذكية</h6>
                                <div class="d-flex align-items-center gap-2 small">
                                    <span class="badge bg-danger rounded-pill">خصم -40%</span>
                                    <span class="text-white-50 text-decoration-line-through">EGP 1500</span>
                                    <span class="fw-bold text-success fs-5">EGP 899</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="#order" class="btn btn-success btn-lg rounded-pill px-4 fw-bold shadow">
                                <i class="bi bi-cart-fill"></i> اطلب الآن والدفع عند الاستلام
                            </a>
                        </div>
                    </div>
                </div>
            `
        });

        bm.add('product-card-showcase', {
            label: '📦 بطاقة المنتج والخصم',
            category: 'عناصر المنتجات والخصومات',
            content: `
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden my-4 max-w-500 mx-auto">
                    <div class="position-relative">
                        <img src="https://via.placeholder.com/600x400" class="card-img-top" alt="اسم المنتج">
                        <span class="position-absolute top-0 start-0 bg-danger text-white px-3 py-1 rounded-end-3 fw-bold small m-3">عرض لفترة محدودة</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-2">اسم المنتج التجاري المميز</h4>
                        <p class="text-muted small mb-3">وصف سريع للمنتج والمميزات التي يحصل عليها العميل فور الطلب.</p>
                        <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                            <span class="text-muted text-decoration-line-through fs-5">1500 ج.م</span>
                            <span class="fw-bold text-success fs-2">899 ج.م</span>
                        </div>
                        <a href="#order" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow">أطلب المنتج الآن (دفع عند الاستلام)</a>
                    </div>
                </div>
            `
        });

        bm.add('floating-whatsapp', {
            label: '💬 زر الواتساب العائم (WhatsApp)',
            category: 'عناصر المنتجات والخصومات',
            content: `
                <a href="https://wa.me/201060500236?text=مرحباً،%20أريد%20الاستفسار%20عن%20العرض" target="_blank" class="position-fixed bottom-0 end-0 m-4 btn btn-success btn-lg rounded-circle shadow-lg d-flex align-items-center justify-content-center z-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-whatsapp fs-2"></i>
                </a>
            `
        });

        bm.add('countdown-timer', {
            label: '⏱️ عداد تنازلي للخصومات',
            category: 'عناصر المنتجات والخصومات',
            content: `
                <div class="p-3 bg-danger bg-opacity-10 border border-danger rounded-4 text-center my-3">
                    <h6 class="fw-bold text-danger mb-2">⚠️ ينتهي الخصم والعرض خلال:</h6>
                    <div class="d-flex justify-content-center gap-3 fw-bold fs-4 text-danger">
                        <div class="bg-danger text-white px-3 py-1 rounded-3">02 <span class="d-block text-xs fw-normal">ساعة</span></div>
                        <div class="bg-danger text-white px-3 py-1 rounded-3">45 <span class="d-block text-xs fw-normal">دقيقة</span></div>
                        <div class="bg-danger text-white px-3 py-1 rounded-3">30 <span class="d-block text-xs fw-normal">ثانية</span></div>
                    </div>
                </div>
            `
        });

        // 1. PRODUCT IMAGE GALLERY SLIDER BLOCK (Replaces slide placeholders)
        bm.add('product-gallery-slider', {
            label: '🖼️ معرض صور المنتج التفاعلي (Slider)',
            category: 'عناصر المنتجات والخصومات',
            content: `
                <div class="product-gallery-slider my-4 text-center">
                    <div class="main-image-preview mb-3 bg-light p-2 rounded-4 shadow-sm">
                        <img src="https://via.placeholder.com/800x600" id="mainGalleryImage" class="img-fluid rounded-3 max-h-500 w-100 object-fit-cover" alt="معرض صور المنتج">
                    </div>
                    <div class="row g-2 justify-content-center thumbnails-row">
                        <div class="col-2"><img src="https://via.placeholder.com/150?text=1" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 1"></div>
                        <div class="col-2"><img src="https://via.placeholder.com/150?text=2" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 2"></div>
                        <div class="col-2"><img src="https://via.placeholder.com/150?text=3" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 3"></div>
                        <div class="col-2"><img src="https://via.placeholder.com/150?text=4" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 4"></div>
                        <div class="col-2"><img src="https://via.placeholder.com/150?text=5" class="img-thumbnail rounded-3 cursor-pointer opacity-75 hover-opacity-100 gallery-thumb" alt="صورة 5"></div>
                    </div>
                </div>
            `
        });

        // 2. PRICING BUNDLES & DISCOUNT OFFERS BLOCK (Identical to User Reference Screenshot 2 & 4)
        bm.add('pricing-bundles-checkout', {
            label: '💰 حزم التوفير والأسعار (Bundles & Checkout)',
            category: 'عناصر المنتجات والخصومات',
            content: `
                <div class="pricing-bundles-wrapper my-4 max-w-700 mx-auto p-4 bg-white rounded-4 shadow-sm border border-light">
                    <h5 class="fw-bold mb-3 text-center text-dark">الرجاء إدخال معلوماتك هنا للطلب:</h5>
                    
                    <form action="{{ route('inquiries.store') }}" method="POST" class="mb-4">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="landing_page_id" value="{{ $page->id }}">
                        <input type="hidden" name="lead_form_id" value="{{ $page->assigned_lead_form_id ?? '' }}">

                        <div class="mb-3 position-relative">
                            <input type="text" name="name" class="form-control form-control-lg bg-light" placeholder="الاسم الكامل" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <input type="tel" name="phone" class="form-control form-control-lg bg-light" placeholder="رقم الهاتف / الواتساب" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <input type="text" name="address" class="form-control form-control-lg bg-light" placeholder="العنوان / المدينة">
                        </div>

                        <h6 class="fw-bold my-3 text-dark">اختر حزمة التوفير المناسبة:</h6>
                        <div class="bundle-options-list d-flex flex-column gap-3 mb-4">
                            <!-- BUNDLE 1 -->
                            <label class="bundle-card p-3 border rounded-4 bg-light d-flex align-items-center justify-content-between cursor-pointer">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="selected_bundle" value="1" class="form-check-input fs-4" checked>
                                    <div>
                                        <span class="fw-bold d-block text-dark">اشتري قطعة واحدة</span>
                                        <span class="badge bg-secondary">سعر عادي</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-success fs-4">EGP 927.71</span>
                                </div>
                            </label>

                            <!-- BUNDLE 2 -->
                            <label class="bundle-card p-3 border border-primary border-2 rounded-4 bg-primary bg-opacity-10 d-flex align-items-center justify-content-between cursor-pointer">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="selected_bundle" value="2" class="form-check-input fs-4">
                                    <div>
                                        <span class="fw-bold d-block text-dark">اشتري 2 واحصل على خصم إضافي</span>
                                        <span class="badge bg-danger">الأكثر مبيعاً 🔥 (خصم 20%)</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted text-decoration-line-through small d-block">EGP 1855</span>
                                    <span class="fw-bold text-success fs-4">EGP 1500.00</span>
                                </div>
                            </label>

                            <!-- BUNDLE 3 -->
                            <label class="bundle-card p-3 border border-success rounded-4 bg-success bg-opacity-10 d-flex align-items-center justify-content-between cursor-pointer">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="selected_bundle" value="3" class="form-check-input fs-4">
                                    <div>
                                        <span class="fw-bold d-block text-dark">اشتري 3 واحصل على 1 مجاناً</span>
                                        <span class="badge bg-success">شحن مجاني 🚚</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted text-decoration-line-through small d-block">EGP 2780</span>
                                    <span class="fw-bold text-success fs-4">EGP 2200.00</span>
                                </div>
                            </label>
                        </div>

                        <!-- TOTAL SUMMARY BAR -->
                        <div class="total-summary-bar p-3 bg-dark text-white rounded-4 d-flex align-items-center justify-content-between mb-4">
                            <span class="fw-bold fs-5">إجمالي المبلغ:</span>
                            <span class="fw-bold text-warning fs-3">EGP 927.71</span>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold fs-4 py-3 shadow">أطلب الآن والدفع عند الاستلام</button>
                    </form>
                </div>
            `
        });

        // 3. SYSTEM LEAD FORM BLOCK (Connected directly to Lead Forms in DB)
        bm.add('system-lead-form', {
            label: '📝 نموذج السيستم (System Lead Form)',
            category: 'عناصر المنتجات والخصومات',
            content: `
                <div class="system-lead-form-box p-4 bg-light rounded-4 shadow-sm border border-secondary my-4 max-w-600 mx-auto">
                    <h4 class="fw-bold text-center mb-2">تواصل معنا وسجل طلبك</h4>
                    <p class="text-muted text-center small mb-4">سجل بياناتك وسيتم التواصل معك مباشرة.</p>
                    <form action="{{ route('inquiries.store') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="landing_page_id" value="{{ $page->id }}">
                        <input type="hidden" name="lead_form_id" value="{{ $page->assigned_lead_form_id ?? '' }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control form-control-lg" placeholder="أدخل اسمك" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">رقم الجوال / الواتساب</label>
                            <input type="tel" name="phone" class="form-control form-control-lg" placeholder="أدخل رقم الجوال" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">المدينة / العنوان</label>
                            <input type="text" name="address" class="form-control form-control-lg" placeholder="أدخل العنوان">
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold rounded-pill shadow">إرسال الطلب الآن</button>
                    </form>
                </div>
            `
        });

        // FORCE UNLOCK & MAKE EVERY COMPONENT 100% EDITABLE
        function forceUnlockSelected() {
            const selected = editor.getSelected();
            if (!selected) {
                alert('يرجى تحديد عنصر أولاً من مساحة التصميم.');
                return;
            }
            selected.set({
                editable: true,
                badgable: true,
                stylable: true,
                highlightable: true,
                copyable: true,
                resizable: true,
                draggable: true,
                droppable: true,
                removable: true
            });
            const el = selected.getEl();
            if (el) {
                el.removeAttribute('contenteditable');
                el.setAttribute('contenteditable', 'true');
                el.classList.remove('non-editable-area', 'non-editable');
                el.querySelectorAll('*').forEach(child => {
                    child.removeAttribute('contenteditable');
                    child.setAttribute('contenteditable', 'true');
                    child.classList.remove('non-editable-area', 'non-editable');
                });
            }
            editor.trigger('component:update', selected);
            alert('🔓 تم إلغاء القفل بنجاح! يمكنك الآن تعديل النص، الأسعار، التنسيقات، والعناصر داخله بكل حرية.');
        }

        // AUTOMATIC UNLOCK LISTENER ON ALL COMPONENTS
        editor.on('component:add component:selected', (component) => {
            if (component) {
                component.set({
                    editable: true,
                    badgable: true,
                    stylable: true,
                    highlightable: true,
                    copyable: true,
                    resizable: true,
                    draggable: true,
                    droppable: true,
                    removable: true
                });
                const el = component.getEl();
                if (el) {
                    el.removeAttribute('contenteditable');
                    el.classList.remove('non-editable-area', 'non-editable');
                }
            }
        });

        // VIEWPORT SWITCHING
        function setDevice(deviceName) {
            editor.setDevice(deviceName);
            document.querySelectorAll('.gjs-viewport-btn').forEach(btn => btn.classList.remove('active'));
            if (deviceName === 'Desktop') document.getElementById('viewDesktop').classList.add('active');
            if (deviceName === 'Tablet') document.getElementById('viewTablet').classList.add('active');
            if (deviceName === 'Mobile') document.getElementById('viewMobile').classList.add('active');
        }

        // SIDEBAR TAB SWITCHING
        function switchTab(tabId, btn) {
            document.querySelectorAll('.gjs-panel-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.gjs-tab-pane').forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        // TOGGLE ACTIONS
        function toggleOutlines() {
            const cmd = editor.Commands;
            if (cmd.isActive('sw-visibility')) {
                cmd.stop('sw-visibility');
                document.getElementById('btnOutlines').classList.remove('active');
            } else {
                cmd.run('sw-visibility');
                document.getElementById('btnOutlines').classList.add('active');
            }
        }

        function togglePreview() {
            const cmd = editor.Commands;
            if (cmd.isActive('core:preview')) {
                cmd.stop('core:preview');
            } else {
                cmd.run('core:preview');
            }
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        function clearCanvas() {
            if (confirm('هل أنت متاكد من مسح جميع مكونات اللوحة؟')) {
                editor.DomComponents.clear();
            }
        }

        // BREADCRUMBS TRAIL UPDATE ON SELECTION
        editor.on('component:selected', (component) => {
            const trail = document.getElementById('breadcrumbsTrail');
            if (!component) {
                trail.innerHTML = '<span class="text-white-50">body</span>';
                return;
            }

            const parents = [];
            let curr = component;
            while (curr) {
                const tag = curr.get('tagName') || 'div';
                const id = curr.get('attributes').id ? '#' + curr.get('attributes').id : '';
                parents.unshift({ tag: tag + id, comp: curr });
                curr = curr.parent();
            }

            trail.innerHTML = parents.map(p => `<span class="gjs-breadcrumb-item" onclick="selectComponentById('${p.comp.getId()}')">${p.tag}</span>`).join(' > ');
        });

        function selectComponentById(id) {
            const comp = editor.DomComponents.findById(id);
            if (comp) editor.select(comp);
        }

        // SAVE PAGE DATA TO BACKEND
        function savePageData() {
            const badge = document.getElementById('saveStatusBadge');
            badge.className = 'badge bg-warning text-dark';
            badge.innerText = 'جاري الحفظ...';

            const projectData = editor.getProjectData();
            const html = editor.getHtml();
            const css = editor.getCss();

            const customCss = document.getElementById('modalCustomCss').value;
            const customJs = document.getElementById('modalCustomJs').value;

            const payload = {
                structure: {
                    gjs_project: projectData,
                    html: html,
                    css: css,
                    updated_at: new Date().toISOString()
                },
                custom_css: customCss || css,
                custom_js: customJs
            };

            fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                badge.className = 'badge bg-success';
                badge.innerText = 'تم الحفظ (' + new Date().toLocaleTimeString('ar-EG') + ')';
            })
            .catch(err => {
                badge.className = 'badge bg-danger';
                badge.innerText = 'خطأ بالحفظ';
            });
        }

        // FUNNEL SETTINGS SAVE HANDLER
        function saveFunnelSettings() {
            const form = document.getElementById('funnelSettingsForm');
            const formData = new FormData(form);
            const data = {};
            formData.forEach((val, key) => data[key] = val);
            data['is_active'] = document.getElementById('isActiveSwitch').checked ? 1 : 0;

            fetch(pageUpdateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => {
                if (res.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('funnelSettingsModal')).hide();
                    alert('تم تحديث إعدادات الـ Funnel والصفحة بنجاح!');
                } else {
                    alert('حدث خطأ أثناء تحديث الإعدادات.');
                }
            });
        }

        // APPLY CUSTOM CODE FROM MODAL
        function applyCustomCodeModal() {
            savePageData();
            bootstrap.Modal.getInstance(document.getElementById('codeEditorModal')).hide();
        }

        // KEYBOARD SHORTCUT (CTRL + S) FOR QUICK SAVE
        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                savePageData();
            }
        });

        // GALLERY THUMBNAIL CLICK DELEGATION
        document.addEventListener('click', (e) => {
            if (e.target && e.target.classList.contains('gallery-thumb')) {
                const wrapper = e.target.closest('.product-gallery-slider');
                if (wrapper) {
                    const mainImg = wrapper.querySelector('#mainGalleryImage') || wrapper.querySelector('.main-image-preview img');
                    if (mainImg) mainImg.src = e.target.src;
                }
            }
        });
    </script>
</body>
</html>
