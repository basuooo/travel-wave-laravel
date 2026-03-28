@extends('layouts.app')

@section('title', $page->localized('title'))

@php
    $isArabic = app()->getLocale() === 'ar';
    $copy = fn (string $ar, string $en) => $isArabic ? $ar : $en;
    $allCountries = $categories->flatMap->countries->unique('id')->sortBy('sort_order')->values();
    $popularCountries = $featuredCountries->isNotEmpty()
        ? $featuredCountries->take(6)
        : $allCountries->take(6);
    $gridCountries = $allCountries->isNotEmpty()
        ? $allCountries->take(10)
        : $popularCountries;
    $heroCountry = $popularCountries->first() ?: $allCountries->first();
    $heroBackground = $heroCountry?->hero_image
        ? asset('storage/' . $heroCountry->hero_image)
        : null;
    $filterDestinations = $allCountries->map(fn ($country) => [
        'label' => $country->localized('name'),
        'url' => route('visas.country', $country),
    ])->values();
    $popularSliderId = 'travelWaveVisaDestinations';
@endphp

@section('content')
<div class="tw-visa-hub-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
    <section class="container pt-4 pt-lg-5">
        <div class="tw-visa-hub-hero" style="{{ $heroBackground ? "--visa-hub-hero:url('" . $heroBackground . "');" : '' }}">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="tw-visa-hub-badge">{{ $copy('منصة سفر وتأشيرات احترافية', 'Premium Visa Concierge') }}</span>
                    <h1 class="tw-visa-hub-display">{{ $copy('خدمات التأشيرات الخارجية', 'External Visa Services') }}</h1>
                    <p class="tw-visa-hub-lead">
                        {{ $copy(
                            'نُسهّل عليك إجراءات التأشيرة من أول خطوة وحتى التقديم، من خلال متابعة احترافية، تنظيم ذكي للمستندات، ودعم واضح يمنحك تجربة أكثر راحة وثقة.',
                            'We make visa processing easier from the first step to final submission with professional follow-up, organized documents, and clearer guidance.'
                        ) }}
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg tw-btn-primary">{{ $copy('ابدأ الآن', 'Start Now') }}</a>
                        <a href="#visa-hub-popular" class="btn btn-lg tw-visa-hub-outline-btn">{{ $copy('استعرض الوجهات', 'Browse Destinations') }}</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="tw-visa-hub-hero-panel">
                        <div class="tw-visa-hub-hero-panel-label">{{ $copy('لماذا Travel Wave؟', 'Why Travel Wave?') }}</div>
                        <div class="tw-visa-hub-hero-metrics">
                            <div class="tw-visa-hub-metric-card">
                                <strong>+24</strong>
                                <span>{{ $copy('وجهة خارجية متاحة', 'Available destinations') }}</span>
                            </div>
                            <div class="tw-visa-hub-metric-card">
                                <strong>15-30</strong>
                                <span>{{ $copy('يوماً كمتوسط معالجة', 'Average processing days') }}</span>
                            </div>
                            <div class="tw-visa-hub-metric-card">
                                <strong>360°</strong>
                                <span>{{ $copy('دعم كامل حتى التقديم', 'Full support until submission') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container position-relative">
        <div class="tw-visa-hub-search-card">
            <form class="row g-3 align-items-end js-visa-hub-filter" data-default-url="{{ route('visas.index') }}">
                <div class="col-lg-3">
                    <label class="form-label" for="visa-hub-service">{{ $copy('نوع الخدمة', 'Service Type') }}</label>
                    <select id="visa-hub-service" class="form-select js-visa-hub-service">
                        <option value="tourism">{{ $copy('تأشيرة سياحية', 'Tourist Visa') }}</option>
                        <option value="family">{{ $copy('زيارة عائلية', 'Family Visit') }}</option>
                        <option value="business">{{ $copy('تأشيرة أعمال', 'Business Visa') }}</option>
                        <option value="multiple">{{ $copy('متعددة السفر', 'Multiple Entry') }}</option>
                    </select>
                </div>
                <div class="col-lg-5">
                    <label class="form-label" for="visa-hub-destination">{{ $copy('الوجهة', 'Destination') }}</label>
                    <select id="visa-hub-destination" class="form-select js-visa-hub-destination">
                        <option value="">{{ $copy('اختر الوجهة', 'Select destination') }}</option>
                        @foreach($filterDestinations as $destination)
                            <option value="{{ $destination['url'] }}">{{ $destination['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label" for="visa-hub-type">{{ $copy('نوع التأشيرة', 'Visa Type') }}</label>
                    <select id="visa-hub-type" class="form-select js-visa-hub-visa-type">
                        <option value="short">{{ $copy('قصيرة الإقامة', 'Short Stay') }}</option>
                        <option value="long">{{ $copy('طويلة الإقامة', 'Long Stay') }}</option>
                        <option value="schengen">{{ $copy('شنغن', 'Schengen') }}</option>
                        <option value="appointment">{{ $copy('حجز موعد', 'Appointment Support') }}</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary tw-btn-primary w-100">{{ $copy('ابحث الآن', 'Search Now') }}</button>
                </div>
            </form>
        </div>
    </section>

    <section class="container py-5" id="visa-hub-popular">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <span class="tw-visa-hub-section-pill">{{ $copy('الأكثر طلباً', 'Most Requested') }}</span>
                <h2 class="tw-section-title h2 mb-2">{{ $copy('أشهر وجهات التأشيرات', 'Popular Visa Destinations') }}</h2>
                <p class="text-muted mb-0">{{ $copy('بطاقات مختارة لوجهات مطلوبة مع معلومات سريعة وتجربة عرض سلسة.', 'Selected destination cards with quick details and smooth browsing.') }}</p>
            </div>
            @if($popularCountries->count() > 1)
                <div class="tw-visa-hub-slider-controls">
                    <button type="button" class="tw-visa-hub-slider-arrow js-visa-hub-prev" aria-label="{{ $copy('الوجهة السابقة', 'Previous destination') }}">
                        <span class="tw-visa-hub-slider-icon tw-visa-hub-slider-icon-prev"></span>
                    </button>
                    <button type="button" class="tw-visa-hub-slider-arrow js-visa-hub-next" aria-label="{{ $copy('الوجهة التالية', 'Next destination') }}">
                        <span class="tw-visa-hub-slider-icon tw-visa-hub-slider-icon-next"></span>
                    </button>
                </div>
            @endif
        </div>

        <div
            id="{{ $popularSliderId }}"
            class="tw-visa-hub-slider js-visa-hub-slider"
            data-autoplay="true"
            data-interval="3600"
        >
            <div class="tw-visa-hub-slider-viewport">
                <div class="tw-visa-hub-slider-track">
                    @foreach($popularCountries as $country)
                        @php
                            $processingText = $country->localized('processing_time') ?: $copy('حوالي 15 إلى 30 يوم عمل', 'Around 15 to 30 business days');
                            $countryImage = $country->hero_image
                                ? asset('storage/' . $country->hero_image)
                                : ($country->intro_image ? asset('storage/' . $country->intro_image) : null);
                        @endphp
                        <article class="tw-visa-hub-destination-card{{ $loop->first ? ' is-active' : '' }}">
                            <div class="tw-visa-hub-destination-media">
                                @if($countryImage)
                                    <img src="{{ $countryImage }}" alt="{{ $country->localized('name') }}" class="tw-visa-hub-destination-image">
                                @else
                                    <div class="tw-visa-hub-destination-placeholder">{{ strtoupper(substr($country->localized('name'), 0, 2)) }}</div>
                                @endif
                                <div class="tw-visa-hub-destination-overlay"></div>
                                <span class="tw-visa-hub-destination-badge">{{ $country->localized('visa_type') ?: $copy('تأشيرة خارجية', 'Visa Service') }}</span>
                            </div>
                            <div class="tw-visa-hub-destination-body">
                                <h3>{{ $country->localized('name') }}</h3>
                                <div class="tw-visa-hub-destination-meta">
                                    <span>{{ $copy('المدة', 'Processing') }}</span>
                                    <strong>{{ \Illuminate\Support\Str::limit($processingText, 42) }}</strong>
                                </div>
                                <a href="{{ route('visas.country', $country) }}" class="btn btn-outline-primary">{{ $copy('عرض التفاصيل', 'View Details') }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="container py-4">
        <div class="row g-4">
            @foreach([
                ['title' => $copy('مراجعة المستندات', 'Document Review'), 'text' => $copy('نفحص الملف بالكامل ونحدد النواقص قبل التقديم لتقليل الأخطاء ورفع الجاهزية.', 'We review the full file, identify missing items, and improve readiness before submission.'), 'tag' => '01'],
                ['title' => $copy('متابعة الملف', 'Case Follow-Up'), 'text' => $copy('فريقنا يتابع معك كل خطوة بداية من التجهيز وحتى ما بعد التقديم.', 'Our team follows your case from preparation through post-submission updates.'), 'tag' => '02'],
                ['title' => $copy('حجز طيران وفنادق', 'Flights & Hotels'), 'text' => $copy('ننسق الحجوزات بشكل متوافق مع نوع الرحلة ومتطلبات الملف.', 'We align bookings with your itinerary and visa file requirements.'), 'tag' => '03'],
                ['title' => $copy('تنظيم برنامج الرحلة', 'Trip Planning'), 'text' => $copy('نساعدك في بناء تصور رحلة أوضح وأكثر مهنية أمام جهة المعالجة.', 'We help structure a clearer and more credible trip plan for the application.'), 'tag' => '04'],
                ['title' => $copy('سرعة في التنفيذ', 'Fast Execution'), 'text' => $copy('تجهيز أسرع للمستندات والنماذج مع خطوات واضحة تقلل التأخير.', 'Faster preparation of documents and forms with clearer next steps.'), 'tag' => '05'],
                ['title' => $copy('دعم كامل حتى التقديم', 'Full Submission Support'), 'text' => $copy('نبقى معك حتى مرحلة الحجز والتقديم والمتابعة بعد تسليم الملف.', 'We stay with you through appointment booking, submission, and follow-up.'), 'tag' => '06'],
            ] as $feature)
                <div class="col-md-6 col-xl-4">
                    <div class="tw-visa-hub-feature-card h-100">
                        <span class="tw-visa-hub-feature-tag">{{ $feature['tag'] }}</span>
                        <h3>{{ $feature['title'] }}</h3>
                        <p class="mb-0">{{ $feature['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container py-5">
        <div class="tw-visa-hub-steps-shell">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div>
                    <span class="tw-visa-hub-section-pill">{{ $copy('الخطوات', 'Process') }}</span>
                    <h2 class="tw-section-title h2 mb-2">{{ $copy('خطوات التقديم باحترافية', 'Application Steps') }}</h2>
                </div>
            </div>
            <div class="row g-3 g-lg-4">
                @foreach([
                    $copy('اختر الوجهة', 'Choose the destination'),
                    $copy('أرسل المستندات', 'Send the documents'),
                    $copy('مراجعة الملف', 'Review the file'),
                    $copy('حجز الموعد', 'Book the appointment'),
                    $copy('التقديم والمتابعة', 'Submission and follow-up'),
                ] as $step)
                    <div class="col-md-6 col-xl">
                        <div class="tw-visa-hub-step-card h-100">
                            <div class="tw-visa-hub-step-number">{{ $loop->iteration }}</div>
                            <h3>{{ $step }}</h3>
                            <p class="mb-0">{{ $copy('خطوة واضحة ومدروسة تمنحك تجربة أكثر سهولة وثقة.', 'A clear, guided step that keeps the journey organized and stress-free.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container py-4">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <span class="tw-visa-hub-section-pill">{{ $copy('الوجهات', 'Destinations') }}</span>
                <h2 class="tw-section-title h2 mb-2">{{ $copy('شبكة وجهات التأشيرات', 'Countries Grid') }}</h2>
            </div>
        </div>
        <div class="row g-4">
            @foreach($gridCountries as $country)
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route('visas.country', $country) }}" class="tw-visa-hub-country-card text-decoration-none">
                        <div class="tw-visa-hub-country-card-top">
                            <span class="tw-visa-hub-country-name">{{ $country->localized('name') }}</span>
                            <span class="tw-visa-hub-country-chip">{{ $country->localized('visa_type') ?: $copy('خدمة خارجية', 'Visa Service') }}</span>
                        </div>
                        <p class="mb-0">{{ \Illuminate\Support\Str::limit($country->localized('excerpt') ?: $copy('خدمة متكاملة لتجهيز الملف والمتابعة.', 'Complete file preparation and follow-up support.'), 92) }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4">
            @foreach([
                ['title' => $copy('مدة المعالجة', 'Processing Time'), 'value' => $copy('من 15 إلى 30 يوم عمل', '15 to 30 business days'), 'tone' => 'navy'],
                ['title' => $copy('الرسوم', 'Fees'), 'value' => $copy('تبدأ بعد مراجعة الملف ونوع التأشيرة', 'Quoted after file review and visa type'), 'tone' => 'royal'],
                ['title' => $copy('المستندات المطلوبة', 'Required Documents'), 'value' => $copy('جواز سفر، صور، كشف حساب، حجوزات، وخطاب داعم حسب الحالة', 'Passport, photos, bank statement, bookings, and profile-based supporting documents'), 'tone' => 'amber'],
                ['title' => $copy('نسبة سهولة الملف', 'File Readiness'), 'value' => $copy('مرتفعة عند اكتمال البيانات وتناسق المستندات', 'Higher when documents are complete and consistent'), 'tone' => 'slate'],
            ] as $info)
                <div class="col-md-6 col-xl-3">
                    <div class="tw-visa-hub-info-card tw-visa-hub-info-card-{{ $info['tone'] }} h-100">
                        <span>{{ $info['title'] }}</span>
                        <strong>{{ $info['value'] }}</strong>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container py-5">
        <div class="tw-visa-hub-cta-banner">
            <div>
                <span class="tw-visa-hub-section-pill tw-visa-hub-section-pill-light">{{ $copy('ابدأ بخطوة واثقة', 'Start with Confidence') }}</span>
                <h2>{{ $copy('جاهز لبدء ملف تأشيرتك الآن؟', 'Ready to start your visa file now?') }}</h2>
                <p class="mb-0">{{ $copy('نمنحك تجربة أكثر وضوحاً واحترافية من أول استشارة وحتى مرحلة التقديم، مع متابعة حقيقية تقلل التشتت وتزيد الاطمئنان.', 'Get a clearer, more premium experience from your first consultation to final submission, with real follow-up along the way.') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('contact') }}" class="btn btn-primary btn-lg tw-btn-primary">{{ $copy('احجز استشارتك الآن', 'Book Your Consultation') }}</a>
                <a href="https://wa.me/201000000000" class="btn btn-lg tw-visa-hub-outline-btn-light">{{ $copy('تواصل واتساب', 'WhatsApp') }}</a>
            </div>
        </div>
    </section>

    <section class="container py-4">
        <div class="row g-4">
            <div class="col-lg-5">
                <span class="tw-visa-hub-section-pill">{{ $copy('الأسئلة الشائعة', 'FAQ') }}</span>
                <h2 class="tw-section-title h2 mt-3 mb-3">{{ $copy('إجابات سريعة قبل البدء', 'Quick answers before you start') }}</h2>
                <p class="text-muted mb-0">{{ $copy('أكثر الأسئلة التي يطرحها العملاء قبل تجهيز الملف أو حجز الموعد.', 'Common questions clients ask before preparing the file or booking the appointment.') }}</p>
            </div>
            <div class="col-lg-7">
                <div class="accordion tw-visa-hub-faq" id="travelWaveVisaFaq">
                    @foreach([
                        ['q' => $copy('ما مدة استخراج التأشيرة؟', 'How long does visa processing take?'), 'a' => $copy('تختلف المدة حسب الدولة والموسم واكتمال الملف، لكن غالباً تكون بين 15 و30 يوم عمل في كثير من الوجهات.', 'It depends on the country, season, and file completeness, but many destinations fall within 15 to 30 business days.')],
                        ['q' => $copy('ما الأوراق المطلوبة؟', 'What documents are required?'), 'a' => $copy('تختلف حسب الوجهة ونوع التأشيرة، لكن الأساس يشمل جواز السفر، الصور، المستندات المالية، والحجوزات المناسبة.', 'Requirements vary by destination and visa type, but commonly include passport, photos, financial documents, and aligned bookings.')],
                        ['q' => $copy('هل يوجد متابعة بعد التقديم؟', 'Is there follow-up after submission?'), 'a' => $copy('نعم، فريق Travel Wave يواصل المتابعة معك في مراحل ما بعد التقديم ويوضح لك الخطوة التالية.', 'Yes, Travel Wave continues follow-up after submission and keeps you informed on the next step.')],
                        ['q' => $copy('هل يمكن المساعدة في الحجوزات؟', 'Can you help with bookings?'), 'a' => $copy('نعم، يمكن المساعدة في تنسيق حجوزات الطيران والفنادق بما يتناسب مع ملف التأشيرة والرحلة.', 'Yes, we help coordinate flights and hotel bookings so they fit the visa file and travel plan.')],
                        ['q' => $copy('ما أفضل وقت للتقديم؟', 'When is the best time to apply?'), 'a' => $copy('كلما كان التقديم مبكراً كان أفضل، خاصة قبل المواسم المزدحمة أو في حال وجود مواعيد محدودة.', 'Earlier is usually better, especially before peak seasons or when appointment availability is tight.')],
                    ] as $faq)
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="visaHubFaqHeading{{ $loop->iteration }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#visaHubFaq{{ $loop->iteration }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ $faq['q'] }}
                                </button>
                            </h3>
                            <div id="visaHubFaq{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#travelWaveVisaFaq">
                                <div class="accordion-body">{{ $faq['a'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-visa-hub-filter').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const destinationSelect = form.querySelector('.js-visa-hub-destination');
            const targetUrl = destinationSelect?.value || form.dataset.defaultUrl;
            window.location.href = targetUrl;
        });
    });

    document.querySelectorAll('.js-visa-hub-slider').forEach((slider) => {
        const viewport = slider.querySelector('.tw-visa-hub-slider-viewport');
        const cards = Array.from(slider.querySelectorAll('.tw-visa-hub-destination-card'));
        const shell = slider.closest('.container');
        const prevButton = shell?.querySelector('.js-visa-hub-prev');
        const nextButton = shell?.querySelector('.js-visa-hub-next');
        const autoplayEnabled = slider.dataset.autoplay === 'true';
        const interval = Math.max(1500, parseInt(slider.dataset.interval || '3600', 10));

        if (!viewport || cards.length <= 1) {
            return;
        }

        let currentIndex = 0;
        let timer = null;
        let touchStartX = null;

        const syncActive = () => {
            cards.forEach((card, index) => {
                card.classList.toggle('is-active', index === currentIndex);
            });
        };

        const goTo = (index, behavior = 'smooth') => {
            const card = cards[index];
            if (!card) {
                return;
            }

            currentIndex = index;
            syncActive();
            viewport.scrollTo({
                left: Math.max(0, card.offsetLeft - 8),
                behavior,
            });
        };

        const step = (direction) => {
            const nextIndex = direction > 0
                ? ((currentIndex + 1) % cards.length)
                : ((currentIndex - 1 + cards.length) % cards.length);

            goTo(nextIndex);
        };

        const stopAutoplay = () => {
            window.clearInterval(timer);
            timer = null;
        };

        const startAutoplay = () => {
            stopAutoplay();
            if (!autoplayEnabled || document.hidden) {
                return;
            }

            timer = window.setInterval(() => step(1), interval);
        };

        prevButton?.addEventListener('click', () => {
            step(-1);
            startAutoplay();
        });

        nextButton?.addEventListener('click', () => {
            step(1);
            startAutoplay();
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
            startAutoplay();
        }, { passive: true });

        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);
        document.addEventListener('visibilitychange', () => document.hidden ? stopAutoplay() : startAutoplay());

        syncActive();
        startAutoplay();
    });
});
</script>
@endsection
