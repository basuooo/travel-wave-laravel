<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 admin-sidebar text-white p-3">
            <div class="fw-bold fs-4 mb-4">Travel Wave CMS</div>
            <nav class="nav flex-column gap-1">
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.settings.edit') }}">Site Settings</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.pages.index') }}">Pages</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.visa-categories.index') }}">Visa Categories</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.visa-countries.index') }}">Visa Countries</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.destinations.index') }}">Destinations</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.hero-slides.index') }}">Hero Slider</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.blog-categories.index') }}">Blog Categories</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.blog-posts.index') }}">Blog Posts</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.testimonials.index') }}">Testimonials</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.menu-items.index') }}">Navigation</a>
                <a class="nav-link rounded px-3 py-2" href="{{ route('admin.inquiries.index') }}">Inquiries</a>
                <form method="post" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf
                    <button class="btn btn-outline-light w-100">Logout</button>
                </form>
            </nav>
        </aside>
        <div class="col-lg-10 px-0">
            <div class="admin-topbar border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 mb-0">@yield('page_title', 'Dashboard')</h1>
                    <div class="text-muted small">@yield('page_description')</div>
                </div>
                <a class="btn btn-primary" href="{{ route('home') }}" target="_blank">View Website</a>
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
