@php
    $variant = $variant ?? 'header';
    $settings = $settings ?? $siteSettings ?? null;
    $logoPath = $settings?->normalizedMediaPath($settings?->logoPathFor($variant));
    $logoDisk = \Illuminate\Support\Facades\Storage::disk('public');
    $hasLogoFile = $logoPath && $logoDisk->exists($logoPath);
    $logoWidth = $settings?->logoWidthFor($variant) ?? ($variant === 'footer' ? 200 : 220);
    $logoHeight = $settings?->logoHeightFor($variant);
    $keepAspectRatio = $settings?->logoKeepsAspectRatio($variant) ?? true;
    $displayMode = $settings?->logoDisplayModeFor($variant) ?? 'original';
    $brandName = $settings?->localized('site_name') ?: 'Travel Wave';
    $logoUrl = $settings?->logoUrlFor($variant);
    $logoStyle = 'display:block;max-width:100%;';

    if ($displayMode === 'original') {
        $logoStyle .= 'width:auto;height:auto;max-height:none;object-fit:initial;';
    } elseif ($displayMode === 'contain') {
        if ($logoWidth) {
            $logoStyle .= 'width:min(100%, ' . (int) $logoWidth . 'px);';
        } else {
            $logoStyle .= 'width:auto;';
        }
        if ($logoHeight) {
            $logoStyle .= 'height:' . (int) $logoHeight . 'px;';
        } else {
            $logoStyle .= 'height:auto;';
        }
        $logoStyle .= 'object-fit:contain;';
    } elseif ($displayMode === 'cover') {
        if ($logoWidth) {
            $logoStyle .= 'width:min(100%, ' . (int) $logoWidth . 'px);';
        } else {
            $logoStyle .= 'width:100%;';
        }
        if ($logoHeight) {
            $logoStyle .= 'height:' . (int) $logoHeight . 'px;';
        } else {
            $logoStyle .= 'height:auto;';
        }
        $logoStyle .= 'object-fit:cover;';
    } else {
        if ($logoWidth) {
            $logoStyle .= 'width:min(100%, ' . (int) $logoWidth . 'px);';
        } else {
            $logoStyle .= 'width:auto;';
        }
        if ($logoHeight) {
            $logoStyle .= 'height:' . (int) $logoHeight . 'px;';
            $logoStyle .= $keepAspectRatio ? 'object-fit:contain;' : 'object-fit:fill;';
        } else {
            $logoStyle .= 'height:auto;';
        }
    }
@endphp

@if($hasLogoFile)
    <img
        src="{{ $logoUrl }}"
        alt="{{ $brandName }}"
        class="tw-brand-logo {{ $className ?? '' }}"
        style="{{ $logoStyle }}"
    >
@else
    <span class="tw-brand-wordmark {{ $className ?? '' }}">{{ $brandName }}</span>
@endif
