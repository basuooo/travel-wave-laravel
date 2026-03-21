@php
    $hasCopy = !empty($section['eyebrow']) || !empty($section['title']) || !empty($section['subtitle']);
@endphp

<section class="container py-4">
    <div class="row g-4">
        @if($hasCopy)
            <div class="col-lg-5">
                @include('frontend.services.sections.heading', [
                    'eyebrow' => $section['eyebrow'] ?? null,
                    'title' => $section['title'] ?? null,
                    'subtitle' => $section['subtitle'] ?? null,
                ])
            </div>
        @endif
        <div class="{{ $hasCopy ? 'col-lg-7' : 'col-12' }}">
            <div class="accordion tw-visa-hub-faq" id="serviceFaqAccordion">
                @foreach(($section['items'] ?? $servicePage['faqs'] ?? []) as $faq)
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="serviceFaqHeading{{ $loop->iteration }}">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#serviceFaq{{ $loop->iteration }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $faq['question'] ?? $faq['q'] ?? '' }}
                            </button>
                        </h3>
                        <div id="serviceFaq{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#serviceFaqAccordion">
                            <div class="accordion-body">{{ $faq['answer'] ?? $faq['a'] ?? '' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
