@php
    $steps = $schema['steps'] ?? [];
    $results = $schema['results'] ?? [];
    $design = $schema['design_settings'] ?? [];
    $primaryColor = $design['primary_color'] ?? '#2563eb';
    $fontFamily = $design['font_family'] ?? 'Tajawal, sans-serif';
    $scoringEnabled = $design['scoring_enabled'] ?? true;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معاينة قالب: {{ $template->name }} — Travel Wave</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
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
        .progress-fill {
            height: 100%;
            background-color: var(--primary-color);
            width: 20%;
            transition: width 0.3s ease;
        }

        .simulator-content {
            padding: 2rem 2.5rem;
            color: #0f172a;
        }

        .option-card-interactive {
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .option-card-interactive:hover {
            border-color: var(--primary-color);
            background: #eff6ff;
            transform: translateY(-2px);
        }
        .option-card-interactive.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: #ffffff;
        }
        .option-card-interactive.selected iconify-icon {
            color: #ffffff !important;
        }

        /* Radio Choice */
        .radio-option-card {
            border: 2px solid #e2e8f0;
            background: #ffffff;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .radio-option-card:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }
        .radio-option-card.selected {
            border-color: var(--primary-color);
            background: #eff6ff;
        }
        .radio-circle-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .radio-option-card.selected .radio-circle-indicator {
            border-color: var(--primary-color);
            background: var(--primary-color);
        }
        .radio-circle-indicator::after {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #fff;
            display: none;
        }
        .radio-option-card.selected .radio-circle-indicator::after {
            display: block;
        }

        /* Checkbox Choice */
        .checkbox-option-card {
            border: 2px solid #e2e8f0;
            background: #ffffff;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .checkbox-option-card:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }
        .checkbox-option-card.selected {
            border-color: var(--primary-color);
            background: #eff6ff;
        }
        .checkbox-square-indicator {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
        }
        .checkbox-option-card.selected .checkbox-square-indicator {
            border-color: var(--primary-color);
            background: var(--primary-color);
        }

        .btn-simulator-primary {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-size: 1.05rem;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
        }

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
    </style>
</head>
<body>

<!-- TOP TOOLBAR -->
<div class="preview-toolbar">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.funnels.templates.index') }}" class="btn btn-outline-light btn-sm rounded-3">
            ← العودة للقوالب
        </a>
        <div>
            <h6 class="mb-0 fw-bold text-white">{{ $template->name }}</h6>
            <small class="text-secondary">{{ $template->category }} | {{ $template->complexity }}</small>
        </div>
    </div>

    <!-- Switch Viewport Device -->
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="device-toggle-btn active" id="btn_desktop" onclick="setDevice('desktop')">
            <iconify-icon icon="solar:laptop-minimalistic-bold"></iconify-icon>
            <span>كمبيوتر</span>
        </button>
        <button type="button" class="device-toggle-btn" id="btn_mobile" onclick="setDevice('mobile')">
            <iconify-icon icon="solar:smartphone-bold"></iconify-icon>
            <span>جوال</span>
        </button>
    </div>

    <!-- Use Template Action -->
    <form action="{{ route('admin.funnels.templates.use', $template) }}" method="POST" class="m-0">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm fw-bold px-3 rounded-3">
            ⚡ استخدام هذا القالب
        </button>
    </form>
</div>

<!-- SIMULATOR CANVAS AREA -->
<div class="simulator-area">
    <div class="simulator-viewport" id="simulator_container">
        
        <!-- Progress Header -->
        <div class="funnel-header-bar">
            <div class="progress-track">
                <div class="progress-fill" id="sim_progress"></div>
            </div>
        </div>

        <div class="simulator-content">
            <div id="sim_step_wrapper">
                <!-- Rendered dynamically by JS Engine -->
            </div>
        </div>

    </div>
</div>

<script>
    const schemaData = @json($schema);
    const steps = schemaData.steps || [];
    const results = schemaData.results || [];
    const scoringEnabled = {{ $scoringEnabled ? 'true' : 'false' }};
    let currentStepIdx = 0;
    let accumulatedScore = 0;

    document.addEventListener('DOMContentLoaded', () => {
        renderSimulatorStep();
    });

    function setDevice(type) {
        const container = document.getElementById('simulator_container');
        const btnDesk = document.getElementById('btn_desktop');
        const btnMob = document.getElementById('btn_mobile');

        if (type === 'mobile') {
            container.classList.add('mobile-mode');
            btnMob.classList.add('active');
            btnDesk.classList.remove('active');
        } else {
            container.classList.remove('mobile-mode');
            btnDesk.classList.add('active');
            btnMob.classList.remove('active');
        }
    }

    function renderSimulatorStep() {
        const wrapper = document.getElementById('sim_step_wrapper');
        const progressBar = document.getElementById('sim_progress');

        if (steps.length === 0) {
            wrapper.innerHTML = '<div class="text-center py-5 text-muted">لا توجد خطوات في هذا القالب.</div>';
            return;
        }

        const pct = Math.round(((currentStepIdx + 1) / steps.length) * 100);
        progressBar.style.width = pct + '%';

        const step = steps[currentStepIdx];
        let html = `
            <div class="mb-4">
                <h3 class="fw-bold mb-1 text-dark">${escapeHtml(step.title || '')}</h3>
                ${step.subtitle ? `<p class="text-muted fs-6 mb-0">${escapeHtml(step.subtitle)}</p>` : ''}
            </div>
            <div class="d-flex flex-column gap-3 mb-4">
        `;

        (step.elements || []).forEach((el) => {
            const reqStar = el.is_required ? '<span class="text-danger ms-1">*</span>' : '';
            
            // Radio Choice
            if (el.element_type === 'radio_choice') {
                html += `<label class="fw-bold text-dark mb-1">${escapeHtml(el.label || '')}${reqStar}</label>`;
                (el.properties?.options || []).forEach(opt => {
                    html += `
                        <div class="radio-option-card" onclick="selectSimRadioOption(${opt.score || 0}, this)">
                            <div class="d-flex align-items-center gap-3">
                                <div class="radio-circle-indicator"></div>
                                <span>${escapeHtml(opt.label || opt.value)}</span>
                            </div>
                            ${scoringEnabled && (opt.score || 0) > 0 ? `<span class="badge bg-primary-subtle text-primary small">+${opt.score} نقطة</span>` : ''}
                        </div>
                    `;
                });

            // Checkbox Choice
            } else if (el.element_type === 'checkbox_choice') {
                html += `<label class="fw-bold text-dark mb-1">${escapeHtml(el.label || '')}${reqStar}</label>`;
                (el.properties?.options || []).forEach(opt => {
                    html += `
                        <div class="checkbox-option-card" onclick="toggleSimCheckboxOption(${opt.score || 0}, this)">
                            <div class="d-flex align-items-center gap-3">
                                <div class="checkbox-square-indicator"><iconify-icon icon="solar:check-bold"></iconify-icon></div>
                                <span>${escapeHtml(opt.label || opt.value)}</span>
                            </div>
                            ${scoringEnabled && (opt.score || 0) > 0 ? `<span class="badge bg-primary-subtle text-primary small">+${opt.score} نقطة</span>` : ''}
                        </div>
                    `;
                });

            // Single / Multiple Choice
            } else if (el.element_type === 'single_choice' || el.element_type === 'multiple_choice' || el.element_type === 'yes_no') {
                html += `<label class="fw-bold text-dark mb-1">${escapeHtml(el.label || '')}${reqStar}</label>`;
                (el.properties?.options || []).forEach(opt => {
                    html += `
                        <div class="option-card-interactive" onclick="selectSimOption(${opt.score || 0}, this)">
                            <span>${escapeHtml(opt.label || opt.value)}</span>
                            <iconify-icon icon="solar:alt-arrow-left-bold" class="text-muted"></iconify-icon>
                        </div>
                    `;
                });

            // Image Cards
            } else if (['image_choice', 'single_image_choice', 'multiple_image_choice'].includes(el.element_type)) {
                html += `<label class="fw-bold text-dark mb-1">${escapeHtml(el.label || '')}${reqStar}</label><div class="row g-2">`;
                (el.properties?.options || []).forEach(opt => {
                    html += `
                        <div class="col-6">
                            <div class="option-card-interactive text-center flex-column p-3 h-100" onclick="selectSimOption(${opt.score || 0}, this)">
                                ${opt.image_url ? `<img src="${opt.image_url}" class="rounded-3 mb-2" style="max-height: 80px; width: 100%; object-fit: cover;">` : '<iconify-icon icon="solar:gallery-bold-duotone" width="36" class="text-primary mb-2"></iconify-icon>'}
                                <span class="fw-bold small">${escapeHtml(opt.label || '')}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';

            // Slider & Currency
            } else if (el.element_type === 'slider' || el.element_type === 'currency') {
                const min = el.properties?.min || 0;
                const max = el.properties?.max || 50000;
                const unit = el.properties?.show_currency !== false ? (el.properties?.currency_code || 'SAR') : (el.properties?.custom_unit || '');
                html += `
                    <label class="fw-bold text-dark mb-1">${escapeHtml(el.label || '')}${reqStar}</label>
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <h4 class="fw-bold text-primary mb-1">${(min + max)/2} ${unit}</h4>
                        <input type="range" class="form-range" min="${min}" max="${max}">
                        <div class="d-flex justify-content-between text-muted small"><span>${min} ${unit}</span><span>${max} ${unit}</span></div>
                    </div>
                `;

            // File Upload
            } else if (el.element_type === 'file_upload') {
                html += `
                    <label class="fw-bold text-dark mb-1">${escapeHtml(el.label || '')}${reqStar}</label>
                    <div class="p-4 border border-dashed rounded-3 text-center bg-light cursor-pointer">
                        <iconify-icon icon="solar:upload-track-bold-duotone" width="36" class="text-primary mb-1"></iconify-icon>
                        <p class="mb-0 fw-bold small text-dark">انقر لاختيار ملف أو اسحبه هنا</p>
                        <small class="text-muted">PDF, JPG, PNG حتى 10MB</small>
                    </div>
                `;

            // Contact Form
            } else if (el.element_type === 'contact_form') {
                html += `
                    <div class="bg-light p-3 rounded-4 border">
                        <h6 class="fw-bold text-primary mb-2">بيانات التواصل:</h6>
                        <input type="text" class="form-control mb-2" placeholder="الاسم الكريم">
                        <input type="tel" class="form-control mb-2" placeholder="رقم الواتساب">
                        <input type="email" class="form-control" placeholder="البريد الإلكتروني">
                    </div>
                `;

            // Dedicated Contact Inputs
            } else if (el.element_type === 'email') {
                html += `<div class="input-group"><span class="input-group-text bg-light">✉️</span><input type="email" class="form-control" placeholder="name@example.com"></div>`;
            } else if (el.element_type === 'phone') {
                html += `<div class="input-group"><span class="input-group-text bg-light">📱</span><input type="tel" class="form-control" placeholder="05XXXXXXXX"></div>`;
            } else if (el.element_type === 'address') {
                html += `<div class="input-group"><span class="input-group-text bg-light">📍</span><input type="text" class="form-control" placeholder="المدينة، الحي"></div>`;
            } else if (el.element_type === 'website') {
                html += `<div class="input-group"><span class="input-group-text bg-light">🌐</span><input type="url" class="form-control" placeholder="https://example.com"></div>`;
            } else if (el.element_type === 'country') {
                html += `<div class="input-group"><span class="input-group-text bg-light">🌍</span><select class="form-select"><option>🇸🇦 المملكة العربية السعودية</option><option>🇦🇪 الإمارات العربية المتحدة</option></select></div>`;

            // Date & Appointment
            } else if (['date_picker', 'date_time', 'schedule'].includes(el.element_type)) {
                html += `
                    <label class="fw-bold text-dark mb-1">${escapeHtml(el.label || '')}${reqStar}</label>
                    <div class="input-group mb-2"><span class="input-group-text bg-light">📅</span><input type="date" class="form-control"></div>
                    <div class="d-flex gap-2"><span class="badge bg-light text-dark border p-2">صباحاً (09:00 - 12:00)</span><span class="badge bg-light text-dark border p-2">مساءً (04:00 - 08:00)</span></div>
                `;

            // Timer
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

            // Rating
            } else if (el.element_type === 'rating') {
                html += `<label class="fw-bold text-dark mb-1">${escapeHtml(el.label || '')}${reqStar}</label><div class="d-flex gap-2 text-warning fs-3">⭐⭐⭐⭐⭐</div>`;

            // Table
            } else if (el.element_type === 'table') {
                html += `
                    <table class="table table-sm table-bordered bg-light mb-0 small text-center">
                        <tr class="table-primary"><th>الباقة</th><th>الخدمات</th><th>السعر</th></tr>
                        <tr><td>الأساسية</td><td>طلب التأشيرة والموعد</td><td>250 SAR</td></tr>
                    </table>
                `;

            // Testimonials
            } else if (el.element_type === 'testimonials') {
                html += `<div class="p-3 bg-light rounded-3 border text-center"><p class="fst-italic small mb-1">"خدمة استثنائية وسرعة فائقة!"</p><strong class="text-primary small">— فهد الشمري ⭐⭐⭐⭐⭐</strong></div>`;

            // FAQs
            } else if (el.element_type === 'faqs') {
                html += `<div class="p-3 bg-light rounded-3 border"><div class="fw-bold small text-primary mb-1">❓ ما هي شروط التأشيرة؟</div><div class="text-muted small">جواز سفر ساري وحساب بنكي...</div></div>`;

            // Coupon
            } else if (el.element_type === 'coupon_code') {
                html += `<div class="p-3 bg-light rounded-3 border border-dashed text-center"><h5 class="fw-bold text-danger mb-0">كود الخصم: WAVE2026 (خصم 20%)</h5></div>`;

            } else {
                html += `<input type="text" class="form-control" placeholder="${escapeHtml(el.label || 'إجابتك...')}" disabled>`;
            }
        });

        html += '</div>';

        // Navigation Actions
        if (currentStepIdx < steps.length - 1) {
            html += `
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    ${currentStepIdx > 0 ? `<button type="button" class="btn btn-link text-muted" onclick="currentStepIdx--; renderSimulatorStep();">← السابق</button>` : '<div></div>'}
                    <button type="button" class="btn btn-simulator-primary w-auto px-4" onclick="currentStepIdx++; renderSimulatorStep();">التالي ➔</button>
                </div>
            `;
        } else {
            html += `
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    ${currentStepIdx > 0 ? `<button type="button" class="btn btn-link text-muted" onclick="currentStepIdx--; renderSimulatorStep();">← السابق</button>` : '<div></div>'}
                    <button type="button" class="btn btn-simulator-primary w-auto px-5" onclick="showSimulatorResult()">عرض النتيجة 🎉</button>
                </div>
            `;
        }

        wrapper.innerHTML = html;
    }

    function selectSimOption(score, el) {
        accumulatedScore += score;
        el.parentElement.querySelectorAll('.option-card-interactive').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');
        setTimeout(() => {
            if (currentStepIdx < steps.length - 1) {
                currentStepIdx++;
                renderSimulatorStep();
            } else {
                showSimulatorResult();
            }
        }, 220);
    }

    function selectSimRadioOption(score, el) {
        accumulatedScore += score;
        el.parentElement.querySelectorAll('.radio-option-card').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');
        setTimeout(() => {
            if (currentStepIdx < steps.length - 1) {
                currentStepIdx++;
                renderSimulatorStep();
            } else {
                showSimulatorResult();
            }
        }, 220);
    }

    function toggleSimCheckboxOption(score, el) {
        el.classList.toggle('selected');
    }

    function showSimulatorResult() {
        const wrapper = document.getElementById('sim_step_wrapper');
        const res = results[0] || { title: '🎉 تم استلام طلبك بنجاح', description: 'شكراً لتواصلك معنا!' };

        let html = `
            <div class="text-center py-4">
                <iconify-icon icon="solar:verified-check-bold-duotone" width="70" class="text-success mb-3"></iconify-icon>
                <h3 class="fw-bold mb-2">${escapeHtml(res.title)}</h3>
                <p class="text-muted mb-4">${escapeHtml(res.description || '')}</p>
                ${scoringEnabled ? `
                    <div class="p-3 bg-light rounded-4 border d-inline-block px-5 mb-4">
                        <span class="small text-muted fw-bold">النقاط المحسوبة:</span>
                        <div class="h3 fw-bold text-primary mb-0 mt-1">${accumulatedScore} نقطة</div>
                    </div>
                ` : ''}
                <div>
                    <button type="button" class="btn btn-success fw-bold px-4 py-2 rounded-3" onclick="currentStepIdx = 0; accumulatedScore = 0; renderSimulatorStep();">
                        🔄 إعادة تجربة القالب
                    </button>
                </div>
            </div>
        `;
        wrapper.innerHTML = html;
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
