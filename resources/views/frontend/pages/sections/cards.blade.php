<section class="container py-4 py-lg-5">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            @if(!empty($section['eyebrow']))
                <div class="tw-brand-page-eyebrow mb-2">{{ $section['eyebrow'] }}</div>
            @endif
            <h2 class="tw-section-title h2 mb-2">{{ $section['title'] }}</h2>
            @if(!empty($section['subtitle']))
                <p class="text-muted mb-0">{{ $section['subtitle'] }}</p>
            @endif
        </div>
    </div>

    <div class="row g-4">
        @foreach($section['items'] ?? [] as $item)
            <div class="{{ $section['columns'] ?? 'col-md-6 col-xl-4' }}">
                <div class="tw-card tw-brand-card tw-brand-card-{{ $section['variant'] ?? 'default' }} p-4 h-100">
                    <div class="tw-brand-card-icon">@include('partials.frontend.icon', ['icon' => $item['icon'] ?? null, 'fallback' => 'sparkles'])</div>
                    <h3 class="h5 mt-4 mb-2">{{ $item['title'] }}</h3>
                    @if(!empty($item['meta']))
                        <div class="small text-muted mb-2">{{ $item['meta'] }}</div>
                    @endif
                    @if(!empty($item['text']))
                        <p class="text-muted mb-0">{{ $item['text'] }}</p>
                    @endif
                    @if(!empty($item['url']) && !empty($item['link_label']))
                        <div class="mt-4">
                            <a href="{{ $item['url'] }}" class="tw-brand-inline-link">
                                {{ $item['link_label'] }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
