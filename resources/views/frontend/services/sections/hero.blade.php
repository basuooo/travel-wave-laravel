@php
    $heroButtons = $hero['buttons'] ?? array_values(array_filter([
        !empty($hero['primary_cta']) ? $hero['primary_cta'] + ['variant' => 'primary'] : null,
        !empty($hero['secondary_cta']) ? $hero['secondary_cta'] + ['variant' => 'outline'] : null,
    ]));
    $heroMetrics = $hero['metrics'] ?? [];
    $heroText = $hero['subtitle'] ?? ($hero['text'] ?? null);
    $heroImage = $hero['background_image'] ?? ($hero['image'] ?? null);
@endphp

<section class="container pt-4 pt-lg-5">
    <div class="tw-visa-hub-hero" style="{{ !empty($heroImage) ? "--visa-hub-hero:url('" . $heroImage . "');" : '' }}">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                @if(!empty($hero['badge']))
                    <span class="tw-visa-hub-badge">{{ $hero['badge'] }}</span>
                @endif
                <h1 class="tw-visa-hub-display">{{ $hero['title'] ?? '' }}</h1>
                @if(!empty($heroText))
                    <p class="tw-visa-hub-lead">{{ $heroText }}</p>
                @endif
                @if($heroButtons)
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($heroButtons as $button)
                            <a href="{{ $button['url'] ?? '#' }}" class="btn btn-lg {{ ($button['variant'] ?? 'outline') === 'primary' ? 'btn-primary tw-btn-primary' : 'tw-visa-hub-outline-btn' }}">
                                {{ $button['label'] ?? '' }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            @if($heroMetrics)
                <div class="col-lg-5">
                    <div class="tw-visa-hub-hero-panel">
                        <div class="tw-visa-hub-hero-panel-label">{{ $hero['panel_label'] ?? 'Travel Wave' }}</div>
                        <div class="tw-visa-hub-hero-metrics">
                            @foreach($heroMetrics as $metric)
                                <div class="tw-visa-hub-metric-card">
                                    <strong>{{ $metric['value'] ?? '' }}</strong>
                                    @if(!empty($metric['label']))
                                        <span class="fw-bold">{{ $metric['label'] }}</span>
                                    @endif
                                    @if(!empty($metric['text']))
                                        <span>{{ $metric['text'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
