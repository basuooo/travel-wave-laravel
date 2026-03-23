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

        @case(\App\Models\TrackingIntegration::TYPE_TIKTOK_PIXEL)
            @if($placement === 'head' && $integration->tracking_code)
                <script>
                    !function (w, d, t) {
                        w.TiktokAnalyticsObject = t;
                        var ttq = w[t] = w[t] || [];
                        ttq.methods = ["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];
                        ttq.setAndDefer = function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
                        for (var i = 0; i < ttq.methods.length; i++) { ttq.setAndDefer(ttq, ttq.methods[i]); }
                        ttq.instance = function(t){for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) { ttq.setAndDefer(e, ttq.methods[n]); } return e;};
                        ttq.load = function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{};ttq._i[e]=[];ttq._i[e]._u=r;ttq._t=ttq._t||{};ttq._t[e]=+new Date;ttq._o=ttq._o||{};ttq._o[e]=n||{};var o=d.createElement("script");o.type="text/javascript";o.async=!0;o.src=r+"?sdkid="+e+"&lib="+t;var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a);};
                        ttq.load('{{ $integration->tracking_code }}');
                        ttq.page();
                    }(window, document, 'ttq');
                </script>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_SNAP_PIXEL)
            @if($placement === 'head' && $integration->tracking_code)
                <script>
                    (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function()
                    {a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
                    a.queue=[];var s='script';r=t.createElement(s);r.async=!0;
                    r.src=n;var u=t.getElementsByTagName(s)[0];
                    u.parentNode.insertBefore(r,u);})(window,document,
                    'https://sc-static.net/scevent.min.js');

                    snaptr('init', '{{ $integration->tracking_code }}');
                    snaptr('track', 'PAGE_VIEW');
                </script>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_X_PIXEL)
            @if($placement === 'head' && $integration->tracking_code)
                <script>
                    !function(e,t,n,s,u,a){
                        e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);},
                        s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='https://static.ads-twitter.com/uwt.js',
                        a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))
                    }(window,document,'script');
                    twq('config','{{ $integration->tracking_code }}');
                </script>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_LINKEDIN_INSIGHT)
            @if($placement === 'head' && $integration->tracking_code)
                <script type="text/javascript">
                    window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
                    window._linkedin_data_partner_ids.push('{{ $integration->tracking_code }}');
                </script>
                <script type="text/javascript">
                    (function(l) {
                        if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])}; window.lintrk.q=[]}
                        var s = document.getElementsByTagName("script")[0];
                        var b = document.createElement("script");
                        b.type = "text/javascript"; b.async = true;
                        b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
                        s.parentNode.insertBefore(b, s);
                    })(window.lintrk);
                </script>
                <noscript>
                    <img height="1" width="1" style="display:none;" alt=""
                         src="https://px.ads.linkedin.com/collect/?pid={{ $integration->tracking_code }}&fmt=gif" />
                </noscript>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_PINTEREST_TAG)
            @if($placement === 'head' && $integration->tracking_code)
                <script>
                    !function(e){if(!window.pintrk){window.pintrk = function () {
                    window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
                    n=window.pintrk;n.queue=[],n.version="3.0";var
                    t=document.createElement("script");t.async=!0,t.src=e;var
                    r=document.getElementsByTagName("script")[0];
                    r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");
                    pintrk('load', '{{ $integration->tracking_code }}');
                    pintrk('page');
                </script>
                <noscript>
                    <img height="1" width="1" style="display:none;" alt=""
                         src="https://ct.pinterest.com/v3/?event=init&tid={{ $integration->tracking_code }}&noscript=1" />
                </noscript>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_GOOGLE_ADS)
            @if($placement === 'head' && $integration->tracking_code)
                <script async src="https://www.googletagmanager.com/gtag/js?id={{ str_starts_with($integration->tracking_code, 'AW-') ? $integration->tracking_code : 'AW-' . $integration->tracking_code }}"></script>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config', '{{ str_starts_with($integration->tracking_code, 'AW-') ? $integration->tracking_code : 'AW-' . $integration->tracking_code }}');
                </script>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_MICROSOFT_CLARITY)
            @if($placement === 'head' && $integration->tracking_code)
                <script type="text/javascript">
                    (function(c,l,a,r,i,t,y){
                        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
                    })(window, document, "clarity", "script", "{{ $integration->tracking_code }}");
                </script>
            @endif
            @break

        @case(\App\Models\TrackingIntegration::TYPE_CUSTOM_SCRIPT)
            @if($integration->script_code)
                {!! $integration->script_code !!}
            @endif
            @break
    @endswitch
@endforeach
