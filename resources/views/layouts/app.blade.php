<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $siteSettings?->localized('site_name') ?? 'Travel Wave')</title>
    <meta name="description" content="@yield('meta_description', $siteSettings?->localized('default_meta_description'))">
    <link rel="icon" href="{{ $siteSettings?->favicon_path ? asset('storage/' . $siteSettings->favicon_path) : '' }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/site.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.frontend.header')

    <main class="pb-5">
        @if (session('success'))
            <div class="container pt-4">
                <div class="alert alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.frontend.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
