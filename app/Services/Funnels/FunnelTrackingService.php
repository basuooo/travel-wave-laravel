<?php

namespace App\Services\Funnels;

use App\Models\Funnel;
use App\Support\TrackingManager;

class FunnelTrackingService
{
    /**
     * Get tracking pixel code snippets for a funnel.
     */
    public function getHeaderScripts(Funnel $funnel): string
    {
        $settings = $funnel->tracking_settings ?? [];
        $output = [];

        // Meta Pixel
        if (! empty($settings['meta_pixel_id'])) {
            $pixelId = htmlspecialchars($settings['meta_pixel_id'], ENT_QUOTES);
            $output[] = "<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$pixelId}');
fbq('track', 'PageView');
</script>";
        }

        // Google Analytics 4 (GA4)
        if (! empty($settings['ga4_id'])) {
            $gaId = htmlspecialchars($settings['ga4_id'], ENT_QUOTES);
            $output[] = "<!-- Google Analytics GA4 -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id={$gaId}\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$gaId}');
</script>";
        }

        // Google Tag Manager
        if (! empty($settings['gtm_id'])) {
            $gtmId = htmlspecialchars($settings['gtm_id'], ENT_QUOTES);
            $output[] = "<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$gtmId}');</script>";
        }

        // LinkedIn Insight Tag
        if (! empty($settings['linkedin_tag_id'])) {
            $linkId = htmlspecialchars($settings['linkedin_tag_id'], ENT_QUOTES);
            $output[] = "<!-- LinkedIn Insight Tag -->
<script type=\"text/javascript\">
_linkedin_partner_id = \"{$linkId}\";
window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script><script type=\"text/javascript\">
(function(l) {
if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
window.lintrk.q=[]}
var s = document.getElementsByTagName(\"script\")[0];
var b = document.createElement(\"script\");
b.type = \"text/javascript\";b.async = true;
b.src = \"https://snap.licdn.com/li.lms-analytics/insight.min.js\";
s.parentNode.insertBefore(b, s);})(window.lintrk);
</script>";
        }

        // TikTok Pixel
        if (! empty($settings['tiktok_pixel_id'])) {
            $tiktokId = htmlspecialchars($settings['tiktok_pixel_id'], ENT_QUOTES);
            $output[] = "<!-- TikTok Pixel -->
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=[\"page\",\"track\",\"identify\",\"instances\",\"debug\",\"on\",\"off\",\"once\",\"ready\",\"alias\",\"group\",\"enableCookie\",\"disableCookie\"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq.methods[t]||[],n=0;n<e.length;n++)ttq.setAndDefer(e,e[n]);return e},ttq.load=function(e,n){var i=\"https://analytics.tiktok.com/i18n/pixel/events.js\";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement(\"script\");o.type=\"text/javascript\",o.async=!0,o.src=i+\"?sdkid=\"+e+\"&lib=\"+t;var a=document.getElementsByTagName(\"script\")[0];a.parentNode.insertBefore(o,a)};
  ttq.load('{$tiktokId}');
  ttq.page();
}(window, document, 'ttq');
</script>";
        }

        // Snap Pixel
        if (! empty($settings['snap_pixel_id'])) {
            $snapId = htmlspecialchars($settings['snap_pixel_id'], ENT_QUOTES);
            $output[] = "<!-- Snap Pixel Code -->
<script type='text/javascript'>
(function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function()
{a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
a.queue=[];var s='script';r=t.createElement(s);r.async=!0;
r.src=n;var u=t.getElementsByTagName(s)[0];
u.parentNode.insertBefore(r,u);})(window,document,
'https://sc-static.net/scevent.min.js');
snaptr('init', '{$snapId}');
snaptr('track', 'PAGE_VIEW');
</script>";
        }

        // Custom HTML/JS in Header
        if (! empty($settings['custom_head_script'])) {
            $output[] = $settings['custom_head_script'];
        }

        return implode("\n", $output);
    }

    /**
     * Get body/footer scripts snippet.
     */
    public function getBodyScripts(Funnel $funnel): string
    {
        $settings = $funnel->tracking_settings ?? [];
        $output = [];

        if (! empty($settings['custom_body_script'])) {
            $output[] = $settings['custom_body_script'];
        }

        return implode("\n", $output);
    }

    /**
     * Get JS event dispatcher function for client side.
     */
    public function getEventDispatcherJs(): string
    {
        return "<script>
window.trackFunnelEvent = function(eventName, eventData) {
    console.log('[Funnel Event]', eventName, eventData);
    if (window.fbq) { window.fbq('trackCustom', eventName, eventData); }
    if (window.gtag) { window.gtag('event', eventName, eventData); }
    if (window.ttq) { window.ttq.track(eventName, eventData); }
    if (window.snaptr) { window.snaptr('track', eventName, eventData); }
};
</script>";
    }
}
