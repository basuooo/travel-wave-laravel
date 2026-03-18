@extends('layouts.app')

@section('title', $post->localized('meta_title') ?: $post->localized('title'))

@section('content')
<article class="container py-5">
    <div class="tw-card overflow-hidden">
        @if($post->featured_image)<img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid w-100" alt="{{ $post->localized('title') }}">@endif
        <div class="p-4 p-lg-5">
            <div class="text-muted small mb-2">{{ optional($post->published_at)->format('d M Y') }}</div>
            <h1 class="display-6 mb-3">{{ $post->localized('title') }}</h1>
            <div class="text-muted mb-4">{{ $post->localized('excerpt') }}</div>
            <div class="lh-lg">{!! nl2br(e($post->localized('content'))) !!}</div>
        </div>
    </div>
</article>
<section class="container pb-5">
    <h2 class="tw-section-title h2 mb-4">{{ __('ui.latest_articles') }}</h2>
    <div class="row g-4">
        @foreach($relatedPosts as $item)
            <div class="col-md-4">
                <div class="tw-card p-4 h-100">
                    <h3 class="h5">{{ $item->localized('title') }}</h3>
                    <p class="text-muted">{{ $item->localized('excerpt') }}</p>
                    <a href="{{ route('blog.show', $item) }}">{{ __('ui.read_more') }}</a>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
