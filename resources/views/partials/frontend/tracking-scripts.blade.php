@foreach($integrations as $integration)
    @switch($integration->integration_type)
        @case(\App\Models\TrackingIntegration::TYPE_GTM)
            @if($placement === 'head' && $integration->tracking_code)
                <script>
                    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                    })(window,document,'script','dataLayer','{{ $integration->tracking_code }}');
                </script>
            @endif
            @if($placement === 'body_open' && $integration->tracking_code)
                <noscript>
                    <iframe src="https://www.googletagmanager.com/ns.html?id={{ $integration->tracking_code }}"
                            height="0" width="0" style="display:none;visibility:hidden"></iframe>
                </noscript>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_GA4)
            @if($placement === 'head' && $integration->tracking_code)
                <script async src="https://www.googletagmanager.com/gtag/js?id={{ $integration->tracking_code }}"></script>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config', '{{ $integration->tracking_code }}');
                </script>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_META_PIXEL)
            @if($placement === 'head' && $integration->tracking_code)
                <script>
                    !function(f,b,e,v,n,t,s)
                    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                    n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t,s)}(window, document,'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
                    window.twMetaPixelIds = window.twMetaPixelIds || [];
                    if (!window.twMetaPixelIds.includes('{{ $integration->tracking_code }}')) {
                        window.twMetaPixelIds.push('{{ $integration->tracking_code }}');
                    }
                    fbq('init', '{{ $integration->tracking_code }}');
                    if (!window.twMetaTrackBrowser) {
                        window.twMetaTrackBrowser = function (eventName, params, eventId, isCustomEvent) {
                            if (typeof fbq !== 'function') {
                                return;
                            }

                            const options = eventId ? { eventID: eventId } : {};

                            if (isCustomEvent) {
                                fbq('trackCustom', eventName, params || {}, options);
                                return;
                            }

                            fbq('track', eventName, params || {}, options);
                        };
                    }
                    window.twMetaTrackBrowser('PageView', {}, @json($metaPageViewEventId ?? null), false);
                </script>
                <noscript>
                    <img height="1" width="1" style="display:none"
                         src="https://www.facebook.com/tr?id={{ $integration->tracking_code }}&ev=PageView&noscript=1"/>
                </noscript>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_CUSTOM_SCRIPT)
            @if($integration->script_code)
                {!! $integration->script_code !!}
            @endif
            @break
    @endswitch
@endforeach
