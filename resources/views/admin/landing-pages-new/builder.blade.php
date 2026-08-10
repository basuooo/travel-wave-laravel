<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛠️ البناء المرئي الهجين | {{ $page->internal_name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --builder-topbar-h: 60px;
            --builder-sidebar-w: 320px;
            --builder-inspector-w: 300px;
            --builder-bg: #0f172a;
            --panel-bg: #1e293b;
            --panel-border: #334155;
            --accent-color: #3b82f6;
        }

        body {
            background-color: var(--builder-bg);
            color: #f8fafc;
            font-family: system-ui, -apple-system, sans-serif;
            margin: 0;
            overflow: hidden;
            height: 100vh;
        }

        /* TOPBAR */
        .builder-topbar {
            height: var(--builder-topbar-h);
            background: #020617;
            border-bottom: 1px solid var(--panel-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 100;
        }

        .builder-viewport-controls .btn {
            color: #94a3b8;
            border-color: var(--panel-border);
        }
        .builder-viewport-controls .btn.active {
            background-color: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
        }

        /* WORKSPACE CONTAINER */
        .builder-workspace {
            display: flex;
            height: calc(100vh - var(--builder-topbar-h));
        }

        /* SIDEBARS */
        .builder-sidebar, .builder-inspector {
            width: var(--builder-sidebar-w);
            background-color: var(--panel-bg);
            border-left: 1px solid var(--panel-border);
            border-right: 1px solid var(--panel-border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .builder-inspector {
            width: var(--builder-inspector-w);
        }

        .sidebar-section-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 700;
            padding: 1rem;
            border-bottom: 1px solid var(--panel-border);
            background: #0f172a;
        }

        .component-item {
            background: #0f172a;
            border: 1px solid var(--panel-border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .component-item:hover {
            border-color: var(--accent-color);
            transform: translateY(-2px);
            background: #1e293b;
        }

        /* CANVAS AREA */
        .builder-canvas-wrapper {
            flex: 1;
            background-color: #020617;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            overflow-y: auto;
            padding: 2rem;
        }

        .canvas-frame {
            background: #ffffff;
            color: #000000;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            transition: width 0.3s ease;
            min-height: 800px;
            width: 100%;
            position: relative;
        }
        .canvas-frame.viewport-tablet { width: 768px; }
        .canvas-frame.viewport-mobile { width: 375px; }

        /* SECTION HOVER & CONTROLS */
        .builder-section-block {
            position: relative;
            border: 2px transparent dashed;
            transition: border-color 0.2s ease;
        }
        .builder-section-block:hover {
            border-color: var(--accent-color);
        }

        .section-control-bar {
            position: absolute;
            top: 8px;
            left: 8px;
            display: none;
            background: rgba(15, 23, 42, 0.9);
            padding: 4px 8px;
            border-radius: 6px;
            z-index: 50;
            gap: 4px;
        }
        .builder-section-block:hover .section-control-bar {
            display: flex;
        }

        /* CODE EDITOR OVERLAY */
        #codeEditorModal .modal-content {
            background: #0f172a;
            color: #f8fafc;
            border: 1px solid var(--panel-border);
        }
    </style>
</head>
<body>

    <!-- TOPBAR -->
    <header class="builder-topbar">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.landing-pages-new.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                ← العودة لوحة التحكم
            </a>
            <div>
                <span class="fw-bold text-white fs-6">{{ $page->internal_name }}</span>
                <span id="saveStatusBadge" class="badge text-bg-success ms-2">جاهز</span>
            </div>
        </div>

        <!-- VIEWPORT SWITCHER -->
        <div class="builder-viewport-controls btn-group" role="group">
            <button type="button" class="btn btn-sm active" id="btnViewDesktop" onclick="setViewport('desktop')" title="شاشات سطح المكتب">
                <i class="bi bi-display"></i> Desktop
            </button>
            <button type="button" class="btn btn-sm" id="btnViewTablet" onclick="setViewport('tablet')" title="شاشات الأيباد والتابلت">
                <i class="bi bi-tablet"></i> Tablet
            </button>
            <button type="button" class="btn btn-sm" id="btnViewMobile" onclick="setViewport('mobile')" title="شاشات الجوال">
                <i class="bi bi-phone"></i> Mobile
            </button>
        </div>

        <!-- ACTIONS -->
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#codeEditorModal">
                💻 محرر الكود (Code Editor)
            </button>
            <a href="{{ $page->publicUrl() }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                👁️ معاينة الرابط
            </a>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold" onclick="saveCanvasData()">
                💾 حفظ التعديلات
            </button>
        </div>
    </header>

    <!-- WORKSPACE -->
    <div class="builder-workspace">

        <!-- LEFT SIDEBAR: ELEMENTS & SECTIONS -->
        <aside class="builder-sidebar">
            <div class="sidebar-section-title">🧩 العناصر والأقسام Ready Sections</div>
            <div class="p-3">
                <div class="component-item" onclick="addNewSection('hero')">
                    <i class="bi bi-star-fill text-warning fs-5"></i>
                    <div>
                        <div class="fw-bold text-white small">قسم الهيرو (Hero Banner)</div>
                        <div class="text-muted text-xs">عنوان رئيسي، وصف، وزر طلب.</div>
                    </div>
                </div>
                <div class="component-item" onclick="addNewSection('features')">
                    <i class="bi bi-grid-fill text-info fs-5"></i>
                    <div>
                        <div class="fw-bold text-white small">قسم المميزات (Features)</div>
                        <div class="text-muted text-xs">أعمدة وبطاقات مميزات.</div>
                    </div>
                </div>
                <div class="component-item" onclick="addNewSection('cta_form')">
                    <i class="bi bi-ui-checks text-success fs-5"></i>
                    <div>
                        <div class="fw-bold text-white small">قسم النموذج والطلب (Lead Form)</div>
                        <div class="text-muted text-xs">نموذج تواصل وجمع بيانات.</div>
                    </div>
                </div>
                <div class="component-item" onclick="addNewSection('faq')">
                    <i class="bi bi-patch-question-fill text-primary fs-5"></i>
                    <div>
                        <div class="fw-bold text-white small">قسم الأسئلة الشائعة (FAQ)</div>
                        <div class="text-muted text-xs">قائمة أوردون وأسئلة إجابات.</div>
                    </div>
                </div>
                <div class="component-item" onclick="addNewSection('custom_html')">
                    <i class="bi bi-code-slash text-danger fs-5"></i>
                    <div>
                        <div class="fw-bold text-white small">قسم كود HTML مخصص (Custom Section)</div>
                        <div class="text-muted text-xs">إضافة كود HTML/CSS مخصص.</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- CENTER CANVAS AREA -->
        <main class="builder-canvas-wrapper">
            <div id="canvasFrame" class="canvas-frame p-0">
                <div id="canvasElementsContainer">
                    @php
                        $structure = is_string($page->structure) ? json_decode($page->structure, true) : ($page->structure ?? []);
                        $elements = $structure['elements'] ?? [];
                    @endphp

                    @forelse($elements as $index => $el)
                        <div class="builder-section-block" data-id="{{ $el['id'] ?? 'sec_' . $index }}" data-index="{{ $index }}">
                            <div class="section-control-bar">
                                <button type="button" class="btn btn-sm btn-dark text-white p-1 px-2" onclick="moveSectionUp(this)" title="تحريك لأعلى">⬆️</button>
                                <button type="button" class="btn btn-sm btn-dark text-white p-1 px-2" onclick="moveSectionDown(this)" title="تحريك لأسفل">⬇️</button>
                                <button type="button" class="btn btn-sm btn-danger text-white p-1 px-2" onclick="deleteSection(this)" title="حذف القسم">🗑️</button>
                            </div>
                            <div class="section-content-editable" contenteditable="true" spellcheck="false">
                                {!! $el['html'] ?? '' !!}
                            </div>
                        </div>
                    @empty
                        <div class="builder-section-block" data-id="sec_hero_default" data-index="0">
                            <div class="section-control-bar">
                                <button type="button" class="btn btn-sm btn-dark text-white p-1 px-2" onclick="moveSectionUp(this)">⬆️</button>
                                <button type="button" class="btn btn-sm btn-dark text-white p-1 px-2" onclick="moveSectionDown(this)">⬇️</button>
                                <button type="button" class="btn btn-sm btn-danger text-white p-1 px-2" onclick="deleteSection(this)">🗑️</button>
                            </div>
                            <div class="section-content-editable" contenteditable="true" spellcheck="false">
                                <div class="container py-5 text-center">
                                    <h1 class="display-4 fw-bold text-primary mb-3">مرحباً بك في صفحة الهبوط الجديدة</h1>
                                    <p class="lead text-muted mb-4">انقر على أي نص هنا للتعديل المباشر، أو اضف أقسام جديدة من القائمة الجانبية.</p>
                                    <a href="#lead-form" class="btn btn-primary btn-lg rounded-pill px-5">قدم طلبك الآن</a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        <!-- RIGHT INSPECTOR PANEL -->
        <aside class="builder-inspector">
            <div class="sidebar-section-title">🎨 خصائص النمط والإعدادات</div>
            <div class="p-3">
                <div class="mb-3">
                    <label class="form-label text-white-50 small">لون خلفية المعاينة</label>
                    <input type="color" class="form-control form-control-color w-100" id="bgColorPicker" value="#ffffff" onchange="document.getElementById('canvasFrame').style.backgroundColor = this.value">
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50 small">حالة الحفظ التلقائي</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="autosaveToggle" checked>
                        <label class="form-check-label text-white small" for="autosaveToggle">تفعيل Autosave</label>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- CODE EDITOR MODAL -->
    <div class="modal fade" id="codeEditorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">💻 محرر الأكواد المخصص (Custom CSS & JS)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const updateUrl = "{{ route('admin.landing-pages-new.builder.update', $page) }}";
        const autosaveUrl = "{{ route('admin.landing-pages-new.autosave', $page) }}";

        function setViewport(mode) {
            const frame = document.getElementById('canvasFrame');
            document.querySelectorAll('.builder-viewport-controls .btn').forEach(b => b.classList.remove('active'));

            if (mode === 'desktop') {
                frame.className = 'canvas-frame p-0';
                document.getElementById('btnViewDesktop').classList.add('active');
            } else if (mode === 'tablet') {
                frame.className = 'canvas-frame p-0 viewport-tablet';
                document.getElementById('btnViewTablet').classList.add('active');
            } else if (mode === 'mobile') {
                frame.className = 'canvas-frame p-0 viewport-mobile';
                document.getElementById('btnViewMobile').classList.add('active');
            }
        }

        function moveSectionUp(btn) {
            const block = btn.closest('.builder-section-block');
            if (block.previousElementSibling) {
                block.parentNode.insertBefore(block, block.previousElementSibling);
                triggerAutosave();
            }
        }

        function moveSectionDown(btn) {
            const block = btn.closest('.builder-section-block');
            if (block.nextElementSibling) {
                block.parentNode.insertBefore(block.nextElementSibling, block);
                triggerAutosave();
            }
        }

        function deleteSection(btn) {
            if (confirm('هل أنت تأكد من حذف هذا القسم؟')) {
                const block = btn.closest('.builder-section-block');
                block.remove();
                triggerAutosave();
            }
        }

        function addNewSection(type) {
            const container = document.getElementById('canvasElementsContainer');
            const id = 'sec_' + type + '_' + Math.random().toString(36).substr(2, 6);

            let htmlSnippet = '';
            if (type === 'hero') {
                htmlSnippet = `<div class="container py-5 text-center"><h2 class="display-5 fw-bold text-dark mb-3">قسم الهيرو الجديد</h2><p class="lead text-muted mb-4">اكتب هنا نص جذاب يعرض تفاصيل الخدمات والتأشيرات.</p><button class="btn btn-primary btn-lg rounded-pill px-5">قدم طلبك</button></div>`;
            } else if (type === 'features') {
                htmlSnippet = `<div class="container py-5"><h3 class="text-center fw-bold mb-4">مميزات خدماتنا</h3><div class="row g-4"><div class="col-md-4 text-center"><div class="p-4 bg-light rounded-4"><h5>⚡ سرعة الإنجاز</h5><p class="text-muted small">متابعة دقيقة لكل الطلبات.</p></div></div><div class="col-md-4 text-center"><div class="p-4 bg-light rounded-4"><h5>🛡️ موثوقية كاملة</h5><p class="text-muted small">ضمان تقديم الأوراق بالشكل الصحيح.</p></div></div><div class="col-md-4 text-center"><div class="p-4 bg-light rounded-4"><h5>📞 دعم 24/7</h5><p class="text-muted small">فريق مخصص للرد على كافة الاستفسارات.</p></div></div></div></div>`;
            } else if (type === 'cta_form') {
                htmlSnippet = `<div class="container py-5" id="lead-form"><div class="card border-0 shadow p-4 rounded-4 max-w-600 mx-auto"><h3 class="fw-bold text-center mb-3">سجل بياناتك للتواصل معك</h3><form onsubmit="event.preventDefault(); alert('تم استلام الطلب بنجاح');"><div class="mb-3"><input type="text" class="form-control" placeholder="الاسم الكامل" required></div><div class="mb-3"><input type="tel" class="form-control" placeholder="رقم الواتساب / الهاتف" required></div><button type="submit" class="btn btn-success w-100 btn-lg rounded-pill fw-bold">إرسال الطلب الآن</button></form></div></div>`;
            } else {
                htmlSnippet = `<div class="container py-4"><div class="p-4 bg-light rounded-3 text-center"><h4>قسم مخصص جديد</h4><p class="text-muted">انقر للتعديل على المحتوى.</p></div></div>`;
            }

            const newBlock = document.createElement('div');
            newBlock.className = 'builder-section-block';
            newBlock.setAttribute('data-id', id);
            newBlock.innerHTML = `
                <div class="section-control-bar">
                    <button type="button" class="btn btn-sm btn-dark text-white p-1 px-2" onclick="moveSectionUp(this)">⬆️</button>
                    <button type="button" class="btn btn-sm btn-dark text-white p-1 px-2" onclick="moveSectionDown(this)">⬇️</button>
                    <button type="button" class="btn btn-sm btn-danger text-white p-1 px-2" onclick="deleteSection(this)">🗑️</button>
                </div>
                <div class="section-content-editable" contenteditable="true" spellcheck="false">
                    ${htmlSnippet}
                </div>
            `;

            container.appendChild(newBlock);
            triggerAutosave();
        }

        function extractCanvasElements() {
            const blocks = document.querySelectorAll('.builder-section-block');
            const elements = [];

            blocks.forEach((b, idx) => {
                const id = b.getAttribute('data-id') || 'sec_' + idx;
                const editable = b.querySelector('.section-content-editable');
                elements.push({
                    id: id,
                    type: 'section',
                    html: editable ? editable.innerHTML : b.innerHTML,
                });
            });

            return { elements: elements };
        }

        function saveCanvasData() {
            const badge = document.getElementById('saveStatusBadge');
            badge.className = 'badge text-bg-warning ms-2';
            badge.innerText = 'جاري الحفظ...';

            const structure = extractCanvasElements();
            const customCss = document.getElementById('modalCustomCss').value;
            const customJs = document.getElementById('modalCustomJs').value;

            fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    structure: structure,
                    custom_css: customCss,
                    custom_js: customJs
                })
            })
            .then(res => res.json())
            .then(data => {
                badge.className = 'badge text-bg-success ms-2';
                badge.innerText = 'تم الحفظ (' + new Date().toLocaleTimeString('ar-EG') + ')';
            })
            .catch(err => {
                badge.className = 'badge text-bg-danger ms-2';
                badge.innerText = 'خطأ بالحفظ';
            });
        }

        let autosaveTimer = null;
        function triggerAutosave() {
            if (!document.getElementById('autosaveToggle').checked) return;

            clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(() => {
                const structure = extractCanvasElements();
                fetch(autosaveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ structure: structure })
                });
            }, 3000);
        }

        function applyCustomCodeModal() {
            saveCanvasData();
            bootstrap.Modal.getInstance(document.getElementById('codeEditorModal')).hide();
        }

        document.getElementById('canvasElementsContainer').addEventListener('input', triggerAutosave);
    </script>
</body>
</html>
