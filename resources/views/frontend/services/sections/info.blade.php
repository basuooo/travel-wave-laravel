<section class="container py-5">
    @if(!empty($section['title']) || !empty($section['eyebrow']))
        @include('frontend.services.sections.heading', [
            'eyebrow' => $section['eyebrow'] ?? null,
            'title' => $section['title'] ?? null,
            'subtitle' => $section['subtitle'] ?? null,
            'class' => 'mb-4',
        ])
    @endif

    <div class="row g-4">
        @foreach(($section['items'] ?? $servicePage['quick_info'] ?? []) as $item)
            <div class="col-md-6 col-xl-3">
                <div class="tw-visa-hub-info-card tw-visa-hub-info-card-{{ $item['tone'] ?? ['navy', 'royal', 'amber', 'slate'][$loop->index % 4] }} h-100">
                    <span>{{ $item['title'] ?? '' }}</span>
                    <strong>{{ $item['value'] ?? '' }}</strong>
                </div>
            </div>
        @endforeach
    </div>
</section>
