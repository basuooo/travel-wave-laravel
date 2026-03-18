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
    <div class="row g-4">
        @foreach($destinations as $destination)
            <div class="col-md-6 col-xl-4">
                <div class="tw-card h-100 overflow-hidden">
                    @if($destination->hero_image)<img src="{{ asset('storage/' . $destination->hero_image) }}" class="img-fluid" alt="{{ $destination->localized('title') }}">@endif
                    <div class="p-4">
                        <h2 class="h4">{{ $destination->localized('title') }}</h2>
                        <p class="text-muted">{{ $destination->localized('excerpt') }}</p>
                        <a href="{{ route('destinations.show', $destination) }}" class="btn btn-primary tw-btn-primary">{{ __('ui.learn_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
