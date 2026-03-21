@extends('layouts.admin')

@section('page_title', __('admin.dashboard_search'))
@section('page_description', __('admin.dashboard_search_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="get" action="{{ route('admin.search') }}" class="row g-3 align-items-end mb-4">
        <div class="col-lg-10">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input type="text" name="q" class="form-control" value="{{ $query }}" placeholder="{{ __('admin.search_placeholder') }}">
        </div>
        <div class="col-lg-2">
            <button class="btn btn-primary w-100">{{ __('admin.search') }}</button>
        </div>
    </form>

    @if($query === '')
        <p class="text-muted mb-0">{{ __('admin.search_intro') }}</p>
    @elseif(empty($results))
        <p class="text-muted mb-0">{{ __('admin.no_search_results') }}</p>
    @else
        <div class="d-grid gap-4">
            @foreach($results as $group => $items)
                <section>
                    <h2 class="h5 mb-3">{{ $group }}</h2>
                    <div class="list-group">
                        @foreach($items as $item)
                            <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action">
                                <div class="fw-semibold">{{ $item['title'] }}</div>
                                @if(!empty($item['meta']))
                                    <div class="small text-muted">{{ $item['meta'] }}</div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
@endsection
