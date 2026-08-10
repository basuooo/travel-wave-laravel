<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Landing Page Builder V2 | {{ $page->internal_name }}</title>

    <!-- Bootstrap 5 RTL & FontAwesome & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- GrapesJS Core & Preset CSS -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <link rel="stylesheet" href="https://unpkg.com/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --v2-topbar-h: 60px;
            --v2-sidebar-w: 320px;
            --v2-bg-dark: #090d16;
            --v2-panel-bg: #111827;
            --v2-border: #1f2937;
            --v2-accent: #3b82f6;
            --v2-success: #10b981;
            --v2-warning: #f59e0b;
        }

        * { box-sizing: border-box; }
        body, html { height: 100%; margin: 0; padding: 0; overflow: hidden; background-color: var(--v2-bg-dark); color: #f9fafb; font-family: system-ui, -apple-system, sans-serif; }

        /* TOPBAR */
        .v2-top-bar {
            height: var(--v2-topbar-h);
            background: #030712;
            border-bottom: 1px solid var(--v2-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: 1000;
        }

        .btn-v2-green {
            background: #10b981; color: white; font-weight: 700; border: none; border-radius: 8px; padding: 0.45rem 1rem; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none; transition: all 0.2s;
        }
        .btn-v2-green:hover { background: #059669; color: white; }

        .btn-v2-blue {
            background: #2563eb; color: white; font-weight: 700; border: none; border-radius: 8px; padding: 0.45rem 1.25rem; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.4rem; transition: all 0.2s; box-shadow: 0 2px 8px rgba(37,99,235,0.3);
        }
        .btn-v2-blue:hover { background: #1d4ed8; color: white; }

        .mode-switcher {
            background: #1f2937; border-radius: 9999px; padding: 3px; display: inline-flex; gap: 2px; border: 1px solid #374151;
        }
        .mode-btn {
            border: none; background: transparent; color: #9ca3af; padding: 4px 14px; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s;
        }
        .mode-btn.active { background: #3b82f6; color: white; }
        .mode-btn.mode-adv.active { background: #8b5cf6; color: white; }

        .v2-workspace { display: flex; height: calc(100vh - var(--v2-topbar-h)); position: relative; }

        /* LEFT & RIGHT SIDEBARS */
        .v2-sidebar {
            width: var(--v2-sidebar-w); background: var(--v2-panel-bg); border-left: 1px solid var(--v2-border); display: flex; flex-direction: column; height: 100%; z-index: 10;
        }
        .v2-sidebar-right {
            width: 340px; background: var(--v2-panel-bg); border-right: 1px solid var(--v2-border); display: flex; flex-direction: column; height: 100%; z-index: 10;
        }

        .v2-tabs { display: flex; background: #0b0f19; border-bottom: 1px solid var(--v2-border); }
        .v2-tab { flex: 1; padding: 0.75rem 0.25rem; text-align: center; background: transparent; border: none; border-bottom: 2px solid transparent; color: #9ca3af; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .v2-tab.active { color: var(--v2-accent); border-bottom-color: var(--v2-accent); background: #111827; }

        .v2-tab-content { flex: 1; overflow-y: auto; padding: 1rem; }
        .v2-pane { display: none; }
        .v2-pane.active { display: block; }

        /* CANVAS */
        .v2-canvas-container { flex: 1; position: relative; background: #030712; height: 100%; display: flex; flex-direction: column; }
        #gjs-v2 { height: 100%; width: 100%; }

        /* PROPERTY GROUPS IN SIMPLE & ADVANCED MODE */
        .prop-section { background: #1f2937; border: 1px solid #374151; border-radius: 10px; padding: 1rem; margin-bottom: 1rem; }
        .prop-title { font-size: 0.85rem; font-weight: 700; color: #60a5fa; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.03em; display: flex; align-items: center; gap: 6px; }

        .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }

        /* QUICK FLOATING TOOLBAR OVER CANVAS */
        .gjs-toolbar { background-color: #1e293b !important; border-radius: 8px !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5) !important; }

        /* BREADCRUMBS & FOOTER BAR */
        .v2-breadcrumbs-bar { height: 36px; background: #030712; border-top: 1px solid var(--v2-border); display: flex; align-items: center; justify-content: space-between; padding: 0 1rem; font-size: 0.8rem; color: #9ca3af; }
    </style>
</head>
<body>

    <!-- TOPBAR HEADER -->
    <header class="v2-top-bar">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.landing-pages-new.index') }}" class="btn-v2-green">
                <i class="bi bi-arrow-right-short fs-5"></i> العودة للقائمة
            </a>

            <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-2 py-1 fs-6">
                ⭐ Builder V2 (Easy & Advanced)
            </span>
        </div>

        <!-- CENTER VIEWPORT & MODE SWITCHER -->
        <div class="d-flex align-items-center gap-3">
            <div class="mode-switcher">
                <button class="mode-btn active" id="btnModeSimple" onclick="setBuilderMode('simple')">⚡ Simple Mode (سهل)</button>
                <button class="mode-btn mode-adv" id="btnModeAdvanced" onclick="setBuilderMode('advanced')">⚙️ Advanced Mode (متقدم)</button>
            </div>

            <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-outline-secondary text-white active" id="v2Desktop" onclick="editor.setDevice('Desktop')"><i class="bi bi-display"></i></button>
                <button class="btn btn-outline-secondary text-white" id="v2Tablet" onclick="editor.setDevice('Tablet')"><i class="bi bi-tablet"></i></button>
                <button class="btn btn-outline-secondary text-white" id="v2Mobile" onclick="editor.setDevice('Mobile')"><i class="bi bi-phone"></i></button>
            </div>
        </div>

        <!-- RIGHT ACTIONS & SAVE BUTTON -->
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary text-white" onclick="editor.UndoManager.undo()" title="Undo"><i class="bi bi-arrow-counterclockwise"></i></button>
            <button type="button" class="btn btn-sm btn-outline-secondary text-white" onclick="editor.UndoManager.redo()" title="Redo"><i class="bi bi-arrow-clockwise"></i></button>
            <button type="button" class="btn btn-sm btn-outline-secondary text-white" onclick="editor.Commands.run('core:preview')" title="Preview"><i class="bi bi-eye"></i></button>

            <span id="v2SaveBadge" class="badge bg-secondary">جاهز</span>

            <button type="button" class="btn-v2-blue" onclick="saveV2PageData()">
                <i class="bi bi-floppy-fill"></i> Save Page V2
            </button>
        </div>
    </header>

    <!-- WORKSPACE CONTAINER -->
    <div class="v2-workspace">

        <!-- LEFT SIDEBAR: ELEMENTS LIBRARY, SECTIONS, LAYERS -->
        <aside class="v2-sidebar">
            <div class="v2-tabs">
                <button class="v2-tab active" onclick="switchV2Tab('tabAdd', this)">
                    <i class="bi bi-plus-circle fs-5"></i> إضافة
                </button>
                <button class="v2-tab" onclick="switchV2Tab('tabSections', this)">
                    <i class="bi bi-grid-1x2 fs-5"></i> أقسام
                </button>
                <button class="v2-tab" onclick="switchV2Tab('tabLayers', this)">
                    <i class="bi bi-layers fs-5"></i> الطبقات
                </button>
            </div>

            <div class="v2-tab-content">
                <!-- TAB 1: ADD ELEMENTS LIBRARY -->
                <div id="tabAdd" class="v2-pane active">
                    <input type="text" id="elementSearch" class="form-control form-control-sm bg-dark text-white border-secondary mb-3" placeholder="🔍 بحث عن عنصر..." onkeyup="filterBlocks(this.value)">
                    <div id="v2-blocks-container"></div>
                </div>

                <!-- TAB 2: SECTIONS & PRESETS -->
                <div id="tabSections" class="v2-pane">
                    <div class="text-white-50 small mb-2 fw-bold uppercase">🧩 أقسام وقوالب جاهزة</div>
                    <div id="v2-sections-container"></div>
                </div>

                <!-- TAB 3: LAYERS TREE NAVIGATOR -->
                <div id="tabLayers" class="v2-pane">
                    <div class="text-white-50 small mb-2 fw-bold uppercase">🌲 شجرة الطبقات والعناصر</div>
                    <div id="v2-layers-container"></div>
                </div>
            </div>
        </aside>

        <!-- CENTER EDITOR CANVAS -->
        <main class="v2-canvas-container">
            <div id="gjs-v2"></div>

            <div class="v2-breadcrumbs-bar">
                <div class="d-flex align-items-center gap-3">
                    <span id="v2SelectedTag" class="text-info fw-bold">اختر عنصراً للتعديل</span>
                    <button type="button" class="btn btn-sm btn-outline-warning border-0 text-warning px-2 fw-bold" onclick="forceUnlockSelectedV2()">
                        <i class="bi bi-unlock-fill"></i> 🔓 إلغاء قفل العنصر
                    </button>
                </div>
                <div>
                    <span class="text-white-50 small">Builder V2 Engine</span>
                </div>
            </div>
        </main>

        <!-- RIGHT SIDEBAR: CONTEXTUAL PROPERTIES PANEL -->
        <aside class="v2-sidebar-right">
            <div class="p-3 bg-dark border-bottom border-secondary d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-white mb-0" id="contextPropHeader">⚙️ خصائص العنصر المحدد</h6>
                <span class="badge bg-info text-dark" id="currentModeLabel">SIMPLE MODE</span>
            </div>

            <div class="v2-tab-content" id="contextPropBody">
                <div class="text-center text-white-50 py-5" id="emptySelectNotice">
                    <i class="bi bi-hand-index-thumb fs-1 text-primary d-block mb-3"></i>
                    <p class="small">انقر على أي عنصر داخل مساحة التصميم لتعديله فوراً.</p>
                </div>

                <!-- DYNAMIC SIMPLE PROPERTIES CONTAINER -->
                <div id="simplePropsContainer" style="display:none;"></div>

                <!-- DYNAMIC ADVANCED PROPERTIES CONTAINER -->
                <div id="advancedPropsContainer" style="display:none;">
                    <div id="v2-style-container" class="mb-3"></div>
                    <div id="v2-traits-container"></div>
                </div>
            </div>
        </aside>

    </div>

    <!-- GrapesJS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/grapesjs"></script>
    <script src="https://unpkg.com/grapesjs-preset-webpage"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const updateUrlV2 = "{{ route('admin.landing-pages-new.builder-v2.update', $page) }}";
        const systemFormsList = @json($forms->map(fn($f) => ['id' => (string)$f->id, 'name' => $f->name]));

        let currentBuilderMode = 'simple';

        @php
            $structure = is_string($page->structure) ? json_decode($page->structure, true) : ($page->structure ?? []);
            $gjsProject = $structure['v2_project'] ?? ($structure['gjs_project'] ?? null);
            $initialHtml = $structure['html'] ?? '';

            if (empty($initialHtml) && !empty($structure['elements'])) {
                foreach ($structure['elements'] as $el) {
                    $initialHtml .= ($el['html'] ?? '');
                }
            }

            if (empty($initialHtml)) {
                $initialHtml = '<div class="container py-5 text-center"><h1 class="display-4 fw-bold text-dark mb-3">مرحباً بك في Builder V2</h1><p class="lead text-muted mb-4">انقر على أي عنصر للتعديل السريع، أو اسحب عناصر جديدة من القائمة الجانبية.</p><a href="#order" class="btn btn-primary btn-lg rounded-pill px-5">اطلب الآن</a></div>';
            }

            $initialHtml = preg_replace('/contenteditable=["\']false["\']/i', 'contenteditable="true"', $initialHtml);
            $initialHtml = preg_replace('/\bnon-editable-area\b/i', 'editable-area', $initialHtml);
        @endphp

        const initialGjsProject = @json($gjsProject);
        const initialHtmlContent = @json($initialHtml);
        const initialCustomCss = @json($page->custom_css ?? '');

        // INITIALIZE GRAPESJS V2 ENGINE
        const editor = grapesjs.init({
            container: '#gjs-v2',
            height: '100%',
            width: 'auto',
            storageManager: false,
            fromElement: false,
            blockManager: { appendTo: '#v2-blocks-container' },
            styleManager: { appendTo: '#v2-style-container' },
            traitManager: { appendTo: '#v2-traits-container' },
            layerManager: { appendTo: '#v2-layers-container' },
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

        // LOAD INITIAL CONTENT
        if (initialGjsProject) {
            try { editor.loadProjectData(initialGjsProject); } catch (e) { editor.setComponents(initialHtmlContent); }
        } else {
            editor.setComponents(initialHtmlContent);
            if (initialCustomCss) editor.setStyle(initialCustomCss);
        }

        // ADD CUSTOM V2 COMPONENTS WITH TRAITS & SIMPLE EDITING
        const bm = editor.BlockManager;

        // 1. TIMER COMPONENT
        bm.add('v2-timer', {
            label: '⏱️ عداد تنازلي (Countdown Timer)',
            category: 'MARKETING / تسويق',
            content: `
                <div class="p-3 bg-danger bg-opacity-10 border border-danger rounded-4 text-center my-3" data-gjs-type="v2-timer">
                    <h6 class="fw-bold text-danger mb-2">⚠️ ينتهي الخصم والعرض خلال:</h6>
                    <div class="d-flex justify-content-center gap-3 fw-bold fs-4 text-danger">
                        <div class="bg-danger text-white px-3 py-1 rounded-3">02 <span class="d-block text-xs fw-normal">ساعة</span></div>
                        <div class="bg-danger text-white px-3 py-1 rounded-3">45 <span class="d-block text-xs fw-normal">دقيقة</span></div>
                        <div class="bg-danger text-white px-3 py-1 rounded-3">30 <span class="d-block text-xs fw-normal">ثانية</span></div>
                    </div>
                </div>
            `
        });

        // 2. PRODUCT COMPONENT (STANDALONE)
        bm.add('v2-product', {
            label: '📦 منتج مستقل (Standalone Product)',
            category: 'PRODUCT / منتجات',
            content: `
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden my-4 max-w-500 mx-auto" data-gjs-type="v2-product">
                    <img src="https://via.placeholder.com/600x400" class="card-img-top" alt="اسم المنتج">
                    <div class="card-body p-4 text-center">
                        <h4 class="fw-bold mb-2">ساعة Watcha الذكية المميزة</h4>
                        <p class="text-muted small mb-3">شاشة عالية الدقة، مقاومة للماء مع تتبع كامل للأنشطة الرياضية.</p>
                        <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                            <span class="text-muted text-decoration-line-through fs-5">1500 ج.م</span>
                            <span class="fw-bold text-success fs-2">899 ج.م</span>
                        </div>
                        <a href="#order" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow">أطلب الآن (دفع عند الاستلام)</a>
                    </div>
                </div>
            `
        });

        // 3. SYSTEM FORM INTEGRATION
        bm.add('v2-lead-form', {
            label: '📝 نموذج الطلب (Lead Form Integration)',
            category: 'FORMS / نماذج',
            content: `
                <div class="system-lead-form-box p-4 bg-light rounded-4 shadow-sm border border-secondary my-4 max-w-600 mx-auto" data-gjs-type="v2-lead-form">
                    <h4 class="fw-bold text-center mb-2">تواصل معنا وسجل طلبك</h4>
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
                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold rounded-pill shadow">إرسال الطلب الآن</button>
                    </form>
                </div>
            `
        });

        // CONTEXTUAL SELECTION & SIMPLE / ADVANCED SIDEBAR SWITCHER
        editor.on('component:selected', (component) => {
            const emptyNotice = document.getElementById('emptySelectNotice');
            const simpleContainer = document.getElementById('simplePropsContainer');
            const advContainer = document.getElementById('advancedPropsContainer');
            const selectedTag = document.getElementById('breadcrumbsTrail');

            if (!component) {
                emptyNotice.style.display = 'block';
                simpleContainer.style.display = 'none';
                advContainer.style.display = 'none';
                return;
            }

            emptyNotice.style.display = 'none';
            document.getElementById('v2SelectedTag').innerText = component.get('tagName').toUpperCase() + (component.get('attributes').id ? ' #' + component.get('attributes').id : '');

            if (currentBuilderMode === 'simple') {
                simpleContainer.style.display = 'block';
                advContainer.style.display = 'none';
                renderSimpleProperties(component, simpleContainer);
            } else {
                simpleContainer.style.display = 'none';
                advContainer.style.display = 'block';
            }
        });

        function renderSimpleProperties(component, container) {
            let html = '<div class="prop-section"><div class="prop-title"><i class="bi bi-sliders"></i> التعديل البسيط (Simple Controls)</div>';

            const tag = component.get('tagName');

            if (tag === 'img') {
                const src = component.get('src') || component.getAttributes().src || '';
                html += `
                    <div class="mb-3">
                        <label class="form-label text-white-50 small fw-bold">رابط الصورة (URL)</label>
                        <input type="text" class="form-control form-control-sm" value="${src}" onchange="updateSelectedAttr('src', this.value)">
                    </div>
                    <button class="btn btn-sm btn-primary w-100" onclick="promptReplaceImage()"><i class="bi bi-image"></i> استبدال الصورة</button>
                `;
            } else if (['h1','h2','h3','h4','h5','h6','p','span','a','button'].includes(tag)) {
                const content = component.get('content') || (component.getEl() ? component.getEl().innerText : '');
                html += `
                    <div class="mb-3">
                        <label class="form-label text-white-50 small fw-bold">النص المكتوب</label>
                        <textarea class="form-control form-control-sm" rows="3" onkeyup="updateSelectedContent(this.value)">${content}</textarea>
                    </div>
                `;
            } else {
                html += `<p class="text-white-50 small">اختر نصاً أو صورة أو عنصراً فرعياً للتعديل السريع.</p>`;
            }

            html += `
                <div class="hr border-secondary my-3"></div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="visibilityToggle" checked onchange="toggleSelectedVisibility(this.checked)">
                    <label class="form-check-label text-white small" for="visibilityToggle">إظهار العنصر (Visible)</label>
                </div>
            </div>`;

            container.innerHTML = html;
        }

        function updateSelectedAttr(attr, val) {
            const selected = editor.getSelected();
            if (selected) {
                selected.set(attr, val);
                const el = selected.getEl();
                if (el) el.setAttribute(attr, val);
            }
        }

        function updateSelectedContent(text) {
            const selected = editor.getSelected();
            if (selected) {
                selected.set('content', text);
                const el = selected.getEl();
                if (el) el.innerText = text;
            }
        }

        function toggleSelectedVisibility(visible) {
            const selected = editor.getSelected();
            if (selected) {
                selected.setStyle({ display: visible ? '' : 'none' });
            }
        }

        function promptReplaceImage() {
            const selected = editor.getSelected();
            if (selected && selected.is('image')) {
                const currentSrc = selected.get('src') || '';
                const newUrl = prompt('أدخل رابط الصورة الجديدة:', currentSrc);
                if (newUrl && newUrl.trim() !== '') {
                    selected.set('src', newUrl.trim());
                    const el = selected.getEl();
                    if (el) el.setAttribute('src', newUrl.trim());
                    renderSimpleProperties(selected, document.getElementById('simplePropsContainer'));
                }
            }
        }

        function setBuilderMode(mode) {
            currentBuilderMode = mode;
            document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
            if (mode === 'simple') {
                document.getElementById('btnModeSimple').classList.add('active');
                document.getElementById('currentModeLabel').innerText = 'SIMPLE MODE';
                document.getElementById('currentModeLabel').className = 'badge bg-info text-dark';
            } else {
                document.getElementById('btnModeAdvanced').classList.add('active');
                document.getElementById('currentModeLabel').innerText = 'ADVANCED MODE';
                document.getElementById('currentModeLabel').className = 'badge bg-warning text-dark';
            }
            const selected = editor.getSelected();
            if (selected) editor.trigger('component:selected', selected);
        }

        function switchV2Tab(tabId, btn) {
            document.querySelectorAll('.v2-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.v2-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function forceUnlockSelectedV2() {
            const selected = editor.getSelected();
            if (!selected) { alert('يرجى تحديد عنصر أولاً.'); return; }
            selected.set({ editable: true, badgable: true, stylable: true, copyable: true, resizable: true, draggable: true, removable: true });
            const el = selected.getEl();
            if (el) {
                el.removeAttribute('contenteditable');
                el.setAttribute('contenteditable', 'true');
                el.classList.remove('non-editable-area', 'non-editable');
            }
            alert('🔓 تم فك قفل العنصر في Builder V2!');
        }

        function saveV2PageData() {
            const badge = document.getElementById('v2SaveBadge');
            badge.className = 'badge bg-warning text-dark'; badge.innerText = 'جاري الحفظ...';

            const projectData = editor.getProjectData();
            const html = editor.getHtml();
            const css = editor.getCss();

            const payload = {
                structure: {
                    v2_project: projectData,
                    html: html,
                    css: css,
                    updated_at: new Date().toISOString()
                },
                custom_css: css
            };

            fetch(updateUrlV2, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                badge.className = 'badge bg-success'; badge.innerText = 'تم الحفظ (' + new Date().toLocaleTimeString('ar-EG') + ')';
            })
            .catch(err => {
                badge.className = 'badge bg-danger'; badge.innerText = 'خطأ بالحفظ';
            });
        }
    </script>
</body>
</html>
