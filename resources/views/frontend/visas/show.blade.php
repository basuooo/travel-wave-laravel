@extends('layouts.app')

@section('title', $country->localized('meta_title') ?: $country->localized('name'))

@section('content')
<section class="container py-5">
    <div class="tw-page-header tw-section-shell p-4 p-lg-5">
        <span class="badge bg-light text-dark mb-3">{{ $country->category?->localized('name') }}</span>
        <h1 class="display-5">{{ $country->localized('hero_title') ?: $country->localized('name') }}</h1>
        <p class="lead text-white-50 mb-0">{{ $country->localized('hero_subtitle') ?: $country->localized('excerpt') }}</p>
    </div>
</section>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="tw-card tw-section-shell p-4 mb-4">
                <h2 class="tw-section-title h2">Overview</h2>
                <div class="text-muted">{!! nl2br(e($country->localized('overview'))) !!}</div>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="tw-card tw-section-shell p-4 h-100">
                        <h3 class="h4">{{ __('ui.required_documents') }}</h3>
                        <ul class="list-unstyled tw-list-check mb-0">
                            @foreach($country->required_documents ?? [] as $item)
                                <li>{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="tw-card tw-section-shell p-4 h-100">
                        <h3 class="h4">{{ __('ui.application_steps') }}</h3>
                        <ul class="list-unstyled tw-list-check mb-0">
                            @foreach($country->application_steps ?? [] as $item)
                                <li>{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tw-card tw-section-shell p-4 mt-4">
                <h3 class="h4">{{ __('ui.our_services') }}</h3>
                <ul class="list-unstyled tw-list-check mb-0">
                    @foreach($country->services ?? [] as $item)
                        <li>{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="tw-card tw-section-shell p-4 mb-4">
                <h3 class="h4">{{ __('ui.processing_time') }}</h3>
                <p class="text-muted">{{ $country->localized('processing_time') }}</p>
                <h3 class="h4">{{ __('ui.fees') }}</h3>
                <p class="text-muted mb-0">{{ $country->localized('fees') }}</p>
            </div>
            <div class="tw-sticky-form">
                @include('partials.frontend.inquiry-form', ['type' => 'visa', 'source' => 'visa-country', 'destination' => $country->localized('name')])
            </div>
        </div>
    </div>
</section>

@if(!empty($country->faqs))
<section class="container py-5">
    <div class="tw-card tw-section-shell p-4">
        <h2 class="tw-section-title h2 mb-4">{{ __('ui.faq') }}</h2>
        <div class="accordion" id="visaFaqs">
            @foreach($country->faqs as $item)
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#visa-faq-{{ $loop->iteration }}">
                            {{ app()->getLocale() === 'ar' ? $item['question_ar'] : $item['question_en'] }}
                        </button>
                    </h3>
                    <div id="visa-faq-{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#visaFaqs">
                        <div class="accordion-body">{{ app()->getLocale() === 'ar' ? $item['answer_ar'] : $item['answer_en'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
