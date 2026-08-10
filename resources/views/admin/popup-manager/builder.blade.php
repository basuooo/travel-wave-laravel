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
            --sidebar-w: 420px;
            --bg-dark: #090d16;
            --panel-bg: #111827;
            --border-color: #1f2937;
        }

        body, html { height: 100%; margin: 0; padding: 0; overflow: hidden; background-color: var(--bg-dark); color: #f9fafb; font-family: system-ui, -apple-system, sans-serif; }

        .top-bar { height: var(--topbar-h); background: #030712; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; padding: 0 1rem; z-index: 1000; }
        .workspace { display: flex; height: calc(100vh - var(--topbar-h)); }
        .sidebar { width: var(--sidebar-w); background: var(--panel-bg); border-left: 1px solid var(--border-color); display: flex; flex-direction: column; height: 100%; }

        .tab-btn { flex: 1; padding: 0.75rem 0.2rem; background: transparent; border: none; border-bottom: 2px solid transparent; color: #9ca3af; font-size: 0.78rem; font-weight: 700; cursor: pointer; text-align: center; }
        .tab-btn.active { color: #3b82f6; border-bottom-color: #3b82f6; background: #1f2937; }

        .tab-content { flex: 1; overflow-y: auto; padding: 1rem; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        .canvas-area { flex: 1; background: #030712; display: flex; align-items: center; justify-content: center; position: relative; padding: 2rem; overflow: auto; }

        .popup-preview-box { background: white; color: #111827; border-radius: 20px; width: 100%; max-width: 520px; padding: 2rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7); position: relative; overflow: hidden; }
        .popup-overlay-preview { position: absolute; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center; z-index: 50; }

        .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
        .page-list-scroll { max-height: 160px; overflow-y: auto; background: #030712; border-radius: 8px; padding: 0.5rem; }
        
        .emoji-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; background: #030712; border-radius: 8px; padding: 6px; }
        .emoji-btn { background: #1f2937; border: 1px solid #374151; font-size: 1.25rem; border-radius: 6px; padding: 4px; cursor: pointer; color: white; text-align: center; }
        .emoji-btn:hover { background: #3b82f6; }
        
        .media-grid-item { cursor: pointer; border: 2px solid #374151; transition: all 0.2s; border-radius: 8px; overflow: hidden; background: #1f2937; }
        .media-grid-item:hover { border-color: #3b82f6; transform: scale(1.04); }
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
                <button class="tab-btn active" onclick="showTab('tabPresets', this)">🎨 أشكال القوالب</button>
                <button class="tab-btn" onclick="showTab('tabContent', this)">📝 الخطوط والألوان والزر</button>
                <button class="tab-btn" onclick="showTab('tabTriggers', this)">⚡ الزناد والصفحات</button>
                <button class="tab-btn" onclick="showTab('tabWarning', this)">⚠️ تحذير الإغلاق</button>
            </div>

            <div class="tab-content">

                <!-- TAB 1: 10+ READY POPUP PRESETS WITH PERSISTENT SAVED TEMPLATE SELECTION -->
                <div id="tabPresets" class="tab-pane active">
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <h6 class="fw-bold text-info small mb-2"><i class="bi bi-palette me-1"></i> اختر شكل القالب المناسب (10+ قوالب جاهزة):</h6>
                        
                        @php
                            $savedTemplateId = (string)($popup->structure['template_id'] ?? '');
                        @endphp

                        <select id="presetTemplateSelect" class="form-select form-select-lg bg-secondary text-white border-0" onchange="applyPresetTemplate(this.value)">
                            <option value="" @selected(empty($savedTemplateId))>-- اختر من القائمة لتطبيق القالب --</option>
                            <option value="1" @selected($savedTemplateId === '1')>🔥 1. عرض الخصم الذهبي (Golden Discount Modal)</option>
                            <option value="2" @selected($savedTemplateId === '2')>✈️ 2. استشارة سريعة للتأشيرة (Visa Fast Consultation)</option>
                            <option value="3" @selected($savedTemplateId === '3')>⏳ 3. عداد تنازلي طارئ (Urgent Countdown Offer)</option>
                            <option value="4" @selected($savedTemplateId === '4')>💬 4. شات واتساب مباشر (Floating WhatsApp Chat)</option>
                            <option value="5" @selected($savedTemplateId === '5')>🎁 5. هدية كود خصم (Coupon Gift Box)</option>
                            <option value="6" @selected($savedTemplateId === '6')>📝 6. نموذج طلب سريع (Quick Lead Form Card)</option>
                            <option value="7" @selected($savedTemplateId === '7')>📢 7. تنويه هام / إعلان (Important Announcement)</option>
                            <option value="8" @selected($savedTemplateId === '8')>🚀 8. حزمة توفير (Saver Bundle Offer)</option>
                            <option value="9" @selected($savedTemplateId === '9')>🌟 9. صورة بنر كاملة (Full Banner Image Modal)</option>
                            <option value="10" @selected($savedTemplateId === '10')>💎 10. دعوة حصريّة VIP (Exclusive VIP Invitation)</option>
                        </select>
                        <small class="text-muted text-xs d-block mt-2">اختيار القالب يطبق التنسيق والمكونات فوراً ويتم حفظ القالب المحدد بصفة دائمة.</small>
                    </div>
                </div>

                <!-- TAB 2: TYPOGRAPHY, COLORS, BUTTON ACTION, IMAGE OPTIONS & EMOJI -->
                <div id="tabContent" class="tab-pane">

                    <!-- 1. IMAGE SOURCE OPTIONS (SITE MEDIA LIBRARY OR DIRECT URL) -->
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <h6 class="fw-bold text-warning small mb-2"><i class="bi bi-image me-1"></i> مصدر وصورة الـ Popup</h6>
                        
                        <div class="mb-2">
                            <label class="form-label text-white-50 text-xs fw-bold">الخيار 1: رابط صورة مباشر (Image URL)</label>
                            <input type="text" id="popupImageUrl" class="form-control form-control-sm bg-dark text-white border-secondary mb-2" placeholder="https://domain.com/image.jpg">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 text-xs fw-bold">الخيار 2: اختيار من مكتبة وسائط الموقع</label>
                            <button type="button" class="btn btn-sm btn-outline-info w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#mediaLibraryModal">
                                📂 فتح مكتبة الوسائط المتاحة بموقعك
                            </button>
                        </div>

                        <button type="button" class="btn btn-sm btn-primary w-100 fw-bold" onclick="insertOrUpdateImage()"><i class="bi bi-plus-circle me-1"></i> إدراج / استبدال الصورة</button>
                    </div>

                    <!-- 2. EMOJI GRID & GETEMOJI.COM ACTIVE EXTERNAL LINK -->
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <label class="form-label text-info fw-bold small mb-2"><i class="bi bi-emoji-smile me-1"></i> مكتبة الإيموجي المشهورة</label>
                        <div class="emoji-grid mb-3">
                            <button type="button" class="emoji-btn" onclick="addEmoji('🔥')">🔥</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('⚠️')">⚠️</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🎁')">🎁</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🚀')">🚀</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('💎')">💎</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('⏳')">⏳</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🌟')">🌟</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('✨')">✨</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('✈️')">✈️</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('💬')">💬</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🛑')">🛑</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🚨')">🚨</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🛒')">🛒</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🏷️')">🏷️</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('📍')">📍</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('📞')">📞</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('📌')">📌</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🎯')">🎯</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('💯')">💯</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('🎉')">🎉</button>
                            <button type="button" class="emoji-btn" onclick="addEmoji('👑')">👑</button>
                        </div>

                        <label class="form-label text-white-50 text-xs fw-bold">أو أدخل/الصق إيموجي مخصص:</label>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" id="externalEmojiInput" class="form-control bg-dark text-white border-secondary" placeholder="الصق الإيموجي هنا (Ctrl+V)...">
                            <button type="button" class="btn btn-primary" onclick="addCustomExternalEmoji()"><i class="bi bi-plus-lg"></i> إدراج</button>
                        </div>

                        <!-- ACTIVE LINK TO GETEMOJI.COM -->
                        <a href="https://getemoji.com" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-warning w-100 fw-bold">
                            <i class="bi bi-box-arrow-up-right me-1"></i> 🔗 فتح موقع getemoji.com للنسخ المباشر
                        </a>
                    </div>

                    <!-- 3. BUTTON STYLING & ACTION LINK (التحكم بالزر والرابط) -->
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <h6 class="fw-bold text-success small mb-2"><i class="bi bi-cursor-fill me-1"></i> التحكم في الزر ورابط التوجيه المباشر</h6>
                        
                        <div class="mb-2">
                            <label class="form-label text-white-50 text-xs fw-bold">نص الكتابة على الزر</label>
                            <input type="text" id="btnTextControl" class="form-control form-control-sm bg-dark text-white border-secondary" value="أطلب الآن والدفع عند الاستلام" onkeyup="updateButtonText(this.value)">
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-white-50 text-xs fw-bold">رابط التوجيه عند النقر على الزر (Button URL)</label>
                            <input type="text" id="btnUrlControl" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="مثال: #lead-form أو https://wa.me/2010..." value="{{ $popup->structure['btn_url'] ?? '#lead-form' }}" onkeyup="updateButtonUrl(this.value)">
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label text-white-50 text-xs fw-bold">لون الزر</label>
                                <input type="color" id="btnBgColor" class="form-control form-control-color w-100 bg-dark border-secondary" value="#10b981" onchange="updateButtonStyles()">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-white-50 text-xs fw-bold">حجم خط الزر</label>
                                <select id="btnFontSize" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="updateButtonStyles()">
                                    <option value="1rem">عادي (Medium)</option>
                                    <option value="1.25rem" selected>كبير (Large)</option>
                                    <option value="1.5rem">كبير جداً (XL)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. FORM INTEGRATION & HTML CODE -->
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
                        <label class="form-label text-info fw-bold small">كود HTML المباشر</label>
                        <textarea id="rawHtmlCode" class="form-control form-control-sm bg-dark text-white border-secondary font-monospace" rows="6" onkeyup="updateCanvasFromHtml(this.value)">{!! is_array($popup->structure) ? ($popup->structure['html'] ?? '') : '' !!}</textarea>
                    </div>
                </div>

                <!-- TAB 3: TRIGGERS, RANDOM TIMING & PAGE TARGETING -->
                <div id="tabTriggers" class="tab-pane">
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <label class="form-label text-info fw-bold small">⚡ نوع الزناد (Trigger Mode)</label>
                        <select id="triggerModeSelect" class="form-select form-select-sm bg-secondary text-white border-0 mb-3" onchange="toggleTriggerOptions(this.value)">
                            <option value="random_time" @selected(($popup->trigger_settings['mode'] ?? '') === 'random_time')>🎲 توقيت عشوائي (Random Time Delay)</option>
                            <option value="delay" @selected(($popup->trigger_settings['mode'] ?? '') === 'delay')>⏱️ تأخير زمني محدد (Time Delay)</option>
                            <option value="immediately" @selected(($popup->trigger_settings['mode'] ?? '') === 'immediately')>⚡ فور فتح الصفحة (Immediately)</option>
                            <option value="scroll" @selected(($popup->trigger_settings['mode'] ?? '') === 'scroll')>📜 تمرير عشوائي/محدد (Scroll Position)</option>
                            <option value="exit_intent" @selected(($popup->trigger_settings['mode'] ?? '') === 'exit_intent')>🚪 Exit Intent (محاولة الخروج)</option>
                        </select>

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
                        </div>

                        <div id="fixedTimeBox" class="p-2 bg-secondary bg-opacity-25 rounded-3 mb-2" style="display:none;">
                            <label class="form-label text-white-50 text-xs fw-bold">المدة بالثواني</label>
                            <input type="number" id="fixedDelaySec" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $popup->trigger_settings['delay_seconds'] ?? 10 }}">
                        </div>
                    </div>

                    <!-- PAGE TARGETING -->
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <h6 class="fw-bold text-info small mb-2"><i class="bi bi-geo-alt-fill me-1"></i> تحديد صفحات الظهور</h6>
                        <select id="pagesModeSelect" class="form-select form-select-sm bg-secondary text-white border-0 mb-2" onchange="togglePagesList(this.value)">
                            <option value="all" @selected(($popup->condition_settings['pages_mode'] ?? 'all') === 'all')>🌐 جميع صفحات الموقع</option>
                            <option value="specific" @selected(($popup->condition_settings['pages_mode'] ?? '') === 'specific')>🎯 صفحات محددة بالسيستم</option>
                        </select>

                        <div id="specificPagesContainer" style="display: {{ ($popup->condition_settings['pages_mode'] ?? '') === 'specific' ? 'block' : 'none' }};">
                            <div class="page-list-scroll border border-secondary mb-2">
                                <div class="form-check mb-1">
                                    <input class="form-check-input page-checkbox" type="checkbox" value="/" id="pageHome" @checked(in_array('/', $popup->condition_settings['specific_urls'] ?? []))>
                                    <label class="form-check-label text-white small" for="pageHome">🏠 الصفحة الرئيسية (Home Page)</label>
                                </div>

                                @foreach($landingPages as $lp)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input page-checkbox" type="checkbox" value="/lp-new/{{ $lp->slug }}" id="pageLp_{{ $lp->id }}" @checked(in_array('/lp-new/' . $lp->slug, $popup->condition_settings['specific_urls'] ?? []))>
                                        <label class="form-check-label text-white small" for="pageLp_{{ $lp->id }}">🚀 {{ $lp->internal_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <h6 class="fw-bold text-info small mb-2">الأجهزة المستهدفة</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-white">Desktop</span>
                            <input class="form-check-input" type="checkbox" id="devDesktop" @checked($popup->condition_settings['devices']['desktop'] ?? true)>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-white">Mobile</span>
                            <input class="form-check-input" type="checkbox" id="devMobile" @checked($popup->condition_settings['devices']['mobile'] ?? true)>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: EXIT CONFIRMATION WARNING (RETAINING ONLY "العودة" BUTTON AS REQUESTED) -->
                <div id="tabWarning" class="tab-pane">
                    <div class="p-3 bg-dark border border-secondary rounded-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-warning mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i> تحذير عند محاولة إغلاق الـ Popup</h6>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="exitWarningToggle" @checked($popup->structure['exit_warning']['enable'] ?? false)>
                            </div>
                        </div>

                        <!-- RESTORE BUTTON ONLY (REMOVED PREVIEW BUTTON PER REQUEST 3) -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-light w-100 fw-bold" onclick="restoreOriginalPopupPreview()" title="العودة لمعاينة الـ Popup الأصلي">
                                ↺ العودة لمعاينة الـ Popup الأصلي
                            </button>
                        </div>

                        <label class="form-label text-white-50 text-xs fw-bold">اختر من 10+ تحذيرات جاهزة:</label>
                        <select class="form-select form-select-sm bg-secondary text-white border-0 mb-3" onchange="applyExitWarningPreset(this.value)">
                            <option value="">-- اختر صيغة التحذير --</option>
                            <option value="1">⚠️ 1. هل أنت متأكد من خسارة الخصم 20% الآن؟</option>
                            <option value="2">⏳ 2. هذا العرض سينتهي ولن يظهر لك مجدداً!</option>
                            <option value="3">🎁 3. نضمن لك استشارة مجانية عند إكمال الطلب الآن.</option>
                            <option value="4">✈️ 4. هل تود تفويت فرصة حجز التأشيرة بسعر التكلفة؟</option>
                            <option value="5">🔥 5. انتظر! خصم إضافي بقيمة 500 ج.م ينتظرك.</option>
                            <option value="6">💎 6. احصل على هدية مجانية عند البقاء بالصفحة!</option>
                            <option value="7">🛑 7. لا تغلق! يمكنك الاستفسار عبر الواتساب فوراً.</option>
                            <option value="8">🚨 8. آخر فرصة للتسجيل اليوم!</option>
                            <option value="9">⏱️ 9. المتبقي فقط 3 أماكن، هل ترغب بالإلغاء؟</option>
                            <option value="10">🎯 10. اضغط هنا للحصول على الاستثناء الخاص بك!</option>
                        </select>

                        <div class="mb-2">
                            <label class="form-label text-white-50 text-xs fw-bold">عنوان التحذير</label>
                            <input type="text" id="exitWarningTitle" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $popup->structure['exit_warning']['title'] ?? '⚠️ هل أنت متأكد من الإلغاء؟' }}">
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-white-50 text-xs fw-bold">رسالة التحذير</label>
                            <textarea id="exitWarningMsg" class="form-control form-control-sm bg-dark text-white border-secondary" rows="3">{{ $popup->structure['exit_warning']['msg'] ?? 'بإغلاقك لهذه النافذة ستخسر فرصة الحصول على الخصم الحصري اليوم!' }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
        </aside>

    </div>

    <!-- SITE MEDIA LIBRARY MODAL (MediaAsset MODEL) -->
    <div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold"><i class="bi bi-folder-fill text-warning me-2"></i> مكتبة وسائط الموقع (Media Asset Library)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 450px; overflow-y: auto;">
                    <div class="row g-3">
                        @forelse($assets as $asset)
                            @php
                                $imgUrl = $asset->public_url ?: $asset->url;
                                $title = $asset->title ?: ($asset->file_name ?: 'مكـون وسائط');
                            @endphp
                            <div class="col-4 col-md-3">
                                <div class="media-grid-item p-2 text-center" onclick="selectMediaAsset('{{ $imgUrl }}')">
                                    <img src="{{ $imgUrl }}" class="img-fluid rounded max-h-120 object-fit-cover w-100" alt="Asset" onerror="this.src='https://via.placeholder.com/200x150?text=Media+Image'">
                                    <span class="d-block text-truncate text-white-50 text-xs mt-2">{{ $title }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-white-50">
                                <i class="bi bi-images fs-1 d-block mb-2 text-muted"></i>
                                يمكنك استخدام رابط صورة مباشر بالخانة الجانبية.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const updateUrl = "{{ route('admin.popups.builder.update', $popup) }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        let originalPopupHtml = `{!! is_array($popup->structure) ? ($popup->structure['html'] ?? '') : '' !!}`;
        let isPreviewingWarning = false;

        function showTab(tabId, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function toggleTriggerOptions(mode) {
            const randBox = document.getElementById('randomTimeBox');
            const fixBox = document.getElementById('fixedTimeBox');
            if (randBox) randBox.style.display = (mode === 'random_time') ? 'block' : 'none';
            if (fixBox) fixBox.style.display = (mode === 'delay') ? 'block' : 'none';
        }

        function togglePagesList(mode) {
            const container = document.getElementById('specificPagesContainer');
            if (container) container.style.display = (mode === 'specific') ? 'block' : 'none';
        }

        function updateCanvasFromHtml(html) {
            isPreviewingWarning = false;
            originalPopupHtml = html;
            document.getElementById('popupLiveContent').innerHTML = html;
        }

        function addEmoji(emoji) {
            if (isPreviewingWarning) restoreOriginalPopupPreview();
            const liveContent = document.getElementById('popupLiveContent');
            const h = liveContent.querySelector('h1, h2, h3, h4, h5, h6');
            if (h) {
                h.innerText = emoji + ' ' + h.innerText;
            } else {
                liveContent.innerHTML = `<h3>${emoji}</h3>` + liveContent.innerHTML;
            }
            originalPopupHtml = liveContent.innerHTML;
            document.getElementById('rawHtmlCode').value = liveContent.innerHTML;
        }

        function addCustomExternalEmoji() {
            const input = document.getElementById('externalEmojiInput');
            if (!input || !input.value.trim()) { alert('يرجى لصق الإيموجي في الخانة أولاً.'); return; }
            addEmoji(input.value.trim());
            input.value = '';
        }

        function selectMediaAsset(url) {
            document.getElementById('popupImageUrl').value = url;
            const modalEl = document.getElementById('mediaLibraryModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            insertOrUpdateImage();
        }

        function insertOrUpdateImage() {
            if (isPreviewingWarning) restoreOriginalPopupPreview();
            const url = document.getElementById('popupImageUrl').value.trim();
            if (!url) { alert('يرجى إدخال رابط الصورة أو اختيارها من المكتبة.'); return; }
            const liveContent = document.getElementById('popupLiveContent');
            let img = liveContent.querySelector('img');
            if (img) {
                img.src = url;
            } else {
                liveContent.innerHTML = `<img src="${url}" class="img-fluid rounded-3 mb-3 w-100 object-fit-cover" style="max-height: 250px;">` + liveContent.innerHTML;
            }
            originalPopupHtml = liveContent.innerHTML;
            document.getElementById('rawHtmlCode').value = liveContent.innerHTML;
        }

        function updateButtonText(text) {
            if (isPreviewingWarning) restoreOriginalPopupPreview();
            const liveContent = document.getElementById('popupLiveContent');
            const btn = liveContent.querySelector('a, button');
            if (btn) btn.innerText = text;
            originalPopupHtml = liveContent.innerHTML;
            document.getElementById('rawHtmlCode').value = liveContent.innerHTML;
        }

        function updateButtonUrl(url) {
            if (isPreviewingWarning) restoreOriginalPopupPreview();
            const liveContent = document.getElementById('popupLiveContent');
            let btn = liveContent.querySelector('a');
            if (btn) {
                btn.href = url;
            } else {
                const buttonEl = liveContent.querySelector('button');
                if (buttonEl) {
                    const newLink = document.createElement('a');
                    newLink.href = url;
                    newLink.className = buttonEl.className;
                    newLink.innerText = buttonEl.innerText;
                    buttonEl.parentNode.replaceChild(newLink, buttonEl);
                }
            }
            originalPopupHtml = liveContent.innerHTML;
            document.getElementById('rawHtmlCode').value = liveContent.innerHTML;
        }

        function updateButtonStyles() {
            if (isPreviewingWarning) restoreOriginalPopupPreview();
            const color = document.getElementById('btnBgColor').value;
            const fontSize = document.getElementById('btnFontSize').value;
            const liveContent = document.getElementById('popupLiveContent');
            const btn = liveContent.querySelector('a, button');
            if (btn) {
                btn.style.backgroundColor = color;
                btn.style.borderColor = color;
                btn.style.fontSize = fontSize;
            }
            originalPopupHtml = liveContent.innerHTML;
            document.getElementById('rawHtmlCode').value = liveContent.innerHTML;
        }

        function applyPresetTemplate(id) {
            if (!id) return;
            isPreviewingWarning = false;
            const liveContent = document.getElementById('popupLiveContent');
            const btnUrl = document.getElementById('btnUrlControl') ? document.getElementById('btnUrlControl').value || '#lead-form' : '#lead-form';

            const templates = {
                1: `<div class="p-4 bg-white rounded-4 text-center"><span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-2 fw-bold">🔥 عرض حصري اليوم</span><h2 class="fw-bold text-dark mb-2">خصم 20% على خدمات التأشيرات</h2><p class="text-muted mb-4">أدخل بياناتك الآن للاستفادة من الخصم قبل انتهاء العداد.</p><a href="${btnUrl}" class="btn btn-warning btn-lg w-100 rounded-pill fw-bold text-dark shadow py-3">استلم الكوبون والخصم الآن 🎁</a></div>`,
                2: `<div class="p-4 bg-white rounded-4 text-center"><div class="mb-3"><i class="bi bi-airplane-engines-fill text-primary display-3"></i></div><h3 class="fw-bold mb-2">استشارة مجانية لسفرك</h3><p class="text-muted small mb-4">فريقنا متواجد لمساعدتك في استخراج كافة أوراق السفر فوراً.</p><a href="${btnUrl}" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow py-3">تواصل مع المستشار الآن ✈️</a></div>`,
                3: `<div class="p-4 bg-danger bg-opacity-10 rounded-4 text-center border border-danger"><h4 class="fw-bold text-danger mb-2">⏳ ينتهي العرض خلال وقت قصير!</h4><p class="text-muted mb-4">المقاعد المتبقية 3 فقط للحصول على تكلفة الاستخراج المخفضة.</p><a href="${btnUrl}" class="btn btn-danger btn-lg w-100 rounded-pill fw-bold shadow py-3">احجز مكانك فوراً 🚨</a></div>`,
                4: `<div class="p-4 bg-success bg-opacity-10 rounded-4 text-center border border-success"><i class="bi bi-whatsapp text-success display-2 mb-2 d-block"></i><h4 class="fw-bold text-dark mb-2">تواصل مباشر عبر الواتساب</h4><p class="text-muted small mb-4">تحدث مع أحد ممثلي المبيعات مباشرة للاستفسارات السريعة.</p><a href="https://wa.me/201000000000" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow py-3">فتح المحادثة الآن 💬</a></div>`,
                5: `<div class="p-4 bg-white rounded-4 text-center border border-primary border-2"><div class="fs-1 mb-2">🎁</div><h3 class="fw-bold text-dark mb-2">كود خصم خاص بك</h3><code class="fs-4 d-block my-2 text-primary fw-bold">TRAVEL2026</code><p class="text-muted small mb-4">استخدم هذا الكود للحصول على خصم مباشر.</p><a href="${btnUrl}" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow py-3">تطبيق الخصم والطلب ✨</a></div>`,
                6: `<div class="p-4 bg-white rounded-4 text-center"><h4 class="fw-bold mb-3">سجل طلبك السريع</h4><div class="mb-3"><input type="text" placeholder="الاسم الكامل" class="form-control form-control-lg"></div><div class="mb-3"><input type="tel" placeholder="رقم الهاتف" class="form-control form-control-lg"></div><a href="${btnUrl}" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow py-3">تأكيد الطلب 📝</a></div>`,
                7: `<div class="p-4 bg-dark text-white rounded-4 text-center"><h3 class="fw-bold text-warning mb-2">📢 تنبيه هام للعملاء</h3><p class="mb-4">تم فتح مواعيد التقديم للتأشيرات الأوروبية لهذا الشهر.</p><a href="${btnUrl}" class="btn btn-warning btn-lg w-100 rounded-pill fw-bold text-dark shadow py-3">اعرف المواعيد والأسعار 🚀</a></div>`,
                8: `<div class="p-4 bg-white rounded-4 text-center border border-warning"><span class="badge bg-danger mb-2">توفير 30%</span><h3 class="fw-bold mb-2">حزمة التوفير الشاملة</h3><p class="text-muted mb-4">تأشيرة + تأمين سفر + ترجمة مستندات بسعر موحد.</p><a href="${btnUrl}" class="btn btn-warning btn-lg w-100 rounded-pill fw-bold text-dark shadow py-3">اطلب حزمة التوفير 💎</a></div>`,
                9: `<img src="https://via.placeholder.com/600x300" class="img-fluid rounded-top-4 w-100 object-fit-cover"><div class="p-4 text-center"><h4 class="fw-bold mb-2">بنر الصور والتأشيرات</h4><a href="${btnUrl}" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow">مشاهدة العرض 🌟</a></div>`,
                10: `<div class="p-4 bg-dark text-warning rounded-4 text-center border border-warning"><div class="fs-1 mb-2">💎</div><h3 class="fw-bold mb-2">دعوة خاصة للعملاء المميزين</h3><p class="text-white-50 mb-4">احصل على خدمة المعاملة الفاخرة والاستخراج السريع.</p><a href="${btnUrl}" class="btn btn-warning btn-lg w-100 rounded-pill fw-bold text-dark shadow py-3">الانضمام لقائمة VIP ✨</a></div>`
            };

            if (templates[id]) {
                originalPopupHtml = templates[id];
                liveContent.innerHTML = templates[id];
                const rawInput = document.getElementById('rawHtmlCode');
                if (rawInput) rawInput.value = templates[id];
            }
        }

        function applyExitWarningPreset(id) {
            if (!id) return;
            const warnings = {
                1: { title: "⚠️ هل أنت متأكد من خسارة الخصم 20% الآن؟", msg: "بإغلاقك لهذه النافذة ستفقد خصم الـ 20% المتاح لليوم فقط." },
                2: { title: "⏳ هذا العرض سينتهي ولن يظهر لك مجدداً!", msg: "الفرصة متاحة فقط للزوار الحاليين، هل ترغب بالاستمرار بالفقد؟" },
                3: { title: "🎁 نضمن لك استشارة مجانية عند إكمال الطلب الآن", msg: "لا تضيع فرصة الحصول على التوجيه المستندي من الخبراء مجاناً." },
                4: { title: "✈️ هل تود تفويت فرصة حجز التأشيرة بسعر التكلفة؟", msg: "الأسعار ستعود للاسعار الأصلية فور مغادرتك للصفحة." },
                5: { title: "🔥 انتظر! خصم إضافي بقيمة 500 ج.م ينتظرك", msg: "سجل بياناتك الآن وحصل على قسيمة الـ 500 ج.م الفورية." },
                6: { title: "💎 احصل على هدية مجانية عند البقاء بالصفحة!", msg: "اختر هديتك المستندية المباشرة قبل الإغلاق." },
                7: { title: "🛑 لا تغلق! يمكنك الاستفسار عبر الواتساب فوراً", msg: "فريقنا متواجد ومستعد للإجابة على كافة أسئلتك بدون التزام." },
                8: { title: "🚨 آخر فرصة للتسجيل اليوم!", msg: "أماكن التقديم المتاحة لهذا اليوم أوشكت على الانتهاء." },
                9: { title: "⏱️ المتبقي فقط 3 أماكن، هل ترغب بالإلغاء؟", msg: "إلغاؤك الآن سيسمح لزائر آخر بأخذ مكانك." },
                10: { title: "🎯 اضغط هنا للحصول على الاستثناء الخاص بك!", msg: "يمكننا تقديم استثناء خاص لك عند الاتصال بنا مباشرة." }
            };

            if (warnings[id]) {
                const titleInput = document.getElementById('exitWarningTitle');
                const msgInput = document.getElementById('exitWarningMsg');
                const toggle = document.getElementById('exitWarningToggle');
                if (titleInput) titleInput.value = warnings[id].title;
                if (msgInput) msgInput.value = warnings[id].msg;
                if (toggle) toggle.checked = true;
            }
        }

        function restoreOriginalPopupPreview() {
            isPreviewingWarning = false;
            document.getElementById('popupLiveContent').innerHTML = originalPopupHtml;
        }

        function savePopupAllData() {
            const badge = document.getElementById('saveStatusBadge');
            badge.className = 'badge bg-warning text-dark';
            badge.innerText = 'جاري الحفظ...';

            try {
                if (isPreviewingWarning) {
                    restoreOriginalPopupPreview();
                }

                const htmlToSave = originalPopupHtml || document.getElementById('popupLiveContent').innerHTML;

                const templateSelect = document.getElementById('presetTemplateSelect');
                const selectedTemplateId = templateSelect ? templateSelect.value : '';

                const btnUrlEl = document.getElementById('btnUrlControl');
                const exitToggleEl = document.getElementById('exitWarningToggle');
                const exitTitleEl = document.getElementById('exitWarningTitle');
                const exitMsgEl = document.getElementById('exitWarningMsg');

                const payload = {
                    name: "{{ $popup->name }}",
                    is_active: document.getElementById('popupActiveToggle') ? document.getElementById('popupActiveToggle').checked : true,
                    layout: document.getElementById('popupLayout') ? document.getElementById('popupLayout').value : 'center',
                    priority: parseInt(document.getElementById('popupPriority') ? document.getElementById('popupPriority').value : 10) || 10,
                    assigned_lead_form_id: document.getElementById('assignedLeadFormId') ? document.getElementById('assignedLeadFormId').value : null,
                    trigger_settings: {
                        mode: document.getElementById('triggerModeSelect') ? document.getElementById('triggerModeSelect').value : 'random_time',
                        min_delay_seconds: parseInt(document.getElementById('minDelaySec') ? document.getElementById('minDelaySec').value : 20) || 20,
                        max_delay_seconds: parseInt(document.getElementById('maxDelaySec') ? document.getElementById('maxDelaySec').value : 60) || 60,
                        delay_seconds: parseInt(document.getElementById('fixedDelaySec') ? document.getElementById('fixedDelaySec').value : 10) || 10,
                    },
                    condition_settings: {
                        pages_mode: document.getElementById('pagesModeSelect') ? document.getElementById('pagesModeSelect').value : 'all',
                        devices: {
                            desktop: document.getElementById('devDesktop') ? document.getElementById('devDesktop').checked : true,
                            mobile: document.getElementById('devMobile') ? document.getElementById('devMobile').checked : true
                        }
                    },
                    frequency_settings: {
                        mode: document.getElementById('frequencySelect') ? document.getElementById('frequencySelect').value : 'once_per_session'
                    },
                    structure: {
                        html: htmlToSave,
                        template_id: selectedTemplateId,
                        btn_url: btnUrlEl ? btnUrlEl.value : '#lead-form',
                        exit_warning: {
                            enable: exitToggleEl ? exitToggleEl.checked : false,
                            title: exitTitleEl ? exitTitleEl.value : '',
                            msg: exitMsgEl ? exitMsgEl.value : ''
                        }
                    },
                    custom_css: document.getElementById('customCssCode') ? document.getElementById('customCssCode').value : ''
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
                    console.error(err);
                    badge.className = 'badge bg-danger';
                    badge.innerText = 'خطأ بالحفظ';
                });
            } catch (err) {
                console.error(err);
                badge.className = 'badge bg-danger';
                badge.innerText = 'خطأ كود بالحفظ';
            }
        }
    </script>
</body>
</html>
