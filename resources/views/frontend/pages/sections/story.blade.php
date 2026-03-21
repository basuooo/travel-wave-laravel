<section class="container py-4 py-lg-5">
    <div class="tw-card tw-brand-story-card p-4 p-lg-5">
        <div class="row g-4 align-items-center {{ !empty($section['reverse']) ? 'flex-lg-row-reverse' : '' }}">
            <div class="col-lg-5">
                <div class="tw-brand-story-media">
                    @if(!empty($section['image']))
                        <img src="{{ $section['image'] }}" alt="{{ $section['title'] }}" class="tw-image-cover">
                    @else
                        <div class="tw-brand-story-media-placeholder">{{ $section['title'] }}</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                @if(!empty($section['eyebrow']))
                    <div class="tw-brand-page-eyebrow mb-3">{{ $section['eyebrow'] }}</div>
                @endif
                <h2 class="tw-section-title h2 mb-3">{{ $section['title'] }}</h2>
                @if(!empty($section['description']))
                    <p class="tw-copy mb-4">{{ $section['description'] }}</p>
                @endif
                @if(!empty($section['points']))
                    <div class="row g-3">
                        @foreach($section['points'] as $point)
                            <div class="col-md-6">
                                <div class="tw-brand-story-point">
                                    <span class="tw-brand-story-point-dot"></span>
                                    <span>{{ $point }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
