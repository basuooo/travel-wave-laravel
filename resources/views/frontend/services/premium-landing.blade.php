@extends('layouts.app')

@section('title', $servicePage['page_title'] ?? $page->localized('title'))

@php
    // This shared page is layout-only. All section content should come from
    // the structured service page array prepared in FrontendController.
    $hero = $servicePage['hero'] ?? [];
    $search = $servicePage['search_box'] ?? ($servicePage['search'] ?? []);
    $featured = $servicePage['featured_section'] ?? ($servicePage['popular'] ?? []);
    $features = $servicePage['features_section'] ?? ['title' => $servicePage['features_title'] ?? null, 'items' => $servicePage['features'] ?? []];
    $cards = $servicePage['cards_section'] ?? ($servicePage['packages'] ?? []);
    $steps = $servicePage['steps_section'] ?? ['title' => $servicePage['steps_title'] ?? null, 'items' => $servicePage['steps'] ?? []];
    $grid = $servicePage['grid_section'] ?? ($servicePage['grid'] ?? []);
    $quickInfo = $servicePage['quick_info_section'] ?? ['items' => $servicePage['quick_info'] ?? []];
    $cta = $servicePage['cta_section'] ?? (!empty($servicePage['cta']) ? [
        'enabled' => true,
        'eyebrow' => data_get($servicePage, 'cta.eyebrow'),
        'title' => data_get($servicePage, 'cta.title'),
        'description' => data_get($servicePage, 'cta.text'),
        'buttons' => array_values(array_filter([
            data_get($servicePage, 'cta.primary') ? data_get($servicePage, 'cta.primary') + ['variant' => 'primary'] : null,
            data_get($servicePage, 'cta.secondary') ? data_get($servicePage, 'cta.secondary') + ['variant' => 'light-outline'] : null,
        ])),
    ] : []);
    $faq = $servicePage['faq_section'] ?? ['items' => $servicePage['faqs'] ?? []];
    $form = $servicePage['form_section'] ?? (!empty($servicePage['contact']) ? [
        'enabled' => true,
        'eyebrow' => data_get($servicePage, 'contact.eyebrow'),
        'title' => data_get($servicePage, 'contact.title'),
        'subtitle' => data_get($servicePage, 'contact.text'),
        'checklist' => data_get($servicePage, 'contact.checklist', []),
        'type' => data_get($servicePage, 'contact.type', 'general'),
        'source' => data_get($servicePage, 'contact.source', $page->localized('title')),
        'submit_text' => data_get($servicePage, 'contact.submit_text', __('ui.send_request')),
        'fields' => data_get($servicePage, 'contact.fields', []),
    ] : []);
@endphp

@section('content')
@php($orderedSections = $page->getOrderedSections())
<div class="tw-visa-hub-page" dir="{{ $servicePage['direction'] ?? 'rtl' }}">
    @include('partials.frontend.form-zone', ['assignments' => $managedForms['top'] ?? [], 'position' => 'top', 'sourcePage' => $page->key])
    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['top'] ?? [], 'position' => 'top'])

    @foreach($orderedSections as $sKey => $sMeta)
        @continue(empty($sMeta['enabled']))

        @switch($sKey)
            @case('hero')
                @if(!empty($hero))
                    @include('frontend.services.sections.hero', ['hero' => $hero])
                    @include('partials.frontend.form-zone', ['assignments' => $managedForms['below_hero'] ?? [], 'position' => 'below_hero', 'sourcePage' => $page->key])
                    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['below_hero'] ?? [], 'position' => 'below_hero'])
                @endif
                @break

            @case('search')
                @if(!empty($search['fields']))
                    @include('frontend.services.sections.search', ['search' => $search])
                @endif
                @break

            @case('featured')
                @if(!empty($featured['items']))
                    @include('frontend.services.sections.featured', ['popular' => $featured])
                @endif
                @break

            @case('features')
                @if(!empty($features['items']))
                    @include('frontend.services.sections.features', ['section' => $features, 'servicePage' => $servicePage])
                @endif
                @break

            @case('cards')
                @if(!empty($cards['items']))
                    @include('frontend.services.sections.cards', ['section' => $cards])
                @endif
                @break

            @case('steps')
                @if(!empty($steps['items']))
                    @include('frontend.services.sections.steps', ['section' => $steps, 'servicePage' => $servicePage])
                @endif
                @break

            @case('grid')
                @if(!empty($grid['items']))
                    @include('frontend.services.sections.grid', ['section' => $grid, 'servicePage' => $servicePage])
                @endif
                @break

            @case('quick_info')
                @if(!empty($quickInfo['items']))
                    @include('frontend.services.sections.info', ['section' => $quickInfo, 'servicePage' => $servicePage])
                @endif
                @break

            @case('cta')
                @if(!empty($cta))
                    @include('frontend.services.sections.cta', ['section' => $cta])
                @endif
                @break

            @case('faq')
                @if(!empty($faq['items']))
                    @include('partials.frontend.form-zone', ['assignments' => $managedForms['before_faq'] ?? [], 'position' => 'before_faq', 'sourcePage' => $page->key])
                    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['before_faq'] ?? [], 'position' => 'before_faq'])

                    @include('frontend.services.sections.faq', ['section' => $faq, 'servicePage' => $servicePage])

                    @include('partials.frontend.form-zone', ['assignments' => $managedForms['after_faq'] ?? [], 'position' => 'after_faq', 'sourcePage' => $page->key])
                    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['after_faq'] ?? [], 'position' => 'after_faq'])
                @endif
                @break
        @endswitch
    @endforeach

    @include('partials.frontend.form-zone', ['assignments' => $managedForms['middle'] ?? [], 'position' => 'middle', 'sourcePage' => $page->key])
    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['middle'] ?? [], 'position' => 'middle'])

    @include('partials.frontend.form-zone', ['assignments' => $managedForms['bottom'] ?? [], 'position' => 'bottom', 'sourcePage' => $page->key])
    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['bottom'] ?? [], 'position' => 'bottom'])
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-premium-service-filter').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const targetUrl = Array.from(form.querySelectorAll('.js-premium-service-select'))
                .map((select) => select.value)
                .find(Boolean) || form.dataset.defaultUrl;

            window.location.href = targetUrl;
        });
    });

    document.querySelectorAll('.js-service-slider').forEach((slider) => {
        const viewport = slider.querySelector('.tw-visa-hub-slider-viewport');
        const cards = Array.from(slider.querySelectorAll('.tw-visa-hub-destination-card'));
        const shell = slider.closest('.container');
        const prevButton = shell?.querySelector('.js-service-slider-prev');
        const nextButton = shell?.querySelector('.js-service-slider-next');
        const autoplayEnabled = slider.dataset.autoplay !== 'false';
        const interval = Math.max(1500, parseInt(slider.dataset.interval || '3600', 10));

        if (!viewport || cards.length <= 1) {
            return;
        }

        let currentIndex = 0;
        let timer = null;
        let touchStartX = null;

        const syncActive = () => {
            cards.forEach((card, index) => card.classList.toggle('is-active', index === currentIndex));
        };

        const goTo = (index, behavior = 'smooth') => {
            const card = cards[index];
            if (!card) {
                return;
            }

            currentIndex = index;
            syncActive();
            viewport.scrollTo({ left: Math.max(0, card.offsetLeft - 8), behavior });
        };

        const step = (direction) => {
            currentIndex = direction > 0
                ? ((currentIndex + 1) % cards.length)
                : ((currentIndex - 1 + cards.length) % cards.length);

            goTo(currentIndex);
        };

        const stop = () => {
            window.clearInterval(timer);
            timer = null;
        };

        const start = () => {
            stop();
            if (!autoplayEnabled || document.hidden) {
                return;
            }

            timer = window.setInterval(() => step(1), interval);
        };

        prevButton?.addEventListener('click', () => {
            step(-1);
            start();
        });

        nextButton?.addEventListener('click', () => {
            step(1);
            start();
        });

        viewport.addEventListener('touchstart', (event) => {
            touchStartX = event.changedTouches[0]?.clientX ?? null;
        }, { passive: true });

        viewport.addEventListener('touchend', (event) => {
            if (touchStartX === null) {
                return;
            }

            const deltaX = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
            touchStartX = null;

            if (Math.abs(deltaX) < 42) {
                return;
            }

            step(deltaX < 0 ? 1 : -1);
            start();
        }, { passive: true });

        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);
        document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());

        syncActive();
        start();
    });
});
</script>
@endsection
