<section class="container py-4 py-lg-5">
    <div class="tw-brand-page-cta" style="{{ !empty($section['background_image']) ? "background-image:linear-gradient(135deg, rgba(7, 27, 46, 0.92), rgba(17, 55, 93, 0.84)), url('" . $section['background_image'] . "');" : '' }}">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                @if(!empty($section['eyebrow']))
                    <div class="tw-brand-page-eyebrow text-white-50 mb-2">{{ $section['eyebrow'] }}</div>
                @endif
                <h2 class="display-6 mb-3">{{ $section['title'] }}</h2>
                @if(!empty($section['description']))
                    <p class="lead mb-0">{{ $section['description'] }}</p>
                @endif
            </div>
            <div class="col-lg-5">
                <div class="d-flex flex-wrap justify-content-lg-end gap-3">
                    @foreach($section['buttons'] ?? [] as $button)
                        <a href="{{ $button['url'] }}" class="btn btn-lg px-4 {{ ($button['variant'] ?? 'primary') === 'outline' ? 'btn-outline-light' : 'btn-primary tw-btn-primary' }}">
                            {{ $button['text'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
