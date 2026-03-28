<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoMetaData['title'] ?? trim($__env->yieldContent('title', $siteSettings?->localized('site_name') ?? 'Travel Wave')) }}</title>
    <meta name="description" content="{{ $seoMetaData['description'] ?? trim($__env->yieldContent('meta_description', $siteSettings?->localized('default_meta_description'))) }}">
    <meta name="robots" content="{{ $seoMetaData['robots'] ?? 'index,follow' }}">
    <link rel="canonical" href="{{ $seoMetaData['canonical'] ?? request()->fullUrl() }}">
    <meta property="og:title" content="{{ $seoMetaData['og_title'] ?? trim($__env->yieldContent('og_title', $__env->yieldContent('title', $siteSettings?->localized('site_name') ?? 'Travel Wave'))) }}">
    <meta property="og:description" content="{{ $seoMetaData['og_description'] ?? trim($__env->yieldContent('og_description', $__env->yieldContent('meta_description', $siteSettings?->localized('default_meta_description')))) }}">
    @if(!empty($seoMetaData['og_image']))
        <meta property="og:image" content="{{ $seoMetaData['og_image'] }}">
    @endif
    @if(!empty($seoMetaData['twitter_title']))
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoMetaData['twitter_title'] }}">
        <meta name="twitter:description" content="{{ $seoMetaData['twitter_description'] }}">
        @if(!empty($seoMetaData['twitter_image']))
            <meta name="twitter:image" content="{{ $seoMetaData['twitter_image'] }}">
        @endif
    @endif
    @if(!empty($seoMetaData['hreflang_en_url']))
        <link rel="alternate" hreflang="en" href="{{ $seoMetaData['hreflang_en_url'] }}">
    @endif
    @if(!empty($seoMetaData['hreflang_ar_url']))
        <link rel="alternate" hreflang="ar" href="{{ $seoMetaData['hreflang_ar_url'] }}">
    @endif
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    <link rel="icon" href="{{ $siteSettings?->favicon_path ? asset('storage/' . $siteSettings->favicon_path) : '' }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/site.css') }}" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    @php($trackingContext = $trackingContext ?? \App\Support\TrackingManager::contextFromRequest(request()))
    @php($pageTrackingIntegrations = collect($pageTrackingIntegrations ?? []))
    @php($headTrackingIntegrations = collect(\App\Support\TrackingManager::resolveForPlacement('head', $trackingContext))->merge($pageTrackingIntegrations->filter(fn ($integration) => in_array($integration->placement, ['standard', 'head'], true)))->unique('id')->values())
    @php($bodyOpenTrackingIntegrations = collect(\App\Support\TrackingManager::resolveForPlacement('body_open', $trackingContext))->merge($pageTrackingIntegrations->filter(fn ($integration) => $integration->integration_type === \App\Models\TrackingIntegration::TYPE_GTM && in_array($integration->placement, ['standard', 'body_open'], true)))->unique('id')->values())
    @php($bodyEndTrackingIntegrations = collect(\App\Support\TrackingManager::resolveForPlacement('body_end', $trackingContext))->merge($pageTrackingIntegrations->filter(fn ($integration) => $integration->integration_type === \App\Models\TrackingIntegration::TYPE_CUSTOM_SCRIPT && $integration->placement === 'body_end'))->unique('id')->values())
    @php($metaPageViewEventId = (string) \Illuminate\Support\Str::uuid())
    @php($metaEventSourceUrl = request()->fullUrl())
    @php($metaPageName = trim($__env->yieldContent('title', $siteSettings?->localized('site_name') ?? 'Travel Wave')))
    @php($metaCapiEnabled = $siteSettings?->metaConversionApiConfigured() ?? false)
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
    @include('partials.frontend.tracking-scripts', ['integrations' => $headTrackingIntegrations, 'placement' => 'head', 'metaPageViewEventId' => $metaPageViewEventId])
    @if(!empty($seoMetaData['schema']))
        @foreach($seoMetaData['schema'] as $schemaItem)
            <script type="application/ld+json">{!! json_encode($schemaItem, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
        @endforeach
    @endif
</head>
<body class="{{ $isRtl ? 'rtl' : 'ltr' }}">
    @include('partials.frontend.tracking-scripts', ['integrations' => $bodyOpenTrackingIntegrations, 'placement' => 'body_open'])
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
    @include('partials.frontend.floating-whatsapp')
    @include('partials.frontend.chatbot-widget')
    @include('partials.frontend.tracking-scripts', ['integrations' => $bodyEndTrackingIntegrations, 'placement' => 'body_end'])

    @if($metaCapiEnabled)
        <script>
            window.twMetaCapiConfig = {
                endpoint: @json(route('tracking.meta.events.store')),
                eventSourceUrl: @json($metaEventSourceUrl),
                pageName: @json($metaPageName),
                pageViewEventId: @json($metaPageViewEventId)
            };

            if (!window.twMetaSendServerEvent) {
                window.twMetaSendServerEvent = function (payload) {
                    if (!window.twMetaCapiConfig || !window.twMetaCapiConfig.endpoint) {
                        return;
                    }

                    try {
                        fetch(window.twMetaCapiConfig.endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload),
                            credentials: 'same-origin',
                            keepalive: true
                        }).catch(function () {});
                    } catch (error) {
                    }
                };
            }

            document.addEventListener('DOMContentLoaded', function () {
                if (window.twMetaCapiConfig?.pageViewEventId) {
                    window.twMetaSendServerEvent({
                        event_name: 'PageView',
                        event_id: window.twMetaCapiConfig.pageViewEventId,
                        event_source_url: window.twMetaCapiConfig.eventSourceUrl,
                        custom_data: {
                            page_name: window.twMetaCapiConfig.pageName
                        }
                    });
                }
            });

            document.addEventListener('submit', function (event) {
                const form = event.target;

                if (!(form instanceof HTMLFormElement) || !form.dataset.metaEventName) {
                    return;
                }

                const eventName = form.dataset.metaEventName;
                const eventIdField = form.querySelector('input[name="meta_event_id"]');
                const eventId = eventIdField?.value || crypto?.randomUUID?.() || String(Date.now());
                const customData = {
                    page_name: form.dataset.metaPageName || window.twMetaCapiConfig?.pageName || '',
                    form_name: form.dataset.metaFormName || '',
                    destination: form.dataset.metaDestination || '',
                    service_type: form.dataset.metaServiceType || ''
                };

                if (eventIdField && !eventIdField.value) {
                    eventIdField.value = eventId;
                }

                if (typeof window.twMetaTrackBrowser === 'function') {
                    window.twMetaTrackBrowser(eventName, customData, eventId, form.dataset.metaCustomEvent === '1');
                }
            });

            document.addEventListener('click', function (event) {
                const button = event.target.closest('[data-meta-whatsapp="1"]');

                if (!button) {
                    return;
                }

                const eventId = crypto?.randomUUID?.() || String(Date.now());
                const payload = {
                    page_name: button.dataset.metaPageName || window.twMetaCapiConfig?.pageName || '',
                    source_page: button.dataset.metaSourcePage || window.location.pathname
                };

                if (typeof window.twMetaTrackBrowser === 'function') {
                    window.twMetaTrackBrowser('WhatsAppClick', payload, eventId, true);
                }

                window.twMetaSendServerEvent({
                    event_name: 'WhatsAppClick',
                    event_id: eventId,
                    event_source_url: window.location.href,
                    custom_data: payload
                });
            });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
