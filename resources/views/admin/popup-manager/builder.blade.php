<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎯 Popup Builder | {{ $popup->name }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --topbar-h: 60px;
            --sidebar-w: 380px;
            --bg-dark: #090d16;
            --panel-bg: #111827;
            --border-color: #1f2937;
        }

        body, html { height: 100%; margin: 0; padding: 0; overflow: hidden; background-color: var(--bg-dark); color: #f9fafb; font-family: system-ui, -apple-system, sans-serif; }

        .top-bar { height: var(--topbar-h); background: #030712; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; padding: 0 1rem; z-index: 1000; }
        .workspace { display: flex; height: calc(100vh - var(--topbar-h)); }
        .sidebar { width: var(--sidebar-w); background: var(--panel-bg); border-left: 1px solid var(--border-color); display: flex; flex-direction: column; height: 100%; }

        .tab-btn { flex: 1; padding: 0.75rem 0.2rem; background: transparent; border: none; border-bottom: 2px solid transparent; color: #9ca3af; font-size: 0.8rem; font-weight: 700; cursor: pointer; text-align: center; }
        .tab-btn.active { color: #3b82f6; border-bottom-color: #3b82f6; background: #1f2937; }

        .tab-content { flex: 1; overflow-y: auto; padding: 1rem; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        .canvas-area { flex: 1; background: #030712; display: flex; align-items: center; justify-content: center; position: relative; padding: 2rem; overflow: auto; }

        .popup-preview-box { background: white; color: #111827; border-radius: 20px; width: 100%; max-width: 520px; padding: 2rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7); position: relative; overflow: hidden; }
        .popup-overlay-preview { position: absolute; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 50; }

        .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
        .page-list-scroll { max-height: 180px; overflow-y: auto; background: #030712; border-radius: 8px; padding: 0.5rem; }
    </style>
</head>
<body>

    <!-- TOPBAR -->
    <header class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.popups.dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-arrow-right"></i> الداشبورد
            </a>
            <h5 class="fw-bold mb-0 text-white">{{ $popup->name }}</h5>
            <span class="badge bg-primary bg-opacity-25 text-primary border border-primary px-2 py-1 fs-6">🎯 Popup Builder</span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="popupActiveToggle" @checked($popup->is_active) onchange="updatePopupActive(this.checked)">
                <label class="form-check-label text-white small fw-bold" for="popupActiveToggle">تفعيل (ON)</label>
            </div>

            <span id="saveStatusBadge" class="badge bg-secondary">جاهز</span>

            <button type="button" class="btn btn-primary fw-bold rounded-pill px-4" onclick="savePopupAllData()">
                <i class="bi bi-floppy-fill me-1"></i> حفظ التغييرات
            </button>
        </div>
    </header>

    <!-- WORKSPACE -->
    <div class="workspace">

        <!-- CANVAS MAIN AREA -->
        <main class="canvas-area">
            <div class="popup-overlay-preview" id="previewOverlay">
                <div class="popup-preview-box" id="previewModalBox">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" id="previewCloseBtn" style="z-index: 10;"></button>
                    <div id="popupLiveContent" contenteditable="true" style="outline: none;">
                        {!! is_array($popup->structure) ? ($popup->structure['html'] ?? '') : '' !!}
                    </div>
                </div>
            </div>
        </main>

        <!-- RIGHT SIDEBAR SETTINGS -->
        <aside class="sidebar">
            <div class="d-flex border-bottom border-secondary">
                <button class="tab-btn active" onclick="showTab('tabTriggers', this)">⚡ الزناد والصفحات</button>
                <button class="tab-btn" onclick="showTab('tabContent', this)">🖼️ الصور والمحتوى</button>
                <button class="tab-btn" onclick="showTab('tabDesign', this)">🎨 التصميم والموضعة</button>
            </div>

            <div class="tab-content">

                <!-- TAB 1: TRIGGERS, RANDOM TIMING & PAGE TARGETING -->
                <div id="tabTriggers" class="tab-pane active">

                    <!-- TRIGGER TYPE -->
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <label class="form-label text-info fw-bold small">⚡ نوع الزناد الأساسي (Trigger Mode)</label>
                        <select id="triggerModeSelect" class="form-select form-select-sm bg-secondary text-white border-0 mb-3" onchange="toggleTriggerOptions(this.value)">
                            <option value="random_time" @selected(($popup->trigger_settings['mode'] ?? '') === 'random_time')>🎲 توقيت عشوائي (Random Time Delay)</option>
                            <option value="delay" @selected(($popup->trigger_settings['mode'] ?? '') === 'delay')>⏱️ تأخير زمني محدد (Time Delay)</option>
                            <option value="immediately" @selected(($popup->trigger_settings['mode'] ?? '') === 'immediately')>⚡ فور فتح الصفحة (Immediately)</option>
                            <option value="scroll" @selected(($popup->trigger_settings['mode'] ?? '') === 'scroll')>📜 تمرير عشوائي/محدد (Scroll Position)</option>
                            <option value="exit_intent" @selected(($popup->trigger_settings['mode'] ?? '') === 'exit_intent')>🚪 Exit Intent (محاولة الخروج)</option>
                        </select>

                        <!-- RANDOM TIMING SETTINGS (MIN - MAX) -->
                        <div id="randomTimeBox" class="p-3 bg-secondary bg-opacity-25 rounded-3 mb-2">
                            <h6 class="fw-bold text-warning small mb-2"><i class="bi bi-shuffle me-1"></i> إعدادات التوقيت العشوائي (Random Timing)</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label text-white-50 text-xs fw-bold">الحد الأدنى (ثانية)</label>
                                    <input type="number" id="minDelaySec" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $popup->trigger_settings['min_delay_seconds'] ?? 20 }}" min="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-white-50 text-xs fw-bold">الحد الأقصى (ثانية)</label>
                                    <input type="number" id="maxDelaySec" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $popup->trigger_settings['max_delay_seconds'] ?? 60 }}" min="1">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2 text-xs">يختار النظام وقتاً عشوائياً جديداً بين الخانتين في كل زيارة.</small>
                        </div>

                        <!-- FIXED DELAY SETTINGS -->
                        <div id="fixedTimeBox" class="p-2 bg-secondary bg-opacity-25 rounded-3 mb-2" style="display:none;">
                            <label class="form-label text-white-50 text-xs fw-bold">المدة بالثواني</label>
                            <input type="number" id="fixedDelaySec" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $popup->trigger_settings['delay_seconds'] ?? 10 }}">
                        </div>
                    </div>

                    <!-- 🌐 PAGE TARGETING (اختيار مكان ظهور البوب اب بالظبط) -->
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <h6 class="fw-bold text-info small mb-2"><i class="bi bi-geo-alt-fill me-1"></i> تحديد صفحات ظهور الـ Popup</h6>
                        
                        <div class="mb-2">
                            <select id="pagesModeSelect" class="form-select form-select-sm bg-secondary text-white border-0" onchange="togglePagesList(this.value)">
                                <option value="all" @selected(($popup->condition_settings['pages_mode'] ?? 'all') === 'all')>🌐 جميع صفحات الموقع (Everywhere)</option>
                                <option value="specific" @selected(($popup->condition_settings['pages_mode'] ?? '') === 'specific')>🎯 صفحات محددة بالسيستم (Specific Pages)</option>
                            </select>
                        </div>

                        <!-- SYSTEM PAGES SELECTION LIST -->
                        <div id="specificPagesContainer" style="display: {{ ($popup->condition_settings['pages_mode'] ?? '') === 'specific' ? 'block' : 'none' }};">
                            <label class="form-label text-white-50 text-xs fw-bold mt-2">اختر الصفحات المتاحة بالسيستم:</label>
                            <div class="page-list-scroll border border-secondary mb-2">
                                <div class="form-check mb-1">
                                    <input class="form-check-input page-checkbox" type="checkbox" value="/" id="pageHome" @checked(in_array('/', $popup->condition_settings['specific_urls'] ?? []))>
                                    <label class="form-check-label text-white small" for="pageHome">🏠 الصفحة الرئيسية (Home Page)</label>
                                </div>

                                @foreach($landingPages as $lp)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input page-checkbox" type="checkbox" value="/lp-new/{{ $lp->slug }}" id="pageLp_{{ $lp->id }}" @checked(in_array('/lp-new/' . $lp->slug, $popup->condition_settings['specific_urls'] ?? []))>
                                        <label class="form-check-label text-white small" for="pageLp_{{ $lp->id }}">🚀 {{ $lp->internal_name }} ({{ $lp->slug }})</label>
                                    </div>
                                @endforeach
                            </div>

                            <label class="form-label text-white-50 text-xs fw-bold">أو أدخل رابطاً مخصصاً (Custom Path)</label>
                            <input type="text" id="customUrlPath" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="مثال: /visa-france أو /services" value="{{ $popup->condition_settings['custom_url'] ?? '' }}">
                        </div>
                    </div>

                    <!-- TARGETING DEVICES -->
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <h6 class="fw-bold text-info small mb-3"><i class="bi bi-phone me-1"></i> الأجهزة المستهدفة</h6>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-white">Desktop (كمبيوتر)</span>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="devDesktop" @checked($popup->condition_settings['devices']['desktop'] ?? true)>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-white">Mobile (جوال)</span>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="devMobile" @checked($popup->condition_settings['devices']['mobile'] ?? true)>
                            </div>
                        </div>

                        <hr class="border-secondary my-3">

                        <label class="form-label text-white-50 small fw-bold">الأولوية (Priority)</label>
                        <input type="number" id="popupPriority" class="form-control form-control-sm bg-secondary text-white border-0" value="{{ $popup->priority ?? 10 }}">
                    </div>

                    <!-- FREQUENCY SETTINGS -->
                    <div class="p-3 bg-dark border border-secondary rounded-3">
                        <h6 class="fw-bold text-info small mb-2"><i class="bi bi-repeat me-1"></i> تكرار الظهور (Frequency)</h6>
                        <select id="frequencySelect" class="form-select form-select-sm bg-secondary text-white border-0">
                            <option value="once_per_session" @selected(($popup->frequency_settings['mode'] ?? '') === 'once_per_session')>مرة واحدة في الجلسة (Once Per Session)</option>
                            <option value="every_visit" @selected(($popup->frequency_settings['mode'] ?? '') === 'every_visit')>في كل زيارة (Every Visit)</option>
                            <option value="once_ever" @selected(($popup->frequency_settings['mode'] ?? '') === 'once_ever')>مرة واحدة فقط للأبد (Once Ever)</option>
                        </select>
                    </div>

                </div>

                <!-- TAB 2: IMAGE CONTROLS, CONTENT & FORMS -->
                <div id="tabContent" class="tab-pane">

                    <!-- 🖼️ POPUP IMAGE MANAGER (إضافة وتعديل الصور) -->
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <h6 class="fw-bold text-warning small mb-2"><i class="bi bi-image me-1"></i> صورة الـ Popup (Banner / Image)</h6>
                        
                        <div class="mb-3">
                            <label class="form-label text-white-50 text-xs fw-bold">رابط الصورة (Image URL)</label>
                            <input type="text" id="popupImageUrl" class="form-control form-control-sm bg-dark text-white border-secondary mb-2" placeholder="https://domain.com/banner.jpg">
                            <button type="button" class="btn btn-sm btn-primary w-100 fw-bold" onclick="insertOrUpdateImage()"><i class="bi bi-plus-circle me-1"></i> إدراج / استبدال الصورة داخل الـ Popup</button>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success flex-1" onclick="insertBannerPreset('top')">صورة في الأعلى</button>
                            <button type="button" class="btn btn-sm btn-outline-info flex-1" onclick="insertBannerPreset('bg')">خلفية بالكامل</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-info fw-bold small">نموذج السيستم المربوط</label>
                        <select id="assignedLeadFormId" class="form-select form-select-sm bg-secondary text-white border-0">
                            <option value="">-- بدون نموذج --</option>
                            @foreach($forms as $form)
                                <option value="{{ $form->id }}" @selected($popup->assigned_lead_form_id == $form->id)>{{ $form->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-info fw-bold small">تعديل كود ה-HTML المباشر</label>
                        <textarea id="rawHtmlCode" class="form-control form-control-sm bg-dark text-white border-secondary font-monospace" rows="8" onkeyup="updateCanvasFromHtml(this.value)">{!! is_array($popup->structure) ? ($popup->structure['html'] ?? '') : '' !!}</textarea>
                    </div>
                </div>

                <!-- TAB 3: DESIGN & LAYOUT -->
                <div id="tabDesign" class="tab-pane">
                    <div class="mb-3">
                        <label class="form-label text-info fw-bold small">موضع الـ Popup (Layout)</label>
                        <select id="popupLayout" class="form-select form-select-sm bg-secondary text-white border-0">
                            <option value="center" @selected($popup->layout === 'center')>منتصف الشاشة (Center)</option>
                            <option value="top" @selected($popup->layout === 'top')>أعلى الشاشة (Top Banner)</option>
                            <option value="bottom" @selected($popup->layout === 'bottom')>أسفل الشاشة (Bottom Bar)</option>
                            <option value="fullscreen" @selected($popup->layout === 'fullscreen')>ملء الشاشة (Full Screen)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-info fw-bold small">كود CSS مخصص للـ Popup</label>
                        <textarea id="customCssCode" class="form-control form-control-sm bg-dark text-white border-secondary font-monospace" rows="6">{{ $popup->custom_css }}</textarea>
                    </div>
                </div>

            </div>
        </aside>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const updateUrl = "{{ route('admin.popups.builder.update', $popup) }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showTab(tabId, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function toggleTriggerOptions(mode) {
            document.getElementById('randomTimeBox').style.display = (mode === 'random_time') ? 'block' : 'none';
            document.getElementById('fixedTimeBox').style.display = (mode === 'delay') ? 'block' : 'none';
        }

        function togglePagesList(mode) {
            document.getElementById('specificPagesContainer').style.display = (mode === 'specific') ? 'block' : 'none';
        }

        function updateCanvasFromHtml(html) {
            document.getElementById('popupLiveContent').innerHTML = html;
        }

        function insertOrUpdateImage() {
            const url = document.getElementById('popupImageUrl').value.trim();
            if (!url) { alert('يرجى إدخال رابط الصورة أولاً.'); return; }
            const liveContent = document.getElementById('popupLiveContent');
            let img = liveContent.querySelector('img');
            if (img) {
                img.src = url;
            } else {
                liveContent.innerHTML = `<img src="${url}" class="img-fluid rounded-3 mb-3 w-100 object-fit-cover" style="max-height: 250px;">` + liveContent.innerHTML;
            }
            document.getElementById('rawHtmlCode').value = liveContent.innerHTML;
        }

        function insertBannerPreset(type) {
            const url = document.getElementById('popupImageUrl').value.trim() || 'https://via.placeholder.com/600x300';
            const liveContent = document.getElementById('popupLiveContent');
            if (type === 'top') {
                liveContent.innerHTML = `<img src="${url}" class="img-fluid rounded-top-4 w-100 object-fit-cover mb-3"><div class="p-3"><h4 class="fw-bold mb-2">عرض خاص لفترة محدودة 🔥</h4><p class="text-muted small mb-3">احصل على الاستشارة المجانية فور تسجيل بياناتك.</p><button type="button" class="btn btn-success w-100 rounded-pill fw-bold">أطلب الآن</button></div>`;
            } else if (type === 'bg') {
                liveContent.innerHTML = `<div class="p-4 rounded-4 text-white text-center position-relative overflow-hidden" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('${url}') center/cover no-repeat; min-height: 300px;"><h3 class="fw-bold mb-2">عرض خاص جداً 🌟</h3><p class="mb-4">تواصل معنا واستفد من خصم اليوم المتوفر.</p><button type="button" class="btn btn-warning btn-lg px-5 rounded-pill fw-bold text-dark">احجز مكانك الآن</button></div>`;
            }
            document.getElementById('rawHtmlCode').value = liveContent.innerHTML;
        }

        function savePopupAllData() {
            const badge = document.getElementById('saveStatusBadge');
            badge.className = 'badge bg-warning text-dark';
            badge.innerText = 'جاري الحفظ...';

            const selectedPages = [];
            document.querySelectorAll('.page-checkbox:checked').forEach(cb => selectedPages.push(cb.value));

            const html = document.getElementById('popupLiveContent').innerHTML;

            const payload = {
                name: "{{ $popup->name }}",
                is_active: document.getElementById('popupActiveToggle').checked,
                layout: document.getElementById('popupLayout').value,
                priority: parseInt(document.getElementById('popupPriority').value) || 10,
                assigned_lead_form_id: document.getElementById('assignedLeadFormId').value || null,
                trigger_settings: {
                    mode: document.getElementById('triggerModeSelect').value,
                    min_delay_seconds: parseInt(document.getElementById('minDelaySec').value) || 20,
                    max_delay_seconds: parseInt(document.getElementById('maxDelaySec').value) || 60,
                    delay_seconds: parseInt(document.getElementById('fixedDelaySec').value) || 10,
                },
                condition_settings: {
                    pages_mode: document.getElementById('pagesModeSelect').value,
                    specific_urls: selectedPages,
                    custom_url: document.getElementById('customUrlPath').value,
                    devices: {
                        desktop: document.getElementById('devDesktop').checked,
                        mobile: document.getElementById('devMobile').checked
                    }
                },
                frequency_settings: {
                    mode: document.getElementById('frequencySelect').value
                },
                structure: { html: html },
                custom_css: document.getElementById('customCssCode').value
            };

            fetch(updateUrl, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
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
    </script>
</body>
</html>
