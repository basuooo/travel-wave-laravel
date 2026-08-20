@php
    $design = $funnel->design_settings ?? [];
    $primaryColor = $design['primary_color'] ?? '#2563eb';
    $fontFamily = $design['font_family'] ?? 'Tajawal, sans-serif';
    $scoringEnabled = $design['scoring_enabled'] ?? true;
    $thankYouPage = $design['thank_you_page'] ?? [
        'enabled' => true,
        'title' => app()->getLocale() === 'ar' ? 'شكراً لإكمال البيانات!' : 'Thanks for your submission!',
        'subtitle' => app()->getLocale() === 'ar' ? 'تم استلام معلوماتك بنجاح، وسنواصل التواصل معك في أقرب وقت.' : 'Made with involve.me, the quickest way to create interactive lead funnels.',
        'button_text' => app()->getLocale() === 'ar' ? 'العودة للموقع' : 'Create your own',
        'button_action' => 'restart'
    ];
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
            max-width: 720px;
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

        /* Radio Choice */
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

        /* Checkbox Choice */
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
            min-width: 52px;
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

        /* Phone Country Code Select UI */
        .phone-code-dropdown-container {
            position: relative;
        }
        .phone-code-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            border-radius: 12px 0 0 12px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
        }
        [dir="rtl"] .phone-code-btn {
            border-radius: 0 12px 12px 0;
        }
        .phone-code-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1050;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 300px;
            max-height: 280px;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        [dir="rtl"] .phone-code-menu {
            left: auto;
            right: 0;
        }
        .phone-code-menu.show {
            display: flex;
        }
        .phone-code-list {
            overflow-y: auto;
            max-height: 220px;
        }
        .phone-code-item {
            padding: 9px 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            color: #1e293b;
            transition: background 0.15s;
        }
        .phone-code-item:hover {
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
                            if (!empty($props['always_hide'])) {
                                continue;
                            }
                            $label = $element->label;
                            $subline = $props['subline'] ?? '';
                            $isRequired = !empty($element->is_required) || !empty($props['is_required']);
                            $design = $props['design'] ?? [];
                            $hasCustomDesign = !empty($design['custom_enabled']);

                            $headingStyle = '';
                            $sublineStyle = '';
                            $optCardStyle = '';
                            $containerStyle = '';

                            if ($hasCustomDesign) {
                                $headingStyle = 'font-family: ' . ($design['heading_font'] ?? 'inherit') . ';'
                                    . 'font-size: ' . ($design['heading_size'] ?? 'inherit') . ';'
                                    . 'font-weight: ' . (!empty($design['heading_bold']) ? '800' : 'normal') . ';'
                                    . 'font-style: ' . (!empty($design['heading_italic']) ? 'italic' : 'normal') . ';'
                                    . 'color: ' . ($design['text_color'] ?? 'inherit') . ';'
                                    . 'text-align: ' . ($design['align'] ?? 'inherit') . ';';
                                $sublineStyle = 'font-family: ' . ($design['subline_font'] ?? 'inherit') . ';'
                                    . 'font-size: ' . ($design['subline_size'] ?? 'inherit') . ';'
                                    . 'font-weight: ' . (!empty($design['subline_bold']) ? '700' : 'normal') . ';'
                                    . 'font-style: ' . (!empty($design['subline_italic']) ? 'italic' : 'normal') . ';'
                                    . 'color: ' . ($design['subline_color'] ?? '#64748b') . ';'
                                    . 'text-align: ' . ($design['align'] ?? 'inherit') . ';';
                                $optCardStyle = 'font-family: ' . ($design['options_font'] ?? 'inherit') . ';'
                                    . 'font-size: ' . ($design['options_size'] ?? 'inherit') . ';'
                                    . 'font-weight: ' . (!empty($design['options_bold']) ? '700' : 'normal') . ';'
                                    . 'font-style: ' . (!empty($design['options_italic']) ? 'italic' : 'normal') . ';'
                                    . 'color: ' . ($design['options_text_color'] ?? 'inherit') . ';'
                                    . 'background-color: ' . ($design['options_bg_color'] ?? '#ffffff') . ';'
                                    . 'border: 1.5px solid ' . ($design['options_border_color'] ?? '#e2e8f0') . ';'
                                    . 'border-radius: ' . ($design['options_border_radius'] ?? '14px') . ';'
                                    . 'text-align: ' . ($design['options_align'] ?? 'inherit') . ';';
                                $containerStyle = 'text-align: ' . ($design['align'] ?? 'inherit') . ';'
                                    . (!empty($design['bg_color']) ? 'background-color: ' . $design['bg_color'] . ';' : '');
                            }
                        @endphp

                        <div class="mb-4 element-block-wrapper" id="el_wrapper_{{ $element->id }}" data-element-id="{{ $element->id }}" data-required="{{ $isRequired ? '1' : '0' }}" data-type="{{ $type }}" style="{{ $containerStyle }}">
                            
                            {{-- 1. HEADING --}}
                            @if($type === 'heading')
                                <h3 class="fw-bold text-dark mb-1" style="{{ $headingStyle }}">
                                    {{ $label ?: 'عنوان رئيسي' }}
                                    @if($isRequired) <span class="text-danger">*</span> @endif
                                </h3>
                                @if($subline)
                                    <p class="text-muted small mb-2" style="{{ $sublineStyle }}">{{ $subline }}</p>
                                @endif

                            {{-- 2. PARAGRAPH / TEXT --}}
                            @elseif(in_array($type, ['text', 'paragraph']))
                                <p class="text-muted fs-6 mb-3 leading-relaxed" style="{{ $headingStyle }}">{{ $label }}</p>

                            {{-- 3. RADIO CHOICE --}}
                            @elseif($type === 'radio_choice')
                                @if($label) 
                                    <label class="fw-bold mb-1 d-block fs-6 text-dark" style="{{ $headingStyle }}">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                @if($subline)
                                    <p class="text-muted small mb-2" style="{{ $sublineStyle }}">{{ $subline }}</p>
                                @endif
                                <div class="d-flex flex-column gap-2">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="radio-option-card" style="{{ $optCardStyle }}" onclick="selectRadioOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
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

                            {{-- 4. CHECKBOX CHOICE --}}
                            @elseif(in_array($type, ['checkbox_choice', 'multiple_choice']))
                                @if($label) 
                                    <label class="fw-bold mb-1 d-block fs-6 text-dark" style="{{ $headingStyle }}">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                @if($subline)
                                    <p class="text-muted small mb-2" style="{{ $sublineStyle }}">{{ $subline }}</p>
                                @endif
                                <div class="d-flex flex-column gap-2">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="checkbox-option-card" style="{{ $optCardStyle }}" onclick="toggleCheckboxOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
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
                                    <label class="fw-bold mb-1 d-block fs-6 text-dark" style="{{ $headingStyle }}">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                @if($subline)
                                    <p class="text-muted small mb-2" style="{{ $sublineStyle }}">{{ $subline }}</p>
                                @endif
                                <div class="d-flex flex-column gap-2">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="option-btn" style="{{ $optCardStyle }}" onclick="selectOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                            <span>{{ $opt['label'] ?? $opt['value'] }}</span>
                                            <iconify-icon icon="solar:alt-arrow-left-bold" class="text-muted"></iconify-icon>
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 6. IMAGE CHOICE --}}
                            @elseif(in_array($type, ['image_choice', 'single_image_choice', 'multiple_image_choice']))
                                @if($label) 
                                    <label class="fw-bold mb-1 d-block fs-6 text-dark" style="{{ $headingStyle }}">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                @if($subline)
                                    <p class="text-muted small mb-2" style="{{ $sublineStyle }}">{{ $subline }}</p>
                                @endif
                                <div class="row g-3">
                                    @foreach($props['options'] ?? [] as $opt)
                                        <div class="col-6">
                                            <div class="option-btn text-center flex-column p-2 h-100 shadow-sm border rounded-4 overflow-hidden position-relative transition-hover" onclick="selectOption({{ $element->id }}, '{{ addslashes($opt['value'] ?? $opt['label']) }}', {{ $opt['score'] ?? 0 }}, this)">
                                                @if(!empty($opt['image_url']))
                                                    <img src="{{ $opt['image_url'] }}" class="rounded-3 mb-2 w-100" style="height: 140px; object-fit: cover;" alt="{{ $opt['label'] ?? '' }}">
                                                @else
                                                    <div class="bg-light rounded-3 p-4 mb-2">
                                                        <iconify-icon icon="solar:gallery-bold-duotone" width="48" class="text-primary"></iconify-icon>
                                                    </div>
                                                @endif
                                                <span class="fw-bold small text-dark d-block px-1 pb-1" style="font-size: 13px;">{{ $opt['label'] ?? $opt['value'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            {{-- 7. COUNTRY SELECTOR --}}
                            @elseif($type === 'country')
                                @if($label) 
                                    <label class="fw-bold mb-1 d-block fs-6 text-dark" style="{{ $headingStyle }}">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                @if($subline)
                                    <p class="text-muted small mb-2" style="{{ $sublineStyle }}">{{ $subline }}</p>
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
                                    <label class="fw-bold mb-1 d-block fs-6 text-dark" style="{{ $headingStyle }}">
                                        {{ $label }}
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                @if($subline)
                                    <p class="text-muted small mb-2" style="{{ $sublineStyle }}">{{ $subline }}</p>
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

                            {{-- 10. FILE UPLOAD --}}
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

                            {{-- 11. PHONE WITH INTERNATIONAL SEARCHABLE CODES --}}
                            @elseif($type === 'phone')
                                @if($label) 
                                    <label class="form-label fw-bold small text-dark">
                                        {{ $label }} 
                                        @if($isRequired) <span class="text-danger">*</span> @endif
                                    </label> 
                                @endif
                                <div class="phone-code-dropdown-container">
                                    <div class="input-group input-group-lg">
                                        <button class="phone-code-btn" type="button" id="phone_code_btn_{{ $element->id }}" onclick="togglePhoneCodeMenu({{ $element->id }})">
                                            <span id="phone_flag_{{ $element->id }}">🇸🇦</span>
                                            <span id="phone_dial_{{ $element->id }}">+966</span>
                                            <small class="text-muted ms-1">▾</small>
                                        </button>
                                        <input type="tel" class="form-control" id="input_el_{{ $element->id }}" placeholder="05XXXXXXXX" onchange="userAnswers[{{ $element->id }}] = (document.getElementById('phone_dial_{{ $element->id }}').innerText + ' ' + this.value); clearError({{ $element->id }});">
                                    </div>
                                    <div class="phone-code-menu" id="phone_menu_{{ $element->id }}">
                                        <div class="p-2 border-bottom">
                                            <input type="text" class="form-control form-control-sm" placeholder="🔍 بحث بالدولة أو الكود (مثال: مصر، eg، 20)..." oninput="filterCountryCodeList({{ $element->id }}, this.value)">
                                        </div>
                                        <div class="phone-code-list" id="phone_list_{{ $element->id }}">
                                            <!-- Rendered dynamically -->
                                        </div>
                                    </div>
                                </div>

                            {{-- 12. DEDICATED CONTACT FORM (DYNAMIC FIELDS INCLUDING DROPDOWNS) --}}
                            @elseif($type === 'contact_form')
                                @php
                                    $cFields = $props['fields'] ?? [
                                        ['key' => 'full_name', 'label' => 'الاسم الكريم', 'type' => 'text', 'required' => true, 'placeholder' => 'أدخل اسمك الكريم'],
                                        ['key' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'tel', 'required' => true, 'placeholder' => '05XXXXXXXX'],
                                        ['key' => 'email', 'label' => 'البريد الإلكتروني', 'type' => 'email', 'required' => false, 'placeholder' => 'example@domain.com']
                                    ];
                                @endphp
                                <div class="bg-light p-3 p-md-4 rounded-4 border">
                                    <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                                        <iconify-icon icon="solar:user-id-bold-duotone" width="24"></iconify-icon>
                                        <span>{{ $label ?: 'سجّل بياناتك لاستلام التقرير وخطة المتابعة' }}</span>
                                    </h5>
                                    <div class="d-flex flex-column gap-3">
                                        @foreach($cFields as $f)
                                            @php
                                                $fVisible = is_array($f) ? (($f['visible'] ?? true) !== false) : true;
                                                if (!$fVisible) continue;
                                                $fKey = is_array($f) ? ($f['key'] ?? $f['name'] ?? ('f_' . $loop->index)) : 'f_' . $loop->index;
                                                $fLabel = is_array($f) ? ($f['label'] ?? 'حقل ' . ($loop->index + 1)) : $f;
                                                $fType = is_array($f) ? ($f['type'] ?? 'text') : 'text';
                                                $fReq = is_array($f) ? !empty($f['required']) : false;
                                                $fPlaceholder = is_array($f) ? ($f['placeholder'] ?? '') : '';
                                                $fOptions = is_array($f) ? ($f['options'] ?? []) : [];
                                            @endphp
                                            <div>
                                                <label class="form-label fw-bold small text-dark mb-1">
                                                    {{ $fLabel }}
                                                    @if($fReq) <span class="text-danger">*</span> @endif
                                                </label>
                                                
                                                @if($fType === 'tel' || $fType === 'phone')
                                                    <div class="phone-code-dropdown-container">
                                                        <div class="input-group input-group-lg">
                                                            <button class="phone-code-btn" type="button" id="cf_phone_code_btn_{{ $element->id }}" onclick="togglePhoneCodeMenu('cf_{{ $element->id }}')">
                                                                <span id="phone_flag_cf_{{ $element->id }}">🇸🇦</span>
                                                                <span id="phone_dial_cf_{{ $element->id }}">+966</span>
                                                                <small class="text-muted ms-1">▾</small>
                                                            </button>
                                                            <input type="tel" class="form-control" id="cf_input_{{ $fKey }}" placeholder="{{ $fPlaceholder ?: '05XXXXXXXX' }}" onchange="userAnswers['{{ $fKey }}'] = (document.getElementById('phone_dial_cf_{{ $element->id }}').innerText + ' ' + this.value); clearError({{ $element->id }});">
                                                        </div>
                                                        <div class="phone-code-menu" id="phone_menu_cf_{{ $element->id }}">
                                                            <div class="p-2 border-bottom">
                                                                <input type="text" class="form-control form-control-sm" placeholder="🔍 بحث بالدولة أو الكود (eg, 20)..." oninput="filterCountryCodeList('cf_{{ $element->id }}', this.value)">
                                                            </div>
                                                            <div class="phone-code-list" id="phone_list_cf_{{ $element->id }}"></div>
                                                        </div>
                                                    </div>
                                                @elseif($fType === 'select' || $fType === 'dropdown')
                                                    <select class="form-select form-select-lg rounded-3" id="cf_input_{{ $fKey }}" onchange="userAnswers['{{ $fKey }}'] = this.value; clearError({{ $element->id }});">
                                                        <option value="">{{ $fPlaceholder ?: 'اختر من القائمة...' }}</option>
                                                        @foreach($fOptions as $o)
                                                            <option value="{{ $o['value'] ?? $o['label'] }}">{{ $o['label'] ?? $o['value'] }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($fType === 'textarea')
                                                    <textarea class="form-control form-control-lg rounded-3" id="cf_input_{{ $fKey }}" rows="2" placeholder="{{ $fPlaceholder }}" onchange="userAnswers['{{ $fKey }}'] = this.value; clearError({{ $element->id }});"></textarea>
                                                @elseif($fType === 'date')
                                                    <input type="date" class="form-control form-control-lg rounded-3" id="cf_input_{{ $fKey }}" onchange="userAnswers['{{ $fKey }}'] = this.value; clearError({{ $element->id }});">
                                                @else
                                                    <input type="{{ $fType }}" class="form-control form-control-lg rounded-3" id="cf_input_{{ $fKey }}" placeholder="{{ $fPlaceholder }}" onchange="userAnswers['{{ $fKey }}'] = this.value; clearError({{ $element->id }});">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            {{-- 13. EMAIL --}}
                            @elseif($type === 'email')
                                @if($label) <label class="form-label fw-bold small text-dark">{{ $label }} @if($isRequired) <span class="text-danger">*</span> @endif</label> @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">✉️</span>
                                    <input type="email" class="form-control" id="input_el_{{ $element->id }}" placeholder="name@example.com" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                </div>

                            {{-- 14. ADDRESS --}}
                            @elseif($type === 'address')
                                @if($label) <label class="form-label fw-bold small text-dark">{{ $label }} @if($isRequired) <span class="text-danger">*</span> @endif</label> @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">📍</span>
                                    <input type="text" class="form-control" id="input_el_{{ $element->id }}" placeholder="المدينة، الحي، الشارع" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                </div>

                            {{-- 15. WEBSITE --}}
                            @elseif($type === 'website')
                                @if($label) <label class="form-label fw-bold small text-dark">{{ $label }} @if($isRequired) <span class="text-danger">*</span> @endif</label> @endif
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">🌐</span>
                                    <input type="url" class="form-control" id="input_el_{{ $element->id }}" placeholder="https://www.example.com" onchange="userAnswers[{{ $element->id }}] = this.value; clearError({{ $element->id }});">
                                </div>

                            {{-- 16. COUNTDOWN TIMER (WITH LABELS ABOVE DIGITS) --}}
                            @elseif(in_array($type, ['timer', 'page_timer']))
                                @php
                                    $hrs = $props['duration_hours'] ?? 0;
                                    $mins = $props['duration_minutes'] ?? 15;
                                    $secs = $props['duration_seconds'] ?? 0;
                                    $totalSecs = ($hrs * 3600) + ($mins * 60) + $secs;
                                @endphp
                                <div class="p-4 bg-dark text-white rounded-4 border text-center" id="timer_box_{{ $element->id }}" data-seconds="{{ $totalSecs }}">
                                    <span class="small text-warning fw-bold d-block mb-3">⏰ {{ $label ?: 'احجز الآن! هذا العرض ساري لمدة:' }}</span>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="small text-white-50 fw-bold mb-1" style="font-size: 12px;">الساعات (Hours)</span>
                                            <div class="timer-box-digit" id="timer_hr_{{ $element->id }}">{{ sprintf('%02d', $hrs) }}</div>
                                        </div>
                                        <span class="fs-3 fw-bold text-warning mt-3">:</span>
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="small text-white-50 fw-bold mb-1" style="font-size: 12px;">الدقائق (Minutes)</span>
                                            <div class="timer-box-digit" id="timer_min_{{ $element->id }}">{{ sprintf('%02d', $mins) }}</div>
                                        </div>
                                        <span class="fs-3 fw-bold text-warning mt-3">:</span>
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="small text-white-50 fw-bold mb-1" style="font-size: 12px;">الثواني (Seconds)</span>
                                            <div class="timer-box-digit" id="timer_sec_{{ $element->id }}">{{ sprintf('%02d', $secs) }}</div>
                                        </div>
                                    </div>
                                </div>

                            {{-- 17. APPOINTMENT & DATE PICKER --}}
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

                            {{-- 18. STAR RATING --}}
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

                            {{-- 19. NPS SCORE --}}
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

                            {{-- 20. TABLE --}}
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

                            {{-- 21. TESTIMONIALS --}}
                            @elseif($type === 'testimonials')
                                <div class="bg-light p-3 p-md-4 rounded-4 border text-center">
                                    <iconify-icon icon="solar:chat-round-quotes-bold-duotone" width="36" class="text-primary mb-2"></iconify-icon>
                                    <p class="fst-italic text-dark fs-6 mb-2">"خدمة استثنائية وسرعة فائقة في استخراج التأشيرة وحجز الموعد خلال 48 ساعة فقط!"</p>
                                    <div class="d-flex justify-content-center text-warning gap-1 mb-1">⭐⭐⭐⭐⭐</div>
                                    <strong class="text-primary small">— فهد الدوسري (عميل موثق ✅)</strong>
                                </div>

                            {{-- 22. FAQS --}}
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

                            {{-- 23. COUPON CODE --}}
                            @elseif($type === 'coupon_code')
                                <div class="p-3 bg-light rounded-4 border border-dashed text-center">
                                    <span class="badge bg-warning text-dark fw-bold mb-1">🎁 هدية حصرية لك</span>
                                    <h4 class="fw-bold text-danger mb-1">كود خصم: WAVE2026</h4>
                                    <p class="text-muted small mb-2">استخدم هذا الكود للحصول على خصم 20% على رسوم الخدمة</p>
                                    <button type="button" class="btn btn-sm btn-dark px-3 rounded-pill" onclick="navigator.clipboard.writeText('WAVE2026'); alert('تم نسخ كود الخصم! 🎉')">
                                        نسخ الكود 📋
                                    </button>
                                </div>

                            {{-- 24. SHORT / LONG TEXT --}}
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

            <!-- THANK YOU PAGE CONTAINER -->
            <div class="funnel-step-view d-none text-center py-4" id="thank_you_container">
                <div class="mb-4 d-flex justify-content-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; background: #dcfce7; color: #22c55e;">
                        <iconify-icon icon="solar:check-circle-bold" width="60"></iconify-icon>
                    </div>
                </div>
                <h2 class="fw-bold mb-3 text-dark" id="ty_view_title">
                    {{ $thankYouPage['title'] ?? (app()->getLocale() === 'ar' ? 'شكراً لإكمال البيانات!' : 'Thanks for your submission!') }}
                </h2>
                <p class="text-muted fs-6 mb-4 px-3" style="max-width: 520px; margin: 0 auto;" id="ty_view_subtitle">
                    {{ $thankYouPage['subtitle'] ?? (app()->getLocale() === 'ar' ? 'تم استلام معلوماتك بنجاح، وسنواصل التواصل معك في أقرب وقت.' : 'Made with involve.me, the quickest way to create interactive lead funnels.') }}
                </p>

                <div class="mt-4" id="ty_view_cta_wrapper">
                    @if(($thankYouPage['button_action'] ?? 'restart') === 'url' && !empty($thankYouPage['button_url']))
                        <a href="{{ $thankYouPage['button_url'] }}" class="btn-primary-custom w-auto px-5 text-decoration-none">
                            <span>{{ $thankYouPage['button_text'] ?? 'Create your own' }}</span>
                            <iconify-icon icon="solar:alt-arrow-right-bold" width="18"></iconify-icon>
                        </a>
                    @elseif(($thankYouPage['button_action'] ?? 'restart') === 'whatsapp')
                        <a href="https://wa.me/{{ $thankYouPage['button_whatsapp'] ?? '966500000000' }}?text=مرحباً، أتممت تسجيل بياناتي" target="_blank" class="btn-whatsapp-custom w-auto px-5 text-decoration-none">
                            <iconify-icon icon="logos:whatsapp-icon" width="22"></iconify-icon>
                            <span>{{ $thankYouPage['button_text'] ?? 'تواصل معنا عبر الواتساب' }}</span>
                        </a>
                    @else
                        <button type="button" class="btn-primary-custom w-auto px-5" onclick="location.reload()">
                            <span>{{ $thankYouPage['button_text'] ?? 'Create your own' }}</span>
                        </button>
                    @endif
                </div>
            </div>

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
    const thankYouConfig = @json($thankYouPage);
    let currentStepIndex = 0;
    let accumulatedScore = 0;
    const userAnswers = {};
    const stepIds = [
        @foreach($steps as $s)
            {{ $s->id }},
        @endforeach
    ];

    const ALL_COUNTRY_DIAL_CODES = [
        { name_ar: 'السعودية', name_en: 'Saudi Arabia', code: 'SA', dial_code: '+966', flag: '🇸🇦' },
        { name_ar: 'مصر', name_en: 'Egypt', code: 'EG', dial_code: '+20', flag: '🇪🇬' },
        { name_ar: 'الإمارات', name_en: 'United Arab Emirates', code: 'AE', dial_code: '+971', flag: '🇦🇪' },
        { name_ar: 'الكويت', name_en: 'Kuwait', code: 'KW', dial_code: '+965', flag: '🇰🇼' },
        { name_ar: 'قطر', name_en: 'Qatar', code: 'QA', dial_code: '+974', flag: '🇶🇦' },
        { name_ar: 'البحرين', name_en: 'Bahrain', code: 'BH', dial_code: '+973', flag: '🇧🇭' },
        { name_ar: 'عمان', name_en: 'Oman', code: 'OM', dial_code: '+968', flag: '🇴🇲' },
        { name_ar: 'الأردن', name_en: 'Jordan', code: 'JO', dial_code: '+962', flag: '🇯🇴' },
        { name_ar: 'العراق', name_en: 'Iraq', code: 'IQ', dial_code: '+964', flag: '🇮🇶' },
        { name_ar: 'المغرب', name_en: 'Morocco', code: 'MA', dial_code: '+212', flag: '🇲🇦' },
        { name_ar: 'الجزائر', name_en: 'Algeria', code: 'DZ', dial_code: '+213', flag: '🇩🇿' },
        { name_ar: 'تونس', name_en: 'Tunisia', code: 'TN', dial_code: '+216', flag: '🇹🇳' },
        { name_ar: 'لبنان', name_en: 'Lebanon', code: 'LB', dial_code: '+961', flag: '🇱🇧' },
        { name_ar: 'تركيا', name_en: 'Turkey', code: 'TR', dial_code: '+90', flag: '🇹🇷' },
        { name_ar: 'المملكة المتحدة', name_en: 'United Kingdom', code: 'GB', dial_code: '+44', flag: '🇬🇧' },
        { name_ar: 'الولايات المتحدة', name_en: 'United States', code: 'US', dial_code: '+1', flag: '🇺🇸' },
        { name_ar: 'ألمانيا', name_en: 'Germany', code: 'DE', dial_code: '+49', flag: '🇩🇪' },
        { name_ar: 'فرنسا', name_en: 'France', code: 'FR', dial_code: '+33', flag: '🇫🇷' },
        { name_ar: 'إيطاليا', name_en: 'Italy', code: 'IT', dial_code: '+39', flag: '🇮🇹' },
        { name_ar: 'إسبانيا', name_en: 'Spain', code: 'ES', dial_code: '+34', flag: '🇪🇸' },
        { name_ar: 'سويسرا', name_en: 'Switzerland', code: 'CH', dial_code: '+41', flag: '🇨🇭' },
        { name_ar: 'هولندا', name_en: 'Netherlands', code: 'NL', dial_code: '+31', flag: '🇳🇱' },
        { name_ar: 'كندا', name_en: 'Canada', code: 'CA', dial_code: '+1', flag: '🇨🇦' },
        { name_ar: 'أستراليا', name_en: 'Australia', code: 'AU', dial_code: '+61', flag: '🇦🇺' },
        { name_ar: 'الصين', name_en: 'China', code: 'CN', dial_code: '+86', flag: '🇨🇳' },
        { name_ar: 'اليابان', name_en: 'Japan', code: 'JP', dial_code: '+81', flag: '🇯🇵' },
        { name_ar: 'ماليزيا', name_en: 'Malaysia', code: 'MY', dial_code: '+60', flag: '🇲🇾' },
        { name_ar: 'إندونيسيا', name_en: 'Indonesia', code: 'ID', dial_code: '+62', flag: '🇮🇩' },
        { name_ar: 'الهند', name_en: 'India', code: 'IN', dial_code: '+91', flag: '🇮🇳' },
        { name_ar: 'روسيا', name_en: 'Russia', code: 'RU', dial_code: '+7', flag: '🇷🇺' },
        { name_ar: 'جورجيا', name_en: 'Georgia', code: 'GE', dial_code: '+995', flag: '🇬🇪' },
        { name_ar: 'أذربيجان', name_en: 'Azerbaijan', code: 'AZ', dial_code: '+994', flag: '🇦🇿' },
        { name_ar: 'البوسنة', name_en: 'Bosnia', code: 'BA', dial_code: '+387', flag: '🇧🇦' },
        { name_ar: 'تايلاند', name_en: 'Thailand', code: 'TH', dial_code: '+66', flag: '🇹🇭' },
        { name_ar: 'الفلبين', name_en: 'Philippines', code: 'PH', dial_code: '+63', flag: '🇵🇭' },
        { name_ar: 'جزر المالديف', name_en: 'Maldives', code: 'MV', dial_code: '+960', flag: '🇲🇻' },
        { name_ar: 'سيريلانكا', name_en: 'Sri Lanka', code: 'LK', dial_code: '+94', flag: '🇱🇰' },
        { name_ar: 'سنغافورة', name_en: 'Singapore', code: 'SG', dial_code: '+65', flag: '🇸🇬' },
        { name_ar: 'كوريا الجنوبية', name_en: 'South Korea', code: 'KR', dial_code: '+82', flag: '🇰🇷' },
        { name_ar: 'السودان', name_en: 'Sudan', code: 'SD', dial_code: '+249', flag: '🇸🇩' },
        { name_ar: 'اليمن', name_en: 'Yemen', code: 'YE', dial_code: '+967', flag: '🇾🇪' },
        { name_ar: 'سوريا', name_en: 'Syria', code: 'SY', dial_code: '+963', flag: '🇸🇾' },
        { name_ar: 'فلسطين', name_en: 'Palestine', code: 'PS', dial_code: '+970', flag: '🇵🇸' },
        { name_ar: 'ليبيا', name_en: 'Libya', code: 'LY', dial_code: '+218', flag: '🇱🇾' },
        { name_ar: 'موريتانيا', name_en: 'Mauritania', code: 'MR', dial_code: '+222', flag: '🇲🇷' },
        { name_ar: 'الصومال', name_en: 'Somalia', code: 'SO', dial_code: '+252', flag: '🇸🇴' },
        { name_ar: 'جيبوتي', name_en: 'Djibouti', code: 'DJ', dial_code: '+253', flag: '🇩🇯' },
        { name_ar: 'جزر القمر', name_en: 'Comoros', code: 'KM', dial_code: '+269', flag: '🇰🇲' }
    ];

    document.addEventListener('DOMContentLoaded', () => {
        initTimers();
        initPhoneCodeDropdowns();
    });

    function initPhoneCodeDropdowns() {
        document.querySelectorAll('.phone-code-list').forEach(listEl => {
            const containerId = listEl.id.replace('phone_list_', '');
            renderCountryCodeList(containerId, ALL_COUNTRY_DIAL_CODES);
        });

        // Close dropdown when clicked outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.phone-code-dropdown-container')) {
                document.querySelectorAll('.phone-code-menu').forEach(m => m.classList.remove('show'));
            }
        });
    }

    function togglePhoneCodeMenu(id) {
        const menu = document.getElementById('phone_menu_' + id);
        if (menu) {
            const isShown = menu.classList.contains('show');
            document.querySelectorAll('.phone-code-menu').forEach(m => m.classList.remove('show'));
            if (!isShown) menu.classList.add('show');
        }
    }

    function renderCountryCodeList(id, list) {
        const listEl = document.getElementById('phone_list_' + id);
        if (!listEl) return;
        let html = '';
        list.forEach(c => {
            html += `
                <div class="phone-code-item" onclick="selectCountryDialCode('${id}', '${c.dial_code}', '${c.flag}')">
                    <span>${c.flag} ${c.name_ar}</span>
                    <span class="badge bg-light text-dark border">${c.dial_code}</span>
                </div>
            `;
        });
        listEl.innerHTML = html || '<div class="p-2 text-center text-muted small">لا توجد نتائج</div>';
    }

    function filterCountryCodeList(id, query) {
        query = query.toLowerCase().trim();
        const filtered = ALL_COUNTRY_DIAL_CODES.filter(c => {
            return c.name_ar.includes(query) ||
                   c.name_en.toLowerCase().includes(query) ||
                   c.code.toLowerCase().includes(query) ||
                   c.dial_code.includes(query);
        });
        renderCountryCodeList(id, filtered);
    }

    function selectCountryDialCode(id, dialCode, flag) {
        const flagEl = document.getElementById('phone_flag_' + id);
        const dialEl = document.getElementById('phone_dial_' + id);
        if (flagEl) flagEl.innerText = flag;
        if (dialEl) dialEl.innerText = dialCode;
        document.getElementById('phone_menu_' + id)?.classList.remove('show');
    }

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
            let totalSeconds = parseInt(box.dataset.seconds) || 900;
            const elId = box.id.replace('timer_box_', '');
            const hrEl = document.getElementById('timer_hr_' + elId);
            const minEl = document.getElementById('timer_min_' + elId);
            const secEl = document.getElementById('timer_sec_' + elId);

            const interval = setInterval(() => {
                if (totalSeconds <= 0) {
                    clearInterval(interval);
                    box.innerHTML = '<span class="text-danger fw-bold fs-5">⚠️ انتهت صلاحية هذا العرض الخاص!</span>';
                    return;
                }
                totalSeconds--;
                const h = Math.floor(totalSeconds / 3600);
                const m = Math.floor((totalSeconds % 3600) / 60);
                const s = totalSeconds % 60;
                if (hrEl) hrEl.innerText = String(h).padStart(2, '0');
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

        const fullName = document.getElementById('cf_input_full_name')?.value || userAnswers['full_name'] || 'عميل محتمل';
        const phone = document.getElementById('cf_input_phone')?.value || userAnswers['phone'] || '';
        const email = document.getElementById('cf_input_email')?.value || userAnswers['email'] || '';

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
{!! $bodyScripts ?? '' !!}

</body>
</html>
