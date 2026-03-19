@extends('layouts.admin')

@section('page_title', 'Brand Settings')
@section('page_description', 'Manage core Travel Wave branding, logos, favicon, global color system, default SEO, and site-wide CTA content.')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Brand Identity</h2>
        <div class="row g-4">
            <div class="col-md-6"><label class="form-label">Site Name EN</label><input class="form-control" name="site_name_en" value="{{ old('site_name_en', $setting->site_name_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Site Name AR</label><input class="form-control text-end" dir="rtl" name="site_name_ar" value="{{ old('site_name_ar', $setting->site_name_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Tagline EN</label><input class="form-control" name="site_tagline_en" value="{{ old('site_tagline_en', $setting->site_tagline_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Tagline AR</label><input class="form-control text-end" dir="rtl" name="site_tagline_ar" value="{{ old('site_tagline_ar', $setting->site_tagline_ar) }}"></div>
            <div class="col-lg-4"><label class="form-label">Favicon</label><input type="file" class="form-control" name="favicon" accept="image/*">@if($setting->favicon_path)<div class="mt-3 p-3 border rounded-4 bg-light d-inline-flex"><img src="{{ asset('storage/' . $setting->favicon_path) }}" alt="" style="width: 48px; height: 48px; object-fit: contain;"></div>@endif</div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Global Brand Colors</h2>
        <div class="row g-3">
            <div class="col-md-2"><label class="form-label">Primary Brand</label><input class="form-control form-control-color w-100" type="color" name="primary_color" value="{{ old('primary_color', $setting->primary_color ?: '#12395b') }}"></div>
            <div class="col-md-2"><label class="form-label">Secondary Brand</label><input class="form-control form-control-color w-100" type="color" name="secondary_color" value="{{ old('secondary_color', $setting->secondary_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">Accent / CTA</label><input class="form-control form-control-color w-100" type="color" name="accent_color" value="{{ old('accent_color', $setting->accent_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">Button Color</label><input class="form-control form-control-color w-100" type="color" name="button_color" value="{{ old('button_color', $setting->button_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">Button Hover</label><input class="form-control form-control-color w-100" type="color" name="button_hover_color" value="{{ old('button_hover_color', $setting->button_hover_color ?: '#ef5c00') }}"></div>
            <div class="col-md-2"><label class="form-label">Link Hover</label><input class="form-control form-control-color w-100" type="color" name="link_hover_color" value="{{ old('link_hover_color', $setting->link_hover_color ?: '#ff8c32') }}"></div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">SEO and Global CTA</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Meta Title EN</label><input class="form-control" name="default_meta_title_en" value="{{ old('default_meta_title_en', $setting->default_meta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Title AR</label><input class="form-control text-end" dir="rtl" name="default_meta_title_ar" value="{{ old('default_meta_title_ar', $setting->default_meta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Description EN</label><textarea class="form-control" name="default_meta_description_en" rows="3">{{ old('default_meta_description_en', $setting->default_meta_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Meta Description AR</label><textarea class="form-control text-end" dir="rtl" name="default_meta_description_ar" rows="3">{{ old('default_meta_description_ar', $setting->default_meta_description_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">CTA Title EN</label><input class="form-control" name="global_cta_title_en" value="{{ old('global_cta_title_en', $setting->global_cta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">CTA Title AR</label><input class="form-control text-end" dir="rtl" name="global_cta_title_ar" value="{{ old('global_cta_title_ar', $setting->global_cta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">CTA Text EN</label><textarea class="form-control" name="global_cta_text_en" rows="3">{{ old('global_cta_text_en', $setting->global_cta_text_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">CTA Text AR</label><textarea class="form-control text-end" dir="rtl" name="global_cta_text_ar" rows="3">{{ old('global_cta_text_ar', $setting->global_cta_text_ar) }}</textarea></div>
            <div class="col-md-4"><label class="form-label">CTA Button EN</label><input class="form-control" name="global_cta_button_en" value="{{ old('global_cta_button_en', $setting->global_cta_button_en) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA Button AR</label><input class="form-control text-end" dir="rtl" name="global_cta_button_ar" value="{{ old('global_cta_button_ar', $setting->global_cta_button_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA URL</label><input class="form-control" name="global_cta_url" value="{{ old('global_cta_url', $setting->global_cta_url) }}"></div>
        </div>
    </div>

    <button class="btn btn-primary px-4">Save Brand Settings</button>
</form>
@endsection
