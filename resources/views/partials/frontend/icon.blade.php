@php
    $iconValue = $icon ?? null;
    $iconFallback = $fallback ?? 'sparkles';
    $iconHtml = \App\Support\IconLibrary::render($iconValue, $iconFallback);
@endphp
<span class="tw-global-icon-shell" aria-hidden="true">
    {!! $iconHtml !!}
</span>
