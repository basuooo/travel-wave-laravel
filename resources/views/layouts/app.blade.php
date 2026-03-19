<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $siteSettings?->localized('site_name') ?? 'Travel Wave')</title>
    <meta name="description" content="@yield('meta_description', $siteSettings?->localized('default_meta_description'))">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', $siteSettings?->localized('site_name') ?? 'Travel Wave')))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', $siteSettings?->localized('default_meta_description'))))">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <link rel="icon" href="{{ $siteSettings?->favicon_path ? asset('storage/' . $siteSettings->favicon_path) : '' }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/site.css') }}" rel="stylesheet">
    <style>
        :root {
            --tw-navy: {{ $siteSettings?->primary_color ?: '#12395b' }};
            --tw-orange: {{ $siteSettings?->secondary_color ?: '#ff8c32' }};
            --tw-accent: {{ $siteSettings?->accent_color ?: ($siteSettings?->secondary_color ?: '#ff8c32') }};
            --tw-button: {{ $siteSettings?->button_color ?: ($siteSettings?->accent_color ?: '#ff8c32') }};
            --tw-button-hover: {{ $siteSettings?->button_hover_color ?: '#ef5c00' }};
            --tw-link-hover: {{ $siteSettings?->link_hover_color ?: ($siteSettings?->accent_color ?: '#ff8c32') }};
            --tw-header-bg: {{ $siteSettings?->header_background_color ?: ($siteSettings?->primary_color ?: '#12395b') }};
            --tw-header-text: {{ $siteSettings?->header_text_color ?: '#ffffff' }};
            --tw-header-link: {{ $siteSettings?->header_link_color ?: '#ffffff' }};
            --tw-header-hover: {{ $siteSettings?->header_hover_color ?: '#ff8c32' }};
            --tw-header-active: {{ $siteSettings?->header_active_link_color ?: '#ff8c32' }};
            --tw-header-button: {{ $siteSettings?->header_button_color ?: '#ff8c32' }};
            --tw-header-button-text: {{ $siteSettings?->header_button_text_color ?: '#ffffff' }};
            --tw-footer-bg: {{ $siteSettings?->footer_background_color ?: '#0d2438' }};
            --tw-footer-text: {{ $siteSettings?->footer_text_color ?: '#d9e3ed' }};
            --tw-footer-link: {{ $siteSettings?->footer_link_color ?: '#ffffff' }};
            --tw-footer-hover: {{ $siteSettings?->footer_hover_color ?: '#ff8c32' }};
            --tw-footer-heading: {{ $siteSettings?->footer_heading_color ?: '#ffffff' }};
            --tw-footer-button: {{ $siteSettings?->footer_button_color ?: '#ff8c32' }};
            --tw-footer-button-text: {{ $siteSettings?->footer_button_text_color ?: '#ffffff' }};
            --tw-logo-width: {{ $siteSettings?->logo_width ?: 220 }}px;
            --tw-mobile-logo-width: {{ $siteSettings?->mobile_logo_width ?: 168 }}px;
        }
    </style>
</head>
<body class="{{ $isRtl ? 'rtl' : 'ltr' }}">
    @include('partials.frontend.header')

    <main class="pb-5">
        @if (session('success'))
            <div class="container pt-4">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.frontend.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
