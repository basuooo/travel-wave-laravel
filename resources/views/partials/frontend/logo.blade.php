@php
    $variant = $variant ?? 'header';
    $settings = $settings ?? $siteSettings ?? null;
    $logoPath = $settings?->normalizedMediaPath($settings?->logoPathFor($variant));
    $logoDisk = \Illuminate\Support\Facades\Storage::disk('public');
    $hasLogoFile = $logoPath && $logoDisk->exists($logoPath);
    $logoWidth = $variant === 'mobile'
        ? ($settings?->mobile_logo_width ?: $settings?->logo_width ?: 168)
        : ($settings?->logo_width ?: 220);
    $logoHeight = $settings?->logo_height;
    $brandName = $settings?->localized('site_name') ?: 'Travel Wave';
    $logoUrl = $settings?->logoUrlFor($variant);
@endphp

@if($hasLogoFile)
    <img
        src="{{ $logoUrl }}"
        alt="{{ $brandName }}"
        class="tw-brand-logo {{ $className ?? '' }}"
        style="width: min(100%, {{ (int) $logoWidth }}px); {{ $logoHeight ? 'height:' . (int) $logoHeight . 'px; object-fit:contain;' : 'height:auto;' }}"
    >
@else
    <span class="tw-brand-wordmark {{ $className ?? '' }}">{{ $brandName }}</span>
@endif
