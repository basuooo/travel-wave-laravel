@extends('layouts.app')

@section('title', $page->localized('meta_title') ?: ($page->localized('title') ?: 'فيزا فرنسا 3 🇫🇷'))
@section('meta_description', $page->localized('meta_description') ?: data_get($page->sections, 'hero.subtitle_ar'))

@section('content')
@php
    $sections = $page->sections ?? [];
    $orderedSections = $page->getOrderedSections();
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="tw-france3-page pb-5" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    @include('partials.frontend.form-zone', ['assignments' => $managedForms['top'] ?? [], 'position' => 'top', 'sourcePage' => 'france-3'])

    @foreach($orderedSections as $sKey => $sMeta)
        @continue(empty($sMeta['enabled']))

        @switch($sKey)
            {{-- 1. HERO SECTION --}}
            @case('hero')
                @php($hero = $sections['hero'] ?? [])
                <section class="tw-page-header py-5 mb-4 position-relative overflow-hidden">
                    <div class="container py-lg-4 position-relative" style="z-index: 2;">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8 mx-auto text-center">
                                @if(!empty($hero['eyebrow_ar']) || !empty($hero['eyebrow_en']))
                                    <span class="badge bg-primary bg-opacity-25 text-white border border-primary border-opacity-50 px-3 py-2 rounded-pill mb-3 fs-6">
                                        {{ $isRtl ? ($hero['eyebrow_ar'] ?? '') : ($hero['eyebrow_en'] ?? '') }}
                                    </span>
                                @endif
                                <h1 class="display-4 fw-bold text-white mb-3">
                                    {{ $isRtl ? ($hero['title_ar'] ?? $page->localized('hero_title')) : ($hero['title_en'] ?? $page->localized('hero_title')) }}
                                </h1>
                                @if(!empty($hero['subtitle_ar']) || !empty($hero['subtitle_en']))
                                    <p class="lead text-white-50 mb-4 mx-auto" style="max-width: 750px;">
                                        {{ $isRtl ? ($hero['subtitle_ar'] ?? '') : ($hero['subtitle_en'] ?? '') }}
                                    </p>
                                @endif
                                @if(!empty($hero['primary_cta_text_ar']) || !empty($hero['primary_cta_text_en']))
                                    <a href="{{ $hero['primary_cta_url'] ?? '/contact#lead-form' }}" class="btn btn-primary btn-lg tw-btn-primary px-5 py-3 rounded-pill fw-bold shadow-lg">
                                        {{ $isRtl ? ($hero['primary_cta_text_ar'] ?? 'قيّم حالتك الآن') : ($hero['primary_cta_text_en'] ?? 'Assess Your Case Now') }} ✨
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
                @include('partials.frontend.form-zone', ['assignments' => $managedForms['below_hero'] ?? [], 'position' => 'below_hero', 'sourcePage' => 'france-3'])
                @break

            {{-- 2. QUICK SUMMARY SECTION --}}
            @case('quick_summary')
                @php($qs = $sections['quick_summary'] ?? [])
                @if(!empty($qs['items']))
                <section class="container py-3 mb-3">
                    <div class="tw-card p-4 rounded-4 shadow-sm border border-secondary border-opacity-10">
                        @if(!empty($qs['title_ar']) || !empty($qs['title_en']))
                            <h2 class="h5 fw-bold text-navy mb-3 text-center">
                                📌 {{ $isRtl ? ($qs['title_ar'] ?? 'ملخص سريع عن فيزا فرنسا') : ($qs['title_en'] ?? 'Quick Summary') }}
                            </h2>
                        @endif
                        <div class="row g-3 justify-content-center">
                            @foreach(collect($qs['items'] ?? [])->filter(fn($i) => !isset($i['is_active']) || $i['is_active'])->sortBy('sort_order') as $item)
                                <div class="col-6 col-md-4 col-lg-2-4">
                                    <div class="p-3 bg-light rounded-3 text-center border h-100 d-flex flex-column justify-content-center">
                                        <span class="fs-4 mb-1 d-block">{{ $item['icon'] ?? '⚡' }}</span>
                                        <span class="small text-muted d-block mb-1">{{ $isRtl ? ($item['label_ar'] ?? '') : ($item['label_en'] ?? '') }}</span>
                                        <strong class="text-navy fs-6">{{ $isRtl ? ($item['value_ar'] ?? '') : ($item['value_en'] ?? '') }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif
                @break

            {{-- 3. ABOUT / INTRO SECTION --}}
            @case('intro')
                @php($intro = $sections['intro'] ?? [])
                @if(!empty($intro['title_ar']) || !empty($intro['description_ar']))
                <section class="container py-3">
                    <div class="tw-card p-4 p-md-5 rounded-4 shadow-sm border border-secondary border-opacity-10 text-center mx-auto" style="max-width: 900px;">
                        <h2 class="tw-section-title h3 fw-bold mb-3">
                            {{ $isRtl ? ($intro['title_ar'] ?? 'نبذة عن فيزا فرنسا') : ($intro['title_en'] ?? 'About France Visa') }}
                        </h2>
                        <p class="fs-5 text-muted mb-0 leading-relaxed">
                            {!! nl2br(e($isRtl ? ($intro['description_ar'] ?? '') : ($intro['description_en'] ?? ''))) !!}
                        </p>
                    </div>
                </section>
                @endif
                @break

            {{-- 4. BEST TIME TO APPLY SECTION --}}
            @case('best_time')
                @php($bt = $sections['best_time'] ?? [])
                @if(!empty($bt['title_ar']) || !empty($bt['text_ar']))
                <section class="container py-3">
                    <div class="tw-card p-4 rounded-4 shadow-sm border border-primary border-opacity-25 bg-primary bg-opacity-10 text-center mx-auto" style="max-width: 900px;">
                        <h2 class="h4 fw-bold text-navy mb-2">
                            🗓️ {{ $isRtl ? ($bt['title_ar'] ?? 'متى تبدأ إجراءات فيزا فرنسا؟') : ($bt['title_en'] ?? 'When to Start France Visa Steps?') }}
                        </h2>
                        <p class="fs-6 text-muted mb-2">
                            {{ $isRtl ? ($bt['text_ar'] ?? '') : ($bt['text_en'] ?? '') }}
                        </p>
                        @if(!empty($bt['note_ar']) || !empty($bt['note_en']))
                            <div class="fw-bold fs-6 text-primary mt-2">
                                💡 {{ $isRtl ? ($bt['note_ar'] ?? '') : ($bt['note_en'] ?? '') }}
                            </div>
                        @endif
                    </div>
                </section>
                @endif
                @break

            {{-- 5. REQUIREMENTS SECTION --}}
            @case('requirements')
                @php($req = $sections['requirements'] ?? [])
                @if(!empty($req['items']))
                <section class="container py-4">
                    <div class="text-center mb-4">
                        <h2 class="tw-section-title h3 fw-bold mb-2">
                            {{ $isRtl ? ($req['title_ar'] ?? 'إيه أهم متطلبات فيزا فرنسا؟') : ($req['title_en'] ?? 'Key Requirements') }}
                        </h2>
                        @if(!empty($req['subtitle_ar']) || !empty($req['subtitle_en']))
                            <p class="text-muted fs-6 mb-0">{{ $isRtl ? ($req['subtitle_ar'] ?? '') : ($req['subtitle_en'] ?? '') }}</p>
                        @endif
                    </div>
                    <div class="row g-3 g-md-4 justify-content-center">
                        @foreach(collect($req['items'] ?? [])->filter(fn($i) => !isset($i['is_active']) || $i['is_active'])->sortBy('sort_order') as $item)
                            <div class="col-md-6 col-lg-4">
                                <div class="tw-card p-4 h-100 rounded-4 border border-secondary border-opacity-10 shadow-sm hover-lift">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <span class="fs-3">{{ $item['icon'] ?? '📌' }}</span>
                                        <h3 class="h5 fw-bold mb-0">{{ $isRtl ? ($item['title_ar'] ?? '') : ($item['title_en'] ?? '') }}</h3>
                                    </div>
                                    <p class="text-muted small mb-0">{{ $isRtl ? ($item['text_ar'] ?? '') : ($item['text_en'] ?? '') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if(!empty($req['note_ar']) || !empty($req['note_en']))
                        <div class="alert alert-info border-0 rounded-4 p-4 mt-4 text-center mx-auto shadow-sm" style="max-width: 850px;">
                            <span class="fw-bold fs-6">💡 {{ $isRtl ? ($req['note_ar'] ?? '') : ($req['note_en'] ?? '') }}</span>
                        </div>
                    @endif
                </section>
                @endif
                @break

            {{-- 6. SERVICES SECTION --}}
            @case('services')
                @php($serv = $sections['services'] ?? [])
                @if(!empty($serv['items']))
                <section class="container py-4">
                    <div class="text-center mb-4">
                        <h2 class="tw-section-title h3 fw-bold mb-2">
                            {{ $isRtl ? ($serv['title_ar'] ?? 'إحنا بنساعدك في إيه؟') : ($serv['title_en'] ?? 'How We Help You') }}
                        </h2>
                        @if(!empty($serv['subtitle_ar']) || !empty($serv['subtitle_en']))
                            <p class="text-muted fs-6 mb-0">{{ $isRtl ? ($serv['subtitle_ar'] ?? '') : ($serv['subtitle_en'] ?? '') }}</p>
                        @endif
                    </div>
                    <div class="row g-3 g-md-4">
                        @foreach(collect($serv['items'] ?? [])->filter(fn($i) => !isset($i['is_active']) || $i['is_active'])->sortBy('sort_order') as $item)
                            <div class="col-md-6 col-lg-3">
                                <div class="tw-card p-4 h-100 rounded-4 border border-secondary border-opacity-10 shadow-sm d-flex flex-column align-items-start">
                                    <span class="fs-2 text-primary mb-2">{{ $item['icon'] ?? '✔️' }}</span>
                                    <h3 class="h6 fw-bold mb-1">{{ $isRtl ? ($item['title_ar'] ?? '') : ($item['title_en'] ?? '') }}</h3>
                                    <p class="text-muted small mb-0">{{ $isRtl ? ($item['text_ar'] ?? '') : ($item['text_en'] ?? '') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif
                @break

            {{-- 7. APPLICATION STEPS SECTION --}}
            @case('steps')
                @php($steps = $sections['steps'] ?? [])
                @if(!empty($steps['items']))
                <section class="container py-4">
                    <div class="text-center mb-4">
                        <h2 class="tw-section-title h3 fw-bold mb-2">
                            {{ $isRtl ? ($steps['title_ar'] ?? 'خطوات التقديم') : ($steps['title_en'] ?? 'Application Steps') }}
                        </h2>
                        @if(!empty($steps['subtitle_ar']) || !empty($steps['subtitle_en']))
                            <p class="text-muted fs-6 mb-0">{{ $isRtl ? ($steps['subtitle_ar'] ?? '') : ($steps['subtitle_en'] ?? '') }}</p>
                        @endif
                    </div>
                    <div class="row g-3 justify-content-center">
                        @foreach(collect($steps['items'] ?? [])->filter(fn($i) => !isset($i['is_active']) || $i['is_active'])->sortBy('sort_order') as $item)
                            <div class="col-sm-6 col-md-4 col-lg-2-4">
                                <div class="tw-card p-4 h-100 rounded-4 text-center border border-secondary border-opacity-10 shadow-sm">
                                    <div class="badge bg-primary text-white rounded-circle fs-6 fw-bold mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                        {{ $item['step_number'] ?? sprintf('%02d', $loop->iteration) }}
                                    </div>
                                    <h3 class="h6 fw-bold mb-2">{{ $isRtl ? ($item['title_ar'] ?? '') : ($item['title_en'] ?? '') }}</h3>
                                    <p class="text-muted small mb-0">{{ $isRtl ? ($item['text_ar'] ?? '') : ($item['text_en'] ?? '') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif
                @break

            {{-- 8. WHY TRAVEL WAVE SECTION --}}
            @case('why_choose')
                @php($why = $sections['why_choose'] ?? [])
                @if(!empty($why['items']))
                <section class="container py-4">
                    <div class="text-center mb-4">
                        <h2 class="tw-section-title h3 fw-bold mb-2">
                            {{ $isRtl ? ($why['title_ar'] ?? 'ليه تختار Travel Wave؟') : ($why['title_en'] ?? 'Why Choose Travel Wave?') }}
                        </h2>
                        @if(!empty($why['subtitle_ar']) || !empty($why['subtitle_en']))
                            <p class="text-muted fs-6 mb-0">{{ $isRtl ? ($why['subtitle_ar'] ?? '') : ($why['subtitle_en'] ?? '') }}</p>
                        @endif
                    </div>
                    <div class="row g-3 g-md-4">
                        @foreach(collect($why['items'] ?? [])->filter(fn($i) => !isset($i['is_active']) || $i['is_active'])->sortBy('sort_order') as $item)
                            <div class="col-md-6 col-lg-4">
                                <div class="tw-card p-4 h-100 rounded-4 border border-secondary border-opacity-10 shadow-sm d-flex align-items-start gap-3">
                                    <span class="fs-2 text-primary">{{ $item['icon'] ?? '⭐' }}</span>
                                    <div>
                                        <h3 class="h5 fw-bold mb-1">{{ $isRtl ? ($item['title_ar'] ?? '') : ($item['title_en'] ?? '') }}</h3>
                                        <p class="text-muted small mb-0">{{ $isRtl ? ($item['text_ar'] ?? '') : ($item['text_en'] ?? '') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif
                @break

            {{-- 9. SUITABILITY ASSESSMENT SECTION --}}
            @case('suitability')
                @php($suit = $sections['suitability'] ?? [])
                @if(!empty($suit['title_ar']) || !empty($suit['description_ar']))
                <section class="container py-4">
                    <div class="tw-card p-4 p-md-5 rounded-4 shadow-sm border border-primary border-opacity-25 bg-primary bg-opacity-10 text-center mx-auto" style="max-width: 900px;">
                        <h2 class="h3 fw-bold mb-3 text-navy">
                            {{ $isRtl ? ($suit['title_ar'] ?? 'مش عارف إذا كانت حالتك مناسبة؟') : ($suit['title_en'] ?? 'Not Sure If You Qualify?') }}
                        </h2>
                        <p class="fs-6 text-muted mb-3">
                            {{ $isRtl ? ($suit['description_ar'] ?? '') : ($suit['description_en'] ?? '') }}
                        </p>
                        @if(!empty($suit['note_ar']) || !empty($suit['note_en']))
                            <p class="fw-bold fs-6 text-primary mb-4">
                                {{ $isRtl ? ($suit['note_ar'] ?? '') : ($suit['note_en'] ?? '') }}
                            </p>
                        @endif
                        <a href="{{ $suit['button_url'] ?? '/contact#lead-form' }}" class="btn btn-primary btn-lg tw-btn-primary px-5 py-3 rounded-pill fw-bold">
                            {{ $isRtl ? ($suit['button_text_ar'] ?? 'قيّم حالتك الآن') : ($suit['button_text_en'] ?? 'Assess Your Case Now') }} 💬
                        </a>
                    </div>
                </section>
                @endif
                @break

            {{-- 10. PRICING & DURATION SECTION --}}
            @case('pricing_duration')
                @php($pd = $sections['pricing_duration'] ?? [])
                @if(!empty($pd['duration_title_ar']) || !empty($pd['fees_title_ar']))
                <section class="container py-4">
                    <div class="row g-4 justify-content-center" style="max-width: 900px; margin: 0 auto;">
                        <div class="col-md-6">
                            <div class="tw-card p-4 h-100 rounded-4 border border-secondary border-opacity-10 shadow-sm text-center">
                                <span class="fs-1 mb-2 d-block">⏱️</span>
                                <h3 class="h5 fw-bold mb-2">{{ $isRtl ? ($pd['duration_title_ar'] ?? 'مدة الإجراءات') : ($pd['duration_title_en'] ?? 'Processing Duration') }}</h3>
                                <p class="text-muted small mb-0">{{ $isRtl ? ($pd['duration_text_ar'] ?? '') : ($pd['duration_text_en'] ?? '') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="tw-card p-4 h-100 rounded-4 border border-secondary border-opacity-10 shadow-sm text-center">
                                <span class="fs-1 mb-2 d-block">💳</span>
                                <h3 class="h5 fw-bold mb-2">{{ $isRtl ? ($pd['fees_title_ar'] ?? 'الرسوم') : ($pd['fees_title_en'] ?? 'Fees') }}</h3>
                                <p class="text-muted small mb-0">{{ $isRtl ? ($pd['fees_text_ar'] ?? '') : ($pd['fees_text_en'] ?? '') }}</p>
                            </div>
                        </div>
                    </div>
                </section>
                @endif
                @break

            {{-- 11. FAQ SECTION --}}
            @case('faq')
                @php($faq = $sections['faq'] ?? [])
                @if(!empty($faq['items']))
                <section class="container py-4">
                    <div class="tw-card p-4 p-md-5 rounded-4 shadow-sm border border-secondary border-opacity-10 mx-auto" style="max-width: 900px;">
                        <h2 class="tw-section-title h3 fw-bold text-center mb-4">
                            {{ $isRtl ? ($faq['title_ar'] ?? 'الأسئلة الشائعة عن فيزا فرنسا') : ($faq['title_en'] ?? 'France Visa FAQs') }}
                        </h2>
                        <div class="accordion" id="france3FaqAccordion">
                            @foreach(collect($faq['items'] ?? [])->filter(fn($i) => !isset($i['is_active']) || $i['is_active'])->sortBy('sort_order') as $item)
                                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button fw-bold {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#f3-faq-{{ $loop->iteration }}">
                                            {{ $isRtl ? ($item['question_ar'] ?? '') : ($item['question_en'] ?? '') }}
                                        </button>
                                    </h3>
                                    <div id="f3-faq-{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#france3FaqAccordion">
                                        <div class="accordion-body text-muted leading-relaxed">
                                            {{ $isRtl ? ($item['answer_ar'] ?? '') : ($item['answer_en'] ?? '') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif
                @break

            {{-- 12. IMPORTANT NOTICE SECTION --}}
            @case('notice')
                @php($notice = $sections['notice'] ?? [])
                @if(!empty($notice['text_ar']) || !empty($notice['text_en']))
                <section class="container py-3">
                    <div class="alert alert-warning border border-warning border-opacity-50 rounded-4 p-4 text-center mx-auto shadow-sm" style="max-width: 900px;">
                        <span class="fw-bold fs-6">
                            ⚠️ {{ $isRtl ? ($notice['title_ar'] ?? 'مهم تعرف') : ($notice['title_en'] ?? 'Important Notice') }}:
                            {{ $isRtl ? ($notice['text_ar'] ?? '') : ($notice['text_en'] ?? '') }}
                        </span>
                    </div>
                </section>
                @endif
                @break

            {{-- 13. FINAL CTA SECTION --}}
            @case('cta')
                @php($cta = $sections['cta'] ?? [])
                @if(!empty($cta['title_ar']) || !empty($cta['description_ar']))
                <section class="container py-4">
                    <div class="tw-page-header p-4 p-md-5 rounded-4 text-center shadow-lg mx-auto" style="max-width: 900px;">
                        <h2 class="display-6 fw-bold text-white mb-3">
                            {{ $isRtl ? ($cta['title_ar'] ?? 'جاهز تبدأ إجراءات فيزا فرنسا؟') : ($cta['title_en'] ?? 'Ready to Start France Visa Steps?') }}
                        </h2>
                        <p class="lead text-white-50 mb-4">
                            {{ $isRtl ? ($cta['description_ar'] ?? '') : ($cta['description_en'] ?? '') }}
                        </p>
                        @if(!empty($cta['button_text_ar']) || !empty($cta['button_text_en']))
                            <a href="{{ $cta['button_url'] ?? '/contact#lead-form' }}" class="btn btn-primary btn-lg tw-btn-primary px-5 py-3 rounded-pill fw-bold shadow">
                                {{ $isRtl ? ($cta['button_text_ar'] ?? 'تواصل معنا الآن') : ($cta['button_text_en'] ?? 'Contact Us Now') }} 🚀
                            </a>
                        @endif
                    </div>
                </section>
                @endif
                @break
        @endswitch
    @endforeach

    @include('partials.frontend.form-zone', ['assignments' => $managedForms['middle'] ?? [], 'position' => 'middle', 'sourcePage' => 'france-3'])
    @include('partials.frontend.form-zone', ['assignments' => $managedForms['bottom'] ?? [], 'position' => 'bottom', 'sourcePage' => 'france-3'])
</div>
@endsection
