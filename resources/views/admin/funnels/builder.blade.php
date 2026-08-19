<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ Visual Funnel Builder Pro Suite | {{ $funnel->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 & Google Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <!-- SortableJS for Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <style>
        :root {
            --fb-topbar-h: 62px;
            --fb-sidebar-w: 340px;
            --fb-inspector-w: 420px;
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
            font-family: 'Tajawal', system-ui, -apple-system, sans-serif;
            background: var(--fb-bg-dark);
            color: #f8fafc;
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
            max-width: 720px;
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
            background: #eff6ff;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.15);
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

        /* Timer Flip Box */
        .timer-box-digit {
            background: #1e293b;
            color: #f8fafc;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 22px;
            font-weight: 800;
            min-width: 44px;
            text-align: center;
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
<div id="fb_toast">💾 تم حفظ التعديلات بنجاح!</div>

<!-- MEDIA PICKER MODAL -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold">🖼️ اختيار صورة من مكتبة الموقع</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-muted">إدخال رابط صورة مخصص (Direct Image URL)</label>
                    <div class="input-group">
                        <input type="url" class="form-control form-control-dark" id="modal_custom_img_url" placeholder="https://example.com/image.jpg">
                        <button type="button" class="btn btn-primary" onclick="applyCustomImageUrl()">تطبيق الرابط</button>
                    </div>
                </div>

                <h6 class="fw-bold small text-muted mb-2">أو اختر من المعرض المقترح:</h6>
                <div class="row g-2" id="modal_presets_container" style="max-height: 280px; overflow-y: auto;">
                    <!-- Preset sample images -->
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
        <a href="{{ route('admin.funnels.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
            ← الفانلات
        </a>
        <div>
            <h6 class="mb-0 fw-bold text-white d-flex align-items-center gap-2">
                <span>{{ $funnel->name }}</span>
                @if($funnel->status === 'published')
                    <span class="badge bg-success small">Live</span>
                @else
                    <span class="badge bg-secondary small">Draft</span>
                @endif
            </h6>
            <small class="text-muted">/f/{{ $funnel->slug }}</small>
        </div>
    </div>

    <!-- Viewport Switcher -->
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
        <button type="button" class="btn btn-outline-light btn-sm rounded-3 d-flex align-items-center gap-1" onclick="openLivePreview()">
            <iconify-icon icon="solar:eye-bold" width="16"></iconify-icon>
            <span>معاينة حية 👁️</span>
        </button>
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
    
    <!-- LEFT PANEL: Steps & Involve.me Palette -->
    <aside class="fb-sidebar p-3">
        
        <!-- Steps Header -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-uppercase text-muted fw-bold small mb-0">📌 خطوات الفانل (Steps)</h6>
            <button type="button" class="btn btn-primary btn-sm py-1 px-2 rounded-2 fw-bold" onclick="addNewStep()">
                ➕ خطوة
            </button>
        </div>
        <div id="steps_list_wrapper" class="mb-4">
            <!-- Rendered by JS -->
        </div>

        <hr class="border-secondary my-3">

        <!-- Search Bar -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-uppercase text-muted fw-bold small mb-0">🧩 مكتبة العناصر</h6>
        </div>
        <div class="mb-3">
            <input type="text" class="form-control form-control-sm form-control-dark" id="element_search_input" placeholder="🔍 بحث عن عنصر..." oninput="filterPaletteElements(this.value)">
        </div>

        <!-- ACCORDION PALETTE -->
        <div class="accordion fb-category-accordion" id="palette_accordion">
            
            <!-- 1. Choices -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#cat_choices">
                        <iconify-icon icon="solar:checklist-minimalistic-bold-duotone" class="text-primary me-2" width="18"></iconify-icon>
                        <span>Choices (أنواع الاختيار)</span>
                    </button>
                </h2>
                <div id="cat_choices" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <div class="row g-2">
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="single_choice" onclick="addElementToCurrentStep('single_choice')"><iconify-icon icon="solar:document-text-bold-duotone" class="text-primary"></iconify-icon> Single Choice</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="multiple_choice" onclick="addElementToCurrentStep('multiple_choice')"><iconify-icon icon="solar:list-check-bold-duotone" class="text-primary"></iconify-icon> Multiple Choice</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="radio_choice" onclick="addElementToCurrentStep('radio_choice')"><iconify-icon icon="solar:record-circle-bold-duotone" class="text-primary"></iconify-icon> Radio Choice</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="checkbox_choice" onclick="addElementToCurrentStep('checkbox_choice')"><iconify-icon icon="solar:check-square-bold-duotone" class="text-primary"></iconify-icon> Checkbox Choice</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="yes_no" onclick="addElementToCurrentStep('yes_no')"><iconify-icon icon="solar:shield-check-bold-duotone" class="text-primary"></iconify-icon> Yes/No</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="image_choice" onclick="addElementToCurrentStep('image_choice')"><iconify-icon icon="solar:gallery-bold-duotone" class="text-primary"></iconify-icon> Image Cards</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="dropdown" onclick="addElementToCurrentStep('dropdown')"><iconify-icon icon="solar:menu-dots-square-bold-duotone" class="text-primary"></iconify-icon> Dropdown</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Rating & Ranking -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cat_rating">
                        <iconify-icon icon="solar:star-fall-bold-duotone" class="text-warning me-2" width="18"></iconify-icon>
                        <span>Rating & Ranking (التقييم والسلايدر)</span>
                    </button>
                </h2>
                <div id="cat_rating" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="row g-2">
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="rating" onclick="addElementToCurrentStep('rating')"><iconify-icon icon="solar:star-bold-duotone" class="text-warning"></iconify-icon> Rating Stars</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="slider" onclick="addElementToCurrentStep('slider')"><iconify-icon icon="solar:tuning-square-2-bold-duotone" class="text-warning"></iconify-icon> Slider</div></div>
                            <div class="col-12"><div class="fb-element-pill" draggable="true" data-type="nps" onclick="addElementToCurrentStep('nps')"><iconify-icon icon="solar:like-shapes-bold-duotone" class="text-warning"></iconify-icon> Net Promoter Score ® (0-10)</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Collecting Data -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cat_collecting">
                        <iconify-icon icon="solar:chat-round-line-bold-duotone" class="text-info me-2" width="18"></iconify-icon>
                        <span>Collecting Data (جمع البيانات)</span>
                    </button>
                </h2>
                <div id="cat_collecting" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="row g-2">
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="short_answer" onclick="addElementToCurrentStep('short_answer')"><iconify-icon icon="solar:text-field-bold-duotone" class="text-info"></iconify-icon> Short Answer</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="long_answer" onclick="addElementToCurrentStep('long_answer')"><iconify-icon icon="solar:chat-square-bold-duotone" class="text-info"></iconify-icon> Long Answer</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="number_input" onclick="addElementToCurrentStep('number_input')"><iconify-icon icon="solar:calculator-bold-duotone" class="text-info"></iconify-icon> Number Input</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="currency" onclick="addElementToCurrentStep('currency')"><iconify-icon icon="solar:dollar-bold-duotone" class="text-info"></iconify-icon> Currency (SAR/$)</div></div>
                            <div class="col-12"><div class="fb-element-pill" draggable="true" data-type="file_upload" onclick="addElementToCurrentStep('file_upload')"><iconify-icon icon="solar:upload-track-bold-duotone" class="text-info"></iconify-icon> File Upload (رفع ملف)</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Contact Info -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cat_contact">
                        <iconify-icon icon="solar:user-id-bold-duotone" class="text-success me-2" width="18"></iconify-icon>
                        <span>Contact Info (بيانات الاتصال)</span>
                    </button>
                </h2>
                <div id="cat_contact" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="row g-2">
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="contact_form" onclick="addElementToCurrentStep('contact_form')"><iconify-icon icon="solar:card-2-bold-duotone" class="text-success"></iconify-icon> Contact Form</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="email" onclick="addElementToCurrentStep('email')"><iconify-icon icon="solar:letter-bold-duotone" class="text-success"></iconify-icon> Email</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="phone" onclick="addElementToCurrentStep('phone')"><iconify-icon icon="solar:phone-calling-rounded-bold-duotone" class="text-success"></iconify-icon> Phone/WhatsApp</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="address" onclick="addElementToCurrentStep('address')"><iconify-icon icon="solar:map-point-bold-duotone" class="text-success"></iconify-icon> Address</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="country" onclick="addElementToCurrentStep('country')"><iconify-icon icon="solar:flag-bold-duotone" class="text-success"></iconify-icon> Country</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="website" onclick="addElementToCurrentStep('website')"><iconify-icon icon="solar:global-bold-duotone" class="text-success"></iconify-icon> Website</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Time & Scheduling -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cat_time">
                        <iconify-icon icon="solar:calendar-bold-duotone" class="text-danger me-2" width="18"></iconify-icon>
                        <span>Time & Scheduling (المواعيد والمؤقت)</span>
                    </button>
                </h2>
                <div id="cat_time" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="row g-2">
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="date_picker" onclick="addElementToCurrentStep('date_picker')"><iconify-icon icon="solar:calendar-date-bold-duotone" class="text-danger"></iconify-icon> Date Picker</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="schedule" onclick="addElementToCurrentStep('schedule')"><iconify-icon icon="solar:calendar-mark-bold-duotone" class="text-danger"></iconify-icon> Appointments</div></div>
                            <div class="col-12"><div class="fb-element-pill" draggable="true" data-type="timer" onclick="addElementToCurrentStep('timer')"><iconify-icon icon="solar:clock-circle-bold-duotone" class="text-danger"></iconify-icon> Countdown Timer (مؤقت تنازلي)</div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. Static Elements -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cat_static">
                        <iconify-icon icon="solar:text-bold-duotone" class="text-warning me-2" width="18"></iconify-icon>
                        <span>Static Elements (العناصر الثابتة)</span>
                    </button>
                </h2>
                <div id="cat_static" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="row g-2">
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="heading" onclick="addElementToCurrentStep('heading')"><iconify-icon icon="solar:text-bold-duotone" class="text-warning"></iconify-icon> Heading</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="paragraph" onclick="addElementToCurrentStep('paragraph')"><iconify-icon icon="solar:notes-bold-duotone" class="text-warning"></iconify-icon> Paragraph</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="table" onclick="addElementToCurrentStep('table')"><iconify-icon icon="solar:table-bold-duotone" class="text-warning"></iconify-icon> Table</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="testimonials" onclick="addElementToCurrentStep('testimonials')"><iconify-icon icon="solar:chat-dots-bold-duotone" class="text-warning"></iconify-icon> Testimonials</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="faqs" onclick="addElementToCurrentStep('faqs')"><iconify-icon icon="solar:question-circle-bold-duotone" class="text-warning"></iconify-icon> FAQs</div></div>
                            <div class="col-6"><div class="fb-element-pill" draggable="true" data-type="coupon_code" onclick="addElementToCurrentStep('coupon_code')"><iconify-icon icon="solar:ticket-bold-duotone" class="text-warning"></iconify-icon> Coupon Code</div></div>
                        </div>
                    </div>
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

    <!-- RIGHT PANEL: Inspector -->
    <aside class="fb-inspector p-3">
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
            
            <!-- TAB 1: Properties -->
            <div class="tab-pane fade show active" id="tab_props">
                <div id="inspector_element_panel">
                    <div class="text-center text-muted py-5">
                        <iconify-icon icon="solar:cursor-bold-duotone" width="48" class="opacity-50 mb-2"></iconify-icon>
                        <p class="small">انقر على أي عنصر داخل الشاشة لتعديل خصائصه.</p>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Design & Scoring Settings -->
            <div class="tab-pane fade" id="tab_design">
                <h6 class="fw-bold mb-3">🎨 المظهر ونظام التقييم</h6>
                
                <!-- Scoring Toggle -->
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="scoring_enabled_checkbox" onchange="toggleScoringMode(this.checked)" {{ ($funnel->design_settings['scoring_enabled'] ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold small text-white" for="scoring_enabled_checkbox">
                            تفعيل نظام الدرجات وحساب الأهلية (Quiz / Scoring)
                        </label>
                    </div>
                    <small class="text-muted d-block" style="font-size: 11px;">
                        إذا تم إيقافه، سيعمل الفانل كنموذج تجميع بيانات وليد جنريشن عادي بدون احتساب درجات أو إظهار نسبة مئوية.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">اللون الأساسي (Primary Color)</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" class="form-control form-control-color border-0 p-0" id="design_primary_color" value="{{ $funnel->design_settings['primary_color'] ?? '#2563eb' }}" onchange="updateDesignSettings()">
                        <input type="text" class="form-control form-control-sm form-control-dark" id="design_primary_color_text" value="{{ $funnel->design_settings['primary_color'] ?? '#2563eb' }}" onchange="document.getElementById('design_primary_color').value=this.value; updateDesignSettings()">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">نوع الخط (Font Family)</label>
                    <select class="form-select form-select-sm form-select-dark" id="design_font_family" onchange="updateDesignSettings()">
                        <option value="Tajawal, sans-serif">Tajawal (عربي أنيق)</option>
                        <option value="Cairo, sans-serif">Cairo (عصري)</option>
                        <option value="System">System Default</option>
                    </select>
                </div>
            </div>

            <!-- TAB 3: Results -->
            <div class="tab-pane fade" id="tab_results">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">🏆 شاشات النتائج والأهلية</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" onclick="addNewResult()">➕ إضافة نتيجة</button>
                </div>
                <div id="results_list_wrapper">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- TAB 4: Integrations -->
            <div class="tab-pane fade" id="tab_integrations">
                <h6 class="fw-bold mb-2">⚡ مزامنة CRM و البكسلات</h6>
                
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

                <div class="p-3 bg-dark rounded-3 border border-secondary">
                    <h6 class="fw-bold small mb-2">🎯 بكسلات التتبع</h6>
                    <div class="mb-2">
                        <label class="form-label small text-muted">Meta (Facebook) Pixel ID</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="meta_pixel_input" value="{{ $funnel->tracking_settings['meta_pixel_id'] ?? '' }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted">Google Analytics (GA4) ID</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="ga4_input" value="{{ $funnel->tracking_settings['ga4_id'] ?? '' }}">
                    </div>
                    <div>
                        <label class="form-label small text-muted">TikTok Pixel ID</label>
                        <input type="text" class="form-control form-control-sm form-control-dark" id="tiktok_input" value="{{ $funnel->tracking_settings['tiktok_pixel_id'] ?? '' }}">
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
    const funnelData = @json($funnel);
    if (!funnelData.steps) funnelData.steps = [];
    if (!funnelData.results) funnelData.results = [];
    if (!funnelData.design_settings) funnelData.design_settings = { primary_color: '#2563eb', scoring_enabled: true };

    // Normalize elements is_required
    funnelData.steps.forEach(step => {
        (step.elements || []).forEach(el => {
            if (el.is_required === undefined) {
                el.is_required = el.properties?.is_required || false;
            }
        });
    });

    let activeStepIndex = 0;
    let selectedElementIndex = null;
    let currentEditingOptionIdx = null;

    const WORLD_CURRENCIES = [
        { code: 'SAR', label: '🇸🇦 SAR (ريال سعودي)' },
        { code: 'USD', label: '🇺🇸 USD (دولار أمريكي)' },
        { code: 'EUR', label: '🇪🇺 EUR (يورو أوروبي)' },
        { code: 'AED', label: '🇦🇪 AED (درهم إماراتي)' },
        { code: 'EGP', label: '🇪🇬 EGP (جنيه مصري)' },
        { code: 'KWD', label: '🇰🇼 KWD (دينار كويتي)' },
        { code: 'QAR', label: '🇶🇦 QAR (ريال قطري)' },
        { code: 'BHD', label: '🇧🇭 BHD (دينار بحريني)' },
        { code: 'OMR', label: '🇴🇲 OMR (ريال عماني)' },
        { code: 'GBP', label: '🇬🇧 GBP (جنيه إسترليني)' },
        { code: 'CAD', label: '🇨🇦 CAD (دولار كندي)' },
        { code: 'AUD', label: '🇦🇺 AUD (دولار أسترالي)' },
        { code: 'TRY', label: '🇹🇷 TRY (ليرة تركية)' },
        { code: 'MAD', label: '🇲🇦 MAD (درهم مغربي)' },
        { code: 'JOD', label: '🇯🇴 JOD (دينار أردني)' },
    ];

    document.addEventListener('DOMContentLoaded', () => {
        renderStepsList();
        renderCanvas();
        renderResultsList();
        setupDragAndDrop();
    });

    function filterPaletteElements(query) {
        query = query.toLowerCase().trim();
        document.querySelectorAll('.fb-element-pill').forEach(pill => {
            const text = pill.innerText.toLowerCase();
            pill.parentElement.style.display = (!query || text.includes(query)) ? 'block' : 'none';
        });
    }

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
                    is_required: false,
                    question_key: `q_${Math.random().toString(36).substr(2, 6)}`,
                    properties: {
                        options: [
                            { label: 'الخيار الأول', value: 'Option 1', score: 10, image_url: '' },
                            { label: 'الخيار الثاني', value: 'Option 2', score: 20, image_url: '' },
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
                const reqStar = el.is_required ? '<span class="text-danger ms-1 fw-bold">*</span>' : '';
                const hasScoring = funnelData.design_settings?.scoring_enabled !== false;

                html += `
                    <div class="canvas-element-item ${isSelected ? 'selected' : ''}" data-el-index="${eIdx}" onclick="inspectElement(${eIdx})">
                        <div class="canvas-element-toolbar">
                            <span>${el.element_type} ${el.is_required ? '(إجباري)' : ''}</span>
                            <button type="button" class="btn btn-sm text-white p-0" onclick="event.stopPropagation(); duplicateElement(${eIdx})" title="تكرار">
                                <iconify-icon icon="solar:copy-bold" width="13"></iconify-icon>
                            </button>
                            <button type="button" class="btn btn-sm text-white p-0" onclick="event.stopPropagation(); deleteElement(${eIdx})" title="حذف">
                                <iconify-icon icon="solar:trash-bin-trash-bold" width="13"></iconify-icon>
                            </button>
                        </div>
                `;

                // 1. Heading / Text
                if (el.element_type === 'heading') {
                    html += `<h3 class="fw-bold mb-0 text-primary">${escapeHtml(el.label || 'عنوان توضيحي')}${reqStar}</h3>`;
                } else if (el.element_type === 'text' || el.element_type === 'paragraph') {
                    html += `<p class="text-muted mb-0 fs-6">${escapeHtml(el.label || 'نص فقرة توضيحية...')}</p>`;
                
                // 2. Radio Choice (Standard modern Radio)
                } else if (el.element_type === 'radio_choice') {
                    html += `<label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'اختر إجابة واحدة:')}${reqStar}</label>`;
                    html += '<div class="d-flex flex-column gap-2">';
                    (el.properties?.options || []).forEach(opt => {
                        html += `
                            <div class="p-3 border rounded-3 bg-white d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:record-circle-bold" class="text-primary fs-5"></iconify-icon>
                                    <span class="fw-semibold">${escapeHtml(opt.label || opt.value || '')}</span>
                                </div>
                                ${hasScoring && (opt.score || 0) > 0 ? `<span class="badge bg-primary-subtle text-primary small">+${opt.score} نقطة</span>` : ''}
                            </div>
                        `;
                    });
                    html += '</div>';

                // 3. Checkbox Choice
                } else if (el.element_type === 'checkbox_choice') {
                    html += `<label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'حدد جميع الخيارات:')}${reqStar}</label>`;
                    html += '<div class="d-flex flex-column gap-2">';
                    (el.properties?.options || []).forEach(opt => {
                        html += `
                            <div class="p-3 border rounded-3 bg-white d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="solar:check-square-bold" class="text-primary fs-5"></iconify-icon>
                                    <span class="fw-semibold">${escapeHtml(opt.label || opt.value || '')}</span>
                                </div>
                                ${hasScoring && (opt.score || 0) > 0 ? `<span class="badge bg-primary-subtle text-primary small">+${opt.score} نقطة</span>` : ''}
                            </div>
                        `;
                    });
                    html += '</div>';

                // 4. Single / Multiple Choice
                } else if (el.element_type === 'single_choice' || el.element_type === 'multiple_choice' || el.element_type === 'yes_no') {
                    html += `<label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'سؤال الاختيار:')}${reqStar}</label>`;
                    html += '<div class="d-flex flex-column gap-2">';
                    (el.properties?.options || []).forEach(opt => {
                        html += `
                            <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">${escapeHtml(opt.label || opt.value || '')}</span>
                                ${hasScoring && (opt.score || 0) > 0 ? `<span class="badge bg-primary-subtle text-primary small">+${opt.score} نقطة</span>` : ''}
                            </div>
                        `;
                    });
                    html += '</div>';

                // 5. Image Choice (With image thumbnails)
                } else if (['image_choice', 'single_image_choice', 'multiple_image_choice'].includes(el.element_type)) {
                    html += `<label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'اختر بطاقة صورة:')}${reqStar}</label>`;
                    html += '<div class="row g-2">';
                    (el.properties?.options || []).forEach(opt => {
                        html += `
                            <div class="col-6">
                                <div class="p-3 border rounded-3 text-center bg-light">
                                    ${opt.image_url ? `<img src="${opt.image_url}" class="rounded-3 mb-2" style="max-height: 80px; width: 100%; object-fit: cover;">` : '<iconify-icon icon="solar:gallery-bold-duotone" width="36" class="text-primary mb-1"></iconify-icon>'}
                                    <div class="fw-bold small">${escapeHtml(opt.label || '')}</div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';

                // 6. Contact Form
                } else if (el.element_type === 'contact_form') {
                    html += `
                        <label class="fw-bold mb-2 text-primary d-block">${escapeHtml(el.label || 'نموذج بيانات التواصل (CRM):')}${reqStar}</label>
                        <div class="bg-light p-3 rounded-3 border">
                            <input type="text" class="form-control mb-2" placeholder="الاسم الكريم *" disabled>
                            <input type="tel" class="form-control mb-2" placeholder="رقم الواتساب *" disabled>
                            <input type="email" class="form-control" placeholder="البريد الإلكتروني" disabled>
                        </div>
                    `;

                // 7. Dedicated Contact Inputs
                } else if (el.element_type === 'email') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'البريد الإلكتروني:')}${reqStar}</label>
                        <div class="input-group"><span class="input-group-text bg-light">✉️</span><input type="email" class="form-control" placeholder="name@example.com" disabled></div>
                    `;
                } else if (el.element_type === 'phone') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'رقم الواتساب / الجوال:')}${reqStar}</label>
                        <div class="input-group"><span class="input-group-text bg-light">📱</span><input type="tel" class="form-control" placeholder="05XXXXXXXX" disabled></div>
                    `;
                } else if (el.element_type === 'address') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'العنوان ومقر الإقامة:')}${reqStar}</label>
                        <div class="input-group"><span class="input-group-text bg-light">📍</span><input type="text" class="form-control" placeholder="المدينة، الحي" disabled></div>
                    `;
                } else if (el.element_type === 'website') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'الموقع الإلكتروني:')}${reqStar}</label>
                        <div class="input-group"><span class="input-group-text bg-light">🌐</span><input type="url" class="form-control" placeholder="https://example.com" disabled></div>
                    `;
                } else if (el.element_type === 'country') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'اختر الدولة:')}${reqStar}</label>
                        <div class="input-group"><span class="input-group-text bg-light">🌍</span><select class="form-select bg-light" disabled><option>${escapeHtml(el.properties?.options?.[0]?.label || '🇸🇦 المملكة العربية السعودية')}</option></select></div>
                    `;

                // 8. Slider (Customizable Currency & Range)
                } else if (el.element_type === 'slider' || el.element_type === 'currency') {
                    const min = el.properties?.min || 0;
                    const max = el.properties?.max || 50000;
                    const unit = el.properties?.show_currency !== false ? (el.properties?.currency_code || 'SAR') : (el.properties?.custom_unit || '');
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'حدد القيمة المطلوبة:')}${reqStar}</label>
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <h4 class="fw-bold text-primary mb-1">${(min + max)/2} ${unit}</h4>
                            <input type="range" class="form-range" disabled>
                            <div class="d-flex justify-content-between text-muted small"><span>${min} ${unit}</span><span>${max} ${unit}</span></div>
                        </div>
                    `;

                // 9. File Upload
                } else if (el.element_type === 'file_upload') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'تحميل المستند أو المرفق:')}${reqStar}</label>
                        <div class="p-4 border border-dashed rounded-3 text-center bg-light">
                            <iconify-icon icon="solar:upload-track-bold-duotone" width="36" class="text-primary mb-1"></iconify-icon>
                            <p class="mb-0 fw-bold small text-dark">انقر لاختيار ملف أو اسحبه هنا</p>
                            <small class="text-muted">PDF, JPG, PNG حتى 10MB</small>
                        </div>
                    `;

                // 10. Date / Appointment Picker
                } else if (['date_picker', 'date_time', 'schedule'].includes(el.element_type)) {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'تحديد التاريخ والموعد:')}${reqStar}</label>
                        <div class="input-group mb-2"><span class="input-group-text bg-light">📅</span><input type="date" class="form-control" disabled></div>
                        <div class="d-flex gap-2"><span class="badge bg-light text-dark border p-2">صباحاً (09:00 - 12:00)</span><span class="badge bg-light text-dark border p-2">مساءً (04:00 - 08:00)</span></div>
                    `;

                // 11. Countdown Timer
                } else if (el.element_type === 'timer' || el.element_type === 'page_timer') {
                    const mins = el.properties?.duration_minutes || 15;
                    html += `
                        <div class="p-3 bg-dark text-white rounded-3 border text-center">
                            <span class="small text-warning fw-bold d-block mb-1">⏰ ${escapeHtml(el.label || 'احجز الآن! هذا العرض ساري لمدة:')}</span>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <div class="timer-box-digit">${String(mins).padStart(2, '0')}</div>
                                <span class="fs-4 fw-bold text-warning">:</span>
                                <div class="timer-box-digit">00</div>
                            </div>
                        </div>
                    `;

                // 12. Rating & NPS
                } else if (el.element_type === 'rating') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'التقييم:')}${reqStar}</label>
                        <div class="d-flex gap-2 text-warning fs-3">⭐⭐⭐⭐⭐</div>
                    `;
                } else if (el.element_type === 'nps') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'مقياس الرضا NPS (0-10):')}${reqStar}</label>
                        <div class="d-flex gap-1 justify-content-between"><button class="btn btn-sm btn-outline-secondary">0</button><button class="btn btn-sm btn-outline-secondary">5</button><button class="btn btn-sm btn-primary">10</button></div>
                    `;

                // 13. Table
                } else if (el.element_type === 'table') {
                    html += `
                        <label class="fw-bold mb-2 text-dark d-block">${escapeHtml(el.label || 'جدول مقارنة الأسعار:')}</label>
                        <table class="table table-sm table-bordered bg-light mb-0 small text-center">
                            <tr class="table-primary"><th>الباقة</th><th>الخدمات</th><th>السعر</th></tr>
                            <tr><td>الأساسية</td><td>طلب التأشيرة والموعد</td><td>250 SAR</td></tr>
                        </table>
                    `;

                // 14. Testimonials
                } else if (el.element_type === 'testimonials') {
                    html += `
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <p class="fst-italic small mb-1">"خدمة استثنائية وسرعة فائقة!"</p>
                            <strong class="text-primary small">— فهد الشمري ⭐⭐⭐⭐⭐</strong>
                        </div>
                    `;

                // 15. FAQs
                } else if (el.element_type === 'faqs') {
                    html += `
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="fw-bold small text-primary mb-1">❓ ما هي شروط التأشيرة؟</div>
                            <div class="text-muted small">جواز سفر ساري المفعول وحساب بنكي...</div>
                        </div>
                    `;

                // 16. Coupon
                } else if (el.element_type === 'coupon_code') {
                    html += `
                        <div class="p-3 bg-light rounded-3 border border-dashed text-center">
                            <h5 class="fw-bold text-danger mb-0">كود الخصم: WAVE2026 (خصم 20%)</h5>
                        </div>
                    `;

                // 17. Textarea
                } else if (el.element_type === 'long_answer') {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'ملاحظات وتفاصيل:')}${reqStar}</label>
                        <textarea class="form-control" rows="3" placeholder="اكتب التفاصيل هنا..." disabled></textarea>
                    `;

                } else {
                    html += `
                        <label class="fw-bold mb-2 d-block text-dark">${escapeHtml(el.label || 'حقل إدخال:')}${reqStar}</label>
                        <input type="text" class="form-control" placeholder="اكتب هنا..." disabled>
                    `;
                }

                html += '</div>';
            });
        } else {
            html += `
                <div class="alert alert-light border border-dashed text-center py-5 text-muted rounded-4 my-3">
                    <iconify-icon icon="solar:add-circle-bold-duotone" width="44" class="text-primary opacity-50 mb-2"></iconify-icon>
                    <p class="mb-0 fw-bold">هذه الخطوة فارغة</p>
                    <small>اسحب أي عنصر من القائمة على اليمين أو انقر عليه لإضافته هنا فوراً.</small>
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
            properties: {}
        };

        if (['single_choice', 'multiple_choice', 'radio_choice', 'checkbox_choice', 'image_choice', 'single_image_choice', 'multiple_image_choice', 'dropdown'].includes(type)) {
            newElement.properties.options = [
                { label: 'الخيار الأول', value: 'Option 1', score: 10, image_url: '' },
                { label: 'الخيار الثاني', value: 'Option 2', score: 20, image_url: '' },
                { label: 'الخيار الثالث', value: 'Option 3', score: 30, image_url: '' },
            ];
        } else if (type === 'yes_no') {
            newElement.properties.options = [
                { label: 'نعم (Yes)', value: 'Yes', score: 20 },
                { label: 'لا (No)', value: 'No', score: 0 },
            ];
        } else if (type === 'country') {
            newElement.properties.options = [
                { label: '🇸🇦 المملكة العربية السعودية', value: 'Saudi Arabia' },
                { label: '🇦🇪 الإمارات العربية المتحدة', value: 'UAE' },
                { label: '🇪🇬 جمهورية مصر العربية', value: 'Egypt' },
                { label: '🇰🇼 دولة الكويت', value: 'Kuwait' },
                { label: '🇶🇦 دولة قطر', value: 'Qatar' },
            ];
        } else if (type === 'slider' || type === 'currency') {
            newElement.properties = {
                min: 0,
                max: 50000,
                step: 1000,
                show_currency: true,
                currency_code: 'SAR',
                custom_unit: 'ريال'
            };
        } else if (type === 'timer' || type === 'page_timer') {
            newElement.properties = {
                duration_minutes: 15,
                urgency_message: 'احجز الآن! هذا العرض ساري لمدة:'
            };
        }

        currentStep.elements.push(newElement);
        selectedElementIndex = currentStep.elements.length - 1;
        renderCanvas();
        inspectElement(selectedElementIndex);
    }

    function getDefaultLabelForType(type) {
        switch(type) {
            case 'single_choice': return 'ما هو اختيارك المفضل؟';
            case 'radio_choice': return 'اختر إجابة واحدة:';
            case 'multiple_choice': case 'checkbox_choice': return 'اختر جميع الخيارات المناسبة:';
            case 'yes_no': return 'هل ينطبق عليك هذا الشرط؟';
            case 'image_choice': case 'single_image_choice': case 'multiple_image_choice': return 'اختر البطاقة الأنسب لك:';
            case 'dropdown': return 'اختر من القائمة المنسدلة:';
            case 'contact_form': return 'بيانات التواصل لاستلام التقرير:';
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
            case 'heading': return 'عنوان رئيسي جذاب';
            case 'text': case 'paragraph': return 'اكتب هنا تفاصيل إضافية لتوضيح السؤال.';
            case 'table': return 'جدول مقارنة الأسعار والباقات';
            case 'testimonials': return 'آراء وتجارب العملاء السابقين';
            case 'faqs': return 'الأسئلة الشائعة وتفاصيل الخدمة';
            case 'coupon_code': return 'كود خصم فوري مخصص لك';
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

    // ── 4. INSPECTOR: PROPERTIES ─────────────────────────────────────────────
    function inspectElement(eIdx) {
        selectedElementIndex = eIdx;
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[eIdx]) return;
        const el = currentStep.elements[eIdx];

        const propsTabBtn = document.querySelector('#inspector_tabs button[data-bs-target="#tab_props"]');
        if (propsTabBtn) {
            bootstrap.Tab.getOrCreateInstance(propsTabBtn).show();
        }

        const panel = document.getElementById('inspector_element_panel');
        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-primary text-uppercase">${el.element_type}</span>
                <button type="button" class="btn btn-sm btn-outline-danger py-0" onclick="deleteElement(${eIdx})">
                    <iconify-icon icon="solar:trash-bin-bold"></iconify-icon> حذف
                </button>
            </div>

            <!-- Required Toggle -->
            <div class="p-2 bg-dark rounded-3 border border-secondary mb-3">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="el_required_switch" ${el.is_required ? 'checked' : ''} onchange="updateCurrentElementProp('is_required', this.checked)">
                    <label class="form-check-label fw-bold small text-white" for="el_required_switch">
                        إجباري (Required) - لا يمكن تخطي السؤال بدونه
                    </label>
                </div>
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

        // SLIDER INSPECTOR
        if (el.element_type === 'slider' || el.element_type === 'currency') {
            if (!el.properties) el.properties = {};
            html += `
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <h6 class="fw-bold small text-white mb-2">🎚️ إعدادات السلايدر والعملات</h6>
                    
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="slider_currency_toggle" ${el.properties.show_currency !== false ? 'checked' : ''} onchange="updateSliderProp('show_currency', this.checked); inspectElement(${eIdx});">
                        <label class="form-check-label small fw-bold text-white">إظهار رمز العملة</label>
                    </div>

                    ${el.properties.show_currency !== false ? `
                        <div class="mb-2">
                            <label class="form-label small text-muted">العملة (Currency)</label>
                            <select class="form-select form-select-sm form-select-dark" onchange="updateSliderProp('currency_code', this.value)">
                                ${WORLD_CURRENCIES.map(c => `<option value="${c.code}" ${el.properties.currency_code === c.code ? 'selected' : ''}>${c.label}</option>`).join('')}
                            </select>
                        </div>
                    ` : `
                        <div class="mb-2">
                            <label class="form-label small text-muted">الوحدة المخصصة (Custom Unit)</label>
                            <input type="text" class="form-control form-control-sm form-control-dark" placeholder="مثال: أيام، أشخاص، كم" value="${escapeHtml(el.properties.custom_unit || '')}" oninput="updateSliderProp('custom_unit', this.value)">
                        </div>
                    `}

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small text-muted">الحد الأدنى (Min)</label>
                            <input type="number" class="form-control form-control-sm form-control-dark" value="${el.properties.min || 0}" oninput="updateSliderProp('min', parseInt(this.value)||0)">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">الحد الأقصى (Max)</label>
                            <input type="number" class="form-control form-control-sm form-control-dark" value="${el.properties.max || 50000}" oninput="updateSliderProp('max', parseInt(this.value)||50000)">
                        </div>
                    </div>
                </div>
            `;
        }

        // TIMER INSPECTOR
        if (el.element_type === 'timer' || el.element_type === 'page_timer') {
            if (!el.properties) el.properties = {};
            html += `
                <div class="p-3 bg-dark rounded-3 border border-secondary mb-3">
                    <h6 class="fw-bold small text-white mb-2">⏰ إعدادات المؤقت التنازلي</h6>
                    <div class="mb-2">
                        <label class="form-label small text-muted">مدة المؤقت بالدقائق (Duration)</label>
                        <input type="number" class="form-control form-control-sm form-control-dark" value="${el.properties.duration_minutes || 15}" oninput="updateTimerProp('duration_minutes', parseInt(this.value)||15)">
                    </div>
                </div>
            `;
        }

        // COUNTRY INSPECTOR
        if (el.element_type === 'country') {
            html += `
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small text-muted mb-0">قائمة الدول</label>
                        <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 small" onclick="loadArabCountries()">🌍 الدول العربية</button>
                    </div>
                </div>
            `;
        }

        // OPTIONS INSPECTOR (Choices / Radios / Images / Dropdowns / Country)
        if (el.properties?.options) {
            const hasScoring = funnelData.design_settings?.scoring_enabled !== false;
            html += `
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small text-muted mb-0">خيارات السؤال</label>
                        <button type="button" class="btn btn-sm btn-primary py-0 px-2 small" onclick="addOptionToCurrentElement()">➕ خيار</button>
                    </div>
                    <div class="d-flex flex-column gap-2">
            `;

            el.properties.options.forEach((opt, oIdx) => {
                html += `
                    <div class="p-2 bg-dark rounded-3 border border-secondary">
                        <div class="d-flex gap-1 mb-1">
                            <input type="text" class="form-control form-control-sm form-control-dark" placeholder="نص الخيار" value="${escapeHtml(opt.label || '')}" oninput="updateOptionProp(${oIdx}, 'label', this.value)">
                            <button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="deleteOptionFromCurrentElement(${oIdx})">✕</button>
                        </div>
                        
                        ${['image_choice', 'single_image_choice', 'multiple_image_choice'].includes(el.element_type) ? `
                            <div class="mb-1">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm form-control-dark" placeholder="رابط الصورة (Image URL)" value="${escapeHtml(opt.image_url || '')}" oninput="updateOptionProp(${oIdx}, 'image_url', this.value)">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="openMediaPickerForOption(${oIdx})">🖼️ اختيار</button>
                                </div>
                            </div>
                        ` : ''}

                        ${hasScoring ? `
                            <div class="d-flex gap-2 align-items-center">
                                <span class="small text-muted">النقاط (Score):</span>
                                <input type="number" class="form-control form-control-sm form-control-dark" style="width: 80px;" value="${opt.score || 0}" oninput="updateOptionProp(${oIdx}, 'score', parseInt(this.value)||0)">
                            </div>
                        ` : ''}
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
            <h6 class="fw-bold mb-3">⚙️ خصائص الخطوة (${activeStepIndex + 1})</h6>
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
            if (!currentStep.elements[selectedElementIndex].properties) currentStep.elements[selectedElementIndex].properties = {};
            currentStep.elements[selectedElementIndex].properties[key] = val;
            renderCanvas();
        }
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

    function loadArabCountries() {
        const currentStep = funnelData.steps[activeStepIndex];
        if (!currentStep || !currentStep.elements[selectedElementIndex]) return;
        currentStep.elements[selectedElementIndex].properties.options = [
            { label: '🇸🇦 المملكة العربية السعودية', value: 'Saudi Arabia' },
            { label: '🇦🇪 الإمارات العربية المتحدة', value: 'UAE' },
            { label: '🇪🇬 جمهورية مصر العربية', value: 'Egypt' },
            { label: '🇰🇼 دولة الكويت', value: 'Kuwait' },
            { label: '🇶🇦 دولة قطر', value: 'Qatar' },
            { label: '🇧🇭 مملكة البحرين', value: 'Bahrain' },
            { label: '🇴🇲 سلطنة عمان', value: 'Oman' },
            { label: '🇯🇴 المملكة الأردنية الهاشمية', value: 'Jordan' },
            { label: '🇲🇦 المملكة المغربية', value: 'Morocco' },
            { label: '🌍 دولة أخرى', value: 'Other' },
        ];
        inspectElement(selectedElementIndex);
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
            score: 10,
            image_url: ''
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

    // Media Picker Modal for Options
    function openMediaPickerForOption(oIdx) {
        currentEditingOptionIdx = oIdx;
        const modal = new bootstrap.Modal(document.getElementById('mediaPickerModal'));
        modal.show();
    }

    function selectModalImage(url) {
        if (currentEditingOptionIdx !== null) {
            updateOptionProp(currentEditingOptionIdx, 'image_url', url);
            inspectElement(selectedElementIndex);
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
        if (selectedElementIndex !== null) inspectElement(selectedElementIndex);
    }

    // ── 5. RESULTS MANAGEMENT ────────────────────────────────────────────────
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
            description: 'وصف نتيجة التقييم وخطة المتابعة للعميل.',
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
        showToast('جاري حفظ التعديلات وفتح المعاينة الحية... ⏳');
        saveFunnel(true);
    }

    function saveFunnel(openAfterSave = false) {
        const saveBtn = document.getElementById('btn_save_funnel');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = 'جاري الحفظ... ⏳';
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
                meta_pixel_id: document.getElementById('meta_pixel_input').value,
                ga4_id: document.getElementById('ga4_input').value,
                tiktok_pixel_id: document.getElementById('tiktok_input').value,
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
                saveBtn.innerHTML = '<span>حفظ التعديلات 💾</span>';
            }
            if (data.success) {
                showToast('💾 تم حفظ الفانل بنجاح!');
                if (openAfterSave) {
                    window.open(`{{ route('funnels.public.show', $funnel->slug) }}?preview=1&t=` + Date.now(), '_blank');
                }
            } else {
                alert('حدث خطأ أثناء الحفظ: ' + (data.message || 'Error'));
            }
        })
        .catch(err => {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<span>حفظ التعديلات 💾</span>';
            }
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
