<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->seo_title_ar ?: ($page->title_ar ?: $page->internal_name) }}</title>
    <meta name="description" content="{{ $page->seo_description_ar ?: '' }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @if($page->og_image)
        <meta property="og:image" content="{{ asset('storage/' . $page->og_image) }}">
    @endif

    {!! $page->custom_html_head !!}

    @if(filled($page->custom_css))
        <style>
            {!! $page->custom_css !!}
        </style>
    @endif
</head>
<body class="bg-white text-dark">

    <main class="landing-page-new-wrapper">
        @forelse($elements as $el)
            <div class="landing-section-item" id="{{ $el['id'] ?? '' }}">
                {!! $el['html'] ?? '' !!}
            </div>
        @empty
            <div class="container py-5 text-center">
                <h1 class="fw-bold">{{ $page->title_ar ?: $page->internal_name }}</h1>
                <p class="lead text-muted">مرحباً بك في صفحة الهبوط.</p>
            </div>
        @endforelse
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @if(filled($page->custom_js))
        <script>
            {!! $page->custom_js !!}
        </script>
    @endif
</body>
</html>
