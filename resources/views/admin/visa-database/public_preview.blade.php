@php
    $isEn = app()->getLocale() === 'en';
    $dir = $isEn ? 'ltr' : 'rtl';
    $countryName = $record->country ? ($isEn && $record->country->name_en ? $record->country->name_en : ($record->country->name_ar ?: $record->country->name_en)) : '';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isEn ? 'Visa Details for ' . $countryName : 'معاينة تفاصيل تأشيرة ' . $countryName }} | Travel Wave</title>
    <!-- Bootstrap 5 CSS -->
    @if($isEn)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @endif
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .hero-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 2.5rem 0;
            border-radius: 0 0 1.5rem 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);
        }
        .info-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.05);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
        .price-badge {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            font-size: 1.5rem;
            font-weight: 800;
            padding: 0.5rem 1.25rem;
            border-radius: 0.75rem;
        }
        .documents-content h2, .documents-content h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-top: 1rem;
            margin-bottom: 0.75rem;
        }
        .documents-content ul {
            padding-right: {{ $isEn ? '0' : '1.25rem' }};
            padding-left: {{ $isEn ? '1.25rem' : '0' }};
            margin-bottom: 1rem;
        }
        .documents-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .btn-whatsapp {
            background-color: #25d366;
            color: #ffffff;
            font-weight: 700;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            border: none;
            transition: background-color 0.2s ease;
        }
        .btn-whatsapp:hover {
            background-color: #1eb956;
            color: #ffffff;
        }
        .floating-wa-btn {
            position: fixed;
            bottom: 25px;
            {{ $isEn ? 'right: 25px;' : 'left: 25px;' }}
            background-color: #25d366;
            color: white;
            border-radius: 50px;
            padding: 12px 24px;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(37, 211, 102, 0.4);
            z-index: 1050;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom py-2 sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-primary" href="{{ route('visa-database.public-catalog') }}">
                @if($setting->logo_url)
                    <img src="{{ $setting->logo_url }}" alt="Travel Wave" style="max-width: {{ $setting->logo_width ?: 180 }}px; max-height: {{ $setting->logo_height ?: 50 }}px; object-fit: {{ $setting->logo_keep_aspect_ratio ? 'contain' : 'fill' }};">
                @else
                    <i class="bi bi-airplane-engines-fill fs-3"></i>
                    <span class="fs-5">ترافيل ويف | Travel Wave</span>
                @endif
            </a>
            <div class="d-flex align-items-center gap-2">
                <!-- Language Switcher Button -->
                @if($isEn)
                    <a href="{{ route('locale.switch', 'ar') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-globe me-1"></i> العربية
                    </a>
                @else
                    <a href="{{ route('locale.switch', 'en') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-globe me-1"></i> English
                    </a>
                @endif

                <a href="{{ route('visa-database.public-catalog') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-grid-fill me-1"></i> {{ $isEn ? 'Visa Catalog' : 'دليل التأشيرات' }}
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-banner mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill fs-6 fw-bold">
                            <i class="bi bi-globe-europe-africa me-1"></i> {{ $countryName }}
                        </span>
                        @if(!$isEn && $record->country?->name_en)
                            <span class="text-white-50 fs-6 fw-semibold">({{ $record->country->name_en }})</span>
                        @endif
                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill fs-6">
                            {{ $record->visa_type }}
                        </span>
                    </div>
                    <h1 class="fw-extrabold display-6 mb-2">
                        {{ $isEn ? 'Visa Details & Procedures for ' . $countryName : 'تفاصيل وإجراءات تأشيرة ' . $countryName }}
                    </h1>
                    <p class="text-white-50 m-0">
                        {{ $isEn ? 'Official guidelines, document requirements, and fees for ' . $countryName . ' visa' : 'الدليل التوضيحي المعتمد لمتطلبات التقديم والرسوم وأوراق فيزا ' . $countryName }}
                    </p>
                </div>
                @if(($setting->show_price ?? true))
                    <div class="col-lg-4 text-lg-start text-center">
                        <div class="d-inline-block text-start">
                            <div class="small text-white-50 mb-1 fw-bold">{{ $isEn ? 'Service Fee' : 'سعر الخدمة' }}</div>
                            <div class="price-badge d-inline-block shadow-sm">
                                @if($record->price && $record->price > 0)
                                    {{ number_format($record->price, 0) }} <small class="fs-6">{{ $record->currency ?: 'EGP' }}</small>
                                @else
                                    <span class="fs-5 text-muted">{{ $isEn ? 'Contact us' : 'تواصل معنا' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container pb-5">
        <div class="row g-4 mb-4">
            <!-- Key Info Cards -->
            @if(($setting->show_working_days ?? true))
                <div class="col-md-4 col-sm-6">
                    <div class="info-card p-3 d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary-subtle text-primary">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">{{ $isEn ? 'Working Days' : 'أيام العمل' }}</div>
                            <div class="fw-bold fs-6">{{ $record->working_days ?: ($isEn ? '15–20 working days' : '15–20 يوم عمل') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-4 col-sm-6">
                <div class="info-card p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-success-subtle text-success">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ $isEn ? 'Proposed Duration' : 'مدة التأشيرة المقترحة' }}</div>
                        <div class="fw-bold fs-6">{{ $record->proposed_duration ?: ($isEn ? 'Per Embassy Decision' : 'حسب قرار السفارة') }}</div>
                    </div>
                </div>
            </div>

            @if(($setting->show_embassy_fee ?? true))
                <div class="col-md-4 col-sm-6">
                    <div class="info-card p-3 d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning-subtle text-warning">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">{{ $isEn ? 'Embassy Fee' : 'رسوم السفارة' }}</div>
                            <div class="fw-bold fs-6">
                                @if($record->embassy_fee)
                                    {{ $record->embassy_fee }} {{ $record->embassy_fee_currency }}
                                @else
                                    {{ $isEn ? 'Not specified' : 'غير محدد' }}
                                @endif
                            </div>
                            @if($record->embassy_fee_payment_method)
                                <div class="small text-muted" style="font-size: 0.75rem;">({{ $record->embassy_fee_payment_method }})</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-4 col-sm-6">
                <div class="info-card p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-info-subtle text-info">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ $isEn ? 'Application Center' : 'مكان التقديم (المركز)' }}</div>
                        <div class="fw-bold fs-6">
                            @if($record->application_center_list && count($record->application_center_list) > 0)
                                @foreach($record->application_center_list as $center)
                                    <span class="badge bg-secondary me-1">{{ $center }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary">{{ $isEn ? 'Embassy Direct' : 'السفارة مباشرة' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(($setting->show_biometrics ?? true))
                <div class="col-md-4 col-sm-6">
                    <div class="info-card p-3 d-flex align-items-center gap-3">
                        <div class="icon-box bg-danger-subtle text-danger">
                            <i class="bi bi-fingerprint"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">{{ $isEn ? 'Biometrics Required?' : 'هل البصمة مطلوبة؟' }}</div>
                            <div class="fw-bold fs-6">
                                @if($record->is_biometrics_required)
                                    <span class="text-danger"><i class="bi bi-check-circle-fill me-1"></i> {{ $isEn ? 'Required' : 'مطلوبة' }}</span>
                                @else
                                    <span class="text-success"><i class="bi bi-x-circle-fill me-1"></i> {{ $isEn ? 'Not Required' : 'غير مطلوبة' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(($setting->show_interview ?? true))
                <div class="col-md-4 col-sm-6">
                    <div class="info-card p-3 d-flex align-items-center gap-3">
                        <div class="icon-box bg-purple-subtle text-purple" style="background-color: #f3e8ff; color: #9333ea;">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">{{ $isEn ? 'Interview Required?' : 'هل المقابلة مطلوبة؟' }}</div>
                            <div class="fw-bold fs-6">
                                @if($record->is_interview_required)
                                    <span style="color: #9333ea;"><i class="bi bi-check-circle-fill me-1"></i> {{ $isEn ? 'Required' : 'مطلوبة' }}</span>
                                @else
                                    <span class="text-success"><i class="bi bi-x-circle-fill me-1"></i> {{ $isEn ? 'Not Required' : 'غير مطلوبة' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row g-4">
            <!-- Documents Section -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-text-fill"></i> {{ $isEn ? 'Required Documents' : 'الأوراق والوثائق المطلوبة للتقديم' }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        @if($record->required_documents)
                            <div class="documents-content">
                                {!! nl2br(e($record->required_documents)) !!}
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-1"></i> {{ $isEn ? 'Please contact customer support for the updated list of documents for ' . $countryName . '.' : 'يرجى التواصل مع فريق خدمة العملاء للحصول على قائمة الأوراق المحدثة الخاصة بتأشيرة ' . $countryName . '.' }}
                            </div>
                        @endif
                    </div>
                </div>

                @if(($setting->show_notes ?? true) && $record->notes)
                    <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-4 border-warning">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-warning-emphasis mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $isEn ? 'Important Notes & Guidelines' : 'ملاحظات وتوجيهات هامة' }}
                            </h5>
                            <p class="mb-0 text-secondary leading-relaxed">{!! nl2br(e($record->notes)) !!}</p>
                        </div>
                    </div>
                @endif

                <!-- Selected / Country Map Section -->
                @if($selectedMapSection)
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-danger"></i> {{ $isEn && $selectedMapSection->title_en ? $selectedMapSection->title_en : $selectedMapSection->title_ar }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if($selectedMapSection->embed_code)
                                <div class="ratio ratio-21x9 rounded-3 overflow-hidden shadow-sm border mb-3">
                                    {!! $selectedMapSection->embed_code !!}
                                </div>
                            @endif
                            @if($selectedMapSection->google_maps_url)
                                <div class="text-center">
                                    <a href="{{ $selectedMapSection->google_maps_url }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                                        <i class="bi bi-map me-1"></i> {{ $isEn ? 'Open in Google Maps' : 'فتح الموقع في تطبيق Google Maps' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($record->country?->map_embed_code && ($record->country->map_is_active ?? true))
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-danger"></i> {{ $record->country->map_title_ar ?: ($isEn ? $countryName . ' Embassy Location' : 'موقع سفارة ' . $countryName) }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="ratio ratio-21x9 rounded-3 overflow-hidden shadow-sm border">
                                {!! $record->country->map_embed_code !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Selected Lead Form Section (With Auto Prefill for Country & Visa Type) -->
                @if($selectedLeadForm)
                    <div class="card shadow-sm border-0 rounded-4 mb-4" id="lead-form-section">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h4 class="fw-bold text-primary mb-1"><i class="bi bi-file-earmark-person me-2"></i>{{ $selectedLeadForm->localized('title') ?: $selectedLeadForm->name }}</h4>
                            @if($selectedLeadForm->localized('subtitle'))
                                <p class="text-muted small mb-0">{{ $selectedLeadForm->localized('subtitle') }}</p>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            <form method="post" action="{{ route('inquiries.store') }}" class="row g-3">
                                @csrf
                                <input type="hidden" name="lead_form_id" value="{{ $selectedLeadForm->id }}">
                                <input type="hidden" name="type" value="{{ $selectedLeadForm->form_category ?: 'visa' }}">
                                <input type="hidden" name="source_page" value="{{ request()->path() }}">
                                <input type="hidden" name="visa_record_id" value="{{ $record->id }}">

                                @foreach($selectedLeadForm->fields->where('is_enabled', true) as $field)
                                    @php
                                        $fieldKeyLower = strtolower($field->field_key);
                                        $fieldLabelLower = strtolower($field->localized('label') ?: '');
                                        $recordCountry = $countryName;
                                        $recordVisaType = $record->visa_type ?: 'سياحة';

                                        $defaultValue = $field->default_value;

                                        // Auto-detect Country field
                                        $isCountryField = in_array($fieldKeyLower, ['country', 'visa_country', 'destination', 'destination_country', 'country_name'], true)
                                            || \Illuminate\Support\Str::contains($fieldLabelLower, ['دولة', 'البلد', 'الدولة', 'country', 'destination']);

                                        if ($isCountryField && filled($recordCountry) && !filled($defaultValue)) {
                                            $defaultValue = $recordCountry;
                                        }

                                        // Auto-detect Visa Type field
                                        $isVisaTypeField = in_array($fieldKeyLower, ['visa_type', 'visa_category', 'type', 'service_type'], true)
                                            || \Illuminate\Support\Str::contains($fieldLabelLower, ['نوع التأشيرة', 'نوع التأشيره', 'نوع الفيزا', 'visa type', 'service']);

                                        if ($isVisaTypeField && filled($recordVisaType) && !filled($defaultValue)) {
                                            $defaultValue = $recordVisaType;
                                        }

                                        $currentVal = old($field->field_key, $defaultValue);
                                        $label = $field->localized('label') ?: $field->field_key;
                                        $placeholder = $field->localized('placeholder') ?: '';
                                        $fieldType = $field->type === 'phone' ? 'text' : ($field->type ?: 'text');
                                        $colClass = in_array($field->type, ['textarea'], true) ? 'col-12' : 'col-md-6';
                                    @endphp

                                    @if($field->type === 'hidden')
                                        <input type="hidden" name="{{ $field->field_key }}" value="{{ $currentVal }}">
                                    @else
                                        <div class="{{ $colClass }}">
                                            <label class="form-label fw-bold small">
                                                {{ $label }}
                                                @if($field->is_required) <span class="text-danger">*</span> @endif
                                            </label>
                                            @if($field->type === 'textarea')
                                                <textarea name="{{ $field->field_key }}" class="form-control" placeholder="{{ $placeholder }}" {{ $field->is_required ? 'required' : '' }}>{{ $currentVal }}</textarea>
                                            @elseif($field->type === 'select')
                                                @php
                                                    $existingOptions = collect($field->options ?? [])->map(fn($o) => is_array($o) ? ($o['value'] ?? '') : $o)->all();
                                                    $hasCurrentValInOptions = filled($currentVal) && in_array($currentVal, $existingOptions, true);
                                                @endphp
                                                <select name="{{ $field->field_key }}" class="form-select" {{ $field->is_required ? 'required' : '' }}>
                                                    <option value="">{{ $placeholder ?: ($isEn ? '-- Select --' : '-- اختر --') }}</option>
                                                    @if(filled($currentVal) && ! $hasCurrentValInOptions)
                                                        <option value="{{ $currentVal }}" selected>{{ $currentVal }}</option>
                                                    @endif
                                                    @foreach($field->options ?? [] as $opt)
                                                        @php
                                                            $optVal = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                                                            $optLabel = is_array($opt) ? ($opt['label_ar'] ?? $opt['label_en'] ?? $optVal) : $opt;
                                                            $isSelected = (string)$currentVal === (string)$optVal;
                                                        @endphp
                                                        <option value="{{ $optVal }}" {{ $isSelected ? 'selected' : '' }}>{{ $optLabel }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="{{ $fieldType }}" name="{{ $field->field_key }}" class="form-control" value="{{ $currentVal }}" placeholder="{{ $placeholder }}" {{ $field->is_required ? 'required' : '' }}>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach

                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill">
                                        {{ $selectedLeadForm->localized('submit_text') ?: ($isEn ? 'Submit Application Now' : 'إرسال طلب التقديم الآن') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar / Action Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 sticky-top mb-4" style="top: 90px;">
                    <div class="card-body p-4 text-center">
                        <div class="icon-box bg-success-subtle text-success mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $isEn ? 'Want to start application?' : 'هل ترغب في البدء بالتقديم؟' }}</h5>
                        <p class="text-muted small mb-4">{{ $isEn ? 'Our team is ready to prepare your documents and book embassy appointment immediately.' : 'فريقنا متواجد لمساعدتك في تجهيز الملف وحجز موعد السفارة فوراً.' }}</p>

                        @php
                            $waPhone = $setting->whatsapp_phone ?: '201000000000';
                            $waMessage = rawurlencode($setting->formatWhatsappMessage($record));
                        @endphp

                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waMessage }}" target="_blank" class="btn btn-whatsapp w-100 mb-3 shadow-sm">
                            <i class="bi bi-whatsapp me-1"></i> {{ $isEn ? 'Contact via WhatsApp' : 'تواصل معنا عبر واتساب' }}
                        </a>

                        @if(!empty($setting->custom_buttons))
                            <div class="d-flex flex-column gap-2 mb-3">
                                @foreach($setting->custom_buttons as $btn)
                                    @if(!empty($btn['is_active']))
                                        <a href="{{ $btn['url'] }}" target="_blank" class="btn {{ $btn['button_class'] ?? 'btn-outline-primary' }} w-100 btn-sm fw-bold">
                                            <i class="bi {{ $btn['icon'] ?? 'bi-link' }} me-1"></i> {{ $btn['title_ar'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <button onclick="copyShareLink()" class="btn btn-outline-secondary w-100 btn-sm">
                            <i class="bi bi-clipboard me-1"></i> {{ $isEn ? 'Copy Page Link' : 'نسخ رابط هذه المعاينة' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating WhatsApp Button -->
    @if(($setting->floating_whatsapp_enabled ?? true))
        @php
            $waPhone = $setting->whatsapp_phone ?: '201000000000';
            $waMsg = rawurlencode($setting->formatWhatsappMessage($record));
        @endphp
        <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" class="floating-wa-btn">
            <i class="bi bi-whatsapp fs-4"></i> {{ $isEn ? 'WhatsApp Us' : 'تواصل معنا واتساب' }}
        </a>
    @endif

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="copyToast" class="toast bg-dark text-white border-0 align-items-center" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fs-6">
                    <i class="bi bi-check-circle-fill text-success me-2"></i> {{ $isEn ? 'Link copied successfully!' : 'تم نسخ الرابط بنجاح!' }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyShareLink() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                var toastEl = document.getElementById('copyToast');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        }
    </script>
</body>
</html>
