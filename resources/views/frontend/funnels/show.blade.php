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

    <!-- Google Fonts & Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>

    {!! $headerScripts ?? '' !!}

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
        }
        body {
            font-family: {{ $fontFamily }}, 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        .funnel-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 680px;
            padding: 2.5rem;
            position: relative;
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
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .option-btn:hover {
            border-color: var(--primary-color);
            background: #eff6ff;
            transform: translateY(-2px);
        }
        .option-btn.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: #ffffff;
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
        }
    </style>
</head>
<body>

@if($isPreview)
    <div class="position-fixed top-0 start-0 w-100 bg-warning text-dark text-center fw-bold py-1 z-3 shadow-sm">
        👁️ وضع المعاينة التجريبية (Preview Mode) — لن يتم حفظ طلبات الإنتاج
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
                    <h2 class="fw-bold mb-2 text-dark">{{ $step->title }}</h2>
                    @if($step->subtitle)
                        <p class="text-muted mb-4 fs-6">{{ $step->subtitle }}</p>
                    @endif

                    <!-- Elements -->
                    @foreach($step->elements as $element)
                        <div class="mb-4">
                            @if($element->element_type === 'heading')
                                <h3 class="fw-bold text-dark mb-2">{{ $element->label }}</h3>
                            @elseif($element->element_type === 'text' || $element->element_type === 'paragraph')
                                <p class="text-muted fs-6 mb-3">{{ $element->label }}</p>
                            @elseif(in_array($element->element_type, ['single_choice', 'radio_choice', 'yes_no']))
                                <div class="d-flex flex-column gap-2">
                                    @foreach($element->properties['options'] ?? [] as $opt)
                                        <div class="option-btn" onclick="selectOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                            <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            <iconify-icon icon="solar:alt-arrow-left-bold" class="text-muted"></iconify-icon>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(in_array($element->element_type, ['multiple_choice', 'checkbox_choice']))
                                <div class="d-flex flex-column gap-2">
                                    @foreach($element->properties['options'] ?? [] as $opt)
                                        <div class="option-btn" onclick="toggleMultiOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                            <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            <iconify-icon icon="solar:check-square-bold" class="text-muted opacity-50"></iconify-icon>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($element->element_type === 'image_choice')
                                <div class="row g-3">
                                    @foreach($element->properties['options'] ?? [] as $opt)
                                        <div class="col-6">
                                            <div class="option-btn text-center flex-column p-3 h-100" onclick="selectOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                                <iconify-icon icon="solar:gallery-bold-duotone" width="40" class="text-primary mb-2"></iconify-icon>
                                                <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($element->element_type === 'dropdown')
                                <select class="form-select form-select-lg rounded-3" onchange="userAnswers[{{ $element->id }}] = this.value">
                                    <option value="">اختر من القائمة...</option>
                                    @foreach($element->properties['options'] ?? [] as $opt)
                                        <option value="{{ $opt['value'] ?? $opt['label'] }}">{{ $opt['label'] ?? $opt['value'] }}</option>
                                    @endforeach
                                </select>
                            @elseif($element->element_type === 'contact_form')
                                <div class="d-flex flex-column gap-3">
                                    <div>
                                        <label class="form-label fw-bold">الاسم الكريم <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg rounded-3" id="input_full_name" placeholder="أدخل اسمك الكريم">
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold">رقم الواتساب <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control form-control-lg rounded-3" id="input_phone" placeholder="05XXXXXXXX">
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold">البريد الإلكتروني</label>
                                        <input type="email" class="form-control form-control-lg rounded-3" id="input_email" placeholder="example@domain.com">
                                    </div>
                                </div>
                            @elseif($element->element_type === 'slider' || $element->element_type === 'currency')
                                <label class="form-label fw-bold">{{ $element->label }}</label>
                                <input type="range" class="form-range" min="1000" max="100000" step="1000" oninput="document.getElementById('slider_val_{{ $element->id }}').innerText = Number(this.value).toLocaleString() + ' SAR'; userAnswers[{{ $element->id }}] = this.value;">
                                <div class="text-center fw-bold text-primary fs-5" id="slider_val_{{ $element->id }}">50,000 SAR</div>
                            @elseif($element->element_type === 'button')
                                <button type="button" class="btn-primary-custom mt-3" onclick="nextStep()">
                                    {{ $element->label ?: 'متابعة 🚀' }}
                                </button>
                            @else
                                <label class="form-label fw-bold">{{ $element->label }}</label>
                                <input type="text" class="form-control form-control-lg rounded-3" placeholder="اكتب إجابتك هنا..." onchange="userAnswers[{{ $element->id }}] = this.value">
                            @endif
                        </div>
                    @endforeach

                    <!-- Navigation Footer -->
                    @if($sIndex === 0 && $step->step_type === 'welcome')
                        <button type="button" class="btn-primary-custom mt-3" onclick="nextStep()">
                            ابدأ التقييم الآن ➔
                        </button>
                    @elseif($sIndex === $totalSteps - 1 || $step->step_type === 'lead_form')
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                            <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" onclick="prevStep()">
                                ← السابق
                            </button>
                            <button type="button" class="btn-primary-custom w-auto px-4" id="btn_submit_final" onclick="submitFunnelForm()">
                                عرض النتيجة والتقرير ✨
                            </button>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
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

            <!-- RESULT CONTAINER -->
            <div class="funnel-step-view d-none text-center py-4" id="result_container">
                <div class="mb-3">
                    <iconify-icon icon="solar:verified-check-bold-duotone" width="80" class="text-success"></iconify-icon>
                </div>
                <h2 class="fw-bold mb-2" id="res_title">جاري التقييم...</h2>
                <p class="text-muted fs-6 mb-4" id="res_desc"></p>

                <div class="p-3 bg-light rounded-4 mb-4 border d-inline-block px-5" id="res_score_wrapper">
                    <span class="text-muted small fw-bold">النتيجة الإجمالية للتقييم</span>
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
