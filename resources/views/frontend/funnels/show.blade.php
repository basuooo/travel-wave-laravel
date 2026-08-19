@php
    $design = $funnel->design_settings ?? [];
    $primaryColor = $design['primary_color'] ?? '#2563eb';
    $fontFamily = $design['font_family'] ?? 'Tajawal, sans-serif';
    $scoringEnabled = $design['scoring_enabled'] ?? true;
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
        
        /* Standard Single Choice Card */
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

        /* Radio Choice (Distinct Radio UI) */
        .radio-option-card {
            border: 2px solid #e2e8f0;
            background: #ffffff;
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
        .radio-option-card:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }
        .radio-option-card.selected {
            border-color: var(--primary-color);
            background: #eff6ff;
        }
        .radio-circle-indicator {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .radio-option-card.selected .radio-circle-indicator {
            border-color: var(--primary-color);
            background: var(--primary-color);
        }
        .radio-circle-indicator::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
            display: none;
        }
        .radio-option-card.selected .radio-circle-indicator::after {
            display: block;
        }

        /* Checkbox Choice (Only Check Square) */
        .checkbox-option-card {
            border: 2px solid #e2e8f0;
            background: #ffffff;
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
        .checkbox-option-card:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }
        .checkbox-option-card.selected {
            border-color: var(--primary-color);
            background: #eff6ff;
        }
        .checkbox-square-indicator {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            transition: all 0.2s;
        }
        .checkbox-option-card.selected .checkbox-square-indicator {
            border-color: var(--primary-color);
            background: var(--primary-color);
        }

        /* Primary Button */
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

        /* Validation Error Highlight */
        .has-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
        }

        /* Timer Flip Box */
        .timer-box-digit {
            background: #1e293b;
            color: #f8fafc;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 24px;
            font-weight: 800;
            min-width: 48px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        /* Star Rating */
        .star-rating-box iconify-icon {
            font-size: 36px;
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
            width: 40px;
            height: 40px;
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

        /* Slot Buttons */
        .slot-btn {
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }
        .slot-btn.selected, .slot-btn:hover {
            border-color: var(--primary-color);
            background: #eff6ff;
            color: var(--primary-color);
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
                <div class="funnel-step-view {{ $sIndex === 0 ? '' : 'd-none' }}" id="step_view_{{ $step->id }}" data-step-index="{{ $sIndex }}" data-step-id="{{ $step->id }}">
                    
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
                            $isRequired = !empty($element->is_required);
                        @endphp

                        <div class="mb-4 element-block-wrapper" id="el_wrapper_{{ $element->id }}" data-element-id="{{ $element->id }}" data-required="{{ $isRequired ? '1' : '0' }}" data-type="{{ $type }}">
                            
                            {{-- 1. HEADING --}}
                            @if($type === 'heading')
                                <h3 class="fw-bold text-dark mb-2 border-start border-4 ps-2 border-primary">
                                    {{ $label ?: 'عنوان رئيسي' }}
                                    @if($isRequired) <span class="text-danger">*</span> @endif
                                </h3>

                            {{-- 2. PARAGRAPH / TEXT --}}
                            @elseif(in_array($type, ['text', 'paragraph']))
                                <p class="text-muted fs-6 mb-3 leading-relaxed">{{ $label }}</p>

                            {{-- 3. RADIO CHOICE (Specific Modern Radio) --}}
                            @elseif($type === 'radio_choice')
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="d-flex flex-column gap-2">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="radio-option-card" onclick="selectRadioOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="radio-circle-indicator"></div>
                                                <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            </div>
                                            @if($scoringEnabled && ($opt['score'] ?? 0) > 0)
                                                <span class="badge bg-primary-subtle text-primary small">+{{ $opt['score'] }} نقطة</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 4. CHECKBOX CHOICE (Only Box Checked) --}}
                            @elseif(in_array($type, ['checkbox_choice', 'multiple_choice']))
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="d-flex flex-column gap-2">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="checkbox-option-card" onclick="toggleCheckboxOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="checkbox-square-indicator"><iconify-icon icon="solar:check-bold" width="12"></iconify-icon></div>
                                                <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            </div>
                                            @if($scoringEnabled && ($opt['score'] ?? 0) > 0)
                                                <span class="badge bg-primary-subtle text-primary small">+{{ $opt['score'] }} نقطة</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 5. SINGLE CHOICE / YES-NO --}}
                            @elseif(in_array($type, ['single_choice', 'yes_no']))
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="d-flex flex-column gap-2">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="option-btn" onclick="selectOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                            <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            <iconify-icon icon="solar:alt-arrow-left-bold" class="text-muted"></iconify-icon>
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 6. IMAGE CHOICE (With Image Thumbnails) --}}
                            @elseif(in_array($type, ['image_choice', 'single_image_choice', 'multiple_image_choice']))
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="row g-3">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="col-6">
                                            <div class="option-btn text-center flex-column p-3 h-100" onclick="selectOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                                @if(!empty($opt['image_url']))
                                                    <img src="{{ $opt['image_url'] }}" class="rounded-3 mb-2" style="max-height: 90px; width: 100%; object-fit: cover;" alt="{{ $opt['label'] ?? '' }}">
                                                @else
                                                    <iconify-icon icon="solar:gallery-bold-duotone" width="40" class="text-primary mb-2"></iconify-icon>
                                                @endif
                                                <span class="fw-bold small">{{ $opt['label'] ?? $opt['value'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 7. COUNTRY SELECTOR --}}
                            @elseif($type === 'country')
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">🌍</span>
                                    <select class="form-select" id="input_el_{{ $element->id }}" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                        <option value="">اختر الدولة...</option>
                                        @foreach($props['options'] ?? [['label' => '🇸🇦 المملكة العربية السعودية', 'value' => 'Saudi Arabia'], ['label' => '🇦🇪 الإمارات العربية المتحدة', 'value' => 'UAE'], ['label' => '🇪🇬 جمهورية مصر العربية', 'value' => 'Egypt'], ['label' => '🇰🇼 دولة الكويت', 'value' => 'Kuwait'], ['label' => '🇶🇦 دولة قطر', 'value' => 'Qatar']] as $opt)
                                            <option value="{{ $opt['value'] ?? $opt['label'] }}">{{ $opt['label'] ?? $opt['value'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            {{-- 8. DROPDOWN --}}
                            @elseif(in_array($type, ['dropdown', 'autocomplete']))
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <select class="form-select form-select-lg rounded-3 bg-light" id="input_el_{{ $element->id }}" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                    <option value="">اختر من القائمة...</option>
                                    @foreach($props['options'] ?? [] as $opt)
                                        <option value="{{ $opt['value'] ?? $opt['label'] }}">{{ $opt['label'] ?? $opt['value'] }}</option>
                                    @endforeach
                                </select>

                            {{-- 9. SLIDER & CURRENCY --}}
                            @elseif(in_array($type, ['slider', 'currency']))
                                @php
                                    $min = $props['min'] ?? 0;
                                    $max = $props['max'] ?? 50000;
                                    $stepVal = $props['step'] ?? 1000;
                                    $showCurrency = ($props['show_currency'] ?? true) !== false;
                                    $unit = $showCurrency ? ($props['currency_code'] ?? 'SAR') : ($props['custom_unit'] ?? '');
                                    $defaultVal = ($min + $max) / 2;
                                @endphp
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="bg-light p-3 p-md-4 rounded-4 border text-center">
                                    <div class="h3 fw-bold text-primary mb-2" id="slider_val_{{ $element->id }}">
                                        {{ number_format($defaultVal) }} {{ $unit }}
                                    </div>
                                    <input type="range" class="form-range" min="{{ $min }}" max="{{ $max }}" step="{{ $stepVal }}" value="{{ $defaultVal }}" 
                                        oninput="document.getElementById('slider_val_{{ $element->id }}').innerText = Number(this.value).toLocaleString() + ' ' + '{{ $unit }}'; userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                    <div class="d-flex justify-content-between text-muted small mt-1">
                                        <span>{{ number_format($min) }} {{ $unit }}</span>
                                        <span>{{ number_format($max) }} {{ $unit }}</span>
                                    </div>
                                </div>

                            {{-- 10. FILE UPLOAD (Clickable Drag & Drop) --}}
                            @elseif($type === 'file_upload')
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="p-4 border border-dashed rounded-4 text-center bg-light" style="cursor: pointer;" onclick="document.getElementById('file_input_{{ $element->id }}').click();">
                                    <iconify-icon icon="solar:upload-track-bold-duotone" width="44" class="text-primary mb-2"></iconify-icon>
                                    <p class="mb-1 fw-bold text-dark" id="file_name_{{ $element->id }}">انقر هنا لاختيار ملف أو اسحبه إلى هذا المربع</p>
                                    <small class="text-muted">PDF, JPG, PNG حتى 10 ميجابايت</small>
                                    <input type="file" class="d-none" id="file_input_{{ $element->id }}" onchange="handleFileUpload({{ $element->id }}, this);">
                                </div>

                            {{-- 11. DEDICATED CONTACT INPUTS --}}
                            @elseif($type === 'email')
                                @if($label) <label class="form-label fw-bold small text-dark">{{ $label }} @if($isRequired) <span class="text-danger">*</span> @endif</label> @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">✉️</span>
                                    <input type="email" class="form-control" id="input_el_{{ $element->id }}" placeholder="name@example.com" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                </div>

                            @elseif($type === 'phone')
                                @if($label) <label class="form-label fw-bold small text-dark">{{ $label }} @if($isRequired) <span class="text-danger">*</span> @endif</label> @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">📱</span>
                                    <input type="tel" class="form-control" id="input_el_{{ $element->id }}" placeholder="05XXXXXXXX" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                </div>

                            @elseif($type === 'address')
                                @if($label) <label class="form-label fw-bold small text-dark">{{ $label }} @if($isRequired) <span class="text-danger">*</span> @endif</label> @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">📍</span>
                                    <input type="text" class="form-control" id="input_el_{{ $element->id }}" placeholder="المدينة، الحي، الشارع" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                </div>

                            @elseif($type === 'website')
                                @if($label) <label class="form-label fw-bold small text-dark">{{ $label }} @if($isRequired) <span class="text-danger">*</span> @endif</label> @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">🌐</span>
                                    <input type="url" class="form-control" id="input_el_{{ $element->id }}" placeholder="https://www.example.com" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                </div>

                            {{-- 12. CONTACT FORM --}}
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

                            {{-- 13. COUNTDOWN TIMER (Live Ticking Ticker) --}}
                            @elseif(in_array($type, ['timer', 'page_timer']))
                                @php
                                    $durationMins = $props['duration_minutes'] ?? 15;
                                @endphp
                                <div class="p-3 bg-dark text-white rounded-4 border text-center" id="timer_box_{{ $element->id }}" data-minutes="{{ $durationMins }}">
                                    <span class="small text-warning fw-bold d-block mb-2">⏰ {{ $label ?: 'احجز الآن! هذا العرض ساري لمدة:' }}</span>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <div class="timer-box-digit" id="timer_min_{{ $element->id }}">{{ sprintf('%02d', $durationMins) }}</div>
                                        <span class="fs-4 fw-bold text-warning">:</span>
                                        <div class="timer-box-digit" id="timer_sec_{{ $element->id }}">00</div>
                                    </div>
                                </div>

                            {{-- 14. APPOINTMENT & DATE PICKER --}}
                            @elseif(in_array($type, ['date_picker', 'date_time', 'schedule']))
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="input-group input-group-lg mb-3">
                                    <span class="input-group-text bg-light">📅</span>
                                    <input type="date" class="form-control" id="input_el_{{ $element->id }}" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="slot-btn" onclick="selectSlot({{ $element->id }}, 'صباحاً (09:00 - 12:00)', this)">صباحاً (09:00 - 12:00)</button>
                                    <button type="button" class="slot-btn" onclick="selectSlot({{ $element->id }}, 'ظهراً (12:00 - 04:00)', this)">ظهراً (12:00 - 04:00)</button>
                                    <button type="button" class="slot-btn" onclick="selectSlot({{ $element->id }}, 'مساءً (04:00 - 08:00)', this)">مساءً (04:00 - 08:00)</button>
                                </div>

                            {{-- 15. STAR RATING --}}
                            @elseif($type === 'rating')
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="bg-light p-3 rounded-4 border text-center">
                                    <div class="d-flex justify-content-center gap-2 star-rating-box" id="stars_box_{{ $element->id }}">
                                        @for($i = 1; $i <= 5; $i++)
                                            <iconify-icon icon="solar:star-bold" data-val="{{ $i }}" onclick="setStarRating({{ $element->id }}, {{ $i }}, this)"></iconify-icon>
                                        @endfor
                                    </div>
                                </div>

                            {{-- 16. NPS SCORE (0-10) --}}
                            @elseif($type === 'nps')
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
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

                            {{-- 17. TABLE --}}
                            @elseif($type === 'table')
                                @if($label) <h5 class="fw-bold mb-2 text-dark">{{ $label }}</h5> @endif
                                <div class="table-responsive bg-light p-2 rounded-4 border">
                                    <table class="table table-bordered mb-0 align-middle small text-center">
                                        <thead class="table-primary">
                                            <tr><th>الباقة</th><th>المميزات المشمولة</th><th>المدة</th><th>السعر</th></tr>
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

                            {{-- 18. TESTIMONIALS --}}
                            @elseif($type === 'testimonials')
                                <div class="bg-light p-3 p-md-4 rounded-4 border text-center">
                                    <iconify-icon icon="solar:chat-round-quotes-bold-duotone" width="36" class="text-primary mb-2"></iconify-icon>
                                    <p class="fst-italic text-dark fs-6 mb-2">"خدمة استثنائية وسرعة فائقة في استخراج التأشيرة وحجز الموعد خلال 48 ساعة فقط!"</p>
                                    <div class="d-flex justify-content-center text-warning gap-1 mb-1">⭐⭐⭐⭐⭐</div>
                                    <strong class="text-primary small">— فهد الدوسري (عميل موثق ✅)</strong>
                                </div>

                            {{-- 19. FAQS ACCORDION --}}
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

                            {{-- 20. COUPON CODE --}}
                            @elseif($type === 'coupon_code')
                                <div class="p-3 bg-light rounded-4 border border-dashed text-center">
                                    <span class="badge bg-warning text-dark fw-bold mb-1">🎁 هدية حصرية لك</span>
                                    <h4 class="fw-bold text-danger mb-1">كود خصم: WAVE2026</h4>
                                    <p class="text-muted small mb-2">استخدم هذا الكود للحصول على خصم 20% على رسوم الخدمة</p>
                                    <button type="button" class="btn btn-sm btn-dark px-3 rounded-pill" onclick="navigator.clipboard.writeText('WAVE2026'); alert('تم نسخ كود الخصم! 🎉')">
                                        نسخ الكود 📋
                                    </button>
                                </div>

                            {{-- 21. TEXT INPUT / SHORT / LONG ANSWER --}}
                            @elseif(in_array($type, ['short_answer', 'text_input']))
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <input type="text" class="form-control form-control-lg rounded-3" id="input_el_{{ $element->id }}" placeholder="اكتب إجابتك هنا..." onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">

                            @elseif(in_array($type, ['long_answer', 'textarea']))
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <textarea class="form-control form-control-lg rounded-3" id="input_el_{{ $element->id }}" rows="3" placeholder="اكتب تفاصيل إجابتك هنا..." onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});"></textarea>

                            {{-- 22. BUTTON --}}
                            @elseif($type === 'button')
                                <button type="button" class="btn-primary-custom mt-2" onclick="nextStep()">
                                    {{ $label ?: 'متابعة الخطوة التالية ➔' }}
                                </button>

                            @else
                                @if($label) 
                                    <label class="fw-bold mb-2 d-block fs-6 text-dark">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <input type="text" class="form-control form-control-lg rounded-3" id="input_el_{{ $element->id }}" placeholder="اكتب إجابتك هنا..." onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                            @endif

                        </div>
                    @endforeach

                    <!-- Navigation Action Footer -->
                    @if($sIndex === 0 && $step->step_type === 'welcome')
                        <button type="button" class="btn-primary-custom mt-3" onclick="nextStep()">
                            ابدأ الآن ➔
                        </button>
                    @elseif($sIndex === $totalSteps - 1 || $step->step_type === 'lead_form')
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" onclick="prevStep()">
                                ← السابق
                            </button>
                            <button type="button" class="btn-primary-custom w-auto px-5" id="btn_submit_final" onclick="submitFunnelForm()">
                                {{ $scoringEnabled ? 'عرض النتيجة والتقرير ✨' : 'إرسال الطلب بنجاح ✅' }}
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

            <!-- RESULT / SUCCESS CONTAINER -->
            <div class="funnel-step-view d-none text-center py-4" id="result_container">
                <div class="mb-3">
                    <iconify-icon icon="solar:verified-check-bold-duotone" width="80" class="text-success"></iconify-icon>
                </div>
                <h2 class="fw-bold mb-2 text-dark" id="res_title">🎉 تم استلام طلبك بنجاح</h2>
                <p class="text-muted fs-6 mb-4 px-3" id="res_desc">شكراً لتواصلك معنا! سيقوم فريق المستشارين بمراجعة بياناتك والتواصل معك فوراً.</p>

                @if($scoringEnabled)
                    <div class="p-3 bg-light rounded-4 mb-4 border d-inline-block px-5" id="res_score_wrapper">
                        <span class="text-muted small fw-bold">النتيجة الإجمالية المحسوبة</span>
                        <div class="h2 fw-bold text-primary mb-0 mt-1" id="res_score_value">0%</div>
                    </div>
                @endif

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
    const isScoringActive = {{ $scoringEnabled ? 'true' : 'false' }};
    let currentStepIndex = 0;
    let accumulatedScore = 0;
    const userAnswers = {};
    const stepIds = [
        @foreach($steps as $s)
            {{ $s->id }},
        @endforeach
    ];

    document.addEventListener('DOMContentLoaded', () => {
        initTimers();
    });

    function updateProgress() {
        if (totalSteps <= 0) return;
        const percent = Math.round(((currentStepIndex + 1) / totalSteps) * 100);
        document.getElementById('funnel_progress_bar').style.width = percent + '%';
    }

    function clearError(elementId) {
        const el = document.getElementById('el_wrapper_' + elementId);
        if (el) el.classList.remove('has-error');
    }

    function selectOption(elementId, value, score, el) {
        userAnswers[elementId] = value;
        accumulatedScore += (score || 0);
        clearError(elementId);

        const parent = el.parentElement;
        parent.querySelectorAll('.option-btn').forEach(btn => btn.classList.remove('selected'));
        el.classList.add('selected');

        setTimeout(() => {
            nextStep();
        }, 220);
    }

    function selectRadioOption(elementId, value, score, el) {
        userAnswers[elementId] = value;
        accumulatedScore += (score || 0);
        clearError(elementId);

        const parent = el.parentElement;
        parent.querySelectorAll('.radio-option-card').forEach(btn => btn.classList.remove('selected'));
        el.classList.add('selected');

        setTimeout(() => {
            nextStep();
        }, 220);
    }

    function toggleCheckboxOption(elementId, value, score, el) {
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
        clearError(elementId);
    }

    function handleFileUpload(elementId, fileInput) {
        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            userAnswers[elementId] = file.name;
            document.getElementById('file_name_' + elementId).innerHTML = `✅ تم اختيار: <strong>${file.name}</strong> (${Math.round(file.size / 1024)} KB)`;
            clearError(elementId);
        }
    }

    function selectSlot(elementId, slotText, btnEl) {
        userAnswers[elementId + '_slot'] = slotText;
        const parent = btnEl.parentElement;
        parent.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
        btnEl.classList.add('selected');
        clearError(elementId);
    }

    function setStarRating(elementId, val, iconEl) {
        userAnswers[elementId] = val;
        accumulatedScore += val * 5;
        const box = document.getElementById('stars_box_' + elementId);
        box.querySelectorAll('iconify-icon').forEach(star => {
            const starVal = parseInt(star.dataset.val);
            star.classList.toggle('active', starVal <= val);
        });
        clearError(elementId);
    }

    function setNpsScore(elementId, val, btnEl) {
        userAnswers[elementId] = val;
        accumulatedScore += val * 2;
        const parent = btnEl.parentElement;
        parent.querySelectorAll('.nps-btn').forEach(b => b.classList.remove('selected'));
        btnEl.classList.add('selected');
        clearError(elementId);
    }

    // Required Validation Check
    function validateCurrentStep() {
        const stepView = document.getElementById('step_view_' + stepIds[currentStepIndex]);
        if (!stepView) return true;

        let isValid = true;
        const requiredElements = stepView.querySelectorAll('.element-block-wrapper[data-required="1"]');

        requiredElements.forEach(elWrapper => {
            const elId = elWrapper.dataset.elementId;
            const val = userAnswers[elId];
            const inputVal = document.getElementById('input_el_' + elId)?.value;

            if ((!val || (Array.isArray(val) && val.length === 0)) && !inputVal) {
                isValid = false;
                elWrapper.classList.add('has-error');
            } else {
                elWrapper.classList.remove('has-error');
            }
        });

        if (!isValid) {
            alert('⚠️ يرجى الإجابة على جميع الحقول المطلوبة (*) للمتابعة.');
        }

        return isValid;
    }

    function nextStep() {
        if (!validateCurrentStep()) return;

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

    function initTimers() {
        document.querySelectorAll('[id^="timer_box_"]').forEach(box => {
            const mins = parseInt(box.dataset.minutes) || 15;
            let totalSeconds = mins * 60;
            const elId = box.id.replace('timer_box_', '');
            const minEl = document.getElementById('timer_min_' + elId);
            const secEl = document.getElementById('timer_sec_' + elId);

            const interval = setInterval(() => {
                if (totalSeconds <= 0) {
                    clearInterval(interval);
                    box.innerHTML = '<span class="text-danger fw-bold">⚠️ انتهت صلاحية هذا العرض الخاص!</span>';
                    return;
                }
                totalSeconds--;
                const m = Math.floor(totalSeconds / 60);
                const s = totalSeconds % 60;
                if (minEl) minEl.innerText = String(m).padStart(2, '0');
                if (secEl) secEl.innerText = String(s).padStart(2, '0');
            }, 1000);
        });
    }

    function submitFunnelForm() {
        if (!validateCurrentStep()) return;

        const btn = document.getElementById('btn_submit_final');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'جاري الإرسال... ⏳';
        }

        const fullName = document.getElementById('input_full_name')?.value || userAnswers['full_name'] || 'عميل محتمل';
        const phone = document.getElementById('input_phone')?.value || userAnswers['phone'] || '';
        const email = document.getElementById('input_email')?.value || userAnswers['email'] || '';

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
            document.getElementById('res_title').innerText = res.title || (isScoringActive ? '🎉 تم التقييم بنجاح' : '🎉 تم استلام طلبك بنجاح');
            document.getElementById('res_desc').innerText = res.description || 'تم استلام بياناتك وسيتم التواصل معك بالتقرير وخطة المتابعة فوراً.';

            if (isScoringActive && document.getElementById('res_score_value')) {
                document.getElementById('res_score_value').innerText = (data.score ?? accumulatedScore) + ' نقطة';
            }

            const ctaWrapper = document.getElementById('res_cta_wrapper');
            if (res.cta_type === 'whatsapp' || res.cta_whatsapp_number) {
                const waNum = res.cta_whatsapp_number || '966500000000';
                ctaWrapper.innerHTML = `
                    <a href="https://wa.me/${waNum}?text=مرحباً، أتممت تسجيل بياناتي في Travel Wave وأرغب في المتابعة." target="_blank" class="btn-whatsapp-custom">
                        <iconify-icon icon="logos:whatsapp-icon" width="24"></iconify-icon>
                        <span>${res.cta_label || 'تواصل معنا عبر الواتساب'}</span>
                    </a>
                `;
            }

            document.getElementById('result_container').classList.remove('d-none');
        })
        .catch(err => {
            if (btn) btn.disabled = false;
            alert('حدث خطأ: ' + err.message);
        });
    }
</script>

{!! $dispatcherJs ?? '' !!}

</body>
</html>
