@php
    $steps = $section['items'] ?? ($servicePage['steps'] ?? []);
@endphp

<section class="container py-5">
    <div class="tw-visa-hub-steps-shell">
        @include('frontend.services.sections.heading', [
            'eyebrow' => $section['eyebrow'] ?? null,
            'title' => $section['title'] ?? ($servicePage['steps_title'] ?? null),
            'subtitle' => $section['subtitle'] ?? null,
            'class' => 'mb-4',
        ])

        <div class="row g-3 g-lg-4">
            @foreach($steps as $step)
                @php
                    $stepTitle = is_array($step) ? ($step['title'] ?? '') : $step;
                    $stepText = is_array($step) ? ($step['text'] ?? ($step['description'] ?? '')) : '';
                @endphp
                <div class="col-md-6 col-xl">
                    <div class="tw-visa-hub-step-card h-100">
                        <div class="tw-visa-hub-step-number">{{ $loop->iteration }}</div>
                        <h3>{{ $stepTitle }}</h3>
                        @if($stepText !== '')
                            <p class="mb-0">{{ $stepText }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
