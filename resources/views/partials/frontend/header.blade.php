@php($headerPhoneUrl = $siteSettings?->phoneCallUrl($siteSettings?->phone))
@php($headerPhoneDisplay = ltrim((string) ($siteSettings?->phone ?? ''), '+'))
@php($localeHeaderAlignment = [
    'logo' => $siteSettings?->headerLogoPositionForLocale($currentLocale) ?? ($currentLocale === 'ar' ? 'right' : 'left'),
    'menu' => $siteSettings?->headerMenuPositionForLocale($currentLocale) ?? ($currentLocale === 'ar' ? 'right' : 'left'),
])
<nav class="navbar navbar-expand-lg navbar-dark tw-navbar {{ ($siteSettings?->header_is_sticky ?? true) ? 'sticky-top' : '' }}">
    <div class="container tw-navbar-shell tw-navbar-shell--logo-{{ $localeHeaderAlignment['logo'] }}" style="padding-top: {{ $siteSettings?->header_vertical_padding ?? 8 }}px; padding-bottom: {{ $siteSettings?->header_vertical_padding ?? 8 }}px;">
        <a class="navbar-brand tw-navbar-brand tw-navbar-brand--logo-{{ $localeHeaderAlignment['logo'] }} d-flex align-items-center" href="{{ route('home') }}" aria-label="{{ $siteSettings?->localized('site_name') ?? 'Travel Wave' }}">
            @if($siteSettings?->header_logo_enabled ?? true)
                @include('partials.frontend.logo', ['variant' => 'header'])
            @else
                <span class="tw-brand-wordmark">{{ $siteSettings?->localized('site_name') ?? 'Travel Wave' }}</span>
            @endif
        </a>
        <button class="navbar-toggler tw-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse tw-navbar-collapse-shell tw-navbar-collapse-shell--menu-{{ $localeHeaderAlignment['menu'] }}" id="navbarContent">
            <ul class="navbar-nav tw-navbar-nav tw-navbar-nav-primary tw-navbar-nav-primary--menu-{{ $localeHeaderAlignment['menu'] }} align-items-lg-center gap-lg-2">
                @foreach ($headerMenuItems ?? [] as $item)
                    <li class="nav-item dropdown">
                        @if ($item->children->isNotEmpty())
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ $item->localized('title') }}</a>
                            <ul class="dropdown-menu tw-navbar-dropdown">
                                @foreach ($item->children as $child)
                                    <li><a class="dropdown-item" href="{{ $child->url ?: ($child->route_name ? route($child->route_name) : '#') }}">{{ $child->localized('title') }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            <a class="nav-link" href="{{ $item->url ?: ($item->route_name ? route($item->route_name) : '#') }}">{{ $item->localized('title') }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div class="tw-navbar-tools tw-navbar-tools--menu-{{ $localeHeaderAlignment['menu'] }}">
                @if($siteSettings?->phone && $headerPhoneUrl)
                    <div class="tw-navbar-phone-item">
                        <a class="tw-navbar-phone" href="{{ $headerPhoneUrl }}" dir="ltr" aria-label="Call {{ $siteSettings?->phone }}">
                            <span class="tw-navbar-phone-icon" aria-hidden="true">+</span>
                            <span>{{ $headerPhoneDisplay }}</span>
                        </a>
                    </div>
                @endif
                <div class="dropdown tw-navbar-locale-dropdown d-none d-lg-block" aria-label="Language switcher">
                    <button
                        class="btn tw-navbar-locale-trigger dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <span class="tw-navbar-locale-trigger-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" focusable="false">
                                <path d="M12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3Z" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M3.75 9H20.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M3.75 15H20.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M11.9998 3C14.0045 5.1911 15.1445 8.03434 15.2098 11.0038C15.2751 13.9732 14.2612 16.8638 12.3547 19.1408" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M12.0002 3C9.99554 5.1911 8.85547 8.03434 8.79018 11.0038C8.7249 13.9732 9.7388 16.8638 11.6453 19.1408" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="tw-navbar-locale-trigger-text">
                            {{ $currentLocale === 'ar' ? __('ui.language_arabic') : __('ui.language_english') }}
                        </span>
                    </button>
                    <ul class="dropdown-menu tw-navbar-locale-menu">
                        <li>
                            <a class="dropdown-item tw-navbar-locale-option {{ $currentLocale === 'en' ? 'is-active' : '' }}" href="{{ route('locale.switch', 'en') }}">
                                <span>{{ __('ui.language_english') }}</span>
                                @if($currentLocale === 'en')
                                    <span class="tw-navbar-locale-option-mark" aria-hidden="true">✓</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item tw-navbar-locale-option {{ $currentLocale === 'ar' ? 'is-active' : '' }}" href="{{ route('locale.switch', 'ar') }}">
                                <span>{{ __('ui.language_arabic') }}</span>
                                @if($currentLocale === 'ar')
                                    <span class="tw-navbar-locale-option-mark" aria-hidden="true">✓</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tw-navbar-locale d-lg-none" aria-label="Language switcher">
                    <a
                        class="tw-navbar-locale-link {{ $currentLocale === 'en' ? 'is-active' : '' }}"
                        href="{{ route('locale.switch', 'en') }}"
                        @if($currentLocale === 'en') aria-current="true" @endif
                    >
                        {{ __('ui.language_english') }}
                    </a>
                    <a
                        class="tw-navbar-locale-link {{ $currentLocale === 'ar' ? 'is-active' : '' }}"
                        href="{{ route('locale.switch', 'ar') }}"
                        @if($currentLocale === 'ar') aria-current="true" @endif
                    >
                        {{ __('ui.language_arabic') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

