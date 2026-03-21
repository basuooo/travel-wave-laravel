<section class="container py-4 py-lg-5" id="premium-contact-form">
    <div class="tw-card p-0 overflow-hidden">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="tw-brand-form-copy p-4 p-lg-5 h-100">
                    @if(!empty($section['eyebrow']))
                        <div class="tw-brand-page-eyebrow mb-3">{{ $section['eyebrow'] }}</div>
                    @endif
                    <h2 class="tw-section-title h2 mb-3">{{ $section['title'] }}</h2>
                    @if(!empty($section['subtitle']))
                        <p class="text-muted mb-4">{{ $section['subtitle'] }}</p>
                    @endif
                    @if(!empty($section['checklist']))
                        <div class="tw-brand-form-checklist">
                            @foreach($section['checklist'] as $item)
                                <div class="tw-brand-story-point">
                                    <span class="tw-brand-story-point-dot"></span>
                                    <span>{{ $item }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                @include('partials.frontend.inquiry-form', [
                    'type' => $section['type'] ?? 'general',
                    'source' => $section['source'] ?? request()->path(),
                    'config' => $section['config'] ?? [],
                    'className' => 'h-100 border-0 rounded-0 shadow-none',
                ])
            </div>
        </div>
    </div>
</section>
