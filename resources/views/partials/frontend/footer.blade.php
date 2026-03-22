@php($footerLinks = collect($siteSettings?->footer_quick_links ?: [])->sortBy('sort_order')->values())
@php($socialLinks = collect([
    ['key' => 'facebook', 'url' => $siteSettings?->facebook_url, 'label' => 'Facebook'],
    ['key' => 'instagram', 'url' => $siteSettings?->instagram_url, 'label' => 'Instagram'],
    ['key' => 'twitter', 'url' => $siteSettings?->twitter_url, 'label' => 'X / Twitter'],
    ['key' => 'tiktok', 'url' => $siteSettings?->tiktok_url, 'label' => 'TikTok'],
    ['key' => 'youtube', 'url' => $siteSettings?->youtube_url, 'label' => 'YouTube'],
    ['key' => 'linkedin', 'url' => $siteSettings?->linkedin_url, 'label' => 'LinkedIn'],
    ['key' => 'snapchat', 'url' => $siteSettings?->snapchat_url, 'label' => 'Snapchat'],
    ['key' => 'telegram', 'url' => $siteSettings?->telegram_url, 'label' => 'Telegram'],
])->filter(fn ($item) => filled($item['url']))->values())
@php($footerWhatsappUrl = $siteSettings?->whatsappChatUrl())
@php($primaryPhoneUrl = $siteSettings?->phoneCallUrl($siteSettings?->phone))
@php($secondaryPhoneUrl = $siteSettings?->phoneCallUrl($siteSettings?->secondary_phone))
<footer class="tw-footer mt-5" style="padding-top: {{ $siteSettings?->footer_vertical_padding ?? 80 }}px; padding-bottom: {{ max(32, (int) (($siteSettings?->footer_vertical_padding ?? 80) / 2)) }}px;">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-start">
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('home') }}" class="d-inline-flex align-items-center mb-3" aria-label="{{ $siteSettings?->localized('site_name') ?? 'Travel Wave' }}">
                    @include('partials.frontend.logo', ['variant' => 'footer', 'className' => 'tw-footer-logo'])
                </a>
                <p>{{ $siteSettings?->localized('footer_text') }}</p>
                @if($socialLinks->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 tw-footer-social" aria-label="Social media links">
                        @foreach($socialLinks as $item)
                            <a href="{{ $item['url'] }}"
                               class="tw-footer-social-link"
                               target="_blank"
                               rel="noopener noreferrer"
                               aria-label="{{ $item['label'] }}">
                                @switch($item['key'])
                                    @case('facebook')
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M13.5 21v-7h2.4l.4-3h-2.8V9.1c0-.9.3-1.6 1.6-1.6H16V4.8c-.3 0-1-.1-1.9-.1-1.9 0-3.2 1.2-3.2 3.5V11H8.5v3H11v7h2.5Z"/></svg>
                                        @break
                                    @case('instagram')
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4Zm0 2.2A1.8 1.8 0 0 0 5.2 7v10c0 1 .8 1.8 1.8 1.8h10c1 0 1.8-.8 1.8-1.8V7c0-1-.8-1.8-1.8-1.8H7Zm10.3 1.6a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2ZM12 8a4 4 0 1 1 0 8.1A4 4 0 0 1 12 8Zm0 2.2a1.8 1.8 0 1 0 0 3.6 1.8 1.8 0 0 0 0-3.6Z"/></svg>
                                        @break
                                    @case('tiktok')
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M14.8 3c.2 1.3 1 2.4 2.2 3.1.8.5 1.7.8 2.6.8V9.6c-1.3 0-2.7-.4-3.8-1.2v6.1a5.3 5.3 0 1 1-5.3-5.3c.3 0 .6 0 .9.1v2.8a2.4 2.4 0 1 0 1.6 2.3V3h1.8Z"/></svg>
                                        @break
                                    @case('twitter')
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M18.9 3H22l-6.8 7.8 8 10.2h-6.3l-4.9-6.2-5.4 6.2H3.5l7.3-8.3L3.1 3h6.4L14 8.8 18.9 3Zm-1.1 16h1.7L8.6 4.9H6.8L17.8 19Z"/></svg>
                                        @break
                                    @case('youtube')
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21.6 8.2c-.2-1-.9-1.7-1.8-1.9C18.2 6 12 6 12 6s-6.2 0-7.8.3c-1 .2-1.6.9-1.8 1.9C2 9.8 2 12 2 12s0 2.2.4 3.8c.2 1 .9 1.7 1.8 1.9 1.6.3 7.8.3 7.8.3s6.2 0 7.8-.3c1-.2 1.6-.9 1.8-1.9.4-1.6.4-3.8.4-3.8s0-2.2-.4-3.8ZM10 15.3V8.7l5.2 3.3-5.2 3.3Z"/></svg>
                                        @break
                                    @case('linkedin')
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.4 8.2a1.7 1.7 0 1 1 0-3.4 1.7 1.7 0 0 1 0 3.4ZM4.9 9.8H8V20H4.9V9.8Zm5 0h3v1.4h.1c.4-.8 1.4-1.7 3-1.7 3.2 0 3.8 2.1 3.8 4.8V20h-3.1v-4.9c0-1.2 0-2.6-1.6-2.6s-1.8 1.2-1.8 2.5V20H9.9V9.8Z"/></svg>
                                        @break
                                    @case('snapchat')
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 3.2c2.6 0 4.4 2 4.4 4.5v2c.3.2.7.5 1.3.7.5.2.9.5.9 1 0 .8-1 .9-1.7 1.2-.3.1-.4.3-.4.5-.1.8.5 1.4 1.4 1.5.3 0 .4.2.4.4 0 .4-.5.8-1.3.9-.7.1-1.2.3-1.5.9-.5 1-1.7 1.6-3.5 1.6s-3-.6-3.5-1.6c-.3-.6-.8-.8-1.5-.9-.8-.1-1.3-.5-1.3-.9 0-.2.1-.4.4-.4.9-.1 1.5-.7 1.4-1.5 0-.2-.1-.4-.4-.5-.7-.3-1.7-.4-1.7-1.2 0-.5.4-.8.9-1 .6-.2 1-.5 1.3-.7v-2C7.6 5.2 9.4 3.2 12 3.2Z"/></svg>
                                        @break
                                    @case('telegram')
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.7 4.1 2.9 11c-1.2.5-1.2 1.1-.2 1.4l4.6 1.4 1.8 5.5c.2.6.1.9.8.9.5 0 .7-.2 1-.5l2.3-2.2 4.8 3.5c.9.5 1.5.3 1.7-.8l3-14.3c.3-1.3-.5-1.8-1.4-1.4Zm-3 3.2-8.4 7.6-.3 3 1.4-4.4 7.3-6.9c.3-.3-.1-.5-.4-.3Z"/></svg>
                                        @break
                                @endswitch
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-md-6 col-xl-4">
                <h5 class="text-white">{{ __('ui.contact') }}</h5>
                @if($siteSettings?->phone && $primaryPhoneUrl)
                    <p class="mb-1">
                        <a href="{{ $primaryPhoneUrl }}" class="tw-footer-contact-link" dir="ltr">{{ $siteSettings?->phone }}</a>
                    </p>
                @endif
                @if($siteSettings?->secondary_phone && $secondaryPhoneUrl)
                    <p class="mb-1">
                        <a href="{{ $secondaryPhoneUrl }}" class="tw-footer-contact-link" dir="ltr">{{ $siteSettings?->secondary_phone }}</a>
                    </p>
                @endif
                <p class="mb-1">{{ $siteSettings?->contact_email }}</p>
                @if($siteSettings?->whatsapp_number && $footerWhatsappUrl)
                    <p class="mb-1">
                        <a href="{{ $footerWhatsappUrl }}" class="tw-footer-contact-link tw-footer-whatsapp-link" target="_blank" rel="noopener noreferrer">
                            <span>WhatsApp:</span>
                            <span>{{ $siteSettings?->whatsapp_number }}</span>
                        </a>
                    </p>
                @endif
                <p class="mb-0">{{ $siteSettings?->localized('address') }}</p>
            </div>
            <div class="col-md-12 col-xl-4">
                <h5 class="text-white">{{ __('ui.learn_more') }}</h5>
                <ul class="list-unstyled tw-footer-links mb-0">
                    @forelse ($footerLinks as $item)
                        <li class="mb-2"><a href="{{ $item['url'] ?? '#' }}">{{ app()->getLocale() === 'ar' ? ($item['title_ar'] ?? '') : ($item['title_en'] ?? '') }}</a></li>
                    @empty
                        @foreach ($footerMenuItems ?? [] as $item)
                            <li class="mb-2"><a href="{{ $item->url ?: ($item->route_name ? route($item->route_name) : '#') }}">{{ $item->localized('title') }}</a></li>
                        @endforeach
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="border-top border-secondary mt-4 pt-3 small tw-footer-copy">
            {{ $siteSettings?->localized('copyright_text') }}
        </div>
    </div>
</footer>
