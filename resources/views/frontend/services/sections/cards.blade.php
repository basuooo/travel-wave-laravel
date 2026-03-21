<section class="container py-5" id="{{ $section['section_id'] ?? 'service-cards' }}">
    @include('frontend.services.sections.heading', [
        'eyebrow' => $section['eyebrow'] ?? null,
        'title' => $section['title'] ?? null,
        'subtitle' => $section['subtitle'] ?? null,
        'class' => 'mb-4',
    ])

    <div class="row g-4">
        @foreach(($section['items'] ?? []) as $item)
            <div class="col-md-6 col-xl-4">
                <div class="tw-visa-hub-feature-card h-100">
                    <h3>{{ $item['title'] ?? '' }}</h3>
                    @if(!empty($item['meta']))
                        <p class="text-muted mb-3">{{ $item['meta'] }}</p>
                    @endif
                    @if(!empty($item['highlights']))
                        <div class="tw-visa-reference-form-meta mb-4">
                            @foreach($item['highlights'] as $highlight)
                                <div class="tw-visa-reference-form-meta-item">
                                    <span>{{ $highlight }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        @if(!empty($item['price']))
                            <strong class="tw-section-title">{{ $item['price'] }}</strong>
                        @endif
                        <a href="{{ $item['url'] ?? '#service-form' }}" class="btn btn-primary tw-btn-primary">{{ $item['button'] ?? 'View' }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
