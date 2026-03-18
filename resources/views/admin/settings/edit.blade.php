@extends('layouts.admin')

@section('page_title', 'Site Settings')
@section('page_description', 'Control branding, contact details, SEO defaults, and global CTA content.')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')
    <div class="card admin-card p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Site Name EN</label><input class="form-control" name="site_name_en" value="{{ old('site_name_en', $setting->site_name_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Site Name AR</label><input class="form-control" name="site_name_ar" value="{{ old('site_name_ar', $setting->site_name_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Tagline EN</label><input class="form-control" name="site_tagline_en" value="{{ old('site_tagline_en', $setting->site_tagline_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Tagline AR</label><input class="form-control" name="site_tagline_ar" value="{{ old('site_tagline_ar', $setting->site_tagline_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">Logo</label><input type="file" class="form-control" name="logo"></div>
            <div class="col-md-4"><label class="form-label">Favicon</label><input type="file" class="form-control" name="favicon"></div>
            <div class="col-md-2"><label class="form-label">Primary Color</label><input class="form-control" name="primary_color" value="{{ old('primary_color', $setting->primary_color) }}"></div>
            <div class="col-md-2"><label class="form-label">Secondary Color</label><input class="form-control" name="secondary_color" value="{{ old('secondary_color', $setting->secondary_color) }}"></div>
        </div>
    </div>
    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Contact & Footer</h2>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}"></div>
            <div class="col-md-4"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone', $setting->phone) }}"></div>
            <div class="col-md-4"><label class="form-label">WhatsApp</label><input class="form-control" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}"></div>
            <div class="col-md-6"><label class="form-label">Address EN</label><textarea class="form-control" name="address_en" rows="3">{{ old('address_en', $setting->address_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Address AR</label><textarea class="form-control" name="address_ar" rows="3">{{ old('address_ar', $setting->address_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Working Hours EN</label><textarea class="form-control" name="working_hours_en" rows="3">{{ old('working_hours_en', $setting->working_hours_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Working Hours AR</label><textarea class="form-control" name="working_hours_ar" rows="3">{{ old('working_hours_ar', $setting->working_hours_ar) }}</textarea></div>
            <div class="col-md-12"><label class="form-label">Map Iframe</label><textarea class="form-control" name="map_iframe" rows="4">{{ old('map_iframe', $setting->map_iframe) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Footer Text EN</label><textarea class="form-control" name="footer_text_en" rows="3">{{ old('footer_text_en', $setting->footer_text_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Footer Text AR</label><textarea class="form-control" name="footer_text_ar" rows="3">{{ old('footer_text_ar', $setting->footer_text_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Facebook URL</label><input class="form-control" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}"></div>
            <div class="col-md-6"><label class="form-label">Instagram URL</label><input class="form-control" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}"></div>
            <div class="col-md-6"><label class="form-label">YouTube URL</label><input class="form-control" name="youtube_url" value="{{ old('youtube_url', $setting->youtube_url) }}"></div>
            <div class="col-md-6"><label class="form-label">TikTok URL</label><input class="form-control" name="tiktok_url" value="{{ old('tiktok_url', $setting->tiktok_url) }}"></div>
            <div class="col-md-6"><label class="form-label">Copyright EN</label><input class="form-control" name="copyright_text_en" value="{{ old('copyright_text_en', $setting->copyright_text_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Copyright AR</label><input class="form-control text-end" dir="rtl" name="copyright_text_ar" value="{{ old('copyright_text_ar', $setting->copyright_text_ar) }}"></div>
        </div>
    </div>
    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">SEO & Global CTA</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Meta Title EN</label><input class="form-control" name="default_meta_title_en" value="{{ old('default_meta_title_en', $setting->default_meta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Title AR</label><input class="form-control" name="default_meta_title_ar" value="{{ old('default_meta_title_ar', $setting->default_meta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Description EN</label><textarea class="form-control" name="default_meta_description_en" rows="3">{{ old('default_meta_description_en', $setting->default_meta_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Meta Description AR</label><textarea class="form-control" name="default_meta_description_ar" rows="3">{{ old('default_meta_description_ar', $setting->default_meta_description_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">CTA Title EN</label><input class="form-control" name="global_cta_title_en" value="{{ old('global_cta_title_en', $setting->global_cta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">CTA Title AR</label><input class="form-control" name="global_cta_title_ar" value="{{ old('global_cta_title_ar', $setting->global_cta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">CTA Text EN</label><textarea class="form-control" name="global_cta_text_en" rows="3">{{ old('global_cta_text_en', $setting->global_cta_text_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">CTA Text AR</label><textarea class="form-control" name="global_cta_text_ar" rows="3">{{ old('global_cta_text_ar', $setting->global_cta_text_ar) }}</textarea></div>
            <div class="col-md-4"><label class="form-label">CTA Button EN</label><input class="form-control" name="global_cta_button_en" value="{{ old('global_cta_button_en', $setting->global_cta_button_en) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA Button AR</label><input class="form-control" name="global_cta_button_ar" value="{{ old('global_cta_button_ar', $setting->global_cta_button_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">CTA URL</label><input class="form-control" name="global_cta_url" value="{{ old('global_cta_url', $setting->global_cta_url) }}"></div>
        </div>
    </div>
    <button class="btn btn-primary">Save Settings</button>
</form>
@endsection
