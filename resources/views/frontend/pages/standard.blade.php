@extends('layouts.app')

@section('title', $page->localized('meta_title') ?: $page->localized('title'))

@section('content')
@php($sections = $page->sections ?? [])
@php($orderedSections = $page->getOrderedSections())

@foreach($orderedSections as $sKey => $sMeta)
    @continue(empty($sMeta['enabled']))

    @switch($sKey)
        @case('hero')
            <section class="container py-5">
                <div class="tw-page-header tw-section-shell p-4 p-lg-5">
                    @if($page->localized('hero_badge'))
                        <span class="badge bg-light text-dark mb-3">{{ $page->localized('hero_badge') }}</span>
                    @endif
                    <h1 class="display-5">{{ $page->localized('hero_title') ?: $page->localized('title') }}</h1>
                    @if($page->localized('hero_subtitle'))
                        <p class="lead text-white-50 mb-0">{{ $page->localized('hero_subtitle') }}</p>
                    @endif
                </div>
            </section>
            @break

        @case('intro')
            @if($page->localized('intro_title') || $page->localized('intro_body') || $page->hero_image)
            <section class="container py-4">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="tw-card tw-section-shell p-4 h-100">
                            @if($page->localized('intro_title'))
                                <h2 class="tw-section-title h2">{{ $page->localized('intro_title') }}</h2>
                            @endif
                            <div class="text-muted">{!! nl2br(e($page->localized('intro_body'))) !!}</div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="tw-card tw-media-card overflow-hidden h-100">
                            @if($page->hero_image)
                                <img src="{{ asset('storage/' . $page->hero_image) }}" class="tw-image-cover" alt="{{ $page->localized('title') }}">
                            @else
                                <div class="p-5 text-center text-muted">{{ $page->localized('title') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            @endif
            @break

        @case('feature_blocks')
            @if(!empty($sections['feature_blocks']))
            <section class="container py-5">
                <div class="row g-4">
                    @foreach($sections['feature_blocks'] as $item)
                        <div class="col-md-6 col-xl-4">
                            <div class="tw-card tw-section-shell p-4 h-100">
                                <h3 class="h5">{{ app()->getLocale() === 'ar' ? $item['title_ar'] : $item['title_en'] }}</h3>
                                <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif
            @break

        @case('faqs')
            @if(!empty($sections['faqs']))
            <section class="container py-5">
                <div class="tw-card tw-section-shell p-4">
                    <h2 class="tw-section-title h2 mb-4">{{ __('ui.faq') }}</h2>
                    <div class="accordion" id="faqAccordion">
                        @foreach($sections['faqs'] as $item)
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $loop->iteration }}">
                                        {{ app()->getLocale() === 'ar' ? $item['question_ar'] : $item['question_en'] }}
                                    </button>
                                </h3>
                                <div id="faq-{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">{{ app()->getLocale() === 'ar' ? $item['answer_ar'] : $item['answer_en'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif
            @break

        @case('cta')
            @if(!empty($sections['cta']) && (!empty($sections['cta']['title_ar']) || !empty($sections['cta']['title_en'])))
            <section class="container py-5">
                <div class="tw-page-header p-4 p-lg-5 text-center">
                    <h2 class="display-6">{{ app()->getLocale() === 'ar' ? ($sections['cta']['title_ar'] ?? '') : ($sections['cta']['title_en'] ?? '') }}</h2>
                    <p class="text-white-50">{{ app()->getLocale() === 'ar' ? ($sections['cta']['text_ar'] ?? '') : ($sections['cta']['text_en'] ?? '') }}</p>
                    @if(!empty($sections['cta']['url']))
                        <a href="{{ $sections['cta']['url'] }}" class="btn btn-primary btn-lg tw-btn-primary">{{ app()->getLocale() === 'ar' ? ($sections['cta']['button_ar'] ?? __('ui.learn_more')) : ($sections['cta']['button_en'] ?? __('ui.learn_more')) }}</a>
                    @endif
                </div>
            </section>
            @endif
            @break
    @endswitch
@endforeach

@endsection
