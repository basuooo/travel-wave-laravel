@extends('layouts.app')

@section('title', $destination->localized('meta_title') ?: $destination->localized('title'))

@section('content')
<section class="container py-5">
    <div class="tw-page-header p-4 p-lg-5">
        <h1 class="display-5">{{ $destination->localized('hero_title') ?: $destination->localized('title') }}</h1>
        <p class="lead text-white-50 mb-0">{{ $destination->localized('hero_subtitle') ?: $destination->localized('excerpt') }}</p>
    </div>
</section>
<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="tw-card p-4 mb-4">
                <h2 class="tw-section-title h2">Overview</h2>
                <div class="text-muted">{!! nl2br(e($destination->localized('overview'))) !!}</div>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="tw-card p-4 h-100">
                        <h3 class="h4">Highlights</h3>
                        <ul class="list-unstyled tw-list-check mb-0">
                            @foreach($destination->highlights ?? [] as $item)
                                <li>{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="tw-card p-4 h-100">
                        <h3 class="h4">Packages</h3>
                        <ul class="list-unstyled tw-list-check mb-0">
                            @foreach($destination->packages ?? [] as $item)
                                <li>{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-1">
                <div class="col-md-6">
                    <div class="tw-card p-4 h-100">
                        <h3 class="h4">{{ __('ui.included') }}</h3>
                        <ul class="list-unstyled tw-list-check mb-0">
                            @foreach($destination->included_items ?? [] as $item)
                                <li>{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="tw-card p-4 h-100">
                        <h3 class="h4">{{ __('ui.excluded') }}</h3>
                        <ul class="list-unstyled tw-list-check mb-0">
                            @foreach($destination->excluded_items ?? [] as $item)
                                <li>{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tw-card p-4 mt-4">
                <h3 class="h4">{{ __('ui.itinerary') }}</h3>
                <ul class="list-unstyled tw-list-check mb-0">
                    @foreach($destination->itinerary ?? [] as $item)
                        <li>{{ app()->getLocale() === 'ar' ? $item['text_ar'] : $item['text_en'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-lg-4">
            @include('partials.frontend.inquiry-form', ['type' => 'destination', 'source' => 'destination', 'destination' => $destination->localized('title')])
        </div>
    </div>
</section>

@if(!empty($destination->gallery))
<section class="container py-5">
    <div class="row g-3">
        @foreach($destination->gallery as $image)
            <div class="col-md-4">
                <div class="tw-card overflow-hidden">
                    <img src="{{ asset('storage/' . $image) }}" class="img-fluid" alt="{{ $destination->localized('title') }}">
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@if(!empty($destination->faqs))
<section class="container py-5">
    <div class="tw-card p-4">
        <h2 class="tw-section-title h2 mb-4">{{ __('ui.faq') }}</h2>
        <div class="accordion" id="tripFaqs">
            @foreach($destination->faqs as $item)
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#trip-faq-{{ $loop->iteration }}">
                            {{ app()->getLocale() === 'ar' ? $item['question_ar'] : $item['question_en'] }}
                        </button>
                    </h3>
                    <div id="trip-faq-{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#tripFaqs">
                        <div class="accordion-body">{{ app()->getLocale() === 'ar' ? $item['answer_ar'] : $item['answer_en'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
