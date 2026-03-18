@extends('layouts.app')

@section('title', $category->localized('name'))

@section('content')
<section class="container py-5">
    <div class="tw-page-header p-4 p-lg-5">
        <h1 class="display-5">{{ $category->localized('name') }}</h1>
        <p class="lead text-white-50 mb-0">{{ $category->localized('short_description') }}</p>
    </div>
</section>
<section class="container py-4">
    <div class="row g-4">
        @foreach($category->countries as $country)
            <div class="col-md-6 col-xl-4">
                <div class="tw-card h-100 overflow-hidden">
                    @if($country->hero_image)<img src="{{ asset('storage/' . $country->hero_image) }}" class="img-fluid" alt="{{ $country->localized('name') }}">@endif
                    <div class="p-4">
                        <h2 class="h4">{{ $country->localized('name') }}</h2>
                        <p class="text-muted">{{ $country->localized('excerpt') }}</p>
                        <a href="{{ route('visas.country', $country) }}" class="btn btn-primary tw-btn-primary">{{ __('ui.learn_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
