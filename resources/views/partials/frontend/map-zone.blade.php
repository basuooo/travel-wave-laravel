@php
    $assignments = $assignments ?? [];
    $fallbackSection = $fallbackSection ?? null;
    $hasFallback = !empty($fallbackSection['enabled']) && !empty($fallbackSection['embed_code']);
@endphp

@if(!empty($assignments))
    <section class="container py-4 tw-managed-map-zone tw-managed-map-zone-{{ $position ?? 'default' }}">
        <div class="d-grid gap-4">
            @foreach($assignments as $assignment)
                @include('partials.frontend.managed-map', ['assignment' => $assignment])
            @endforeach
        </div>
    </section>
@elseif($hasFallback)
    @include('frontend.pages.sections.map', ['section' => $fallbackSection])
@endif
