<section class="container py-4 py-lg-5">
    <div class="tw-brand-stats-shell p-4 p-lg-5">
        @if(!empty($section['eyebrow']))
            <div class="tw-brand-page-eyebrow mb-2">{{ $section['eyebrow'] }}</div>
        @endif
        <h2 class="tw-section-title h2 mb-4">{{ $section['title'] }}</h2>
        <div class="row g-4">
            @foreach($section['items'] ?? [] as $item)
                <div class="col-sm-6 col-xl-3">
                    <div class="tw-brand-stat-card h-100">
                        <div class="tw-brand-stat-value">{{ $item['value'] }}</div>
                        <h3 class="h6 mb-2">{{ $item['label'] }}</h3>
                        @if(!empty($item['text']))
                            <p class="mb-0 text-muted">{{ $item['text'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
