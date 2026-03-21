<section class="container py-5">
    <div class="tw-visa-hub-cta-banner">
        <div>
            @if(!empty($section['eyebrow']))
                <span class="tw-visa-hub-section-pill tw-visa-hub-section-pill-light">{{ $section['eyebrow'] }}</span>
            @endif
            <h2>{{ $section['title'] ?? '' }}</h2>
            @if(!empty($section['description']) || !empty($section['text']))
                <p class="mb-0">{{ $section['description'] ?? $section['text'] }}</p>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-3">
            @foreach(($section['buttons'] ?? []) as $button)
                <a href="{{ $button['url'] ?? '#' }}" class="btn btn-lg {{ ($button['variant'] ?? 'primary') === 'light-outline' ? 'tw-visa-hub-outline-btn-light' : 'btn-primary tw-btn-primary' }}">
                    {{ $button['label'] ?? '' }}
                </a>
            @endforeach
        </div>
    </div>
</section>
