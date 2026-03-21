@extends('layouts.admin')

@section('page_title', __('admin.seo_meta_manager'))
@section('page_description', $target['label'] ?? '')

@section('content')
<form method="post" action="{{ route('admin.seo.meta.update', ['targetType' => $entry->target_type, 'targetId' => $entry->target_id]) }}">
    @csrf
    @method('PUT')

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.seo_meta_manager') }}</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Meta Title EN</label><input type="text" name="meta_title_en" class="form-control" value="{{ old('meta_title_en', $entry->meta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Title AR</label><input type="text" name="meta_title_ar" class="form-control" dir="rtl" value="{{ old('meta_title_ar', $entry->meta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Description EN</label><textarea name="meta_description_en" class="form-control" rows="4">{{ old('meta_description_en', $entry->meta_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Meta Description AR</label><textarea name="meta_description_ar" class="form-control" rows="4" dir="rtl">{{ old('meta_description_ar', $entry->meta_description_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Canonical URL</label><input type="url" name="canonical_url" class="form-control" value="{{ old('canonical_url', $entry->canonical_url) }}"></div>
            <div class="col-md-6"><label class="form-label">Robots Meta</label><input type="text" name="robots_meta" class="form-control" value="{{ old('robots_meta', $entry->robots_meta) }}" placeholder="index,follow"></div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Open Graph / Twitter</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">OG Title EN</label><input type="text" name="og_title_en" class="form-control" value="{{ old('og_title_en', $entry->og_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">OG Title AR</label><input type="text" name="og_title_ar" class="form-control" dir="rtl" value="{{ old('og_title_ar', $entry->og_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">OG Description EN</label><textarea name="og_description_en" class="form-control" rows="3">{{ old('og_description_en', $entry->og_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">OG Description AR</label><textarea name="og_description_ar" class="form-control" rows="3" dir="rtl">{{ old('og_description_ar', $entry->og_description_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">OG Image</label><input type="text" name="og_image" class="form-control" value="{{ old('og_image', $entry->og_image) }}"></div>
            <div class="col-md-6"><label class="form-label">Twitter Image</label><input type="text" name="twitter_image" class="form-control" value="{{ old('twitter_image', $entry->twitter_image) }}"></div>
            <div class="col-md-6"><label class="form-label">Twitter Title EN</label><input type="text" name="twitter_title_en" class="form-control" value="{{ old('twitter_title_en', $entry->twitter_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Twitter Title AR</label><input type="text" name="twitter_title_ar" class="form-control" dir="rtl" value="{{ old('twitter_title_ar', $entry->twitter_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Twitter Description EN</label><textarea name="twitter_description_en" class="form-control" rows="3">{{ old('twitter_description_en', $entry->twitter_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Twitter Description AR</label><textarea name="twitter_description_ar" class="form-control" rows="3" dir="rtl">{{ old('twitter_description_ar', $entry->twitter_description_ar) }}</textarea></div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.seo_schema_manager') }} / hreflang</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check">
                    <input type="hidden" name="schema_enabled" value="0">
                    <input type="checkbox" name="schema_enabled" value="1" class="form-check-input" id="schema_enabled" @checked(old('schema_enabled', $entry->schema_enabled))>
                    <label class="form-check-label" for="schema_enabled">{{ __('admin.active') }}</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $entry->is_active))>
                    <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                </div>
            </div>
            <div class="col-md-4"><label class="form-label">Schema Type</label><input type="text" name="schema_type" class="form-control" value="{{ old('schema_type', $entry->schema_type) }}" placeholder="WebPage"></div>
            <div class="col-md-6"><label class="form-label">hreflang EN URL</label><input type="url" name="hreflang_en_url" class="form-control" value="{{ old('hreflang_en_url', $entry->hreflang_en_url) }}"></div>
            <div class="col-md-6"><label class="form-label">hreflang AR URL</label><input type="url" name="hreflang_ar_url" class="form-control" value="{{ old('hreflang_ar_url', $entry->hreflang_ar_url) }}"></div>
        </div>
    </div>

    <button class="btn btn-primary">{{ __('admin.update') }}</button>
</form>
@endsection
