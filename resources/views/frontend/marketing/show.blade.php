@extends('layouts.app')

@section('title', $landingPage->localized('seo_title') ?: ($landingPage->localized('title') ?: $landingPage->internal_name))
@section('meta_description', $landingPage->localized('seo_description') ?: ($landingPage->localizedSectionText('hero.subtitle') ?: $landingPage->campaign_name))

@php
    $locale = app()->getLocale();
    $hero = $landingPage->sections['hero'] ?? [];
    $benefits = $landingPage->sections['benefits'] ?? [];
    $quickInfo = $landingPage->sections['quick_info'] ?? [];
    $testimonials = $landingPage->sections['testimonials'] ?? [];
    $faq = $landingPage->sections['faq'] ?? [];
    $cta = $landingPage->sections['cta'] ?? [];
    $formSection = $landingPage->sections['form'] ?? [];
    $heroButtons = collect([
        [
            'text' => $hero['primary_button_text_' . $locale] ?? $hero['primary_button_text_en'] ?? null,
            'url' => $hero['primary_button_url'] ?? '#marketing-form',
            'event' => 'cta_click',
            'variant' => 'primary',
        ],
        [
            'text' => $hero['secondary_button_text_' . $locale] ?? $hero['secondary_button_text_en'] ?? null,
            'url' => $hero['secondary_button_url'] ?? '#marketing-benefits',
            'event' => str_contains((string) ($hero['secondary_button_url'] ?? ''), 'wa.me') ? 'whatsapp_click' : 'cta_click',
            'variant' => 'outline',
        ],
    ])->filter(fn ($button) => filled($button['text']) && filled($button['url']));

    $ctaButtons = collect([
        [
            'text' => $cta['primary_button_text_' . $locale] ?? $cta['primary_button_text_en'] ?? null,
            'url' => $cta['primary_button_url'] ?? '#marketing-form',
            'event' => 'cta_click',
            'variant' => 'primary',
        ],
        [
            'text' => $cta['secondary_button_text_' . $locale] ?? $cta['secondary_button_text_en'] ?? null,
            'url' => $cta['secondary_button_url'] ?? '#marketing-form',
            'event' => str_contains((string) ($cta['secondary_button_url'] ?? ''), 'wa.me') ? 'whatsapp_click' : 'cta_click',
            'variant' => 'outline',
        ],
    ])->filter(fn ($button) => filled($button['text']) && filled($button['url']));
@endphp

@section('content')
<div class="tw-marketing-page" data-marketing-landing-page-id="{{ $landingPage->id }}">
    <section class="tw-brand-page-hero tw-marketing-hero" style="{{ !empty($hero['background_image']) ? "background-image:linear-gradient(135deg, rgba(7, 27, 46, 0.9), rgba(17, 55, 93, 0.76)), url('" . asset('storage/' . $hero['background_image']) . "');" : '' }}">
        <div class="container py-5">
            <div class="tw-brand-page-hero-shell tw-marketing-hero-shell">
                @if(!empty($hero['eyebrow_' . $locale]) || !empty($hero['eyebrow_en']))
                    <span class="tw-brand-page-eyebrow">{{ $hero['eyebrow_' . $locale] ?? $hero['eyebrow_en'] }}</span>
                @endif
                <h1 class="display-4 mb-3">{{ $hero['title_' . $locale] ?? $hero['title_en'] ?? $landingPage->localized('title') }}</h1>
                @if(!empty($hero['subtitle_' . $locale]) || !empty($hero['subtitle_en']))
                    <p class="lead mb-4">{{ $hero['subtitle_' . $locale] ?? $hero['subtitle_en'] }}</p>
                @endif
                @if($heroButtons->isNotEmpty())
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($heroButtons as $button)
                            <a href="{{ $button['url'] }}" class="btn btn-lg {{ $button['variant'] === 'outline' ? 'btn-outline-light' : 'btn-primary tw-btn-primary' }}" data-marketing-event="{{ $button['event'] }}" data-marketing-label="{{ $button['text'] }}">
                                {{ $button['text'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if(($benefits['enabled'] ?? true) && !empty($benefits['items']))
        <section class="container py-4 py-lg-5" id="marketing-benefits">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div>
                    <h2 class="tw-section-title h2 mb-2">{{ $benefits['title_' . $locale] ?? $benefits['title_en'] ?? __('Benefits') }}</h2>
                    @if(!empty($benefits['subtitle_' . $locale]) || !empty($benefits['subtitle_en']))
                        <p class="text-muted mb-0">{{ $benefits['subtitle_' . $locale] ?? $benefits['subtitle_en'] }}</p>
                    @endif
                </div>
            </div>
            <div class="row g-4">
                @foreach($benefits['items'] as $item)
                    @continue(empty($item['is_active']))
                    <div class="col-md-6 col-xl-4">
                        <div class="tw-card tw-brand-card p-4 h-100">
                            <div class="tw-brand-card-icon">{{ mb_substr($item['title_' . $locale] ?? $item['title_en'] ?? 'TW', 0, 2) }}</div>
                            <h3 class="h5 mt-4 mb-2">{{ $item['title_' . $locale] ?? $item['title_en'] }}</h3>
                            @if(!empty($item['meta_' . $locale]) || !empty($item['meta_en']))
                                <div class="small text-muted mb-2">{{ $item['meta_' . $locale] ?? $item['meta_en'] }}</div>
                            @endif
                            <p class="text-muted mb-0">{{ $item['text_' . $locale] ?? $item['text_en'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if(($quickInfo['enabled'] ?? true) && !empty($quickInfo['items']))
        <section class="container py-4 py-lg-5">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div>
                    <h2 class="tw-section-title h2 mb-2">{{ $quickInfo['title_' . $locale] ?? $quickInfo['title_en'] ?? 'Quick Info' }}</h2>
                    @if(!empty($quickInfo['subtitle_' . $locale]) || !empty($quickInfo['subtitle_en']))
                        <p class="text-muted mb-0">{{ $quickInfo['subtitle_' . $locale] ?? $quickInfo['subtitle_en'] }}</p>
                    @endif
                </div>
            </div>
            <div class="row g-4">
                @foreach($quickInfo['items'] as $item)
                    @continue(empty($item['is_active']))
                    <div class="col-md-4">
                        <div class="tw-card tw-brand-card p-4 h-100">
                            <div class="small text-muted mb-2">{{ $item['label_' . $locale] ?? $item['label_en'] }}</div>
                            <div class="h4 mb-0">{{ $item['value_' . $locale] ?? $item['value_en'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if(($testimonials['enabled'] ?? true) && !empty($testimonials['items']))
        <section class="container py-4 py-lg-5">
            <div class="tw-card p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                    <div>
                        <h2 class="tw-section-title h2 mb-2">{{ $testimonials['title_' . $locale] ?? $testimonials['title_en'] ?? __('Testimonials') }}</h2>
                        @if(!empty($testimonials['subtitle_' . $locale]) || !empty($testimonials['subtitle_en']))
                            <p class="text-muted mb-0">{{ $testimonials['subtitle_' . $locale] ?? $testimonials['subtitle_en'] }}</p>
                        @endif
                    </div>
                </div>
                <div class="row g-4">
                    @foreach($testimonials['items'] as $item)
                        @continue(empty($item['is_active']))
                        <div class="col-md-6">
                            <div class="tw-marketing-testimonial h-100">
                                <p class="mb-3">{{ $item['quote_' . $locale] ?? $item['quote_en'] }}</p>
                                <div class="fw-semibold">{{ $item['author_' . $locale] ?? $item['author_en'] }}</div>
                                @if(!empty($item['role_' . $locale]) || !empty($item['role_en']))
                                    <div class="small text-muted">{{ $item['role_' . $locale] ?? $item['role_en'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(($formSection['enabled'] ?? true) && $landingPage->leadForm)
        <section class="container py-4 py-lg-5" id="marketing-form">
            @include('partials.frontend.marketing-form', ['form' => $landingPage->leadForm, 'landingPage' => $landingPage, 'formSection' => $formSection])
        </section>
    @endif

    @if(($faq['enabled'] ?? true) && !empty($faq['items']))
        <section class="container py-4 py-lg-5">
            <div class="tw-card p-4 p-lg-5">
                <h2 class="tw-section-title h2 mb-4">{{ $faq['title_' . $locale] ?? $faq['title_en'] ?? __('FAQ') }}</h2>
                <div class="accordion tw-visa-faq" id="marketingFaqs">
                    @foreach($faq['items'] as $item)
                        @continue(empty($item['is_active']))
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#marketing-faq-{{ $loop->iteration }}">
                                    {{ $item['question_' . $locale] ?? $item['question_en'] }}
                                </button>
                            </h3>
                            <div id="marketing-faq-{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#marketingFaqs">
                                <div class="accordion-body">{{ $item['answer_' . $locale] ?? $item['answer_en'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(($cta['enabled'] ?? true) && $ctaButtons->isNotEmpty())
        <section class="container py-4 py-lg-5">
            <div class="tw-brand-page-cta" style="{{ !empty($cta['background_image']) ? "background-image:linear-gradient(135deg, rgba(7, 27, 46, 0.92), rgba(17, 55, 93, 0.84)), url('" . asset('storage/' . $cta['background_image']) . "');" : '' }}">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <h2 class="display-6 mb-3">{{ $cta['title_' . $locale] ?? $cta['title_en'] }}</h2>
                        @if(!empty($cta['description_' . $locale]) || !empty($cta['description_en']))
                            <p class="lead mb-0">{{ $cta['description_' . $locale] ?? $cta['description_en'] }}</p>
                        @endif
                    </div>
                    <div class="col-lg-5">
                        <div class="d-flex flex-wrap justify-content-lg-end gap-3">
                            @foreach($ctaButtons as $button)
                                <a href="{{ $button['url'] }}" class="btn btn-lg px-4 {{ $button['variant'] === 'outline' ? 'btn-outline-light' : 'btn-primary tw-btn-primary' }}" data-marketing-event="{{ $button['event'] }}" data-marketing-label="{{ $button['text'] }}">
                                    {{ $button['text'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.querySelector('[data-marketing-landing-page-id]');
    if (!root) return;

    const endpoint = @json(route('marketing.landing-pages.events.store', $landingPage));
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const sendEvent = function (type, payload) {
        if (!endpoint || !token) return;
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ event_type: type, payload: payload || {} }),
        }).catch(() => {});
    };

    document.querySelectorAll('[data-marketing-event]').forEach((element) => {
        element.addEventListener('click', function () {
            sendEvent(element.dataset.marketingEvent, {
                label: element.dataset.marketingLabel || element.textContent.trim(),
                href: element.getAttribute('href'),
            });
        });
    });

    const globalWhatsapp = document.querySelector('.tw-floating-whatsapp');
    if (globalWhatsapp) {
        globalWhatsapp.addEventListener('click', function () {
            sendEvent('whatsapp_click', {
                label: 'floating_whatsapp',
                href: globalWhatsapp.getAttribute('href'),
            });
        });
    }
});
</script>
@endsection
