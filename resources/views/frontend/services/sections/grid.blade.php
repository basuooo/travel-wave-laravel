<section class="container py-4">
    @include('frontend.services.sections.heading', [
        'eyebrow' => $section['eyebrow'] ?? null,
        'title' => $section['title'] ?? ($servicePage['grid']['title'] ?? null),
        'subtitle' => $section['subtitle'] ?? null,
        'class' => 'mb-4',
    ])

    <div class="row g-4">
        @foreach(($section['items'] ?? $servicePage['grid']['items'] ?? []) as $item)
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <a href="{{ $item['url'] ?? '#' }}" class="tw-visa-hub-country-card text-decoration-none">
                    <div class="tw-visa-hub-country-card-top">
                        <span class="tw-visa-hub-country-name">{{ $item['title'] ?? '' }}</span>
                        @if(!empty($item['chip']))
                            <span class="tw-visa-hub-country-chip">{{ $item['chip'] }}</span>
                        @endif
                    </div>
                    @if(!empty($item['text']))
                        <p class="mb-0">{{ $item['text'] }}</p>
                    @endif
                </a>
            </div>
        @endforeach
    </div>
</section>
