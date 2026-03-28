@php
    $mapSection = $assignment->mapSection;
    $layout = $mapSection->layout_type ?: 'split';
    $isRtl = app()->getLocale() === 'ar';
    $title = $mapSection->localized('title');
    $subtitle = $mapSection->localized('subtitle');
    $address = $mapSection->localized('address');
    $buttonText = $mapSection->localized('button_text');
    $buttonLink = $mapSection->button_link ?: $mapSection->map_url;
    $baseHeight = max(200, min(1200, (int) ($mapSection->height ?: 380)));
    $height = max(200, $baseHeight - 40);
    $embedCode = $mapSection->embed_code;
    $mapUrl = $mapSection->map_url;

    if (!$embedCode && $mapUrl) {
        $embedCode = '<iframe src="' . e($mapUrl) . '" width="100%" height="' . $height . '" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    } elseif ($embedCode && !str_contains($embedCode, 'height=')) {
        $embedCode = preg_replace('/<iframe/i', '<iframe height="' . $height . '"', $embedCode, 1) ?: $embedCode;
    }

    $shellClasses = [
        'tw-managed-map',
        'tw-managed-map-' . $layout,
        'tw-managed-map-bg-' . ($mapSection->background_style ?: 'default'),
        'tw-managed-map-spacing-' . ($mapSection->spacing_preset ?: 'normal'),
        $mapSection->rounded_corners ? 'tw-managed-map-rounded' : 'tw-managed-map-sharp',
        $isRtl ? 'tw-managed-map-rtl' : 'tw-managed-map-ltr',
    ];
@endphp

@if($embedCode)
    <section class="{{ implode(' ', $shellClasses) }}">
        @if($layout === 'full_width')
            <div class="tw-managed-map-shell">
                <div class="tw-managed-map-copy mb-4">
                    @if($title)
                        <h2 class="tw-section-title h2 mb-3">{{ $title }}</h2>
                    @endif
                    @if($subtitle)
                        <p class="text-muted mb-0">{{ $subtitle }}</p>
                    @endif
                </div>
                <div class="tw-managed-map-frame" style="--tw-map-height: {{ $height }}px;">
                    {!! $embedCode !!}
                </div>
            </div>
        @elseif($layout === 'compact')
            <div class="tw-managed-map-shell tw-managed-map-shell-compact">
                <div class="tw-managed-map-copy">
                    @if($title)
                        <h2 class="tw-section-title h3 mb-2">{{ $title }}</h2>
                    @endif
                    @if($subtitle)
                        <p class="text-muted mb-3">{{ $subtitle }}</p>
                    @endif
                    @if($buttonText && $buttonLink)
                        <a href="{{ $buttonLink }}" class="btn btn-primary tw-btn-primary">{{ $buttonText }}</a>
                    @endif
                </div>
                <div class="tw-managed-map-frame" style="--tw-map-height: {{ $height }}px;">
                    {!! $embedCode !!}
                </div>
            </div>
        @else
            <div class="tw-managed-map-shell">
                <div class="row g-4 align-items-start">
                    <div class="{{ $layout === 'card' ? 'col-12' : 'col-lg-4 order-lg-2' }}">
                        <div class="tw-managed-map-copy">
                            @if($title)
                                <h2 class="tw-section-title h2 mb-3">{{ $title }}</h2>
                            @endif
                            @if($subtitle)
                                <p class="text-muted mb-4">{{ $subtitle }}</p>
                            @endif
                            @if($address)
                                <div class="tw-managed-map-address">{{ $address }}</div>
                            @endif
                            @if($buttonText && $buttonLink)
                                <a href="{{ $buttonLink }}" class="btn btn-outline-primary tw-btn-outline mt-4">{{ $buttonText }}</a>
                            @endif
                        </div>
                    </div>
                    <div class="{{ $layout === 'card' ? 'col-12' : 'col-lg-8 order-lg-1' }}">
                        <div class="tw-managed-map-frame" style="--tw-map-height: {{ $height }}px;">
                            {!! $embedCode !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endif
