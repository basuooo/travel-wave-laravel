@php
    $settings = $siteSettings;
    $isVisible = $settings?->shouldRenderFloatingWhatsapp(request()) ?? false;
    $buttonText = $settings?->floatingWhatsappButtonText();
    $buttonUrl = $settings?->floatingWhatsappUrl();
    $position = $settings?->floating_whatsapp_position ?: 'bottom_right';
    $animation = $settings?->floating_whatsapp_animation_style ?: 'pulse';
    $speed = max(1000, min(10000, (int) ($settings?->floating_whatsapp_animation_speed ?: 3200)));
    $background = $settings?->floating_whatsapp_background_color ?: '#25D366';
@endphp

@if($isVisible && $buttonUrl)
    <a
        href="{{ $buttonUrl }}"
        class="tw-floating-whatsapp tw-floating-whatsapp-{{ $position }} tw-floating-whatsapp-{{ $animation }}{{ ($settings?->floating_whatsapp_show_desktop ?? true) ? '' : ' tw-floating-whatsapp-hide-desktop' }}{{ ($settings?->floating_whatsapp_show_mobile ?? true) ? '' : ' tw-floating-whatsapp-hide-mobile' }}"
        style="--tw-whatsapp-bg: {{ $background }}; --tw-whatsapp-animation-speed: {{ $speed }}ms;"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="{{ $buttonText ?: 'WhatsApp Travel Wave' }}"
        data-meta-whatsapp="1"
        data-meta-page-name="{{ trim($__env->yieldContent('title', $siteSettings?->localized('site_name') ?? 'Travel Wave')) }}"
        data-meta-source-page="{{ request()->path() }}"
    >
        @if($settings?->floating_whatsapp_show_icon ?? true)
            <span class="tw-floating-whatsapp-icon" aria-hidden="true">
                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 3.2C9.04 3.2 3.4 8.72 3.4 15.54c0 2.4.72 4.72 2.08 6.72L4 29l7.06-1.84a12.81 12.81 0 0 0 4.94.98c6.96 0 12.6-5.52 12.6-12.34C28.6 8.98 22.96 3.2 16 3.2Zm0 22.88c-1.5 0-2.98-.28-4.36-.84l-.32-.12-4.2 1.08 1.12-4.02-.2-.34a10.66 10.66 0 0 1-1.64-5.62c0-5.82 4.82-10.56 10.76-10.56s10.76 4.74 10.76 10.56S21.94 26.08 16 26.08Zm5.9-7.94c-.32-.16-1.9-.92-2.2-1.02-.3-.1-.52-.16-.74.16-.22.32-.84 1.02-1.04 1.22-.2.2-.38.22-.7.08-.32-.16-1.36-.5-2.58-1.58-.96-.84-1.6-1.88-1.78-2.2-.18-.32-.02-.5.14-.66.14-.14.32-.38.48-.56.16-.18.22-.32.34-.54.12-.22.06-.4-.02-.56-.08-.16-.74-1.76-1.02-2.42-.26-.62-.54-.54-.74-.54h-.62c-.22 0-.56.08-.86.4-.3.32-1.14 1.1-1.14 2.7 0 1.6 1.16 3.14 1.32 3.36.16.22 2.26 3.56 5.48 4.84.76.32 1.36.5 1.82.64.76.24 1.46.2 2.02.12.62-.1 1.9-.78 2.16-1.54.26-.76.26-1.42.18-1.56-.08-.14-.28-.22-.6-.38Z" fill="currentColor"/>
                </svg>
            </span>
        @endif
        @if($buttonText)
            <span class="tw-floating-whatsapp-text">{{ $buttonText }}</span>
        @endif
    </a>
@endif
