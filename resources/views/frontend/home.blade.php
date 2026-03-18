@extends('layouts.app')

@section('title', $page->localized('meta_title') ?: $page->localized('title'))

@section('content')
@php($sections = $page->sections ?? [])
<section class="tw-hero py-5">
    <div class="container position-relative" style="z-index:1">
        <div class="row align-items-center g-5 py-5">
            <div class="col-lg-7">
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill mb-3">{{ $page->localized('hero_badge') }}</span>
                <h1 class="display-4 fw-bold mb-3">{{ $page->localized('hero_title') }}</h1>
                <p class="lead text-white-50 mb-4">{{ $page->localized('hero_subtitle') }}</p>
                <div class="d-flex flex-wrap gap-3">
                    @if($page->hero_primary_cta_url)<a href="{{ $page->hero_primary_cta_url }}" class="btn btn-primary btn-lg tw-btn-primary">{{ $page->localized('hero_primary_cta_text') }}</a>@endif
                    @if($page->hero_secondary_cta_url)<a href="{{ $page->hero_secondary_cta_url }}" class="btn btn-lg tw-btn-outline">{{ $page->localized('hero_secondary_cta_text') }}</a>@endif
                </div>
            </div>
            <div class="col-lg-5">
                <div class="tw-panel p-3">
                    @if($page->hero_image)
                        <img src="{{ asset('storage/' . $page->hero_image) }}" class="img-fluid rounded-4" alt="{{ $page->localized('hero_title') }}">
                    @else
                        <div class="p-5 text-center text-muted">Travel Wave</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="text-center mb-5">
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
@endsection
