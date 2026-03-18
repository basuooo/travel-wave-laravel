@extends('layouts.app')

@section('title', $page->localized('title'))

@section('content')
<section class="container py-5">
    <div class="tw-page-header p-4 p-lg-5">
        <h1 class="display-5">{{ $page->localized('hero_title') ?: $page->localized('title') }}</h1>
        <p class="lead text-white-50">{{ $page->localized('hero_subtitle') ?: $page->localized('intro_body') }}</p>
    </div>
</section>
<section class="container py-4">
    <div class="row g-4">
        @foreach($posts as $post)
            <div class="col-md-6 col-xl-4">
                <div class="tw-card h-100 overflow-hidden">
                    @if($post->featured_image)<img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid" alt="{{ $post->localized('title') }}">@endif
                    <div class="p-4">
                        <div class="text-muted small mb-2">{{ optional($post->published_at)->format('d M Y') }}</div>
                        <h2 class="h4">{{ $post->localized('title') }}</h2>
                        <p class="text-muted">{{ $post->localized('excerpt') }}</p>
                        <a href="{{ route('blog.show', $post) }}" class="btn btn-outline-primary">{{ __('ui.read_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $posts->links() }}</div>
</section>
@endsection
