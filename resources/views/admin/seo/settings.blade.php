@extends('layouts.admin')

@section('page_title', __('admin.seo_global_settings'))
@section('page_description', __('admin.seo_settings_desc'))

@section('content')
<form method="post" action="{{ route('admin.seo.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.seo_sitemap_manager') }}</h2>
        <div class="row g-3">
            @foreach([
                'sitemap_include_pages' => __('admin.pages'),
                'sitemap_include_visa_destinations' => __('admin.visa_destinations'),
                'sitemap_include_destinations' => __('admin.destinations'),
                'sitemap_include_blog_posts' => __('admin.blog_posts'),
                'sitemap_include_marketing_pages' => __('admin.marketing_manager'),
                'sitemap_include_images' => __('admin.seo_image_sitemap'),
            ] as $field => $label)
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="hidden" name="{{ $field }}" value="0">
                        <input type="checkbox" name="{{ $field }}" value="1" class="form-check-input" id="{{ $field }}" @checked(old($field, $settings->{$field}))>
                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.seo_robots_manager') }}</h2>
        <div class="mb-3">
            <label class="form-label">{{ __('admin.seo_default_robots_meta') }}</label>
            <input type="text" name="default_robots_meta" class="form-control" value="{{ old('default_robots_meta', $settings->default_robots_meta ?: 'index,follow') }}" placeholder="index,follow">
        </div>
        <div>
            <label class="form-label">robots.txt</label>
            <textarea name="robots_txt_content" class="form-control" rows="10">{{ old('robots_txt_content', $settings->robots_txt_content) }}</textarea>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.seo_schema_manager') }}</h2>
        <div class="row g-3">
            @foreach([
                'schema_organization_enabled' => __('admin.seo_schema_organization'),
                'schema_local_business_enabled' => __('admin.seo_schema_local_business'),
                'schema_breadcrumb_enabled' => __('admin.seo_schema_breadcrumb'),
                'schema_faq_enabled' => __('admin.seo_schema_faq'),
                'schema_article_enabled' => __('admin.seo_schema_article'),
                'schema_destination_enabled' => __('admin.seo_schema_destination'),
            ] as $field => $label)
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="hidden" name="{{ $field }}" value="0">
                        <input type="checkbox" name="{{ $field }}" value="1" class="form-check-input" id="{{ $field }}" @checked(old($field, $settings->{$field}))>
                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                    </div>
                </div>
            @endforeach
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.seo_schema_org_name') }}</label>
                <input type="text" name="schema_organization_name" class="form-control" value="{{ old('schema_organization_name', $settings->schema_organization_name) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.seo_schema_org_logo') }}</label>
                <input type="text" name="schema_organization_logo" class="form-control" value="{{ old('schema_organization_logo', $settings->schema_organization_logo) }}">
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.seo_search_console') }} / {{ __('admin.seo_merchant_center') }}</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.search_console_property') }}</label>
                <input type="text" name="search_console_property" class="form-control" value="{{ old('search_console_property', $settings->search_console_property) }}">
            </div>
            <div class="col-md-6">
                <div class="form-check mt-4">
                    <input type="hidden" name="merchant_center_enabled" value="0">
                    <input type="checkbox" name="merchant_center_enabled" value="1" class="form-check-input" id="merchant_center_enabled" @checked(old('merchant_center_enabled', $settings->merchant_center_enabled))>
                    <label class="form-check-label" for="merchant_center_enabled">{{ __('admin.seo_merchant_center_enable') }}</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.notes') }}</label>
                <textarea name="search_console_notes" class="form-control" rows="3">{{ old('search_console_notes', $settings->search_console_notes) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.seo_merchant_center_verification') }}</label>
                <input type="text" name="merchant_center_verification_code" class="form-control" value="{{ old('merchant_center_verification_code', $settings->merchant_center_verification_code) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.seo_merchant_center_notes') }}</label>
                <textarea name="merchant_center_notes" class="form-control" rows="2">{{ old('merchant_center_notes', $settings->merchant_center_notes) }}</textarea>
            </div>
        </div>
    </div>

    <button class="btn btn-primary">{{ __('admin.update') }}</button>
</form>
@endsection
