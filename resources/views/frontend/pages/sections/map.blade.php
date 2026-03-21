<section class="container py-4 py-lg-5" id="contact-location">
    <div class="tw-card p-4 p-lg-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                @if(!empty($section['eyebrow']))
                    <div class="tw-brand-page-eyebrow mb-3">{{ $section['eyebrow'] }}</div>
                @endif
                <h2 class="tw-section-title h2 mb-3">{{ $section['title'] }}</h2>
                @if(!empty($section['description']))
                    <p class="text-muted mb-4">{{ $section['description'] }}</p>
                @endif
                @if(!empty($section['details']))
                    <div class="tw-brand-map-details">
                        @foreach($section['details'] as $item)
                            <div class="tw-brand-map-detail">
                                <strong>{{ $item['label'] }}</strong>
                                <span>{{ $item['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-lg-8">
                <div class="tw-map-embed tw-brand-map-frame">
                    {!! $section['embed_code'] !!}
                </div>
            </div>
        </div>
    </div>
</section>
