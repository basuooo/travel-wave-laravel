<section class="container py-4">
    @include('frontend.services.sections.heading', [
        'eyebrow' => $section['eyebrow'] ?? null,
        'title' => $section['title'] ?? ($servicePage['features_title'] ?? null),
        'subtitle' => $section['subtitle'] ?? null,
        'class' => 'mb-4',
    ])

    <div class="row g-4">
        @foreach(($section['items'] ?? $servicePage['features'] ?? []) as $feature)
            <div class="col-md-6 col-xl-4">
                <div class="tw-visa-hub-feature-card h-100">
                    @if(!empty($feature['tag']))
                        <span class="tw-visa-hub-feature-tag">{{ $feature['tag'] }}</span>
                    @endif
                    <h3>{{ $feature['title'] ?? '' }}</h3>
                    <p class="mb-0">{{ $feature['text'] ?? ($feature['description'] ?? '') }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
