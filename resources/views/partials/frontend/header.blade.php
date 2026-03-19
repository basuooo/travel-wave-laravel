<nav class="navbar navbar-expand-lg navbar-dark tw-navbar {{ ($siteSettings?->header_is_sticky ?? true) ? 'sticky-top' : '' }}">
    <div class="container tw-navbar-shell" style="padding-top: {{ $siteSettings?->header_vertical_padding ?? 8 }}px; padding-bottom: {{ $siteSettings?->header_vertical_padding ?? 8 }}px;">
        <a class="navbar-brand tw-navbar-brand d-flex align-items-center" href="{{ route('home') }}" aria-label="{{ $siteSettings?->localized('site_name') ?? 'Travel Wave' }}">
            @if($siteSettings?->header_logo_enabled ?? true)
                @include('partials.frontend.logo', ['variant' => 'header'])
            @else
                <span class="tw-brand-wordmark">{{ $siteSettings?->localized('site_name') ?? 'Travel Wave' }}</span>
            @endif
        </a>
        <button class="navbar-toggler tw-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav tw-navbar-nav {{ $isRtl ? 'me-auto' : 'ms-auto' }} align-items-lg-center gap-lg-2">
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
                <li class="nav-item tw-navbar-locale ms-lg-3">
                    <a class="btn btn-sm tw-btn-outline tw-locale-btn" href="{{ route('locale.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}">
                        {{ $currentLocale === 'ar' ? 'English' : 'العربية' }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
