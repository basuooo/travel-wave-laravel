@extends('layouts.admin')

@section('page_title', 'Dashboard')
@section('page_description', 'Quick overview of inquiries, content, and travel entities.')

@section('content')
<div class="row g-4 mb-4">
    @foreach($stats as $label => $count)
        <div class="col-md-6 col-xl-3">
            <div class="card admin-card p-4">
                <div class="text-muted text-uppercase small">{{ str_replace('_', ' ', $label) }}</div>
                <div class="display-6 fw-bold">{{ $count }}</div>
            </div>
        </div>
    @endforeach
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">Quick Management</h2>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="{{ route('admin.pages.edit', 'home') }}">Edit Homepage</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.settings.edit') }}">Site Settings</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.visa-countries.create') }}">Add Visa Country</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.destinations.create') }}">Add Destination</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.blog-posts.create') }}">Create Blog Post</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">Content Coverage</h2>
            <ul class="list-unstyled mb-0">
                <li class="mb-2">Visa pages: {{ $stats['visa_countries'] }}</li>
                <li class="mb-2">Domestic destinations: {{ $stats['destinations'] }}</li>
                <li class="mb-2">Blog posts: {{ $stats['posts'] }}</li>
                <li>Testimonials: {{ $stats['testimonials'] }}</li>
            </ul>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">Latest Inquiries</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Name</th><th>Type</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($latestInquiries as $item)
                        <tr>
                            <td><a href="{{ route('admin.inquiries.show', $item) }}">{{ $item->full_name }}</a></td>
                            <td>{{ $item->type }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">Latest Posts</h2>
            <ul class="list-group list-group-flush">
                @foreach($latestPosts as $item)
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>{{ $item->title_en }}</span>
                        <span class="text-muted">{{ optional($item->published_at)->format('d M Y') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
