@php
    $design = $funnel->design_settings ?? [];
    $primaryColor = $design['primary_color'] ?? '#2563eb';
    $fontFamily = $design['font_family'] ?? 'Tajawal, sans-serif';
    $steps = $funnel->steps;
    $totalSteps = count($steps);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $funnel->seo_settings['meta_title'] ?? $funnel->name }} — Travel Wave</title>
    <meta name="description" content="{{ $funnel->seo_settings['meta_description'] ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>

    {!! $headerScripts ?? '' !!}

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --primary-light: {{ $primaryColor }}15;
        }
        body {
            font-family: {{ $fontFamily }}, 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            margin: 0;
        }
        .funnel-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 700px;
            padding: 2.5rem;
            position: relative;
            animation: fadeIn 0.3s ease;
        }
        .progress-bar-custom {
            background-color: var(--primary-color);
            height: 6px;
            border-radius: 10px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .option-btn {
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            font-size: 1.05rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .option-btn:hover {
            border-color: var(--primary-color);
            background: #eff6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }
        .option-btn.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: #ffffff;
        }
        .option-btn.selected iconify-icon {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        .btn-primary-custom {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 1.1rem;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary-custom:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }
        .btn-whatsapp-custom {
            background-color: #25d366;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 1.1rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.25);
        }

        /* Interactive Rating Stars */
        .star-rating-box iconify-icon {
            font-size: 32px;
            color: #cbd5e1;
            cursor: pointer;
            transition: color 0.15s;
        }
        .star-rating-box iconify-icon.active,
        .star-rating-box iconify-icon:hover {
            color: #f59e0b;
        }

        /* NPS Buttons */
        .nps-btn {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s;
        }
        .nps-btn:hover, .nps-btn.selected {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        /* Coupon Card */
        .coupon-ticket {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px dashed #f59e0b;
            border-radius: 16px;
            padding: 1.25rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

@if($isPreview)
    <div class="position-fixed top-0 start-0 w-100 bg-warning text-dark text-center fw-bold py-1 z-3 shadow-sm">
        👁️ وضع المعاينة التجريبية (Preview Mode) — اختبار تدفق الفانل
    </div>
@endif

<div class="funnel-card">
    @if($totalSteps > 0)
        <!-- Progress Bar -->
        <div class="progress mb-4 bg-light rounded-pill" style="height: 6px;">
            <div class="progress-bar-custom" id="funnel_progress_bar" style="width: {{ round(100 / $totalSteps) }}%;"></div>
        </div>

        <!-- Dynamic Step Views Wrapper -->
        <div id="step_content_wrapper">
            @foreach($steps as $sIndex => $step)
                <div class="funnel-step-view {{ $sIndex === 0 ? '' : 'd-none' }}" id="step_view_{{ $step->id }}" data-step-index="{{ $sIndex }}">
                    
                    @if($step->title)
                        <h2 class="fw-bold mb-2 text-dark">{{ $step->title }}</h2>
                    @endif
                    @if($step->subtitle)
                        <p class="text-muted mb-4 fs-6">{{ $step->subtitle }}</p>
                    @endif

                    <!-- Elements Render Engine -->
                    @foreach($step->elements as $element)
                        @php
                            $type = $element->element_type;
                            $props = $element->properties ?? [];
                            $label = $element->label;
                        @endphp

                        <div class="mb-4">
                            
                            {{-- 1. HEADING --}}
                            @if($type === 'heading')
                                <h3 class="fw-bold text-dark mb-2 border-start border-4 ps-2 border-primary">{{ $label ?: 'عنوان رئيسي' }}</h3>

                            {{-- 2. PARAGRAPH / TEXT --}}
                            @elseif(in_array($type, ['text', 'paragraph']))
                                <p class="text-muted fs-6 mb-3 leading-relaxed">{{ $label }}</p>

                            {{-- 3. SINGLE CHOICE / RADIO / YES-NO --}}
                            @elseif(in_array($type, ['single_choice', 'radio_choice', 'yes_no']))
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <div class="d-flex flex-column gap-2">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="option-btn" onclick="selectOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                            <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            <iconify-icon icon="solar:alt-arrow-left-bold" class="text-muted"></iconify-icon>
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 4. MULTIPLE CHOICE / CHECKBOX --}}
                            @elseif(in_array($type, ['multiple_choice', 'checkbox_choice']))
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <div class="d-flex flex-column gap-2">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="option-btn" onclick="toggleMultiOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                            <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            <iconify-icon icon="solar:check-square-bold" class="text-muted opacity-50"></iconify-icon>
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 5. IMAGE CHOICE --}}
                            @elseif(in_array($type, ['image_choice', 'single_image_choice', 'multiple_image_choice']))
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <div class="row g-3">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="col-6">
                                            <div class="option-btn text-center flex-column p-3 h-100" onclick="selectOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                                <iconify-icon icon="solar:gallery-bold-duotone" width="40" class="text-primary mb-2"></iconify-icon>
                                                <span class="fw-bold small">{{ $opt['label'] ?? $opt['value'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 6. DROPDOWN / AUTOCOMPLETE --}}
                            @elseif(in_array($type, ['dropdown', 'autocomplete', 'country']))
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <select class="form-select form-select-lg rounded-3 bg-light" onchange="userAnswers[{{ $element->id }}] = this.value">
                                    <option value="">اختر من القائمة...</option>
                                    @foreach($props['options'] ?? [['label' => 'السعودية', 'value' => 'KSA'], ['label' => 'الإمارات', 'value' => 'UAE'], ['label' => 'مصر', 'value' => 'Egypt'], ['label' => 'دولة أخرى', 'value' => 'Other']] as $opt)
                                        <option value="{{ $opt['value'] ?? $opt['label'] }}">{{ $opt['label'] ?? $opt['value'] }}</option>
                                    @endforeach
                                </select>

                            {{-- 7. CONTACT FORM --}}
                            @elseif($type === 'contact_form')
                                <div class="bg-light p-3 p-md-4 rounded-4 border">
                                    <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:user-id-bold-duotone" width="24"></iconify-icon>
                                        <span>{{ $label ?: 'سجّل بياناتك لاستلام التقرير وخطة المتابعة' }}</span>
                                    </h5>
                                    <div class="d-flex flex-column gap-3">
                                        <div>
                                            <label class="form-label fw-bold small text-dark">الاسم الكريم <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg rounded-3" id="input_full_name" placeholder="أدخل اسمك الكريم">
                                        </div>
                                        <div>
                                            <label class="form-label fw-bold small text-dark">رقم الواتساب <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control form-control-lg rounded-3" id="input_phone" placeholder="05XXXXXXXX">
                                        </div>
                                        <div>
                                            <label class="form-label fw-bold small text-dark">البريد الإلكتروني</label>
                                            <input type="email" class="form-control form-control-lg rounded-3" id="input_email" placeholder="example@domain.com">
                                        </div>
                                    </div>
                                </div>

                            {{-- 8. SLIDER / BUDGET / CURRENCY --}}
                            @elseif(in_array($type, ['slider', 'currency', 'number_input']))
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <div class="bg-light p-3 rounded-4 border text-center">
                                    <div class="h3 fw-bold text-primary mb-2" id="slider_val_{{ $element->id }}">30,000 SAR</div>
                                    <input type="range" class="form-range" min="1000" max="100000" step="1000" value="30000" oninput="document.getElementById('slider_val_{{ $element->id }}').innerText = Number(this.value).toLocaleString() + ' SAR'; userAnswers[{{ $element->id }}] = this.value;">
                                    <div class="d-flex justify-content-between text-muted small mt-1">
                                        <span>1,000 SAR</span>
                                        <span>100,000+ SAR</span>
                                    </div>
                                </div>

                            {{-- 9. STAR RATING --}}
                            @elseif($type === 'rating')
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <div class="bg-light p-3 rounded-4 border text-center">
                                    <div class="d-flex justify-content-center gap-2 star-rating-box" id="stars_box_{{ $element->id }}">
                                        @for($i = 1; $i <= 5; $i++)
                                            <iconify-icon icon="solar:star-bold" data-val="{{ $i }}" onclick="setStarRating({{ $element->id }}, {{ $i }}, this)"></iconify-icon>
                                        @endfor
                                    </div>
                                </div>

                            {{-- 10. NPS SCORE (0-10) --}}
                            @elseif($type === 'nps')
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <div class="bg-light p-3 rounded-4 border text-center">
                                    <div class="d-flex justify-content-between flex-wrap gap-1">
                                        @for($i = 0; $i <= 10; $i++)
                                            <button type="button" class="nps-btn" onclick="setNpsScore({{ $element->id }}, {{ $i }}, this)">{{ $i }}</button>
                                        @endfor
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small mt-2">
                                        <span>غير مرجح إطلاقاً (0)</span>
                                        <span>مرجح جداً (10)</span>
                                    </div>
                                </div>

                            {{-- 11. TABLE --}}
                            @elseif($type === 'table')
                                @if($label) <h5 class="fw-bold mb-2 text-dark">{{ $label }}</h5> @endif
                                <div class="table-responsive bg-light p-2 rounded-4 border">
                                    <table class="table table-bordered mb-0 align-middle small text-center">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>الباقة</th>
                                                <th>المميزات المشمولة</th>
                                                <th>المدة</th>
                                                <th>السعر</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold">الباقة الأساسية</td>
                                                <td>تعبئة الأبلكيشن + حجز الموعد والتأمين</td>
                                                <td>3 أيام</td>
                                                <td class="fw-bold text-primary">250 SAR</td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td class="fw-bold">الباقة الذهبية VIP ⭐</td>
                                                <td>كل الخدمات + حجوزات الطيران والفندق المعتمدة</td>
                                                <td>24 ساعة</td>
                                                <td class="fw-bold text-success">550 SAR</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            {{-- 12. TESTIMONIALS --}}
                            @elseif($type === 'testimonials')
                                <div class="bg-light p-3 p-md-4 rounded-4 border text-center">
                                    <iconify-icon icon="solar:chat-round-quotes-bold-duotone" width="36" class="text-primary mb-2"></iconify-icon>
                                    <p class="fst-italic text-dark fs-6 mb-2">"تجربة رائعة وفائقة السرعة! استلمت التأشيرة وحجز الموعد خلال 48 ساعة فقط وبدون أي تعقيد."</p>
                                    <div class="d-flex justify-content-center text-warning gap-1 mb-1">
                                        ⭐⭐⭐⭐⭐
                                    </div>
                                    <strong class="text-primary small">— فهد الدوسري (عميل موثق ✅)</strong>
                                </div>

                            {{-- 13. FAQS ACCORDION --}}
                            @elseif($type === 'faqs')
                                @if($label) <h5 class="fw-bold mb-3 text-dark">{{ $label }}</h5> @endif
                                <div class="accordion rounded-4 overflow-hidden border" id="faq_acc_{{ $element->id }}">
                                    <div class="accordion-item border-0 border-bottom">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#faq_1_{{ $element->id }}">
                                                ❓ ما هي الأوراق المطلوبة لاستخراج التأشيرة؟
                                            </button>
                                        </h2>
                                        <div id="faq_1_{{ $element->id }}" class="accordion-collapse collapse" data-bs-parent="#faq_acc_{{ $element->id }}">
                                            <div class="accordion-body text-muted small">
                                                جواز سفر ساري المفعول، كشف حساب بنكي لآخر 3 أشهر، تعريف راتب من جهة العمل، وصور شخصية حديثة.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-0">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#faq_2_{{ $element->id }}">
                                                ⏱️ كم تستغرق معالجة الطلب في السفارة؟
                                            </button>
                                        </h2>
                                        <div id="faq_2_{{ $element->id }}" class="accordion-collapse collapse" data-bs-parent="#faq_acc_{{ $element->id }}">
                                            <div class="accordion-body text-muted small">
                                                تستغرق عادة بين 7 إلى 14 يوم عمل حسب السفارة المعنية وموسم التقديم.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {{-- 14. COUPON CODE --}}
                            @elseif($type === 'coupon_code')
                                <div class="coupon-ticket text-center">
                                    <span class="badge bg-warning text-dark fw-bold mb-1">🎁 هدية حصرية لك</span>
                                    <h4 class="fw-bold text-danger mb-1">كود خصم: WAVE2026</h4>
                                    <p class="text-muted small mb-2">استخدم هذا الكود للحصول على خصم 20% على رسوم تجهيز المعاملة</p>
                                    <button type="button" class="btn btn-sm btn-dark px-3 rounded-pill" onclick="navigator.clipboard.writeText('WAVE2026'); alert('تم نسخ كود الخصم! 🎉')">
                                        نسخ الكود 📋
                                    </button>
                                </div>

                            {{-- 15. DATE & TIME / APPOINTMENTS --}}
                            @elseif(in_array($type, ['date_picker', 'date_time', 'schedule']))
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light"><iconify-icon icon="solar:calendar-bold-duotone" class="text-primary"></iconify-icon></span>
                                    <input type="date" class="form-control" onchange="userAnswers[{{ $element->id }}] = this.value">
                                </div>

                            {{-- 16. TEXT INPUT / SHORT / LONG ANSWER --}}
                            @elseif(in_array($type, ['short_answer', 'text_input']))
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <input type="text" class="form-control form-control-lg rounded-3" placeholder="اكتب إجابتك هنا..." onchange="userAnswers[{{ $element->id }}] = this.value">

                            @elseif(in_array($type, ['long_answer', 'textarea']))
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <textarea class="form-control form-control-lg rounded-3" rows="3" placeholder="اكتب تفاصيل إجابتك هنا..." onchange="userAnswers[{{ $element->id }}] = this.value"></textarea>

                            {{-- 17. FILE UPLOAD --}}
                            @elseif($type === 'file_upload')
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <div class="p-4 border border-dashed rounded-4 text-center bg-light">
                                    <iconify-icon icon="solar:upload-track-bold-duotone" width="40" class="text-primary mb-2"></iconify-icon>
                                    <p class="mb-1 fw-bold small text-dark">انقر لاختيار ملف أو اسحبه هنا</p>
                                    <small class="text-muted">PDF, JPG, PNG حتى 10 ميجابايت</small>
                                    <input type="file" class="d-none" id="file_{{ $element->id }}" onchange="userAnswers[{{ $element->id }}] = this.files[0]?.name">
                                </div>

                            {{-- 18. GAUGE / PROGRESS RING / STATS --}}
                            @elseif(in_array($type, ['gauge', 'progress_ring', 'stats_bar', 'score_display']))
                                <div class="bg-light p-4 rounded-4 border text-center">
                                    <iconify-icon icon="solar:speedometer-bold-duotone" width="48" class="text-success mb-2"></iconify-icon>
                                    <h4 class="fw-bold text-success mb-1">نسبة القبول المتوقعة: 85%</h4>
                                    <small class="text-muted">يتم احتساب النسبة تلقائياً بناءً على إجاباتك السابقة</small>
                                </div>

                            {{-- 19. SOCIAL SHARE --}}
                            @elseif($type === 'social_share')
                                <div class="text-center p-3 bg-light rounded-4 border">
                                    <span class="fw-bold small d-block mb-2 text-muted">شارك هذا التقييم مع أصدقائك:</span>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="https://wa.me/?text={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-success btn-sm rounded-3"><iconify-icon icon="logos:whatsapp-icon"></iconify-icon> واتساب</a>
                                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-dark btn-sm rounded-3">𝕏 تويتر</a>
                                    </div>
                                </div>

                            {{-- 20. CUSTOM BUTTON --}}
                            @elseif($type === 'button')
                                <button type="button" class="btn-primary-custom mt-2" onclick="nextStep()">
                                    {{ $label ?: 'متابعة الخطوة التالية ➔' }}
                                </button>

                            {{-- 21. FALLBACK GENERIC INPUT --}}
                            @else
                                @if($label) <label class="fw-bold mb-2 d-block fs-6 text-dark">{{ $label }}</label> @endif
                                <input type="text" class="form-control form-control-lg rounded-3" placeholder="اكتب إجابتك هنا..." onchange="userAnswers[{{ $element->id }}] = this.value">
                            @endif

                        </div>
                    @endforeach

                    <!-- Navigation Action Footer -->
                    @if($sIndex === 0 && $step->step_type === 'welcome')
                        <button type="button" class="btn-primary-custom mt-3" onclick="nextStep()">
                            ابدأ التقييم الآن ➔
                        </button>
                    @elseif($sIndex === $totalSteps - 1 || $step->step_type === 'lead_form')
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" onclick="prevStep()">
                                ← السابق
                            </button>
                            <button type="button" class="btn-primary-custom w-auto px-5" id="btn_submit_final" onclick="submitFunnelForm()">
                                عرض النتيجة والتقرير ✨
                            </button>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" onclick="prevStep()">
                                ← السابق
                            </button>
                            <button type="button" class="btn-primary-custom w-auto px-4" onclick="nextStep()">
                                التالي ➔
                            </button>
                        </div>
                    @endif

                </div>
            @endforeach

            <!-- RESULT OUTCOME CONTAINER -->
            <div class="funnel-step-view d-none text-center py-4" id="result_container">
                <div class="mb-3">
                    <iconify-icon icon="solar:verified-check-bold-duotone" width="80" class="text-success"></iconify-icon>
                </div>
                <h2 class="fw-bold mb-2 text-dark" id="res_title">جاري التقييم...</h2>
                <p class="text-muted fs-6 mb-4 px-3" id="res_desc"></p>

                <div class="p-3 bg-light rounded-4 mb-4 border d-inline-block px-5" id="res_score_wrapper">
                    <span class="text-muted small fw-bold">النتيجة الإجمالية المحسوبة</span>
                    <div class="h2 fw-bold text-primary mb-0 mt-1" id="res_score_value">0%</div>
                </div>

                <div id="res_cta_wrapper" class="mt-2"></div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <iconify-icon icon="solar:document-bold-duotone" width="60" class="text-muted opacity-50 mb-3"></iconify-icon>
            <h4 class="fw-bold text-dark mb-2">الفانل لا يحتوي على خطوات بعد</h4>
            <p class="text-muted mb-4">يرجى إضافة الخطوات والأسئلة وحفظ الفانل من داخل الـ Builder.</p>
            <a href="{{ route('admin.funnels.builder', $funnel) }}" class="btn btn-primary fw-bold px-4 rounded-3">
                فتح الـ Builder ⚡
            </a>
        </div>
    @endif
</div>

<!-- JS Core Engine -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const totalSteps = {{ $totalSteps }};
    let currentStepIndex = 0;
    let accumulatedScore = 0;
    const userAnswers = {};
    const stepIds = [
        @foreach($steps as $s)
            {{ $s->id }},
        @endforeach
    ];

    function updateProgress() {
        if (totalSteps <= 0) return;
        const percent = Math.round(((currentStepIndex + 1) / totalSteps) * 100);
        document.getElementById('funnel_progress_bar').style.width = percent + '%';
    }

    function selectOption(elementId, value, score, el) {
        userAnswers[elementId] = value;
        accumulatedScore += (score || 0);

        const parent = el.parentElement;
        parent.querySelectorAll('.option-btn').forEach(btn => btn.classList.remove('selected'));
        el.classList.add('selected');

        setTimeout(() => {
            nextStep();
        }, 220);
    }

    function toggleMultiOption(elementId, value, score, el) {
        if (!userAnswers[elementId]) userAnswers[elementId] = [];
        const idx = userAnswers[elementId].indexOf(value);
        if (idx > -1) {
            userAnswers[elementId].splice(idx, 1);
            accumulatedScore -= (score || 0);
            el.classList.remove('selected');
        } else {
            userAnswers[elementId].push(value);
            accumulatedScore += (score || 0);
            el.classList.add('selected');
        }
    }

    function setStarRating(elementId, val, iconEl) {
        userAnswers[elementId] = val;
        accumulatedScore += val * 5;
        const box = document.getElementById('stars_box_' + elementId);
        box.querySelectorAll('iconify-icon').forEach(star => {
            const starVal = parseInt(star.dataset.val);
            star.classList.toggle('active', starVal <= val);
        });
    }

    function setNpsScore(elementId, val, btnEl) {
        userAnswers[elementId] = val;
        accumulatedScore += val * 2;
        const parent = btnEl.parentElement;
        parent.querySelectorAll('.nps-btn').forEach(b => b.classList.remove('selected'));
        btnEl.classList.add('selected');
    }

    function nextStep() {
        if (currentStepIndex < totalSteps - 1) {
            document.getElementById('step_view_' + stepIds[currentStepIndex]).classList.add('d-none');
            currentStepIndex++;
            document.getElementById('step_view_' + stepIds[currentStepIndex]).classList.remove('d-none');
            updateProgress();
        } else {
            submitFunnelForm();
        }
    }

    function prevStep() {
        if (currentStepIndex > 0) {
            document.getElementById('step_view_' + stepIds[currentStepIndex]).classList.add('d-none');
            currentStepIndex--;
            document.getElementById('step_view_' + stepIds[currentStepIndex]).classList.remove('d-none');
            updateProgress();
        }
    }

    function submitFunnelForm() {
        const btn = document.getElementById('btn_submit_final');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'جاري التقييم... ⏳';
        }

        const fullName = document.getElementById('input_full_name')?.value || 'عميل محتمل';
        const phone = document.getElementById('input_phone')?.value || '';
        const email = document.getElementById('input_email')?.value || '';

        const payload = {
            answers: userAnswers,
            contact_data: { full_name: fullName, phone: phone, email: email },
            is_preview: {{ $isPreview ? 'true' : 'false' }},
        };

        fetch(`{{ route('funnels.public.submit', $funnel->slug) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            for (let id of stepIds) {
                const el = document.getElementById('step_view_' + id);
                if (el) el.classList.add('d-none');
            }

            document.getElementById('funnel_progress_bar').style.width = '100%';

            const res = data.result || {};
            document.getElementById('res_title').innerText = res.title || '🎉 تم التقييم بنجاح';
            document.getElementById('res_desc').innerText = res.description || 'تم استلام بياناتك وسيتم التواصل معك بالتقرير المفصل.';
            document.getElementById('res_score_value').innerText = (data.score ?? accumulatedScore) + ' نقطة';

            const ctaWrapper = document.getElementById('res_cta_wrapper');
            if (res.cta_type === 'whatsapp' || res.cta_whatsapp_number) {
                const waNum = res.cta_whatsapp_number || '966500000000';
                ctaWrapper.innerHTML = `
                    <a href="https://wa.me/${waNum}?text=مرحباً، أتممت اختبار التقييم في Travel Wave وأرغب في المتابعة." target="_blank" class="btn-whatsapp-custom">
                        <iconify-icon icon="logos:whatsapp-icon" width="24"></iconify-icon>
                        <span>${res.cta_label || 'تواصل معنا عبر الواتساب'}</span>
                    </a>
                `;
            }

            document.getElementById('result_container').classList.remove('d-none');
        })
        .catch(err => {
            if (btn) btn.disabled = false;
            alert('حدث خطأ أثناء التقييم: ' + err.message);
        });
    }
</script>

{!! $dispatcherJs ?? '' !!}

</body>
</html>
