@php
    $isEn = app()->getLocale() === 'en';
    $dir = $isEn ? 'ltr' : 'rtl';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isEn ? 'Visa Directory & Calculator' : 'دليل وحاسبة التأشيرات' }} | Travel Wave</title>
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
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 3rem 0;
            border-radius: 0 0 2rem 2rem;
        }
        .visa-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            transition: all 0.25s ease;
            height: 100%;
        }
        .visa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
            border-color: #3b82f6;
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
            transition: transform 0.2s ease;
        }
        .floating-wa-btn:hover {
            transform: scale(1.05);
            color: white;
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
            </div>
        </div>
    </nav>

    <!-- Hero Header -->
    <div class="hero-banner mb-5 text-center">
        <div class="container">
            <span class="badge bg-primary px-3 py-2 rounded-pill fs-6 mb-3">{{ $isEn ? 'Global Visa Guide' : 'دليل تأشيرات دول العالم' }}</span>
            <h1 class="fw-extrabold display-5 mb-3">{{ $isEn ? 'World Visas & Requirements Database' : 'قاعدة بيانات ومتطلبات التأشيرات والفيزا' }}</h1>
            <p class="text-white-50 fs-5 max-w-2xl mx-auto">{{ $isEn ? 'Browse requirements, documents, and visa fees for countries easily' : 'تصفح الشروط والأوراق المطلوبة وأسعار التأشيرات لكل دول العالم بسهولة' }}</p>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Search & Filter Card -->
        <div class="card shadow-sm border-0 rounded-4 mb-5">
            <div class="card-body p-4">
                <form action="{{ route('visa-database.public-catalog') }}" method="GET" class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label fw-bold">{{ $isEn ? 'Search by Country or Visa Type' : 'بحث باسم الدولة أو نوع التأشيرة' }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="{{ $isEn ? 'Type country name (e.g., France, Germany, Spain)...' : 'اكتب اسم الدولة (مثال: فرنسا، المانيا، إسبانيا)...' }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">{{ $isEn ? 'Category' : 'التصنيف' }}</label>
                        <select name="category_id" class="form-select">
                            <option value="">{{ $isEn ? 'All Categories' : 'جميع التصنيفات' }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $isEn && $cat->name_en ? $cat->name_en : $cat->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2"><i class="bi bi-filter me-1"></i> {{ $isEn ? 'Search' : 'بحث' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Visa Cards Grid -->
        <div class="row g-4 mb-4">
            @forelse($records as $rec)
                @php
                    $recCountryName = $rec->country ? ($isEn && $rec->country->name_en ? $rec->country->name_en : ($rec->country->name_ar ?: $rec->country->name_en)) : '';
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="visa-card p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="fw-bold mb-1 text-dark">{{ $recCountryName }}</h4>
                                    <span class="badge bg-light text-secondary border">{{ $rec->visa_type }}</span>
                                </div>
                                @if(($setting->show_price ?? true))
                                    <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill fw-bold">
                                        @if($rec->price && $rec->price > 0)
                                            {{ number_format($rec->price, 0) }} {{ $rec->currency ?: 'EGP' }}
                                        @else
                                            {{ $isEn ? 'Contact us' : 'تواصل للتحديد' }}
                                        @endif
                                    </span>
                                @endif
                            </div>

                            <ul class="list-unstyled small text-muted mb-4 space-y-2">
                                @if(($setting->show_working_days ?? true))
                                    <li class="mb-2"><i class="bi bi-clock me-2 text-primary"></i> <strong>{{ $isEn ? 'Processing:' : 'مدة العمل:' }}</strong> {{ $rec->working_days ?: ($isEn ? '15-20 days' : '15-20 يوم') }}</li>
                                @endif
                                @if(($setting->show_embassy_fee ?? true))
                                    <li class="mb-2"><i class="bi bi-cash me-2 text-primary"></i> <strong>{{ $isEn ? 'Embassy Fee:' : 'رسوم السفارة:' }}</strong> {{ $rec->embassy_fee ? $rec->embassy_fee . ' ' . $rec->embassy_fee_currency : ($isEn ? 'Not specified' : 'غير محدد') }}</li>
                                @endif
                                <li class="mb-2"><i class="bi bi-building me-2 text-primary"></i> <strong>{{ $isEn ? 'Center:' : 'مكان التقديم:' }}</strong> {{ is_array($rec->application_center) ? implode(', ', $rec->application_center) : ($rec->application_center ?: ($isEn ? 'Embassy Direct' : 'السفارة')) }}</li>
                            </ul>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            @if(($setting->show_preview_button ?? true))
                                <a href="{{ route('visa-database.public-preview', $rec->id) }}" class="btn btn-outline-primary w-100 fw-bold rounded-3 py-2">
                                    <i class="bi bi-eye-fill me-1"></i> {{ $isEn ? 'View Requirements & Details' : 'معاينة الشروط والأوراق' }}
                                </a>
                            @endif

                            @php
                                $waPhone = $setting->whatsapp_phone ?: '201000000000';
                                $waMsg = rawurlencode($setting->formatWhatsappMessage($rec));
                            @endphp
                            <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" class="btn btn-success w-100 fw-bold rounded-3 py-2">
                                <i class="bi bi-whatsapp me-1"></i> {{ $isEn ? 'WhatsApp Us' : 'تواصل عبر واتساب' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search text-muted display-1"></i>
                    <h4 class="fw-bold mt-3 text-muted">{{ $isEn ? 'No results found matching your search' : 'لم يتم العثور على نتائج تطابق بحثك' }}</h4>
                    <p class="text-muted">{{ $isEn ? 'Try searching with a different term or select another category' : 'جرب البحث بكلمة مختلفة أو اختر تصنيف آخر' }}</p>
                </div>
            @endforelse
        </div>

        <!-- Custom Action Buttons if available -->
        @if(!empty($setting->custom_buttons))
            <div class="d-flex flex-wrap gap-2 justify-content-center mb-5">
                @foreach($setting->custom_buttons as $btn)
                    @if(!empty($btn['is_active']))
                        <a href="{{ $btn['url'] }}" target="_blank" class="btn {{ $btn['button_class'] ?? 'btn-outline-primary' }} rounded-pill px-4 py-2 fw-bold">
                            <i class="bi {{ $btn['icon'] ?? 'bi-link' }} me-1"></i> {{ $btn['title_ar'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- System Lead Form Section if selected -->
        @if($selectedLeadForm)
            <div class="card shadow-sm border-0 rounded-4 mb-5" id="lead-form-section">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 text-center">
                    <h3 class="fw-bold text-primary mb-1">{{ $selectedLeadForm->localized('title') ?: $selectedLeadForm->name }}</h3>
                    @if($selectedLeadForm->localized('subtitle'))
                        <p class="text-muted small">{{ $selectedLeadForm->localized('subtitle') }}</p>
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

                        @foreach($selectedLeadForm->fields->where('is_enabled', true) as $field)
                            @php
                                $fieldKey = $field->field_key;
                                $label = $field->localized('label') ?: $fieldKey;
                                $placeholder = $field->localized('placeholder') ?: '';
                                $fieldType = $field->type === 'phone' ? 'text' : ($field->type ?: 'text');
                                $colClass = in_array($field->type, ['textarea'], true) ? 'col-12' : 'col-md-6';
                            @endphp

                            @if($field->type === 'hidden')
                                <input type="hidden" name="{{ $fieldKey }}" value="{{ $field->default_value }}">
                            @else
                                <div class="{{ $colClass }}">
                                    <label class="form-label fw-bold small">
                                        {{ $label }}
                                        @if($field->is_required) <span class="text-danger">*</span> @endif
                                    </label>
                                    @if($field->type === 'textarea')
                                        <textarea name="{{ $fieldKey }}" class="form-control" placeholder="{{ $placeholder }}" {{ $field->is_required ? 'required' : '' }}>{{ old($fieldKey, $field->default_value) }}</textarea>
                                    @elseif($field->type === 'select')
                                        <select name="{{ $fieldKey }}" class="form-select" {{ $field->is_required ? 'required' : '' }}>
                                            <option value="">{{ $placeholder ?: ($isEn ? '-- Select --' : '-- اختر --') }}</option>
                                            @foreach($field->options ?? [] as $opt)
                                                @php
                                                    $optVal = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                                                    $optLabel = is_array($opt) ? ($opt['label_ar'] ?? $opt['label_en'] ?? $optVal) : $opt;
                                                @endphp
                                                <option value="{{ $optVal }}">{{ $optLabel }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $fieldType }}" name="{{ $fieldKey }}" class="form-control" value="{{ old($fieldKey, $field->default_value) }}" placeholder="{{ $placeholder }}" {{ $field->is_required ? 'required' : '' }}>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill">
                                {{ $selectedLeadForm->localized('submit_text') ?: ($isEn ? 'Submit Application Now' : 'إرسال طلب التقديم الآن') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- System Map Section if selected -->
        @if($selectedMapSection)
            <div class="card shadow-sm border-0 rounded-4 mb-5" id="map-section">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h4 class="fw-bold text-dark mb-1"><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $isEn && $selectedMapSection->title_en ? $selectedMapSection->title_en : $selectedMapSection->title_ar }}</h4>
                    @if($selectedMapSection->subtitle_ar || $selectedMapSection->subtitle_en)
                        <p class="text-muted small mb-0">{{ $isEn && $selectedMapSection->subtitle_en ? $selectedMapSection->subtitle_en : $selectedMapSection->subtitle_ar }}</p>
                    @endif
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
                                <i class="bi bi-map me-1"></i> {{ $isEn ? 'Open Location in Google Maps' : 'فتح الموقع في تطبيق Google Maps' }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $records->links() }}
        </div>
    </div>

    <!-- Floating WhatsApp Button -->
    @if(($setting->floating_whatsapp_enabled ?? true))
        @php
            $waPhone = $setting->whatsapp_phone ?: '201000000000';
            $waMsg = rawurlencode($setting->formatWhatsappMessage());
        @endphp
        <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" class="floating-wa-btn">
            <i class="bi bi-whatsapp fs-4"></i> {{ $isEn ? 'WhatsApp Us' : 'تواصل معنا واتساب' }}
        </a>
    @endif
</body>
</html>
