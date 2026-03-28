@extends('layouts.app')

@php
    $pageTitle = $pageData['meta_title'] ?? $pageData['title'];
    $pageDescription = $pageData['meta_description'] ?? $pageData['subtitle'];
    $hero = $pageData['hero'] ?? [];
    $quickInfo = collect($pageData['quick_info']['items'] ?? []);
    $about = $pageData['about'] ?? [];
    $details = $pageData['details'] ?? [];
    $bestTime = $pageData['best_time'] ?? [];
    $highlights = collect($pageData['highlights']['items'] ?? []);
    $services = collect($pageData['services']['items'] ?? []);
    $documents = collect($pageData['documents']['items'] ?? []);
    $steps = collect($pageData['steps']['items'] ?? []);
    $pricing = collect($pageData['pricing']['items'] ?? []);
    $faqItems = collect($pageData['faq']['items'] ?? []);
    $cta = $pageData['cta'] ?? [];
    $form = $pageData['form'] ?? [];
    $map = $pageData['map'] ?? [];
    $excerptLines = collect(preg_split('/\r\n|\r|\n/', (string) ($pageData['subtitle'] ?? '')))
        ->map(fn ($line) => trim(ltrim(trim($line), "-• \t")))
        ->filter()
        ->values();
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDescription)
@section('og_title', $pageTitle)
@section('og_description', $pageDescription)
@if(!empty($pageData['og_image']))
    @section('og_image', $pageData['og_image'])
@endif

@section('content')
@include('partials.frontend.form-zone', ['assignments' => $managedForms['top'] ?? [], 'position' => 'top', 'sourcePage' => $pageData['title'], 'contextData' => $pageData])
@include('partials.frontend.map-zone', ['assignments' => $managedMaps['top'] ?? [], 'position' => 'top'])
<section class="container pt-4 pt-lg-5">
    @if(!empty($pageData['breadcrumbs']))
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb tw-breadcrumb mb-0">
                @foreach($pageData['breadcrumbs'] as $crumb)
                    @if(!$loop->last && !empty($crumb['url']))
                        <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif

    @if(!empty($hero['enabled']))
        <div class="tw-visa-reference-hero tw-destination-page-hero"
             style="{{ !empty($hero['background_image']) ? "--visa-hero-desktop:url('" . $hero['background_image'] . "'); --visa-hero-mobile:url('" . ($hero['mobile_background_image'] ?? $hero['background_image']) . "'); --visa-hero-overlay:" . ($hero['overlay_opacity'] ?? 0.45) . ";" : '' }}">
            <div class="row align-items-end g-4">
                <div class="col-xl-7 col-lg-8">
                    @if(!empty($hero['badge']))
                        <span class="tw-visa-reference-badge">{{ $hero['badge'] }}</span>
                    @endif
                    <h1 class="display-4 mb-3">{{ $hero['title'] }}</h1>
                    @if(!empty($hero['subtitle']))
                        <p class="lead mb-4">{{ $hero['subtitle'] }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-3">
                        @if(!empty($hero['primary_button']['text']))
                            <a href="{{ $hero['primary_button']['url'] ?: '#destination-form' }}" class="btn btn-primary btn-lg tw-btn-primary px-4">
                                {{ $hero['primary_button']['text'] }}
                            </a>
                        @endif
                        @if(!empty($hero['secondary_button']['text']))
                            <a href="{{ $hero['secondary_button']['url'] ?: '#destination-summary' }}" class="btn btn-outline-light btn-lg px-4">
                                {{ $hero['secondary_button']['text'] }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-xl-5 col-lg-4">
                    <div class="tw-visa-reference-note">
                        <div class="small text-uppercase text-muted mb-2">{{ $pageData['quick_info']['title'] ?? __('ui.quick_summary') }}</div>
                        @if($excerptLines->isNotEmpty())
                            <ul class="tw-visa-reference-summary-list mb-0">
                                @foreach($excerptLines->take(5) as $line)
                                    <li><span>{{ $line }}</span></li>
                                @endforeach
                            </ul>
                        @elseif($quickInfo->isNotEmpty())
                            <ul class="tw-visa-reference-summary-list mb-0">
                                @foreach($quickInfo->take(5) as $item)
                                    <li>
                                        <strong>{{ $item['label'] }}:</strong>
                                        <span>{{ $item['value'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @elseif(!empty($pageData['subtitle']))
                            <p class="mb-0">{{ $pageData['subtitle'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>

@include('partials.frontend.form-zone', ['assignments' => $managedForms['below_hero'] ?? [], 'position' => 'below_hero', 'sourcePage' => $pageData['title'], 'contextData' => $pageData])
@include('partials.frontend.map-zone', ['assignments' => $managedMaps['below_hero'] ?? [], 'position' => 'below_hero'])

@if(!empty($pageData['quick_info']['enabled']) && $quickInfo->isNotEmpty())
    <section class="container py-4" id="destination-summary">
        <div class="tw-visa-summary-strip">
            <div class="tw-visa-flag-shell">
                @if(!empty($hero['flag_image']))
                    <img src="{{ $hero['flag_image'] }}" alt="{{ $pageData['title'] }}" class="tw-visa-flag">
                @else
                    <div class="tw-visa-flag-placeholder">{{ strtoupper(mb_substr($pageData['title'], 0, 2)) }}</div>
                @endif
            </div>
            <div class="row g-3 flex-grow-1">
                @foreach($quickInfo as $item)
                    <div class="col-6 col-lg">
                        <div class="tw-visa-reference-summary-card h-100">
                            <span class="tw-visa-reference-summary-icon">@include('partials.frontend.icon', ['icon' => $item['icon'] ?? null, 'fallback' => 'star'])</span>
                            <div class="small text-muted">{{ $item['label'] }}</div>
                            <div class="fw-semibold">{{ $item['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if(!empty($about['enabled']))
    <section class="container py-4">
        <div class="tw-card p-4 p-lg-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <div class="tw-visa-reference-media">
                        @if(!empty($about['image']))
                            <img src="{{ $about['image'] }}" alt="{{ $about['title'] }}" class="tw-image-cover">
                        @else
                            <div class="tw-visa-reference-media-placeholder">{{ $pageData['title'] }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-7">
                    @if(!empty($about['badge']))
                        <div class="tw-visa-reference-inline-badge mb-3">{{ $about['badge'] }}</div>
                    @endif
                    <h2 class="tw-section-title h2 mb-3">{{ $about['title'] }}</h2>
                    @if(!empty($about['description']))
                        <p class="tw-copy mb-4">{!! nl2br(e($about['description'])) !!}</p>
                    @endif
                    @if(!empty($about['points']))
                        <div class="row g-3">
                            @foreach($about['points'] as $point)
                                <div class="col-md-6">
                                    <div class="tw-visa-reference-point">
                                        <span class="tw-visa-reference-point-dot"></span>
                                        <span>{{ $point['text'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif

@if(!empty($details['enabled']) || !empty($bestTime['enabled']))
    <section class="container py-4">
        <div class="row g-4">
            @if(!empty($details['enabled']))
                <div class="col-lg-7">
                    <div class="tw-card p-4 p-lg-5 h-100">
                        <h2 class="tw-section-title h2 mb-3">{{ $details['title'] }}</h2>
                        <div class="tw-copy">{!! nl2br(e($details['description'] ?? '')) !!}</div>
                    </div>
                </div>
            @endif
            @if(!empty($bestTime['enabled']))
                <div class="col-lg-5">
                    <div class="tw-card p-4 p-lg-5 h-100 tw-destination-side-note">
                        <div class="tw-visa-reference-inline-badge mb-3">{{ $bestTime['badge'] ?? __('ui.best_time') }}</div>
                        <h2 class="tw-section-title h3 mb-3">{{ $bestTime['title'] }}</h2>
                        <p class="mb-0">{!! nl2br(e($bestTime['description'] ?? '')) !!}</p>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif

@include('partials.frontend.form-zone', ['assignments' => $managedForms['middle'] ?? [], 'position' => 'middle', 'sourcePage' => $pageData['title'], 'contextData' => $pageData])
@include('partials.frontend.map-zone', ['assignments' => $managedMaps['middle'] ?? [], 'position' => 'middle'])

@if($highlights->isNotEmpty())
    <section class="container py-4" id="destination-highlights">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <div class="small text-uppercase text-muted mb-2">{{ $pageData['highlights']['label'] ?? (app()->getLocale() === 'ar' ? 'أهم الإرشادات' : 'Helpful Guidance Points') }}</div>
                <h2 class="tw-section-title h2 mb-0">{{ $pageData['highlights']['title'] }}</h2>
            </div>
        </div>
        <div class="row g-4">
            @foreach($highlights as $item)
                <div class="col-md-6 col-xl-4">
                    <div class="tw-card p-3 h-100 tw-destination-highlight-card {{ ($pageData['type'] ?? '') === 'visa' ? 'tw-visa-guidance-card' : '' }}">
                        <div class="tw-destination-highlight-media">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="tw-image-cover">
                            @else
                                <div class="tw-destination-highlight-placeholder">@include('partials.frontend.icon', ['icon' => $item['icon'] ?? null, 'fallback' => 'star'])</div>
                            @endif
                        </div>
                        <div class="pt-3">
                            <h3 class="h5 mb-2">{{ $item['title'] }}</h3>
                            @if(!empty($item['description']))
                                <p class="text-muted mb-0">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

@if($services->isNotEmpty())
    <section class="container py-4">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <div class="small text-uppercase text-muted mb-2">{{ __('ui.services') }}</div>
                <h2 class="tw-section-title h2 mb-2">{{ $pageData['services']['title'] }}</h2>
                @if(!empty($pageData['services']['description']))
                    <p class="text-muted mb-0">{{ $pageData['services']['description'] }}</p>
                @endif
            </div>
        </div>
        <div class="row g-4">
            @foreach($services as $item)
                <div class="col-md-6 col-xl-3">
                    <div class="tw-card p-4 h-100 tw-visa-reference-service-card">
                        <span class="tw-visa-reference-service-icon">@include('partials.frontend.icon', ['icon' => $item['icon'] ?? null, 'fallback' => 'support'])</span>
                        <h3 class="h5 mt-3 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-muted mb-0">{{ $item['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

@if($documents->isNotEmpty())
    <section class="container py-4">
        <div class="tw-card p-4 p-lg-5">
            <div class="mb-4">
                <h2 class="tw-section-title h2 mb-2">{{ $pageData['documents']['title'] }}</h2>
                @if(!empty($pageData['documents']['description']))
                    <p class="text-muted mb-0">{{ $pageData['documents']['description'] }}</p>
                @endif
            </div>
            <div class="row g-3">
                @foreach($documents as $item)
                    <div class="col-md-6 col-xl-4">
                        <div class="tw-visa-reference-doc-card h-100">
                            <div class="tw-visa-reference-doc-icon">@include('partials.frontend.icon', ['icon' => $item['icon'] ?? null, 'fallback' => 'check'])</div>
                            <div>
                                <h3 class="h6 mb-2">{{ $item['title'] }}</h3>
                                @if(!empty($item['description']))
                                    <p class="text-muted mb-0">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if($steps->isNotEmpty())
    <section class="container py-4">
        <div class="tw-card p-4 p-lg-5">
            <h2 class="tw-section-title h2 mb-4">{{ $pageData['steps']['title'] }}</h2>
            <div class="tw-visa-reference-steps tw-destination-steps-grid">
                @foreach($steps as $item)
                    <div class="tw-visa-reference-step">
                        <div class="tw-visa-reference-step-number">{{ $item['number'] ?: $loop->iteration }}</div>
                        <div class="tw-visa-reference-step-body">
                            <h3 class="h5 mb-2">{{ $item['title'] }}</h3>
                            <p class="text-muted mb-0">{{ $item['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if($pricing->isNotEmpty())
    <section class="container py-4">
        <div class="tw-card p-4 p-lg-5">
            <div class="row g-4 align-items-start">
                <div class="col-lg-4">
                    <h2 class="tw-section-title h2 mb-3">{{ $pageData['pricing']['title'] }}</h2>
                    @if(!empty($pageData['pricing']['description']))
                        <p class="mb-0">{{ $pageData['pricing']['description'] }}</p>
                    @endif
                </div>
                <div class="col-lg-8">
                    <div class="row g-3">
                        @foreach($pricing as $item)
                            <div class="col-md-6">
                                <div class="tw-visa-price-card h-100">
                                    <div class="small text-muted mb-2">{{ $item['label'] }}</div>
                                    <div class="h5 mb-2">{{ $item['value'] }}</div>
                                    @if(!empty($item['note']))
                                        <p class="mb-0 text-muted">{{ $item['note'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@include('partials.frontend.form-zone', ['assignments' => $managedForms['before_faq'] ?? [], 'position' => 'before_faq', 'sourcePage' => $pageData['title'], 'contextData' => $pageData])
@include('partials.frontend.map-zone', ['assignments' => $managedMaps['before_faq'] ?? [], 'position' => 'before_faq'])

@if($faqItems->isNotEmpty())
    <section class="container py-4">
        <div class="tw-card p-4 p-lg-5">
            <h2 class="tw-section-title h2 mb-4">{{ $pageData['faq']['title'] }}</h2>
            <div class="accordion tw-visa-faq" id="destinationFaqs">
                @foreach($faqItems as $item)
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#destination-faq-{{ $loop->iteration }}">
                                {{ $item['question'] }}
                            </button>
                        </h3>
                        <div id="destination-faq-{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#destinationFaqs">
                            <div class="accordion-body">{{ $item['answer'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

@include('partials.frontend.form-zone', ['assignments' => $managedForms['after_faq'] ?? [], 'position' => 'after_faq', 'sourcePage' => $pageData['title'], 'contextData' => $pageData])
@include('partials.frontend.map-zone', ['assignments' => $managedMaps['after_faq'] ?? [], 'position' => 'after_faq'])

@if(!empty($cta['enabled']))
    <section class="container py-4">
        <div class="tw-visa-final-cta"
             style="{{ !empty($cta['background_image']) ? "background-image:linear-gradient(135deg, rgba(9, 31, 51, 0.9), rgba(18, 57, 91, 0.82)), url('" . $cta['background_image'] . "');" : '' }}">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h2 class="display-6 mb-3">{{ $cta['title'] }}</h2>
                    @if(!empty($cta['description']))
                        <p class="lead mb-0">{{ $cta['description'] }}</p>
                    @endif
                </div>
                <div class="col-lg-5">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-3">
                        @foreach($cta['buttons'] ?? [] as $button)
                            <a href="{{ $button['url'] ?: '#destination-form' }}"
                               class="btn btn-lg px-4 {{ ($button['style'] ?? 'primary') === 'outline' ? 'btn-outline-light' : 'btn-primary tw-btn-primary' }}">
                                {{ $button['text'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@include('partials.frontend.form-zone', ['assignments' => $managedForms['bottom'] ?? [], 'position' => 'bottom', 'sourcePage' => $pageData['title'], 'contextData' => $pageData])
@include('partials.frontend.map-zone', ['assignments' => $managedMaps['bottom'] ?? [], 'position' => 'bottom'])
@endsection
