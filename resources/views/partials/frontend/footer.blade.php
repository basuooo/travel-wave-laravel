<footer class="tw-footer pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-start">
            <div class="col-md-6 col-xl-4">
                <h4 class="text-white">{{ $siteSettings?->localized('site_name') ?? 'Travel Wave' }}</h4>
                <p>{{ $siteSettings?->localized('footer_text') }}</p>
                <div class="d-flex flex-wrap gap-3 tw-footer-social">
                    @if($siteSettings?->facebook_url)<a href="{{ $siteSettings->facebook_url }}">Facebook</a>@endif
                    @if($siteSettings?->instagram_url)<a href="{{ $siteSettings->instagram_url }}">Instagram</a>@endif
                    @if($siteSettings?->youtube_url)<a href="{{ $siteSettings->youtube_url }}">YouTube</a>@endif
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <h5 class="text-white">{{ __('ui.contact') }}</h5>
                <p class="mb-1">{{ $siteSettings?->phone }}</p>
                <p class="mb-1">{{ $siteSettings?->contact_email }}</p>
                <p class="mb-0">{{ $siteSettings?->localized('address') }}</p>
            </div>
            <div class="col-md-12 col-xl-4">
                <h5 class="text-white">{{ __('ui.learn_more') }}</h5>
                <ul class="list-unstyled tw-footer-links mb-0">
                    @foreach ($footerMenuItems ?? [] as $item)
                        <li class="mb-2"><a href="{{ $item->url ?: ($item->route_name ? route($item->route_name) : '#') }}">{{ $item->localized('title') }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="border-top border-secondary mt-4 pt-3 small tw-footer-copy">
            {{ $siteSettings?->localized('copyright_text') }}
        </div>
    </div>
</footer>
