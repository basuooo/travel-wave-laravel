@extends('layouts.app')

@section('title', $page->localized('meta_title') ?: $page->localized('title'))

@section('content')
@php($sections = $page->sections ?? [])
@php($sliderSettings = $heroSliderSettings)
@php($bannerMode = $sliderSettings?->hero_slider_layout_mode ?? 'custom-1408')
@php($safeZoneClass = in_array($bannerMode, ['full-width', 'fullscreen-hero'], true) ? 'container-fluid' : 'container-xxl')
@php($homeSearchText = app()->getLocale() === 'ar'
    ? [
        'eyebrow' => 'اختر خدمتك بسرعة',
        'title' => 'ابدأ من الخدمة المناسبة ثم انتقل مباشرة إلى الصفحة المطلوبة',
        'subtitle' => 'اختيار ذكي للتأشيرات الخارجية والرحلات الداخلية والطيران والفنادق من مكان واحد.',
        'service' => 'اختر الخدمة',
        'region' => 'اختر المنطقة',
        'country' => 'اختر الدولة',
        'destination' => 'اختر الوجهة',
        'button' => 'اذهب الآن',
        'placeholder' => 'اختر',
        'invalid' => 'اختر خدمة ومساراً صالحاً أولاً',
    ]
    : [
        'eyebrow' => 'Find The Right Service',
        'title' => 'Start with the right service and jump straight to the page you need',
        'subtitle' => 'A smarter selection bar for visas, domestic trips, flights, and hotels in one place.',
        'service' => 'Select service',
        'region' => 'Select region',
        'country' => 'Select country',
        'destination' => 'Select destination',
        'button' => 'Search Now',
        'placeholder' => 'Select',
        'invalid' => 'Choose a valid service and destination first',
    ])

@include('partials.frontend.form-zone', ['assignments' => $managedForms['top'] ?? [], 'position' => 'top', 'sourcePage' => 'home'])
@include('partials.frontend.map-zone', ['assignments' => $managedMaps['top'] ?? [], 'position' => 'top'])

<section class="tw-home-slider-wrap tw-home-slider-mode-{{ $bannerMode }}">
    <div class="{{ $safeZoneClass }} tw-home-slider-shell">
    <div id="travelWaveHeroSlider"
         class="carousel slide tw-home-slider"
         data-bs-ride="{{ ($sliderSettings?->hero_slider_autoplay ?? true) ? 'carousel' : 'false' }}"
         data-bs-interval="{{ $sliderSettings?->hero_slider_interval ?? 5000 }}">

        @if(($sliderSettings?->hero_slider_show_dots ?? true) && $heroSlides->count() > 1)
            <div class="carousel-indicators">
                @foreach($heroSlides as $slide)
                    <button type="button" data-bs-target="#travelWaveHeroSlider" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $loop->iteration }}"></button>
                @endforeach
            </div>
        @endif

        <div class="carousel-inner">
            @forelse($heroSlides as $slide)
                @php($headline = trim((string) $slide->localized('headline')))
                @php($subtitle = trim((string) $slide->localized('subtitle')))
                @php($ctaText = trim((string) $slide->localized('cta_text')))
                @php($desktopImage = asset('storage/' . $slide->image_path))
                @php($mobileImage = asset('storage/' . ($slide->mobile_image_path ?: $slide->image_path)))
                @php($hasSlideContent = $headline !== '' || $subtitle !== '' || ($ctaText !== '' && filled($slide->cta_link)))
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <div class="tw-home-slide" style="--slide-desktop-image: url('{{ $desktopImage }}'); --slide-mobile-image: url('{{ $mobileImage }}');">
                        @if($hasSlideContent)
                            <div class="tw-home-slide-overlay" style="--slide-overlay: {{ $sliderSettings?->hero_slider_overlay_opacity ?? 0.45 }}"></div>
                            <div class="tw-home-slide-stage position-relative h-100">
                                <div class="row h-100 align-items-center justify-content-{{ $sliderSettings?->hero_slider_content_alignment ?? 'start' }}">
                                    <div class="col-xl-7 col-lg-8 col-md-10">
                                        <div class="tw-home-slide-content text-{{ $sliderSettings?->hero_slider_content_alignment ?? 'start' }}">
                                            @if($page->localized('hero_badge'))
                                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill mb-3">{{ $page->localized('hero_badge') }}</span>
                                            @endif
                                            @if($headline !== '')
                                                <h1 class="display-4 fw-bold mb-3">{{ $headline }}</h1>
                                            @endif
                                            @if($subtitle !== '')
                                                <p class="lead text-white-50 mb-4">{{ $subtitle }}</p>
                                            @endif
                                            @if(($ctaText !== '' && filled($slide->cta_link)) || $page->hero_secondary_cta_url)
                                                <div class="d-flex flex-wrap gap-3 justify-content-{{ $sliderSettings?->hero_slider_content_alignment ?? 'start' }}">
                                                    @if($ctaText !== '' && filled($slide->cta_link))
                                                        <a href="{{ $slide->cta_link }}" class="btn btn-primary btn-lg tw-btn-primary">{{ $ctaText }}</a>
                                                    @endif
                                                    @if($page->hero_secondary_cta_url && $page->localized('hero_secondary_cta_text'))
                                                        <a href="{{ $page->hero_secondary_cta_url }}" class="btn btn-lg tw-btn-outline">{{ $page->localized('hero_secondary_cta_text') }}</a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="carousel-item active">
                    <section class="tw-hero py-5">
                        <div class="container position-relative" style="z-index:1">
                            <div class="row align-items-center g-5 py-5">
                                <div class="col-lg-7">
                                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill mb-3">{{ $page->localized('hero_badge') }}</span>
                                    <h1 class="display-4 fw-bold mb-3">{{ $page->localized('hero_title') }}</h1>
                                    <p class="lead text-white-50 mb-4">{{ $page->localized('hero_subtitle') }}</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            @endforelse
        </div>

        @if(($sliderSettings?->hero_slider_show_arrows ?? true) && $heroSlides->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#travelWaveHeroSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#travelWaveHeroSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        @endif
    </div>
    </div>
</section>

@include('partials.frontend.form-zone', ['assignments' => $managedForms['below_hero'] ?? [], 'position' => 'below_hero', 'sourcePage' => 'home'])
@include('partials.frontend.map-zone', ['assignments' => $managedMaps['below_hero'] ?? [], 'position' => 'below_hero'])

<section class="container pt-4 pb-2">
    <div class="tw-home-search-shell">
        <div class="tw-home-search-copy">
            <div class="tw-home-search-eyebrow">{{ $homeSearchText['eyebrow'] }}</div>
            <h2 class="tw-section-title h3 mb-2">{{ $homeSearchText['title'] }}</h2>
            <p class="text-muted mb-0">{{ $homeSearchText['subtitle'] }}</p>
        </div>
        <form class="tw-home-search-form js-home-service-search" data-config='@json($homeSearchConfig)' novalidate>
            <div class="tw-home-search-field">
                <label class="form-label" for="home-service-select">{{ $homeSearchText['service'] }}</label>
                <select id="home-service-select" class="form-select js-home-service-type">
                    <option value="">{{ $homeSearchText['service'] }}</option>
                </select>
            </div>
            <div class="tw-home-search-field js-home-search-region-wrap d-none">
                <label class="form-label" for="home-region-select">{{ $homeSearchText['region'] }}</label>
                <select id="home-region-select" class="form-select js-home-service-region" disabled>
                    <option value="">{{ $homeSearchText['region'] }}</option>
                </select>
            </div>
            <div class="tw-home-search-field js-home-search-country-wrap d-none">
                <label class="form-label" for="home-country-select">{{ $homeSearchText['country'] }}</label>
                <select id="home-country-select" class="form-select js-home-service-country" disabled>
                    <option value="">{{ $homeSearchText['country'] }}</option>
                </select>
            </div>
            <div class="tw-home-search-field js-home-search-destination-wrap d-none">
                <label class="form-label" for="home-destination-select">{{ $homeSearchText['destination'] }}</label>
                <select id="home-destination-select" class="form-select js-home-service-destination" disabled>
                    <option value="">{{ $homeSearchText['destination'] }}</option>
                </select>
            </div>
            <div class="tw-home-search-action">
                <button type="submit" class="btn btn-primary tw-btn-primary w-100 js-home-search-submit" disabled>{{ $homeSearchText['button'] }}</button>
                <div class="tw-home-search-hint js-home-search-hint">{{ $homeSearchText['invalid'] }}</div>
            </div>
        </form>
    </div>
</section>

@if($homeCountryStripItems->isNotEmpty())
<section class="container py-4 py-lg-5">
    <div class="tw-home-destinations-shell">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4 mb-lg-5">
            <div class="tw-home-destinations-heading">
                <div class="tw-home-destinations-eyebrow">{{ __('ui.visas') }}</div>
                <h2 class="tw-section-title h2 mb-2">{{ $siteSettings?->localized('home_country_strip_title') ?: __('ui.featured_destinations') }}</h2>
                @if($siteSettings?->localized('home_country_strip_subtitle'))
                    <p class="text-muted mb-0">{{ $siteSettings?->localized('home_country_strip_subtitle') }}</p>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($homeCountryStripItems->count() > 1)
                    <div class="tw-home-destination-controls" aria-label="Featured visa slider controls">
                        <button type="button" class="tw-home-destination-arrow js-home-destination-prev" aria-label="Previous visas">
                            <span class="tw-home-destination-arrow-icon tw-home-destination-arrow-icon-prev" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="tw-home-destination-arrow js-home-destination-next" aria-label="Next visas">
                            <span class="tw-home-destination-arrow-icon tw-home-destination-arrow-icon-next" aria-hidden="true"></span>
                        </button>
                    </div>
                @endif
                <a href="{{ route('visas.index') }}" class="btn btn-outline-primary">{{ __('ui.view_all') }}</a>
            </div>
        </div>
        @php($destinationsAutoplay = $siteSettings?->home_destinations_autoplay ?? ($siteSettings?->home_country_strip_autoplay ?? true))
        @php($destinationsLoop = $siteSettings?->home_destinations_loop ?? true)
        @php($destinationsPauseOnHover = $siteSettings?->home_destinations_pause_on_hover ?? true)
        @php($destinationsInterval = max(1000, (int) ($siteSettings?->home_destinations_interval ?: 3200)))
        @php($destinationsSpeed = max(100, (int) ($siteSettings?->home_destinations_speed ?: 500)))
        <div
            class="tw-home-destinations-carousel js-home-destination-carousel"
            data-autoplay="{{ ($destinationsAutoplay && $homeCountryStripItems->count() > 1) ? 'true' : 'false' }}"
            data-interval="{{ $destinationsInterval }}"
            data-speed="{{ $destinationsSpeed }}"
            data-pause-on-hover="{{ $destinationsPauseOnHover ? 'true' : 'false' }}"
            data-loop="{{ $destinationsLoop ? 'true' : 'false' }}"
        >
            <div class="tw-home-destinations-viewport">
                <div class="tw-home-destinations-track">
                    @foreach($homeCountryStripItems as $item)
                    @php($linkedCountry = $item->visaCountry)
                    @php($itemUrl = $item->resolvedUrl())
                    @php($itemName = $item->displayName())
                    @php($itemSubtitle = $item->displaySubtitle())
                    @php($itemImage = $item->displayImagePath())
                    @php($itemFlag = $item->displayFlagPath())
                    <a href="{{ $itemUrl }}" class="tw-home-destination-card{{ $loop->first ? ' is-active' : '' }}" data-card-index="{{ $loop->index }}">
                        <div class="tw-home-destination-connector" aria-hidden="true">
                            <span class="tw-home-destination-node"></span>
                        </div>
                        <div class="tw-home-destination-media">
                            @if($itemImage)
                                <img src="{{ asset('storage/' . $itemImage) }}" alt="{{ $itemName }}" class="tw-home-destination-image">
                            @else
                                <div class="tw-home-destination-placeholder">{{ strtoupper(substr($itemName, 0, 2)) }}</div>
                            @endif
                            <div class="tw-home-destination-overlay"></div>
                            <div class="tw-home-destination-flag-wrap">
                                <div class="tw-home-destination-flag">
                                    @if($itemFlag)
                                        <img src="{{ asset('storage/' . $itemFlag) }}" alt="{{ $itemName }} flag">
                                    @else
                                        <span>{{ strtoupper(substr($itemName, 0, 1)) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="tw-home-destination-line" aria-hidden="true"></div>
                        </div>
                        <div class="tw-home-destination-body">
                            <div class="tw-home-destination-copy">
                                <h3 class="h5 mb-1">{{ $itemName }}</h3>
                                @if($itemSubtitle)
                                    <p class="mb-0">{{ $itemSubtitle }}</p>
                                @elseif($linkedCountry?->localized('excerpt'))
                                    <p class="mb-0">{{ \Illuminate\Support\Str::limit($linkedCountry->localized('excerpt'), 72) }}</p>
                                @endif
                            </div>
                            <div class="tw-home-destination-cta">
                                <span>{{ __('ui.learn_more') }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@include('partials.frontend.map-zone', ['assignments' => $managedMaps['middle'] ?? [], 'position' => 'middle'])

<section class="container py-5">
    <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center tw-home-brand-lockup mb-4">
            @include('partials.frontend.logo', ['variant' => 'header', 'className' => 'tw-home-brand-logo'])
        </div>
        <h2 class="tw-section-title display-6">{{ $page->localized('intro_title') }}</h2>
        <p class="text-muted mx-auto" style="max-width:780px">{{ $page->localized('intro_body') }}</p>
    </div>
    <div class="row g-4">
        @foreach(($sections['services'] ?? []) as $service)
            <div class="col-md-6 col-xl-4">
                <div class="tw-card p-4 h-100">
                    <div class="tw-icon-badge mb-3">{{ $service['icon'] ?: 'TW' }}</div>
                    <h3 class="h5">{{ app()->getLocale() === 'ar' ? $service['title_ar'] : $service['title_en'] }}</h3>
                    <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? $service['text_ar'] : $service['text_en'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

@include('partials.frontend.map-zone', ['assignments' => $managedMaps['bottom'] ?? [], 'position' => 'bottom'])

<section class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="tw-section-title h1 mb-0">{{ __('ui.popular_destinations') }}</h2>
        <a href="{{ route('visas.index') }}" class="btn btn-outline-primary">{{ __('ui.view_all') }}</a>
    </div>
    <div class="row g-4">
        @foreach($featuredCountries as $country)
            <div class="col-md-6 col-xl-4">
                <div class="tw-card h-100 overflow-hidden">
                    @if($country->hero_image)<img src="{{ asset('storage/' . $country->hero_image) }}" class="img-fluid" alt="{{ $country->localized('name') }}">@endif
                    <div class="p-4">
                        <span class="badge text-bg-light mb-2">{{ $country->category?->localized('name') }}</span>
                        <h3 class="h5">{{ $country->localized('name') }}</h3>
                        <p class="text-muted">{{ $country->localized('excerpt') }}</p>
                        <a href="{{ route('visas.country', $country) }}" class="btn btn-primary tw-btn-primary">{{ __('ui.learn_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="tw-page-header p-4 p-lg-5 h-100">
                <h2 class="display-6">{{ __('ui.featured_categories') }}</h2>
                <p class="text-white-50 mb-0">{{ $siteSettings?->localized('site_tagline') }}</p>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="row g-3">
                @foreach($categories as $category)
                    <div class="col-md-6">
                        <div class="tw-card p-4 h-100">
                            <h3 class="h5">{{ $category->localized('name') }}</h3>
                            <p class="text-muted">{{ $category->localized('short_description') }}</p>
                            <a href="{{ route('visas.category', $category) }}" class="text-decoration-none fw-semibold">{{ __('ui.learn_more') }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="tw-card p-4 h-100">
                <h2 class="tw-section-title h2 mb-4">Why Travel Wave</h2>
                <div class="row g-3">
                    @foreach(($sections['why_choose_us'] ?? []) as $item)
                        <div class="col-12">
                            <div class="border rounded-4 p-3">
                                <h3 class="h5">{{ app()->getLocale() === 'ar' ? $item['title_ar'] : $item['title_en'] }}</h3>
                                <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="tw-card p-4 h-100">
                <h2 class="tw-section-title h2 mb-4">How It Works</h2>
                @foreach(($sections['how_it_works'] ?? []) as $item)
                    <div class="d-flex gap-3 mb-3">
                        <div class="tw-icon-badge">{{ $loop->iteration }}</div>
                        <div>
                            <h3 class="h5">{{ app()->getLocale() === 'ar' ? $item['title_ar'] : $item['title_en'] }}</h3>
                            <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="container py-4">
    <div class="row g-4">
        @foreach($featuredDestinations as $destination)
            <div class="col-md-6 col-xl-4">
                <div class="tw-card h-100 overflow-hidden">
                    @if($destination->hero_image)<img src="{{ asset('storage/' . $destination->hero_image) }}" class="img-fluid" alt="{{ $destination->localized('title') }}">@endif
                    <div class="p-4">
                        <h3 class="h5">{{ $destination->localized('title') }}</h3>
                        <p class="text-muted">{{ $destination->localized('excerpt') }}</p>
                        <a href="{{ route('destinations.show', $destination) }}" class="btn btn-outline-primary">{{ __('ui.learn_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

@if(!empty($sections['promo']))
<section class="container py-5">
    <div class="tw-page-header p-4 p-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="display-6">{{ app()->getLocale() === 'ar' ? $sections['promo']['title_ar'] : $sections['promo']['title_en'] }}</h2>
                <p class="text-white-50 mb-0">{{ app()->getLocale() === 'ar' ? $sections['promo']['text_ar'] : $sections['promo']['text_en'] }}</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ $sections['promo']['url'] }}" class="btn btn-primary tw-btn-primary">{{ app()->getLocale() === 'ar' ? $sections['promo']['button_ar'] : $sections['promo']['button_en'] }}</a>
            </div>
        </div>
    </div>
</section>
@endif

@include('partials.frontend.form-zone', ['assignments' => $managedForms['middle'] ?? [], 'position' => 'middle', 'sourcePage' => 'home'])

<section class="container py-5">
    <div class="row g-4">
        @foreach($testimonials as $testimonial)
            <div class="col-md-6 col-xl-4">
                <div class="tw-card p-4 h-100">
                    <div class="text-warning mb-3">{{ str_repeat('★', $testimonial->rating) }}</div>
                    <p class="mb-4">{{ $testimonial->localized('testimonial') }}</p>
                    <div class="fw-semibold">{{ $testimonial->client_name }}</div>
                    <div class="text-muted small">{{ $testimonial->localized('client_role') }}</div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="tw-section-title h1 mb-0">{{ __('ui.featured_articles') }}</h2>
        <a href="{{ route('blog.index') }}" class="btn btn-outline-primary">{{ __('ui.view_all') }}</a>
    </div>
    <div class="row g-4">
        @foreach($posts as $post)
            <div class="col-md-6 col-xl-4">
                <div class="tw-card h-100 overflow-hidden">
                    @if($post->featured_image)<img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid" alt="{{ $post->localized('title') }}">@endif
                    <div class="p-4">
                        <h3 class="h5">{{ $post->localized('title') }}</h3>
                        <p class="text-muted">{{ $post->localized('excerpt') }}</p>
                        <a href="{{ route('blog.show', $post) }}" class="text-decoration-none fw-semibold">{{ __('ui.read_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="container py-5">
    <div class="row g-4 align-items-center">
        <div class="col-lg-5">
            <h2 class="tw-section-title display-6">{{ app()->getLocale() === 'ar' ? ($sections['inquiry']['title_ar'] ?? '') : ($sections['inquiry']['title_en'] ?? '') }}</h2>
            <p class="text-muted">{{ app()->getLocale() === 'ar' ? ($sections['inquiry']['text_ar'] ?? '') : ($sections['inquiry']['text_en'] ?? '') }}</p>
        </div>
        <div class="col-lg-7">
            @include('partials.frontend.inquiry-form', ['type' => 'general', 'source' => 'home'])
        </div>
    </div>
</section>

@if(!empty($sections['final_cta']))
<section class="container py-5">
    <div class="tw-page-header p-4 p-lg-5 text-center">
        <h2 class="display-6">{{ app()->getLocale() === 'ar' ? $sections['final_cta']['title_ar'] : $sections['final_cta']['title_en'] }}</h2>
        <p class="text-white-50">{{ app()->getLocale() === 'ar' ? $sections['final_cta']['text_ar'] : $sections['final_cta']['text_en'] }}</p>
        <a href="{{ $sections['final_cta']['url'] }}" class="btn btn-primary btn-lg tw-btn-primary">{{ app()->getLocale() === 'ar' ? $sections['final_cta']['button_ar'] : $sections['final_cta']['button_en'] }}</a>
    </div>
</section>
@endif

@include('partials.frontend.form-zone', ['assignments' => $managedForms['bottom'] ?? [], 'position' => 'bottom', 'sourcePage' => 'home'])

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-home-service-search').forEach((form) => {
        const config = JSON.parse(form.dataset.config || '{}');
        const serviceSelect = form.querySelector('.js-home-service-type');
        const regionWrap = form.querySelector('.js-home-search-region-wrap');
        const regionSelect = form.querySelector('.js-home-service-region');
        const countryWrap = form.querySelector('.js-home-search-country-wrap');
        const countrySelect = form.querySelector('.js-home-service-country');
        const destinationWrap = form.querySelector('.js-home-search-destination-wrap');
        const destinationSelect = form.querySelector('.js-home-service-destination');
        const submitButton = form.querySelector('.js-home-search-submit');
        const hint = form.querySelector('.js-home-search-hint');
        const isArabic = document.documentElement.lang === 'ar';

        if (!serviceSelect || !submitButton) {
            return;
        }

        const labelFor = (item) => isArabic ? item.label_ar : item.label_en;
        const placeholderFor = (select) => select.options[0]?.textContent ?? '';

        const resetSelect = (select) => {
            select.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = placeholderFor(select);
            select.appendChild(placeholder);
            select.value = '';
            select.disabled = true;
        };

        const populateSelect = (select, items) => {
            const placeholderText = placeholderFor(select);
            select.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = placeholderText;
            select.appendChild(placeholder);

            items.forEach((item) => {
                const option = document.createElement('option');
                option.value = item.key || item.slug || item.url;
                option.textContent = labelFor(item);
                option.dataset.url = item.url || '';
                select.appendChild(option);
            });

            select.disabled = items.length === 0;
            select.value = '';
        };

        const hideElement = (element) => element.classList.add('d-none');
        const showElement = (element) => element.classList.remove('d-none');

        let targetUrl = '';

        const syncSubmitState = () => {
            submitButton.disabled = !targetUrl;
            hint.classList.toggle('is-active', !targetUrl);
        };

        populateSelect(serviceSelect, config.services || []);
        syncSubmitState();

        serviceSelect.addEventListener('change', () => {
            targetUrl = '';
            hideElement(regionWrap);
            hideElement(countryWrap);
            hideElement(destinationWrap);
            resetSelect(regionSelect);
            resetSelect(countrySelect);
            resetSelect(destinationSelect);

            const service = serviceSelect.value;

            if (service === 'visas') {
                showElement(regionWrap);
                populateSelect(regionSelect, config.visa_regions || []);
            } else if (service === 'domestic') {
                showElement(destinationWrap);
                populateSelect(destinationSelect, config.domestic_destinations || []);
            } else if (service && config.direct_routes?.[service]) {
                targetUrl = config.direct_routes[service];
            }

            syncSubmitState();
        });

        regionSelect.addEventListener('change', () => {
            targetUrl = '';
            hideElement(countryWrap);
            resetSelect(countrySelect);

            const region = (config.visa_regions || []).find((item) => item.key === regionSelect.value);
            if (region) {
                showElement(countryWrap);
                populateSelect(countrySelect, region.countries || []);
            }

            syncSubmitState();
        });

        countrySelect.addEventListener('change', () => {
            targetUrl = countrySelect.selectedOptions[0]?.dataset.url || '';
            syncSubmitState();
        });

        destinationSelect.addEventListener('change', () => {
            targetUrl = destinationSelect.selectedOptions[0]?.dataset.url || '';
            syncSubmitState();
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!targetUrl) {
                syncSubmitState();
                return;
            }

            window.location.href = targetUrl;
        });
    });

    document.querySelectorAll('.js-home-destination-carousel').forEach((carousel) => {
        const viewport = carousel.querySelector('.tw-home-destinations-viewport');
        const shell = carousel.closest('.tw-home-destinations-shell');
        const prevButton = shell?.querySelector('.js-home-destination-prev');
        const nextButton = shell?.querySelector('.js-home-destination-next');
        const track = carousel.querySelector('.tw-home-destinations-track');
        const cards = track ? Array.from(track.children) : [];
        const autoplayEnabled = carousel.dataset.autoplay === 'true';
        const autoplayInterval = Math.max(1000, parseInt(carousel.dataset.interval || '3200', 10));
        const transitionSpeed = Math.max(100, parseInt(carousel.dataset.speed || '500', 10));
        const pauseOnHover = carousel.dataset.pauseOnHover !== 'false';
        const loopEnabled = carousel.dataset.loop !== 'false';
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!viewport || !track || cards.length === 0) {
            return;
        }

        if (cards.length === 1) {
            prevButton?.setAttribute('disabled', 'disabled');
            nextButton?.setAttribute('disabled', 'disabled');
            return;
        }

        let currentIndex = 0;
        let autoplayTimer = null;
        let touchStartX = null;
        let scrollTimeout = null;

        const syncActiveState = () => {
            cards.forEach((card, index) => {
                const isActive = index === currentIndex;
                card.classList.toggle('is-active', isActive);
                card.setAttribute('aria-current', isActive ? 'true' : 'false');
            });
        };

        const scrollToIndex = (index, behavior = 'smooth') => {
            const card = cards[index];
            if (!card) {
                return;
            }

            currentIndex = index;
            syncActiveState();

            const targetLeft = card.offsetLeft - Math.max(0, (viewport.clientWidth - card.offsetWidth) / 2);
            viewport.scrollTo({
                left: Math.max(0, targetLeft),
                behavior: reducedMotion ? 'auto' : behavior,
            });
        };

        const goTo = (direction) => {
            let nextIndex = currentIndex + direction;

            if (nextIndex < 0) {
                nextIndex = loopEnabled ? cards.length - 1 : 0;
            } else if (nextIndex >= cards.length) {
                nextIndex = loopEnabled ? 0 : cards.length - 1;
            }

            if (nextIndex === currentIndex) {
                syncActiveState();
                return;
            }

            scrollToIndex(nextIndex, 'smooth');
        };

        const stopAutoplay = () => {
            window.clearInterval(autoplayTimer);
            autoplayTimer = null;
        };

        const restartAutoplay = () => {
            stopAutoplay();

            if (!autoplayEnabled || reducedMotion || document.hidden) {
                return;
            }

            autoplayTimer = window.setInterval(() => goTo(1), autoplayInterval);
        };

        const detectNearestIndex = () => {
            const viewportCenter = viewport.scrollLeft + (viewport.clientWidth / 2);
            let nearestIndex = currentIndex;
            let nearestDistance = Number.POSITIVE_INFINITY;

            cards.forEach((card, index) => {
                const cardCenter = card.offsetLeft + (card.offsetWidth / 2);
                const distance = Math.abs(cardCenter - viewportCenter);

                if (distance < nearestDistance) {
                    nearestDistance = distance;
                    nearestIndex = index;
                }
            });

            currentIndex = nearestIndex;
            syncActiveState();
        };

        viewport.addEventListener('touchstart', (event) => {
            touchStartX = event.changedTouches[0]?.clientX ?? null;
        }, { passive: true });

        viewport.addEventListener('touchend', (event) => {
            if (touchStartX === null) {
                return;
            }

            const deltaX = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
            touchStartX = null;

            if (Math.abs(deltaX) < 36) {
                return;
            }

            goTo(deltaX < 0 ? 1 : -1);
            restartAutoplay();
        }, { passive: true });

        viewport.addEventListener('scroll', () => {
            window.clearTimeout(scrollTimeout);
            scrollTimeout = window.setTimeout(detectNearestIndex, 100);
        }, { passive: true });

        prevButton?.addEventListener('click', () => {
            goTo(-1);
            restartAutoplay();
        });

        nextButton?.addEventListener('click', () => {
            goTo(1);
            restartAutoplay();
        });

        if (pauseOnHover) {
            carousel.addEventListener('mouseenter', stopAutoplay);
            carousel.addEventListener('mouseleave', restartAutoplay);
            carousel.addEventListener('focusin', stopAutoplay);
            carousel.addEventListener('focusout', restartAutoplay);
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoplay();
            } else {
                restartAutoplay();
            }
        });

        window.addEventListener('resize', () => {
            window.clearTimeout(carousel.__resizeTimer);
            carousel.__resizeTimer = window.setTimeout(() => {
                scrollToIndex(currentIndex, 'auto');
            }, 120);
        });

        track.style.setProperty('--tw-home-destination-speed', `${transitionSpeed}ms`);
        syncActiveState();
        restartAutoplay();
    });
});
</script>
@endsection
