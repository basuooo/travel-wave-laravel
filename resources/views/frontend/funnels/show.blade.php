<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title>{{ $funnel->seo_settings['meta_title'] ?? $funnel->name }}</title>
    <meta name="description" content="{{ $funnel->seo_settings['meta_description'] ?? 'Interactive Funnel by Travel Wave' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>

    {!! $headerScripts !!}
    {!! $dispatcherJs !!}

    <style>
        :root {
            --primary-color: {{ $funnel->design_settings['primary_color'] ?? '#1e40af' }};
            --bg-color: #f8fafc;
        }

        body {
            background-color: var(--bg-color);
            font-family: system-ui, -apple-system, sans-serif;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .funnel-card {
            width: 100%;
            max-width: 640px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .progress-bar-custom {
            height: 6px;
            background-color: var(--primary-color);
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        .option-btn {
            border: 2px solid #e2e8f0;
            background: #ffffff;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            text-align: right;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s ease;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .option-btn:hover, .option-btn.selected {
            border-color: var(--primary-color);
            background-color: #eff6ff;
            color: var(--primary-color);
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 0.85rem 2rem;
            font-weight: 700;
            font-size: 1.05rem;
            transition: all 0.2s ease;
            width: 100%;
        }
        .btn-primary-custom:hover {
            opacity: 0.9;
            color: #ffffff;
        }

        .btn-whatsapp-custom {
            background-color: #25d366;
            color: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 0.85rem 2rem;
            font-weight: 700;
            font-size: 1.05rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            text-decoration: none;
        }
        .btn-whatsapp-custom:hover {
            background-color: #128c7e;
            color: #ffffff;
        }
    </style>
</head>
<body>

@if($isPreview)
    <div class="position-fixed top-0 start-0 w-100 bg-warning text-dark text-center fw-bold py-1 z-3">
        👁️ PREVIEW MODE (No Production Leads or Analytics Created)
    </div>
@endif

<div class="funnel-card">
    <!-- Progress Bar -->
    <div class="progress mb-4 bg-light rounded-pill" style="height: 6px;">
        <div class="progress-bar-custom" id="funnel_progress_bar" style="width: 10%;"></div>
    </div>

    <!-- Dynamic Step Views Wrapper -->
    <div id="step_content_wrapper">
        @foreach($funnel->steps as $sIndex => $step)
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
                        @elseif($element->element_type === 'text')
                            <p class="text-muted fs-6 mb-3">{{ $element->label }}</p>
                        @elseif($element->element_type === 'single_choice')
                            <div class="d-flex flex-column gap-2">
                                @foreach($element->properties['options'] ?? [] as $opt)
                                    <div class="option-btn" onclick="selectOption({{ $element->id }}, '{{ $opt['value'] ?? $opt['label'] }}', {{ $opt['score'] ?? 0 }}, this)">
                                        <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                        <iconify-icon icon="solar:alt-arrow-left-bold" class="text-muted"></iconify-icon>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($element->element_type === 'contact_form')
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <label class="form-label fw-bold">الاسم الكامل <span class="text-danger">*</span></label>
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
                        @elseif($element->element_type === 'button')
                            <button type="button" class="btn-primary-custom mt-3" onclick="nextStep()">
                                {{ $element->label ?: 'متابعة 🚀' }}
                            </button>
                        @endif
                    </div>
                @endforeach

                @if($step->step_type !== 'welcome' && $step->step_type !== 'lead_form')
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" onclick="prevStep()">
                            ← السابق
                        </button>
                        <button type="button" class="btn-primary-custom w-auto px-4" onclick="nextStep()">
                            التالي ➔
                        </button>
                    </div>
                @elseif($step->step_type === 'lead_form')
                    <button type="button" class="btn-primary-custom mt-3" id="btn_submit_final" onclick="submitFunnelForm()">
                        عرض النتيجة والتقرير التفصيلي ✨
                    </button>
                @endif
            </div>
        @endforeach

        <!-- RESULT CONTAINER (Hidden until completion) -->
        <div class="funnel-step-view d-none text-center py-4" id="result_container">
            <div class="mb-3">
                <iconify-icon icon="solar:verified-check-bold-duotone" width="80" class="text-success"></iconify-icon>
            </div>
            <h2 class="fw-bold mb-2" id="res_title">جاري التقييم...</h2>
            <p class="text-muted fs-6 mb-4" id="res_desc"></p>

            <div class="p-3 bg-light rounded-4 mb-4" id="res_score_wrapper">
                <span class="text-muted small">النتيجة الإجمالية للتقييم</span>
                <div class="h2 fw-bold text-primary mb-0 mt-1" id="res_score_value">0%</div>
            </div>

            <div id="res_cta_wrapper"></div>
        </div>
    </div>
</div>

<script>
    const totalSteps = {{ count($funnel->steps) }};
    let currentStepIndex = 0;
    const userAnswers = {};
    const stepIds = [
        @foreach($funnel->steps as $s)
            {{ $s->id }},
        @endforeach
    ];

    function updateProgress() {
        const percent = Math.round(((currentStepIndex + 1) / totalSteps) * 100);
        document.getElementById('funnel_progress_bar').style.width = percent + '%';
    }

    function selectOption(elementId, value, score, el) {
        userAnswers[elementId] = value;
        const parent = el.parentElement;
        parent.querySelectorAll('.option-btn').forEach(btn => btn.classList.remove('selected'));
        el.classList.add('selected');

        if (window.trackFunnelEvent) {
            window.trackFunnelEvent('QuestionAnswered', { elementId, value });
        }

        setTimeout(() => {
            nextStep();
        }, 250);
    }

    function nextStep() {
        if (currentStepIndex < totalSteps - 1) {
            document.getElementById('step_view_' + stepIds[currentStepIndex]).classList.add('d-none');
            currentStepIndex++;
            document.getElementById('step_view_' + stepIds[currentStepIndex]).classList.remove('d-none');
            updateProgress();
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
        const fullName = document.getElementById('input_full_name')?.value;
        const phone = document.getElementById('input_phone')?.value;
        const email = document.getElementById('input_email')?.value;

        if (document.getElementById('input_full_name') && (!fullName || !phone)) {
            alert('الرجاء كتابة الاسم ورقم الواتساب للاستمرار.');
            return;
        }

        if (fullName) userAnswers['full_name'] = fullName;
        if (phone) userAnswers['phone'] = phone;
        if (email) userAnswers['email'] = email;

        const btn = document.getElementById('btn_submit_final');
        if (btn) btn.disabled = true;

        fetch(`{{ route('funnels.public.submit', $funnel->slug) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                answers: userAnswers,
                is_preview: {{ $isPreview ? 'true' : 'false' }}
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.funnel-step-view').forEach(e => e.classList.add('d-none'));
                const resBox = document.getElementById('result_container');
                resBox.classList.remove('d-none');

                document.getElementById('res_title').innerText = data.result?.title || 'تم التقييم بنجاح';
                document.getElementById('res_desc').innerText = data.result?.description || '';
                document.getElementById('res_score_value').innerText = data.score + ' نقطة';

                let ctaHtml = '';
                if (data.result?.cta_type === 'whatsapp' && data.result?.cta_whatsapp_number) {
                    const waNum = data.result.cta_whatsapp_number.replace(/\D/g, '');
                    const waText = encodeURIComponent('مرحباً، أود استكمال التقديم بناءً على نتيجة التقييم: ' + (data.result?.title || ''));
                    ctaHtml = `<a href="https://wa.me/${waNum}?text=${waText}" target="_blank" class="btn-whatsapp-custom">
                        <iconify-icon icon="logos:whatsapp-icon" width="24"></iconify-icon>
                        ${data.result?.cta_label || 'تواصل معنا عبر الواتساب'}
                    </a>`;
                } else if (data.result?.cta_url) {
                    ctaHtml = `<a href="${data.result.cta_url}" target="_blank" class="btn-primary-custom text-decoration-none">
                        ${data.result?.cta_label || 'المتابعة الان'}
                    </a>`;
                }

                document.getElementById('res_cta_wrapper').innerHTML = ctaHtml;

                if (window.trackFunnelEvent) {
                    window.trackFunnelEvent('Lead', { score: data.score });
                    window.trackFunnelEvent('ResultViewed', { title: data.result?.title });
                }
            }
        })
        .catch(err => {
            alert('حدث خطأ أثناء الاتصال، الرجاء المحاولة مرة أخرى.');
            if (btn) btn.disabled = false;
        });
    }
</script>

</body>
</html>
