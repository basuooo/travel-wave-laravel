@extends('layouts.admin')

@section('page_title', __('admin.meta_conversion_api'))
@section('page_description', __('admin.meta_conversion_api_desc'))

@section('content')
<form method="post" action="{{ route('admin.meta-conversion-api-settings.update') }}">
    @csrf
    @method('PUT')

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.core_settings') }}</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check">
                    <input type="hidden" name="meta_conversion_api_enabled" value="0">
                    <input type="checkbox" name="meta_conversion_api_enabled" value="1" class="form-check-input" id="meta-conversion-api-enabled" @checked(old('meta_conversion_api_enabled', $setting->meta_conversion_api_enabled ?? false))>
                    <label class="form-check-label" for="meta-conversion-api-enabled">{{ __('admin.meta_conversion_api_enable') }}</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.meta_pixel_id') }}</label>
                <input type="text" name="meta_pixel_id" class="form-control" value="{{ old('meta_pixel_id', $setting->meta_pixel_id) }}" placeholder="123456789012345">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.meta_test_event_code') }}</label>
                <input type="text" name="meta_conversion_api_test_event_code" class="form-control" value="{{ old('meta_conversion_api_test_event_code', $setting->meta_conversion_api_test_event_code) }}" placeholder="TEST12345">
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.meta_access_token') }}</label>
                <textarea name="meta_conversion_api_access_token" class="form-control" rows="4" placeholder="EAAG...">{{ old('meta_conversion_api_access_token', $setting->meta_conversion_api_access_token) }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.meta_default_event_source_url') }}</label>
                <input type="url" name="meta_conversion_api_default_event_source_url" class="form-control" value="{{ old('meta_conversion_api_default_event_source_url', $setting->meta_conversion_api_default_event_source_url) }}" placeholder="https://travelwave.example.com">
                <div class="form-text">{{ __('admin.meta_default_event_source_url_help') }}</div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-4">{{ __('admin.update') }}</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
    </div>
</form>
@endsection
