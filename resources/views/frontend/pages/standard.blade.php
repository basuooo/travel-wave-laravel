@extends('layouts.app')

@section('title', $page->localized('meta_title') ?: $page->localized('title'))

@section('content')
@php($sections = $page->sections ?? [])
<section class="container py-5">
    <div class="tw-page-header tw-section-shell p-4 p-lg-5">
        <span class="badge bg-light text-dark mb-3">{{ $page->localized('hero_badge') }}</span>
        <h1 class="display-5">{{ $page->localized('hero_title') }}</h1>
        <p class="lead text-white-50 mb-0">{{ $page->localized('hero_subtitle') }}</p>
    </div>
</section>

<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="tw-card tw-section-shell p-4 h-100">
                <h2 class="tw-section-title h2">{{ $page->localized('intro_title') }}</h2>
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

@if($page->key === 'contact')
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="tw-card tw-section-shell p-4 h-100">
                <h2 class="tw-section-title h3 mb-4">{{ __('ui.contact') }}</h2>
                <p class="mb-2"><strong>{{ __('ui.phone') }}:</strong> {{ $siteSettings?->phone }}</p>
                <p class="mb-2"><strong>{{ __('ui.email') }}:</strong> {{ $siteSettings?->contact_email }}</p>
                <p class="mb-3">{{ $siteSettings?->localized('address') }}</p>
                <p class="text-muted mb-0">{{ $siteSettings?->localized('working_hours') }}</p>
            </div>
        </div>
    </div>
</section>
@endif

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

@endsection
