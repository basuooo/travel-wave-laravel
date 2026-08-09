<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Visual Builder - {{ $landingPage->internal_name }}</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --builder-topbar-height: 56px;
            --builder-sidebar-width: 320px;
            --builder-inspector-width: 340px;
            --builder-bg: #0f172a;
            --builder-panel-bg: #1e293b;
            --builder-border: #334155;
            --builder-accent: #0284c7;
        }

        body {
            background-color: var(--builder-bg);
            color: #f8fafc;
            font-family: system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
        }

        /* Top Bar */
        .builder-topbar {
            height: var(--builder-topbar-height);
            background: #090d16;
            border-bottom: 1px solid var(--builder-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-inline: 16px;
            position: relative;
            z-index: 100;
        }

        .builder-device-btn {
            background: transparent;
            border: 1px solid var(--builder-border);
            color: #94a3b8;
            padding: 4px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .builder-device-btn.active, .builder-device-btn:hover {
            background: var(--builder-accent);
            color: #ffffff;
            border-color: var(--builder-accent);
        }

        /* Layout Grid */
        .builder-shell {
            display: flex;
            height: calc(100vh - var(--builder-topbar-height));
            overflow: hidden;
        }

        /* Left Sidebar */
        .builder-sidebar {
            width: var(--builder-sidebar-width);
            background: var(--builder-panel-bg);
            border-end: 1px solid var(--builder-border);
            display: flex;
            flex-direction: column;
            z-index: 90;
        }

        .builder-nav-tabs .nav-link {
            color: #94a3b8;
            border: none;
            border-bottom: 2px solid transparent;
            font-size: 12px;
            font-weight: 700;
            padding: 10px 8px;
        }
        .builder-nav-tabs .nav-link.active {
            color: var(--builder-accent);
            border-bottom-color: var(--builder-accent);
            background: transparent;
        }

        .builder-elements-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 15px;
            overflow-y: auto;
        }

        .builder-element-card {
            background: #0f172a;
            border: 1px solid var(--builder-border);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            cursor: grab;
            user-select: none;
            transition: all 0.2s;
        }
        .builder-element-card:hover {
            border-color: var(--builder-accent);
            transform: translateY(-2px);
            background: #1e293b;
        }
        .builder-element-card:active {
            cursor: grabbing;
        }

        /* Center Canvas */
        .builder-canvas-wrap {
            flex: 1;
            background: #090d16;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            overflow-y: auto;
            position: relative;
        }

        .builder-canvas-frame {
            background: #ffffff;
            color: #1e293b;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            transition: width 0.3s ease, border-color 0.2s ease;
            min-height: 800px;
            position: relative;
            border: 2px dashed transparent;
        }
        .builder-canvas-frame.drag-over {
            border-color: var(--builder-accent) !important;
            background: #f0f9ff !important;
        }

        .builder-canvas-frame.device-desktop { width: 100%; max-width: 1200px; }
        .builder-canvas-frame.device-tablet { width: 768px; }
        .builder-canvas-frame.device-mobile { width: 375px; }

        /* Canvas Element Selection & Hover */
        .canvas-element {
            position: relative;
            outline: 1px dashed transparent;
            transition: outline 0.15s;
            margin-bottom: 2px;
        }
        .canvas-element:hover {
            outline: 2px dashed var(--builder-accent);
        }
        .canvas-element.selected {
            outline: 2px solid var(--builder-accent) !important;
        }

        .canvas-element-toolbar {
            position: absolute;
            top: -28px;
            right: 0;
            background: var(--builder-accent);
            color: #ffffff;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 4px 4px 0 0;
            display: none;
            gap: 8px;
            z-index: 50;
        }
        .canvas-element:hover .canvas-element-toolbar,
        .canvas-element.selected .canvas-element-toolbar {
            display: flex;
        }

        /* Right Inspector */
        .builder-inspector {
            width: var(--builder-inspector-width);
            background: var(--builder-panel-bg);
            border-start: 1px solid var(--builder-border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            padding: 15px;
            z-index: 90;
        }

        .property-group {
            background: #0f172a;
            border: 1px solid var(--builder-border);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
        }
        .property-group-title {
            font-size: 12px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <div class="builder-topbar">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.landing-pages.index') }}" class="text-white text-decoration-none me-2">← Exit</a>
            <span class="fw-bold text-white fs-6">{{ $landingPage->internal_name }}</span>
            <span class="badge bg-secondary font-monospace" id="saveStatusIndicator">Saved</span>
        </div>

        <!-- Device Mode Switcher -->
        <div class="d-flex gap-1">
            <button class="builder-device-btn active" data-device="desktop" title="Desktop View"><i class="bi bi-display"></i> Desktop</button>
            <button class="builder-device-btn" data-device="tablet" title="Tablet View"><i class="bi bi-tablet"></i> Tablet</button>
            <button class="builder-device-btn" data-device="mobile" title="Mobile View"><i class="bi bi-phone"></i> Mobile</button>
        </div>

        <!-- Action Controls -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.landing-pages.versions', $landingPage) }}" class="btn btn-sm btn-outline-light"><i class="bi bi-clock-history"></i> History</a>
            
            <form method="post" action="{{ route('admin.landing-pages.save-as-template', $landingPage) }}" class="d-inline">
                @csrf
                <input type="hidden" name="name_en" value="{{ $landingPage->internal_name }} Template">
                <input type="hidden" name="name_ar" value="{{ $landingPage->title_ar ?: $landingPage->internal_name }}">
                <button type="submit" class="btn btn-sm btn-outline-warning"><i class="bi bi-bookmark-star"></i> Save Template</button>
            </form>

            <a href="{{ route('admin.landing-pages.export', $landingPage) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-download"></i> Export</a>

            <button class="btn btn-sm btn-primary fw-bold px-3" id="btnSaveCanvas"><i class="bi bi-floppy"></i> Save</button>

            <a href="{{ $landingPage->publicUrl() }}" target="_blank" class="btn btn-sm btn-success fw-bold"><i class="bi bi-box-arrow-up-right"></i> Preview</a>
        </div>
    </div>

    <!-- MAIN BUILDER SHELL -->
    <div class="builder-shell">

        <!-- LEFT SIDEBAR -->
        <div class="builder-sidebar">
            <ul class="nav nav-tabs builder-nav-tabs nav-fill" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabElements">Elements</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabSections">Sections</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabDynamic">Data Binding</button></li>
            </ul>

            <div class="tab-content flex-grow-1 overflow-auto">
                <!-- Elements Tab -->
                <div class="tab-pane fade show active" id="tabElements">
                    <div class="p-2 text-center text-white-50 small border-bottom border-secondary">
                        💡 Drag & Drop or Click to Add Elements
                    </div>
                    <div class="builder-elements-grid">
                        <div class="builder-element-card" draggable="true" data-type="heading">
                            <i class="bi bi-type-h1 fs-3 text-info"></i>
                            <div class="small mt-1">Heading</div>
                        </div>
                        <div class="builder-element-card" draggable="true" data-type="paragraph">
                            <i class="bi bi-text-paragraph fs-3 text-info"></i>
                            <div class="small mt-1">Text</div>
                        </div>
                        <div class="builder-element-card" draggable="true" data-type="image">
                            <i class="bi bi-image fs-3 text-success"></i>
                            <div class="small mt-1">Image</div>
                        </div>
                        <div class="builder-element-card" draggable="true" data-type="button">
                            <i class="bi bi-menu-button-wide fs-3 text-warning"></i>
                            <div class="small mt-1">Button</div>
                        </div>
                        <div class="builder-element-card" draggable="true" data-type="hero">
                            <i class="bi bi-layout-header fs-3 text-primary"></i>
                            <div class="small mt-1">Hero Block</div>
                        </div>
                        <div class="builder-element-card" draggable="true" data-type="visa_card">
                            <i class="bi bi-passport fs-3 text-primary"></i>
                            <div class="small mt-1">Visa Card</div>
                        </div>
                        <div class="builder-element-card" draggable="true" data-type="destination_grid">
                            <i class="bi bi-geo-alt fs-3 text-success"></i>
                            <div class="small mt-1">Destinations</div>
                        </div>
                        <div class="builder-element-card" draggable="true" data-type="lead_form">
                            <i class="bi bi-ui-checks fs-3 text-danger"></i>
                            <div class="small mt-1">Lead Form</div>
                        </div>
                        <div class="builder-element-card" draggable="true" data-type="custom_code">
                            <i class="bi bi-code-slash fs-3 text-secondary"></i>
                            <div class="small mt-1">Custom Code</div>
                        </div>
                    </div>
                </div>

                <!-- Sections Tab -->
                <div class="tab-pane fade p-3" id="tabSections">
                    <h6 class="fw-bold text-white mb-3">Pre-designed Sections</h6>
                    <div class="d-grid gap-2">
                        @foreach($sectionCategories as $cat)
                            <div class="card bg-dark border-secondary p-2 text-white">
                                <div class="fw-bold text-info small">{{ $cat->name_en }} / {{ $cat->name_ar }}</div>
                                <div class="small text-muted">{{ $cat->sections->count() }} preset blocks</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Dynamic Data Binding Tab -->
                <div class="tab-pane fade p-3" id="tabDynamic">
                    <h6 class="fw-bold text-white mb-2">Live Travel Data Sources</h6>
                    <p class="text-muted small">Bind components with real database records.</p>
                    
                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Visa Countries</label>
                        <select class="form-select form-select-sm bg-dark text-white border-secondary" id="dynamicVisaSelect">
                            <option value="">-- Select Country --</option>
                            @foreach($visaCountries as $country)
                                <option value="{{ $country->id }}">
                                    {{ $country->name_en }} / {{ $country->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white small fw-bold">Domestic Destinations</label>
                        <select class="form-select form-select-sm bg-dark text-white border-secondary" id="dynamicDestSelect">
                            <option value="">-- Select Destination --</option>
                            @foreach($destinations as $dest)
                                <option value="{{ $dest->id }}">
                                    {{ $dest->title_en }} / {{ $dest->title_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- CENTER CANVAS -->
        <div class="builder-canvas-wrap" id="builderCanvasWrap">
            <div class="builder-canvas-frame device-desktop" id="builderCanvasStage">
                <!-- Elements will be rendered here dynamically -->
            </div>
        </div>

        <!-- RIGHT INSPECTOR (PROPERTY PANEL) -->
        <div class="builder-inspector" id="propertyInspector">
            <h6 class="fw-bold text-white mb-3"><i class="bi bi-sliders"></i> Property Inspector</h6>
            
            <div id="noSelectionNotice" class="text-muted small py-4 text-center">
                Click any element on the canvas to customize its content, style, responsiveness, and layout.
            </div>

            <div id="inspectorControls" style="display: none;">
                <!-- Content Properties -->
                <div class="property-group">
                    <div class="property-group-title">Content & Text</div>
                    <div class="mb-2">
                        <label class="form-label small text-white-50">English Content</label>
                        <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary" id="propTextEn">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-white-50">Arabic Content</label>
                        <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary text-end" dir="rtl" id="propTextAr">
                    </div>
                </div>

                <!-- Style & Colors -->
                <div class="property-group">
                    <div class="property-group-title">Style & Colors</div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small text-white-50">Background</label>
                            <input type="color" class="form-control form-control-color w-100 bg-dark border-secondary" id="propBgColor">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-white-50">Text Color</label>
                            <input type="color" class="form-control form-control-color w-100 bg-dark border-secondary" id="propTextColor">
                        </div>
                    </div>
                </div>

                <!-- Responsive Device Toggles -->
                <div class="property-group">
                    <div class="property-group-title">Device Visibility</div>
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="propHideDesktop">
                        <label class="form-check-label small text-white" for="propHideDesktop">Hide on Desktop</label>
                    </div>
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="propHideTablet">
                        <label class="form-check-label small text-white" for="propHideTablet">Hide on Tablet</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="propHideMobile">
                        <label class="form-check-label small text-white" for="propHideMobile">Hide on Mobile</label>
                    </div>
                </div>

                <!-- Advanced CSS -->
                <div class="property-group">
                    <div class="property-group-title">Advanced CSS</div>
                    <textarea class="form-control form-control-sm bg-dark text-white border-secondary font-monospace" id="propCustomCss" rows="3" placeholder="e.g. border-radius: 12px;"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Scripts & Drag-and-Drop Engine -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const initialStructure = @json($landingPage->structure ?? []);
        const pageUpdateUrl = @json(route('admin.landing-pages.update', $landingPage));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        let canvasData = initialStructure.elements || [
            {
                id: 'el_hero_1',
                type: 'hero',
                content: {
                    title_en: @json($landingPage->title_en ?: 'Discover Your Destination'),
                    title_ar: @json($landingPage->title_ar ?: 'اكتشف وجهتك السياحية'),
                    subtitle_en: 'Explore premium visa and travel services with ease.',
                    subtitle_ar: 'استمتع بأفضل خدمات التأشيرات والسفر بكل سهولة.',
                },
                style: {
                    background_color: '#1e3a8a',
                    text_color: '#ffffff'
                },
                responsive: { hide_desktop: false, hide_tablet: false, hide_mobile: false }
            }
        ];

        let selectedElementId = null;

        // Render Canvas Elements
        function renderCanvas() {
            const stage = document.getElementById('builderCanvasStage');
            stage.innerHTML = '';

            canvasData.forEach((el, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = `canvas-element ${selectedElementId === el.id ? 'selected' : ''}`;
                wrapper.dataset.id = el.id;

                // Toolbar
                const toolbar = document.createElement('div');
                toolbar.className = 'canvas-element-toolbar';
                toolbar.innerHTML = `
                    <span>${el.type}</span>
                    <button class="btn btn-sm btn-link text-white p-0" onclick="duplicateElement('${el.id}')" title="Duplicate"><i class="bi bi-copy"></i></button>
                    <button class="btn btn-sm btn-link text-white p-0" onclick="deleteElement('${el.id}')" title="Delete"><i class="bi bi-trash"></i></button>
                `;
                wrapper.appendChild(toolbar);

                // Render Content Based on Type
                const contentDiv = document.createElement('div');
                contentDiv.style.backgroundColor = el.style?.background_color || 'transparent';
                contentDiv.style.color = el.style?.text_color || 'inherit';

                if (el.type === 'hero' || el.type === 'heading') {
                    contentDiv.className = 'p-5 text-center';
                    contentDiv.innerHTML = `
                        <h1 class="display-5 fw-bold mb-2">${el.content?.title_ar || el.content?.title_en || 'عنوان جديد'}</h1>
                        <p class="lead mb-0">${el.content?.subtitle_ar || el.content?.subtitle_en || ''}</p>
                    `;
                } else if (el.type === 'paragraph') {
                    contentDiv.className = 'p-4';
                    contentDiv.innerHTML = `<p class="fs-5 mb-0">${el.content?.title_ar || el.content?.title_en || 'فقرة نصية جديدة هنا...'}</p>`;
                } else if (el.type === 'image') {
                    contentDiv.className = 'p-3 text-center';
                    contentDiv.innerHTML = `<img src="${el.content?.image_url || 'https://via.placeholder.com/800x350'}" class="img-fluid rounded shadow-sm" alt="Image">`;
                } else if (el.type === 'button') {
                    contentDiv.className = 'p-3 text-center';
                    contentDiv.innerHTML = `<button class="btn btn-primary px-5 py-3 fs-5 fw-bold shadow-sm">${el.content?.title_ar || el.content?.title_en || 'اضغط هنا للتقديم'}</button>`;
                } else if (el.type === 'visa_card') {
                    contentDiv.className = 'p-4 border rounded-4 bg-light text-dark shadow-sm text-center';
                    contentDiv.innerHTML = `
                        <div class="display-4 text-primary mb-2"><i class="bi bi-passport"></i></div>
                        <h3 class="fw-bold mb-2">${el.content?.title_ar || 'تأشيرة دخول إلكترونية'}</h3>
                        <div class="fs-4 text-success fw-bold">${el.content?.price || 'SAR 450'}</div>
                    `;
                } else if (el.type === 'destination_grid') {
                    contentDiv.className = 'p-4 bg-white text-dark';
                    contentDiv.innerHTML = `
                        <h3 class="fw-bold text-center mb-4">${el.content?.title_ar || 'أبرز الوجهات السياحية'}</h3>
                        <div class="row g-3">
                            <div class="col-6"><div class="p-3 bg-light rounded text-center border fw-bold">باريس</div></div>
                            <div class="col-6"><div class="p-3 bg-light rounded text-center border fw-bold">لندن</div></div>
                        </div>
                    `;
                } else if (el.type === 'lead_form') {
                    contentDiv.className = 'p-4 border rounded-4 bg-light text-dark shadow-sm';
                    contentDiv.innerHTML = `
                        <h4 class="fw-bold mb-3 text-center">${el.content?.title_ar || 'نموذج استقبال استفسارات العملاء'}</h4>
                        <div class="row g-3">
                            <div class="col-md-6"><input type="text" class="form-control form-control-lg" placeholder="الاسم الكامل"></div>
                            <div class="col-md-6"><input type="text" class="form-control form-control-lg" placeholder="رقم الهاتف"></div>
                            <div class="col-12"><button class="btn btn-success w-100 fw-bold py-3 fs-5">إرسال الطلب الآن</button></div>
                        </div>
                    `;
                } else if (el.type === 'custom_code') {
                    contentDiv.className = 'p-3 bg-dark text-warning font-monospace small';
                    contentDiv.innerHTML = el.content?.html || '&lt;div class="custom-html"&gt;كود مخصص Custom HTML&lt;/div&gt;';
                } else {
                    contentDiv.className = 'p-3';
                    contentDiv.innerHTML = `<div>${el.content?.title_ar || el.content?.title_en || 'Content Block'}</div>`;
                }

                wrapper.appendChild(contentDiv);

                wrapper.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectElement(el.id);
                });

                stage.appendChild(wrapper);
            });
        }

        // Add New Element to Canvas
        function addElementToCanvas(type) {
            const newId = 'el_' + type + '_' + Math.random().toString(36).substr(2, 7);
            let newEl = {
                id: newId,
                type: type,
                content: {},
                style: { background_color: 'transparent', text_color: '#1e293b' },
                responsive: { hide_desktop: false, hide_tablet: false, hide_mobile: false }
            };

            if (type === 'heading') {
                newEl.content = { title_ar: 'عنوان جديد', title_en: 'New Heading' };
            } else if (type === 'paragraph') {
                newEl.content = { title_ar: 'نص فقرة جديد استعراضي هنا...', title_en: 'New paragraph content here...' };
            } else if (type === 'button') {
                newEl.content = { title_ar: 'احجز الآن', title_en: 'Book Now' };
                newEl.style = { background_color: '#0284c7', text_color: '#ffffff' };
            } else if (type === 'hero') {
                newEl.content = { title_ar: 'عنوان الهيدر الرئيسي', title_en: 'Hero Heading', subtitle_ar: 'وصف جذب للعملاء والاستفسارات' };
                newEl.style = { background_color: '#0f172a', text_color: '#ffffff' };
            } else if (type === 'visa_card') {
                newEl.content = { title_ar: 'تأشيرة دخول إلكترونية', price: 'SAR 450' };
            } else if (type === 'destination_grid') {
                newEl.content = { title_ar: 'أبرز الوجهات السياحية 2026' };
            } else if (type === 'lead_form') {
                newEl.content = { title_ar: 'نموذج استقبال الاستفسارات والطلبات' };
            } else if (type === 'image') {
                newEl.content = { image_url: 'https://via.placeholder.com/800x400' };
            } else if (type === 'custom_code') {
                newEl.content = { html: '<div class="p-3 bg-light text-dark">كود مخصص Custom HTML</div>' };
            }

            canvasData.push(newEl);
            renderCanvas();
            selectElement(newId);
        }

        // HTML5 DRAG AND DROP ENGINE
        const elementCards = document.querySelectorAll('.builder-element-card');
        const canvasStage = document.getElementById('builderCanvasStage');
        const canvasWrap = document.getElementById('builderCanvasWrap');

        elementCards.forEach(card => {
            // Click to Add
            card.addEventListener('click', () => {
                const type = card.dataset.type;
                if (type) addElementToCanvas(type);
            });

            // Drag Start
            card.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', card.dataset.type);
                e.dataTransfer.effectAllowed = 'copy';
            });
        });

        // Drag Over (Crucial: preventDefault allows drop!)
        [canvasStage, canvasWrap].forEach(target => {
            target.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
                canvasStage.classList.add('drag-over');
            });

            target.addEventListener('dragleave', () => {
                canvasStage.classList.remove('drag-over');
            });

            target.addEventListener('drop', (e) => {
                e.preventDefault();
                canvasStage.classList.remove('drag-over');
                const elementType = e.dataTransfer.getData('text/plain');
                if (elementType) {
                    addElementToCanvas(elementType);
                }
            });
        });

        function selectElement(id) {
            selectedElementId = id;
            const el = canvasData.find(item => item.id === id);
            
            document.getElementById('noSelectionNotice').style.display = el ? 'none' : 'block';
            document.getElementById('inspectorControls').style.display = el ? 'block' : 'none';

            if (el) {
                document.getElementById('propTextEn').value = el.content?.title_en || '';
                document.getElementById('propTextAr').value = el.content?.title_ar || '';
                document.getElementById('propBgColor').value = el.style?.background_color || '#ffffff';
                document.getElementById('propTextColor').value = el.style?.text_color || '#000000';
                document.getElementById('propHideDesktop').checked = !!el.responsive?.hide_desktop;
                document.getElementById('propHideTablet').checked = !!el.responsive?.hide_tablet;
                document.getElementById('propHideMobile').checked = !!el.responsive?.hide_mobile;
            }

            renderCanvas();
        }

        function duplicateElement(id) {
            const index = canvasData.findIndex(item => item.id === id);
            if (index !== -1) {
                const clone = JSON.parse(JSON.stringify(canvasData[index]));
                clone.id = 'el_' + Math.random().toString(36).substr(2, 9);
                canvasData.splice(index + 1, 0, clone);
                renderCanvas();
            }
        }

        function deleteElement(id) {
            canvasData = canvasData.filter(item => item.id !== id);
            if (selectedElementId === id) selectElement(null);
            renderCanvas();
        }

        // Save Canvas AJAX
        document.getElementById('btnSaveCanvas').addEventListener('click', async () => {
            const statusInd = document.getElementById('saveStatusIndicator');
            statusInd.textContent = 'Saving...';
            statusInd.className = 'badge bg-warning text-dark font-monospace';

            try {
                const response = await fetch(pageUpdateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        internal_name: @json($landingPage->internal_name),
                        slug: @json($landingPage->slug),
                        status: @json($landingPage->status),
                        is_active: @json($landingPage->is_active),
                        header_mode: @json($landingPage->header_mode),
                        footer_mode: @json($landingPage->footer_mode),
                        structure: { elements: canvasData }
                    })
                });

                if (response.ok) {
                    statusInd.textContent = 'Saved';
                    statusInd.className = 'badge bg-success font-monospace';
                } else {
                    throw new Error('Save failed');
                }
            } catch (err) {
                statusInd.textContent = 'Error';
                statusInd.className = 'badge bg-danger font-monospace';
            }
        });

        // Device Mode Switcher
        document.querySelectorAll('.builder-device-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.builder-device-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const device = btn.dataset.device;
                const stage = document.getElementById('builderCanvasStage');
                stage.className = `builder-canvas-frame device-${device}`;
            });
        });

        // Property Listeners
        ['propTextEn', 'propTextAr'].forEach(id => {
            document.getElementById(id).addEventListener('input', (e) => {
                if (!selectedElementId) return;
                const el = canvasData.find(item => item.id === selectedElementId);
                if (el) {
                    if (!el.content) el.content = {};
                    if (id === 'propTextEn') el.content.title_en = e.target.value;
                    if (id === 'propTextAr') el.content.title_ar = e.target.value;
                    renderCanvas();
                }
            });
        });

        renderCanvas();
    </script>
</body>
</html>
