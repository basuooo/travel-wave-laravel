@php
    $iconValue = $icon ?? null;
    $iconFallback = $fallback ?? 'sparkles';
@endphp
{!! \App\Support\IconLibrary::render($iconValue, $iconFallback) !!}
