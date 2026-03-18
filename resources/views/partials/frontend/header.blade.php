<nav class="navbar navbar-expand-lg navbar-dark tw-navbar sticky-top">
    <div class="container py-2">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            {{ $siteSettings?->localized('site_name') ?? 'Travel Wave' }}<span class="tw-brand-dot">.</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                @foreach ($headerMenuItems ?? [] as $item)
                    <li class="nav-item dropdown">
                        @if ($item->children->isNotEmpty())
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ $item->localized('title') }}</a>
                            <ul class="dropdown-menu">
                                @foreach ($item->children as $child)
                                    <li><a class="dropdown-item" href="{{ $child->url ?: ($child->route_name ? route($child->route_name) : '#') }}">{{ $child->localized('title') }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            <a class="nav-link" href="{{ $item->url ?: ($item->route_name ? route($item->route_name) : '#') }}">{{ $item->localized('title') }}</a>
                        @endif
                    </li>
                @endforeach
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-sm tw-btn-outline" href="{{ route('locale.switch', $currentLocale === 'ar' ? 'en' : 'ar') }}">
                        {{ $currentLocale === 'ar' ? 'English' : 'العربية' }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
