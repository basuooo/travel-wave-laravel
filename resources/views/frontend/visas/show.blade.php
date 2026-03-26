@extends('layouts.app')

@php
    $summaryItems = collect($country->quick_summary_items ?: [])->filter(fn ($item) => ($item['is_active'] ?? true))->sortBy('sort_order')->values();
    $summaryItems = $summaryItems->isNotEmpty() ? $summaryItems : collect([
        ['title_en' => __('ui.visa_type'), 'title_ar' => __('ui.visa_type'), 'value_en' => $country->localized('visa_type'), 'value_ar' => $country->localized('visa_type'), 'icon' => 'VS'],
        ['title_en' => __('ui.processing_time'), 'title_ar' => __('ui.processing_time'), 'value_en' => $country->localized('processing_time'), 'value_ar' => $country->localized('processing_time'), 'icon' => 'PT'],
        ['title_en' => __('ui.stay_duration'), 'title_ar' => __('ui.stay_duration'), 'value_en' => $country->localized('stay_duration'), 'value_ar' => $country->localized('stay_duration'), 'icon' => 'SD'],
        ['title_en' => __('ui.approx_fees'), 'title_ar' => __('ui.approx_fees'), 'value_en' => $country->localized('fees'), 'value_ar' => $country->localized('fees'), 'icon' => 'FE'],
    ]);
    $visaTypeSummary = $country->localized('visa_type') ?: optional($summaryItems->firstWhere('icon', 'VS'), fn ($item) => $country->repeaterValue($item, 'value'));
    $stayDurationSummary = optional($summaryItems->firstWhere('icon', 'SD'), fn ($item) => $country->repeaterValue($item, 'value')) ?: $country->localized('stay_duration');
    $processingTimeSummary = optional($summaryItems->firstWhere('icon', 'PT'), fn ($item) => $country->repeaterValue($item, 'value')) ?: $country->localized('processing_time');
    $rawExcerpt = trim((string) $country->localized('excerpt'));
    $summaryBullets = collect(preg_split('/\r\n|\r|\n/u', $rawExcerpt ?: ''))
        ->map(fn ($line) => trim((string) preg_replace('/^[\-\x{2022}\s]+/u', '', (string) $line)))
        ->filter()
        ->values();

    if ($summaryBullets->count() <= 1) {
        $whyChooseTitles = collect($country->why_choose_items ?: [])
            ->filter(fn ($item) => ($item['is_active'] ?? true) && filled($country->repeaterValue($item, 'title')))
            ->sortBy('sort_order')
            ->map(fn ($item) => $country->repeaterValue($item, 'title'))
            ->take(3)
            ->values();

        $supportSummary = '';

        if ($whyChooseTitles->isNotEmpty()) {
            $supportSummary = __('ui.visa_summary_support', [
                'items' => $whyChooseTitles->join(app()->getLocale() === 'ar' ? '¡ ' : ', '),
            ]);
        }

        $summaryBullets = collect(array_filter([
            $visaTypeSummary ? __('ui.visa_summary_falls_under', ['country' => $country->localized('name'), 'type' => $visaTypeSummary]) : null,
            collect($country->highlights ?: [])
                ->filter(fn ($item) => filled($country->repeaterValue($item, 'text')))
                ->sortBy('sort_order')
                ->map(fn ($item) => $country->repeaterValue($item, 'text'))
                ->first(),
            $stayDurationSummary ? __('ui.visa_summary_stay_duration', ['duration' => $stayDurationSummary]) : null,
            $processingTimeSummary ? __('ui.visa_summary_processing_time', ['time' => $processingTimeSummary]) : null,
            $supportSummary ? rtrim($supportSummary, '. ') . '.' : null,
        ]))->values();
    }

    $heroTitle = $country->localized('hero_title') ?: $country->localized('name') . ' Visa';
    $heroSubtitle = $country->localized('hero_subtitle')
        ?: $country->localized('overview')
        ?: $summaryBullets->first()
        ?: $rawExcerpt;
    $heroCtaText = $country->localized('hero_cta_text') ?: __('ui.inquire_now');
    $heroCtaUrl = $country->hero_cta_url ?: '#visa-inquiry';
    $introTitle = $country->localized('introduction_title') ?: __('ui.visa_overview');
    $introBadge = $country->localized('introduction_badge');
    $introPoints = collect($country->introduction_points ?: [])->filter(fn ($item) => filled($country->repeaterValue($item, 'text')));
    $servicesTitle = $country->localized('why_choose_title') ?: __('ui.why_choose_travel_wave');
    $servicesIntro = $country->localized('why_choose_intro');
    $documentsTitle = $country->localized('documents_title') ?: __('ui.required_documents');
    $documentsSubtitle = $country->localized('documents_subtitle');
    $stepsTitle = $country->localized('steps_title') ?: __('ui.application_steps');
    $feesTitle = $country->localized('fees_title') ?: __('ui.fees_processing');
    $feesNotes = $country->localized('fees_notes');
    $faqTitle = $country->localized('faq_title') ?: __('ui.faq');
    $mapTitle = $country->localized('map_title') ?: __('ui.location_map');
    $mapDescription = $country->localized('map_description');
    $inquiryTitle = $country->localized('inquiry_form_title') ?: __('ui.ask_about_visa');
    $inquirySubtitle = $country->localized('inquiry_form_subtitle');
    $inquiryButton = $country->localized('inquiry_form_button') ?: __('ui.inquire_now');
    $inquirySuccess = $country->localized('inquiry_form_success');
    $finalCtaTitle = $country->localized('cta_title') ?: __('ui.ready_to_apply');
    $finalCtaText = $country->localized('cta_text');
    $finalCtaButton = $country->localized('cta_button') ?: $heroCtaText;
    $whyChooseItems = collect($country->why_choose_items ?: [])->filter(fn ($item) => ($item['is_active'] ?? true))->sortBy('sort_order')->values();
    $documentItems = collect($country->document_items ?: [])->filter(fn ($item) => ($item['is_active'] ?? true))->sortBy('sort_order')->values();
    $stepItems = collect($country->step_items ?: [])->filter(fn ($item) => ($item['is_active'] ?? true))->sortBy('sort_order')->values();
    $feeItems = collect($country->fee_items ?: [])->filter(fn ($item) => ($item['is_active'] ?? true))->sortBy('sort_order')->values();
    $faqItems = collect($country->faqs ?: [])->filter(fn ($item) => ($item['is_active'] ?? true))->sortBy('sort_order')->values();
    $formConfig = [
        'title' => $inquiryTitle,
        'subtitle' => $inquirySubtitle,
        'submit_text' => $inquiryButton,
        'default_service_type' => $country->inquiry_form_default_service_type ?: $country->localized('name') . ' Visa',
        'success_message' => $inquirySuccess,
        'visible_fields' => $country->inquiry_form_visible_fields ?: ['email', 'travel_date', 'message'],
    ];
    $heroDesktop = $country->hero_image ? asset('storage/' . $country->hero_image) : null;
    $heroMobile = ($country->hero_mobile_image || $country->hero_image)
        ? asset('storage/' . ($country->hero_mobile_image ?: $country->hero_image))
        : null;
@endphp

@section('title', $country->localized('meta_title') ?: $heroTitle)
@section('meta_description', $country->localized('meta_description') ?: $country->localized('excerpt'))
@section('og_title', $country->localized('meta_title') ?: $heroTitle)
@section('og_description', $country->localized('meta_description') ?: $country->localized('excerpt'))
@if($country->og_image)
    @section('og_image', asset('storage/' . $country->og_image))
@elseif($country->hero_image)
    @section('og_image', asset('storage/' . $country->hero_image))
@endif

@section('content')
<section class="container pt-4 pt-lg-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb tw-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('ui.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('visas.index') }}">{{ __('ui.visas') }}</a></li>
            @if($country->category)
                <li class="breadcrumb-item"><a href="{{ route('visas.category', $country->category) }}">{{ $country->category->localized('name') }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $country->localized('name') }}</li>
        </ol>
    </nav>

    <div class="tw-visa-reference-hero" style="{{ $heroDesktop ? "--visa-hero-desktop:url('" . $heroDesktop . "'); --visa-hero-mobile:url('" . $heroMobile . "'); --visa-hero-overlay:" . ($country->hero_overlay_opacity ?? 0.45) . ";" : '' }}">
        <div class="row align-items-end g-4">
            <div class="col-xl-7 col-lg-8">
                @if($country->localized('hero_badge'))
                    <span class="tw-visa-reference-badge">{{ $country->localized('hero_badge') }}</span>
                @endif
                <h1 class="display-4 mb-3">{{ $heroTitle }}</h1>
                @if($heroSubtitle)
                    <p class="lead mb-4">{{ $heroSubtitle }}</p>
                @endif
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ $heroCtaUrl }}" class="btn btn-primary btn-lg tw-btn-primary px-4">{{ $heroCtaText }}</a>
                </div>
            </div>
            <div class="col-xl-5 col-lg-4">
                <div class="tw-visa-reference-note">
                    <div class="small text-uppercase text-muted mb-2">{{ __('ui.quick_summary') }}</div>
                    @if($summaryBullets->isNotEmpty())
                        <ul class="tw-visa-reference-summary-list mb-0">
                            @foreach($summaryBullets as $bullet)
                                <li>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                    @elseif($rawExcerpt)
                        <p class="mb-0">{{ $rawExcerpt }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-4">
    <div class="tw-visa-summary-strip">
        <div class="tw-visa-flag-shell">
            @if($country->flag_image)
                <img src="{{ asset('storage/' . $country->flag_image) }}" alt="{{ $country->localized('name') }}" class="tw-visa-flag">
            @else
                <div class="tw-visa-flag-placeholder">{{ strtoupper(substr($country->localized('name'), 0, 2)) }}</div>
            @endif
        </div>
        <div class="row g-3 flex-grow-1">
            <div class="col-6 col-lg">
                <div class="tw-visa-reference-summary-card h-100">
                    <span class="tw-visa-reference-summary-icon">{{ strtoupper(substr($country->localized('name'), 0, 2)) }}</span>
                    <div class="small text-muted">{{ __('ui.country') }}</div>
                    <div class="fw-semibold">{{ $country->localized('name') }}</div>
                </div>
            </div>
            @foreach($summaryItems as $item)
                <div class="col-6 col-lg">
                    <div class="tw-visa-reference-summary-card h-100">
                        <span class="tw-visa-reference-summary-icon">{{ $item['icon'] ?: strtoupper(substr($country->repeaterValue($item, 'title'), 0, 2)) }}</span>
                        <div class="small text-muted">{{ $country->repeaterValue($item, 'title') }}</div>
                        <div class="fw-semibold">{{ $country->repeaterValue($item, 'value') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="container py-4">
    <div class="tw-card p-4 p-lg-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <div class="tw-visa-reference-media">
                    @if($country->intro_image)
                        <img src="{{ asset('storage/' . $country->intro_image) }}" alt="{{ $introTitle }}" class="tw-image-cover">
                    @elseif($country->hero_image)
                        <img src="{{ asset('storage/' . $country->hero_image) }}" alt="{{ $introTitle }}" class="tw-image-cover">
                    @else
                        <div class="tw-visa-reference-media-placeholder">{{ $country->localized('name') }}</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                @if($introBadge)
                    <div class="tw-visa-reference-inline-badge mb-3">{{ $introBadge }}</div>
                @endif
                <h2 class="tw-section-title h2 mb-3">{{ $introTitle }}</h2>
                <p class="tw-copy mb-4">{{ $country->localized('overview') }}</p>
                @if($introPoints->isNotEmpty())
                    <div class="row g-3">
                        @foreach($introPoints as $point)
                            <div class="col-md-6">
                                <div class="tw-visa-reference-point">
                                    <span class="tw-visa-reference-point-dot"></span>
                                    <span>{{ $country->repeaterValue($point, 'text') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@if($whyChooseItems->isNotEmpty())
<section class="container py-4">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <div class="small text-uppercase text-muted mb-2">{{ __('ui.why_choose_travel_wave') }}</div>
            <h2 class="tw-section-title h2 mb-2">{{ $servicesTitle }}</h2>
            @if($servicesIntro)
                <p class="text-muted mb-0">{{ $servicesIntro }}</p>
            @endif
        </div>
    </div>
    <div class="row g-4">
        @foreach($whyChooseItems as $item)
            <div class="col-md-6 col-xl-3">
                <div class="tw-card p-4 h-100 tw-visa-reference-service-card">
                    <span class="tw-visa-reference-service-icon">{{ strtoupper(substr($item['icon'] ?: $country->repeaterValue($item, 'title'), 0, 2)) }}</span>
                    <h3 class="h5 mt-3 mb-2">{{ $country->repeaterValue($item, 'title') }}</h3>
                    <p class="text-muted mb-0">{{ $country->repeaterValue($item, 'description') }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@if($documentItems->isNotEmpty())
<section class="container py-4">
    <div class="tw-card p-4 p-lg-5">
        <div class="mb-4">
            <h2 class="tw-section-title h2 mb-2">{{ $documentsTitle }}</h2>
            @if($documentsSubtitle)
                <p class="text-muted mb-0">{{ $documentsSubtitle }}</p>
            @endif
        </div>
        <div class="row g-3">
            @foreach($documentItems as $item)
                <div class="col-md-6 col-xl-4">
                    <div class="tw-visa-reference-doc-card h-100">
                        <div class="tw-visa-reference-doc-icon">OK</div>
                        <div>
                            <h3 class="h6 mb-2">{{ $country->repeaterValue($item, 'name') }}</h3>
                            @if($country->repeaterValue($item, 'description'))
                                <p class="text-muted mb-0">{{ $country->repeaterValue($item, 'description') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($stepItems->isNotEmpty())
<section class="container py-4">
    <div class="tw-card p-4 p-lg-5">
        <h2 class="tw-section-title h2 mb-4">{{ $stepsTitle }}</h2>
        <div class="tw-visa-reference-steps">
            @foreach($stepItems as $item)
                <div class="tw-visa-reference-step">
                    <div class="tw-visa-reference-step-number">{{ $item['step_number'] ?? $loop->iteration }}</div>
                    <div class="tw-visa-reference-step-body">
                        <h3 class="h5 mb-2">{{ $country->repeaterValue($item, 'title') }}</h3>
                        <p class="text-muted mb-0">{{ $country->repeaterValue($item, 'description') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="container py-4">
    <div class="tw-card p-4 p-lg-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <h2 class="tw-section-title h2 mb-3">{{ $feesTitle }}</h2>
                <p class="text-muted mb-3">{{ $country->localized('processing_time') }}</p>
                @if($feesNotes)
                    <p class="mb-0">{{ $feesNotes }}</p>
                @endif
            </div>
            <div class="col-lg-8">
                <div class="row g-3">
                    @if($feeItems->isNotEmpty())
                        @foreach($feeItems as $item)
                            <div class="col-md-6">
                                <div class="tw-visa-price-card h-100">
                                    <div class="small text-muted mb-2">{{ $country->repeaterValue($item, 'label') }}</div>
                                    <div class="h5 mb-0">{{ $country->repeaterValue($item, 'value') }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-6">
                            <div class="tw-visa-price-card h-100">
                                <div class="small text-muted mb-2">{{ __('ui.processing_time') }}</div>
                                <div class="h5 mb-0">{{ $country->localized('processing_time') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="tw-visa-price-card h-100">
                                <div class="small text-muted mb-2">{{ __('ui.approx_fees') }}</div>
                                <div class="h5 mb-0">{{ $country->localized('fees') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if($faqItems->isNotEmpty())
<section class="container py-4">
    <div class="tw-card p-4 p-lg-5">
        <h2 class="tw-section-title h2 mb-4">{{ $faqTitle }}</h2>
        <div class="accordion tw-visa-faq" id="visaFaqs">
            @foreach($faqItems as $item)
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#visa-faq-{{ $loop->iteration }}">
                            {{ $country->repeaterValue($item, 'question') }}
                        </button>
                    </h3>
                    <div id="visa-faq-{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#visaFaqs">
                        <div class="accordion-body">{{ $country->repeaterValue($item, 'answer') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($country->final_cta_is_active)
<section class="container py-4">
    <div class="tw-visa-final-cta" style="{{ $country->final_cta_background_image ? "background-image:linear-gradient(135deg, rgba(9, 31, 51, 0.9), rgba(18, 57, 91, 0.82)), url('" . asset('storage/' . $country->final_cta_background_image) . "');" : '' }}">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h2 class="display-6 mb-3">{{ $finalCtaTitle }}</h2>
                @if($finalCtaText)
                    <p class="lead mb-0">{{ $finalCtaText }}</p>
                @endif
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ $country->cta_url ?: '#visa-inquiry' }}" class="btn btn-primary btn-lg tw-btn-primary px-4">{{ $finalCtaButton }}</a>
            </div>
        </div>
    </div>
</section>
@endif
@endsection


