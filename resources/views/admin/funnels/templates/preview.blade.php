@php
    $steps = $schema['steps'] ?? [];
    $results = $schema['results'] ?? [];
    $design = $schema['design_settings'] ?? [];
    $primaryColor = $design['primary_color'] ?? '#2563eb';
    $fontFamily = $design['font_family'] ?? 'Tajawal, sans-serif';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معاينة قالب: {{ $template->name }} — Travel Wave</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <!-- Iconify -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
        }
        body {
            font-family: {{ $fontFamily }}, 'Tajawal', sans-serif;
            background-color: #0f172a;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Preview Toolbar */
        .preview-toolbar {
            background-color: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 12px 24px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
        }

        .device-toggle-btn {
            background: #334155;
            border: 1px solid #475569;
            color: #cbd5e1;
            padding: 6px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .device-toggle-btn.active, .device-toggle-btn:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        /* Simulator Container */
        .simulator-area {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 30px 15px;
            overflow-y: auto;
            background: radial-gradient(circle at top, #1e293b 0%, #0f172a 100%);
        }

        .simulator-viewport {
            background: #ffffff;
            width: 100%;
            max-width: 680px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            transition: max-width 0.3s ease, min-height 0.3s ease;
            position: relative;
        }

        .simulator-viewport.mobile-mode {
            max-width: 390px;
            min-height: 680px;
            border: 12px solid #1e293b;
            border-radius: 36px;
        }

        /* Funnel Content Elements */
        .funnel-header-bar {
            padding: 20px 24px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .progress-track {
            height: 6px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-bar-fill {
            background-color: var(--primary-color);
            height: 100%;
            width: 15%;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-panel {
            padding: 32px 28px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .option-choice-card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 20px;
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .option-choice-card:hover {
            border-color: var(--primary-color);
            background: #f0f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }
        .option-choice-card.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: #fff;
        }

        .btn-action-primary {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-action-primary:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-wa-action {
            background-color: #25d366;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 16px rgba(37, 211, 102, 0.25);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- Top Preview Toolbar -->
    <div class="preview-toolbar">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.funnels.templates.index') }}" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1 rounded-3">
                <iconify-icon icon="solar:arrow-right-linear" width="18"></iconify-icon>
                <span>العودة لمكتبة القوالب</span>
            </a>
            <div>
                <span class="badge bg-primary me-2">{{ $template->category }}</span>
                <strong class="text-white fs-6">{{ $template->name }}</strong>
                <span class="text-muted ms-2 small d-none d-md-inline">({{ count($steps) }} خطوات تفاعلية)</span>
            </div>
        </div>

        <!-- Viewport switchers -->
        <div class="d-flex align-items-center gap-2">
            <button class="device-toggle-btn active" id="btn_desktop" onclick="setDevice('desktop')">
                <iconify-icon icon="solar:laptop-minimalistic-bold" width="18"></iconify-icon>
                <span>كمبيوتر</span>
            </button>
            <button class="device-toggle-btn" id="btn_mobile" onclick="setDevice('mobile')">
                <iconify-icon icon="solar:smartphone-bold" width="18"></iconify-icon>
                <span>جوال</span>
            </button>
        </div>

        <!-- Use Template Action -->
        <div>
            <form action="{{ route('admin.funnels.templates.use', $template) }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-success fw-bold px-4 rounded-3 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:magic-stick-3-bold" width="20"></iconify-icon>
                    <span>استخدام هذا القالب 🚀</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Simulator Screen Area -->
    <div class="simulator-area">
        <div class="simulator-viewport" id="simulator_box">
            
            <!-- Progress Bar -->
            <div class="funnel-header-bar">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold" id="step_indicator_text">الخطوة 1 من {{ count($steps) }}</span>
                    <button class="btn btn-sm btn-light border-0 py-0 px-2 text-muted" id="btn_restart" onclick="restartPreview()" title="إعادة تشغيل">
                        <iconify-icon icon="solar:restart-bold" width="16"></iconify-icon>
                        <span class="small">إعادة</span>
                    </button>
                </div>
                <div class="progress-track">
                    <div class="progress-bar-fill" id="progress_bar_fill"></div>
                </div>
            </div>

            <!-- Steps Container -->
            @foreach($steps as $sIndex => $step)
                <div class="step-panel {{ $sIndex === 0 ? '' : 'd-none' }}" id="step_view_{{ $sIndex }}">
                    
                    @if(!empty($step['title']))
                        <h3 class="fw-bolder text-dark mb-2">{{ $step['title'] }}</h3>
                    @endif

                    @if(!empty($step['subtitle']))
                        <p class="text-muted mb-4 fs-6">{{ $step['subtitle'] }}</p>
                    @endif

                    <!-- Elements -->
                    @foreach($step['elements'] ?? [] as $eIndex => $element)
                        <div class="mb-4">
                            @if(($element['element_type'] ?? '') === 'heading')
                                <h4 class="fw-bold text-primary mb-3">{{ $element['label'] ?? '' }}</h4>
                            @elseif(($element['element_type'] ?? '') === 'text')
                                <p class="text-muted mb-3 fs-6">{{ $element['label'] ?? '' }}</p>
                            @elseif(($element['element_type'] ?? '') === 'single_choice')
                                <div class="d-flex flex-column gap-2">
                                    @foreach($element['properties']['options'] ?? [] as $opt)
                                        <div class="option-choice-card" onclick="chooseOption({{ $sIndex }}, {{ $opt['score'] ?? 0 }}, '{{ addslashes($opt['value'] ?? $opt['label'] ?? '') }}', this)">
                                            <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            <iconify-icon icon="solar:alt-arrow-left-linear" width="20" class="text-muted opacity-50"></iconify-icon>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(($element['element_type'] ?? '') === 'contact_form')
                                <div class="bg-light p-3 rounded-4 mb-3 border">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">الاسم بالكامل <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control rounded-3" placeholder="مثال: أحمد محمد" value="أحمد محمد">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">رقم الواتساب <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control rounded-3" placeholder="05XXXXXXXX" value="0501234567">
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold small">البريد الإلكتروني</label>
                                        <input type="email" class="form-control rounded-3" placeholder="ahmad@example.com" value="ahmad@example.com">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Navigation Action Buttons -->
                    @if($sIndex === 0)
                        <button type="button" class="btn-action-primary mt-3" onclick="nextStep({{ $sIndex }})">
                            <span>ابدأ التقييم الآن ➔</span>
                            <iconify-icon icon="solar:alt-arrow-left-bold" width="20"></iconify-icon>
                        </button>
                    @elseif($sIndex === count($steps) - 1 || ($step['step_type'] ?? '') === 'lead_form')
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                            <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" onclick="prevStep({{ $sIndex }})">
                                ← السابق
                            </button>
                            <button type="button" class="btn-action-primary w-auto px-5" onclick="finishFunnel()">
                                <span>عرض النتيجة والتقرير ✨</span>
                            </button>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                            <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" onclick="prevStep({{ $sIndex }})">
                                ← السابق
                            </button>
                            <button type="button" class="btn-action-primary w-auto px-4" onclick="nextStep({{ $sIndex }})">
                                التالي ➔
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Final Result Box -->
            <div class="step-panel text-center py-5 d-none" id="result_panel">
                <div class="mb-3">
                    <iconify-icon icon="solar:verified-check-bold-duotone" width="80" class="text-success"></iconify-icon>
                </div>
                <h3 class="fw-bold mb-2 text-dark" id="res_title">🎉 مؤهل بنجاح</h3>
                <p class="text-muted fs-6 mb-4 px-3" id="res_desc">نتيجتك ممتازة! تفاصيل ملفك مطابقة للمعايير المطلوبة بناءً على إجاباتك.</p>

                <div class="p-3 bg-light rounded-4 mb-4 d-inline-block px-5 border">
                    <span class="text-muted small fw-bold">النتيجة الإجمالية المحسوبة</span>
                    <div class="h2 fw-bold text-primary mb-0 mt-1" id="res_score">85%</div>
                </div>

                <div class="mt-2">
                    <a href="javascript:void(0)" class="btn-wa-action" onclick="alert('في الفانل المنشور الفعلي، ينقلك هذا الزر مباشرة لمحادثة الواتساب مع المستشار المسؤول!')">
                        <iconify-icon icon="logos:whatsapp-icon" width="24"></iconify-icon>
                        <span id="res_cta_text">تواصل معنا الآن عبر الواتساب</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        const totalSteps = {{ count($steps) }};
        let currentStep = 0;
        let userScore = 0;
        const resultsList = @json($results);

        function updateProgress(stepIdx) {
            const percent = Math.round(((stepIdx + 1) / totalSteps) * 100);
            document.getElementById('progress_bar_fill').style.width = percent + '%';
            document.getElementById('step_indicator_text').innerText = `الخطوة ${stepIdx + 1} من ${totalSteps}`;
        }

        function setDevice(type) {
            const box = document.getElementById('simulator_box');
            const btnDesk = document.getElementById('btn_desktop');
            const btnMob = document.getElementById('btn_mobile');

            if (type === 'mobile') {
                box.classList.add('mobile-mode');
                btnMob.classList.add('active');
                btnDesk.classList.remove('active');
            } else {
                box.classList.remove('mobile-mode');
                btnDesk.classList.add('active');
                btnMob.classList.remove('active');
            }
        }

        function chooseOption(stepIdx, score, val, el) {
            userScore += score;
            const parent = el.parentElement;
            parent.querySelectorAll('.option-choice-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');

            setTimeout(() => {
                nextStep(stepIdx);
            }, 250);
        }

        function nextStep(currentIdx) {
            if (currentIdx < totalSteps - 1) {
                document.getElementById('step_view_' + currentIdx).classList.add('d-none');
                const nextIdx = currentIdx + 1;
                document.getElementById('step_view_' + nextIdx).classList.remove('d-none');
                updateProgress(nextIdx);
            } else {
                finishFunnel();
            }
        }

        function prevStep(currentIdx) {
            if (currentIdx > 0) {
                document.getElementById('step_view_' + currentIdx).classList.add('d-none');
                const prevIdx = currentIdx - 1;
                document.getElementById('step_view_' + prevIdx).classList.remove('d-none');
                updateProgress(prevIdx);
            }
        }

        function finishFunnel() {
            for (let i = 0; i < totalSteps; i++) {
                const el = document.getElementById('step_view_' + i);
                if (el) el.classList.add('d-none');
            }

            document.getElementById('progress_bar_fill').style.width = '100%';
            document.getElementById('step_indicator_text').innerText = 'تم التقييم بنجاح ✅';

            // Match result based on calculated score
            let matchedResult = resultsList[0] || {};
            for (let res of resultsList) {
                if (userScore >= (res.min_score || 0) && userScore <= (res.max_score || 100)) {
                    matchedResult = res;
                    break;
                }
            }

            document.getElementById('res_title').innerText = matchedResult.title || 'تم التقييم بنجاح';
            document.getElementById('res_desc').innerText = matchedResult.description || '';
            document.getElementById('res_score').innerText = userScore + ' نقطة';
            if (matchedResult.cta_label) {
                document.getElementById('res_cta_text').innerText = matchedResult.cta_label;
            }

            document.getElementById('result_panel').classList.remove('d-none');
        }

        function restartPreview() {
            userScore = 0;
            document.getElementById('result_panel').classList.add('d-none');
            for (let i = 0; i < totalSteps; i++) {
                const el = document.getElementById('step_view_' + i);
                if (el) {
                    if (i === 0) el.classList.remove('d-none');
                    else el.classList.add('d-none');
                }
            }
            document.querySelectorAll('.option-choice-card').forEach(c => c.classList.remove('selected'));
            updateProgress(0);
        }

        // Initialize progress
        updateProgress(0);
    </script>
</body>
</html>
