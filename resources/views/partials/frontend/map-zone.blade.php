@php
    $assignments = $assignments ?? [];
@endphp

@if(!empty($assignments))
    <section class="container py-4 tw-managed-map-zone tw-managed-map-zone-{{ $position ?? 'default' }}">
        <div class="d-grid gap-4">
            @foreach($assignments as $assignment)
                @include('partials.frontend.managed-map', ['assignment' => $assignment])
            @endforeach
        </div>
    </section>
@endif
