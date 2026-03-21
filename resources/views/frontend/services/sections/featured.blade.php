<section class="container py-5" id="{{ $popular['section_id'] ?? 'service-popular' }}">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        @include('frontend.services.sections.heading', [
            'eyebrow' => $popular['eyebrow'] ?? null,
            'title' => $popular['title'] ?? null,
            'subtitle' => $popular['text'] ?? ($popular['subtitle'] ?? null),
        ])

        @if(count($popular['items'] ?? []) > 1)
            <div class="tw-visa-hub-slider-controls">
                <button type="button" class="tw-visa-hub-slider-arrow js-service-slider-prev" aria-label="Previous">
                    <span class="tw-visa-hub-slider-icon tw-visa-hub-slider-icon-prev"></span>
                </button>
                <button type="button" class="tw-visa-hub-slider-arrow js-service-slider-next" aria-label="Next">
                    <span class="tw-visa-hub-slider-icon tw-visa-hub-slider-icon-next"></span>
                </button>
            </div>
        @endif
    </div>

    <div class="tw-visa-hub-slider js-service-slider" data-autoplay="{{ data_get($popular, 'slider.autoplay', true) ? 'true' : 'false' }}" data-interval="{{ data_get($popular, 'slider.interval', 3600) }}">
        <div class="tw-visa-hub-slider-viewport">
            <div class="tw-visa-hub-slider-track">
                @foreach(($popular['items'] ?? []) as $item)
                    <article class="tw-visa-hub-destination-card{{ $loop->first ? ' is-active' : '' }}">
                        <div class="tw-visa-hub-destination-media">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] ?? '' }}" class="tw-visa-hub-destination-image">
                            @else
                                <div class="tw-visa-hub-destination-placeholder">{{ strtoupper(substr($item['title'] ?? 'TW', 0, 2)) }}</div>
                            @endif
                            <div class="tw-visa-hub-destination-overlay"></div>
                            @if(!empty($item['badge']))
                                <span class="tw-visa-hub-destination-badge">{{ $item['badge'] }}</span>
                            @endif
                        </div>
                        <div class="tw-visa-hub-destination-body">
                            <h3>{{ $item['title'] ?? '' }}</h3>
                            <div class="tw-visa-hub-destination-meta">
                                @if(!empty($item['subtitle']))
                                    <span>{{ $item['subtitle'] }}</span>
                                @endif
                                @if(!empty($item['meta']))
                                    <strong>{{ $item['meta'] }}</strong>
                                @endif
                            </div>
                            <a href="{{ $item['url'] ?? '#' }}" class="btn btn-outline-primary">{{ $item['button'] ?? 'View' }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
