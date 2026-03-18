@extends('layouts.app')

@section('title', $page->localized('title'))

@section('content')
<section class="container py-5">
    <div class="tw-page-header p-4 p-lg-5">
        <h1 class="display-5">{{ $page->localized('hero_title') }}</h1>
        <p class="lead text-white-50">{{ $page->localized('hero_subtitle') }}</p>
    </div>
</section>
<section class="container py-4">
    @foreach($categories as $category)
        <div class="tw-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div>
                    <h2 class="h3 mb-1">{{ $category->localized('name') }}</h2>
                    <p class="text-muted mb-0">{{ $category->localized('short_description') }}</p>
                </div>
                <a href="{{ route('visas.category', $category) }}" class="btn btn-outline-primary">{{ __('ui.learn_more') }}</a>
            </div>
            <div class="row g-3">
                @foreach($category->countries as $country)
                    <div class="col-md-6 col-xl-3">
                        <a href="{{ route('visas.country', $country) }}" class="text-decoration-none">
                            <div class="border rounded-4 p-3 h-100">
                                <strong class="d-block text-dark">{{ $country->localized('name') }}</strong>
                                <span class="text-muted small">{{ $country->localized('excerpt') }}</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</section>
@endsection
