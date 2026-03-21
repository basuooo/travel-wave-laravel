@if(!empty($eyebrow) || !empty($title) || !empty($subtitle))
    <div class="{{ $class ?? '' }}">
        @if(!empty($eyebrow))
            <span class="tw-visa-hub-section-pill">{{ $eyebrow }}</span>
        @endif
        @if(!empty($title))
            <h2 class="tw-section-title h2 {{ !empty($eyebrow) ? 'mt-3' : '' }} mb-2">{{ $title }}</h2>
        @endif
        @if(!empty($subtitle))
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        @endif
    </div>
@endif
