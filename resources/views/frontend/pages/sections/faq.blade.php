<section class="container py-4 py-lg-5">
    <div class="tw-card p-4 p-lg-5">
        @if(!empty($section['eyebrow']))
            <div class="tw-brand-page-eyebrow mb-2">{{ $section['eyebrow'] }}</div>
        @endif
        <h2 class="tw-section-title h2 mb-4">{{ $section['title'] }}</h2>
        <div class="accordion tw-visa-faq" id="brandPageFaqs">
            @foreach($section['items'] ?? [] as $item)
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#brand-faq-{{ $loop->iteration }}">
                            {{ $item['question'] }}
                        </button>
                    </h3>
                    <div id="brand-faq-{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#brandPageFaqs">
                        <div class="accordion-body">{{ $item['answer'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
