<section class="tw-brand-page-hero" style="{{ !empty($section['background_image']) ? "background-image:linear-gradient(135deg, rgba(7, 27, 46, 0.86), rgba(17, 55, 93, 0.78)), url('" . $section['background_image'] . "');" : '' }}">
    <div class="container py-5">
        @if(!empty($section['breadcrumbs']))
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb tw-breadcrumb tw-brand-page-breadcrumb mb-0">
                    @foreach($section['breadcrumbs'] as $crumb)
                        @if(!$loop->last && !empty($crumb['url']))
                            <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        @endif
        <div class="tw-brand-page-hero-shell">
            @if(!empty($section['eyebrow']))
                <span class="tw-brand-page-eyebrow">{{ $section['eyebrow'] }}</span>
            @endif
            <h1 class="display-4 mb-3">{{ $section['title'] }}</h1>
            @if(!empty($section['subtitle']))
                <p class="lead mb-0">{{ $section['subtitle'] }}</p>
            @endif
        </div>
    </div>
</section>
