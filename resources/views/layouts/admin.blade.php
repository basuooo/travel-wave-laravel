<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('admin.admin_dashboard'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
</head>
<body class="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 admin-sidebar text-white p-3">
            <div class="fw-bold fs-4 mb-4">Travel Wave CMS</div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.dashboard') }}">{{ __('admin.dashboard') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.settings.edit') }}">{{ __('admin.brand_settings') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.header-settings.edit') }}">{{ __('admin.header_settings') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.footer-settings.edit') }}">{{ __('admin.footer_settings') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.floating-whatsapp-settings.edit') }}">{{ __('admin.floating_whatsapp') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.meta-conversion-api-settings.edit') }}">{{ __('admin.meta_conversion_api') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.seo.dashboard') }}">{{ __('admin.seo_manager') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.tracking-integrations.index') }}">{{ __('admin.tracking_manager') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.marketing-landing-pages.index') }}">{{ __('admin.marketing_manager') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.pages.index') }}">{{ __('admin.pages') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.visa-categories.index') }}">{{ __('admin.visa_categories') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.visa-countries.index') }}">{{ __('admin.visa_destinations') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.destinations.index') }}">{{ __('admin.destinations') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.hero-slides.index') }}">{{ __('admin.hero_slider') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.home-country-strip.index') }}">{{ __('admin.homepage_country_strip') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.forms.index') }}">{{ __('admin.forms_manager') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.map-sections.index') }}">{{ __('admin.maps_manager') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.forms.submissions') }}">{{ __('admin.form_submissions') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.blog-categories.index') }}">{{ __('admin.blog_categories') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.blog-posts.index') }}">{{ __('admin.blog_posts') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.testimonials.index') }}">{{ __('admin.testimonials') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.menu-items.index') }}">{{ __('admin.navigation') }}</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.inquiries.index') }}">{{ __('admin.inquiries') }}</a>
                <form method="post" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf
                    <button class="btn btn-outline-light w-100">{{ __('admin.logout') }}</button>
                </form>
            </nav>
        </aside>
        <div class="col-lg-10 px-0">
            <div class="admin-topbar border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 mb-0">@yield('page_title', __('admin.dashboard'))</h1>
                    <div class="text-muted small">@yield('page_description')</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="get" action="{{ route('admin.search') }}" class="d-none d-md-flex">
                        <input type="text" name="q" class="form-control" placeholder="{{ __('admin.search_placeholder') }}" value="{{ request('q') }}">
                    </form>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('locale.switch', 'en') }}">EN</a>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('locale.switch', 'ar') }}">AR</a>
                    <a class="btn btn-primary" href="{{ route('home') }}" target="_blank">{{ __('admin.view_website') }}</a>
                </div>
            </div>
            <div class="p-4">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
