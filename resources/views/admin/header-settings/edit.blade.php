@extends('layouts.admin')

@section('page_title', 'Header Settings')
@section('page_description', 'Control the header appearance, logo display, sticky behavior, link states, and spacing from one dedicated module.')

@section('content')
<form method="post" action="{{ route('admin.header-settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Header Styling</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Header Logo</label>
                <input type="file" class="form-control" name="header_logo" accept="image/*">
                @if($setting->logoUrlFor('header'))
                    <div class="mt-3 p-3 border rounded-4 bg-light">
                        <img src="{{ $setting->logoUrlFor('header') }}" alt="{{ $setting->localized('site_name') ?: 'Travel Wave' }}" class="img-fluid" style="max-height: 96px; object-fit: contain;">
                    </div>
                @else
                    <div class="mt-3 p-3 border rounded-4 bg-light text-muted small">
                        No header logo uploaded yet. The frontend will show the brand text fallback until you upload one.
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <div class="row g-3">
            <div class="col-md-2"><label class="form-label">Background</label><input class="form-control form-control-color w-100" type="color" name="header_background_color" value="{{ old('header_background_color', $setting->header_background_color ?: '#12395b') }}"></div>
            <div class="col-md-2"><label class="form-label">Text</label><input class="form-control form-control-color w-100" type="color" name="header_text_color" value="{{ old('header_text_color', $setting->header_text_color ?: '#ffffff') }}"></div>
            <div class="col-md-2"><label class="form-label">Link</label><input class="form-control form-control-color w-100" type="color" name="header_link_color" value="{{ old('header_link_color', $setting->header_link_color ?: '#ffffff') }}"></div>
            <div class="col-md-2"><label class="form-label">Hover</label><input class="form-control form-control-color w-100" type="color" name="header_hover_color" value="{{ old('header_hover_color', $setting->header_hover_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">Active Link</label><input class="form-control form-control-color w-100" type="color" name="header_active_link_color" value="{{ old('header_active_link_color', $setting->header_active_link_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">Button</label><input class="form-control form-control-color w-100" type="color" name="header_button_color" value="{{ old('header_button_color', $setting->header_button_color ?: '#ff8c32') }}"></div>
            <div class="col-md-3"><label class="form-label">Button Text</label><input class="form-control form-control-color w-100" type="color" name="header_button_text_color" value="{{ old('header_button_text_color', $setting->header_button_text_color ?: '#ffffff') }}"></div>
            <div class="col-md-3"><label class="form-label">Header Logo Display Mode</label><select class="form-select" name="header_logo_display_mode"><option value="original" @selected(old('header_logo_display_mode', $setting->header_logo_display_mode ?: $setting->logoDisplayModeFor('header')) === 'original')>Original</option><option value="contain" @selected(old('header_logo_display_mode', $setting->header_logo_display_mode ?: $setting->logoDisplayModeFor('header')) === 'contain')>Contain</option><option value="cover" @selected(old('header_logo_display_mode', $setting->header_logo_display_mode ?: $setting->logoDisplayModeFor('header')) === 'cover')>Cover</option><option value="custom" @selected(old('header_logo_display_mode', $setting->header_logo_display_mode ?: $setting->logoDisplayModeFor('header')) === 'custom')>Custom Size</option></select><div class="form-text">Original preserves the uploaded logo without forcing custom dimensions.</div></div>
            <div class="col-md-3"><label class="form-label">Header Logo Width</label><input class="form-control" type="number" name="header_logo_width" value="{{ old('header_logo_width', $setting->header_logo_width ?: $setting->logo_width ?: 220) }}"><div class="form-text">Affects header logo only.</div></div>
            <div class="col-md-3"><label class="form-label">Header Logo Height</label><input class="form-control" type="number" name="header_logo_height" value="{{ old('header_logo_height', $setting->header_logo_height ?: $setting->logo_height) }}"><div class="form-text">Optional. Leave empty for automatic height.</div></div>
            <div class="col-md-3"><label class="form-label">Mobile Logo Width</label><input class="form-control" type="number" name="header_mobile_logo_width" value="{{ old('header_mobile_logo_width', $setting->header_mobile_logo_width ?: $setting->mobile_logo_width ?: 168) }}"><div class="form-text">Header mobile width only.</div></div>
            <div class="col-md-3 d-flex align-items-end"><div class="form-check pb-2"><input type="hidden" name="header_logo_keep_aspect_ratio" value="0"><input class="form-check-input" type="checkbox" name="header_logo_keep_aspect_ratio" value="1" id="header_logo_keep_aspect_ratio" @checked(old('header_logo_keep_aspect_ratio', $setting->header_logo_keep_aspect_ratio ?? $setting->logo_keep_aspect_ratio ?? true))><label class="form-check-label" for="header_logo_keep_aspect_ratio">Keep aspect ratio</label></div></div>
            <div class="col-md-4"><label class="form-label">Top Spacing / Vertical Padding</label><input class="form-control" type="number" name="header_vertical_padding" value="{{ old('header_vertical_padding', $setting->header_vertical_padding ?: 8) }}"></div>
            <div class="col-md-4">
                <label class="form-label">English Logo Position</label>
                <select class="form-select" name="header_logo_position_en">
                    <option value="left" @selected(old('header_logo_position_en', $setting->header_logo_position_en ?: 'left') === 'left')>Left</option>
                    <option value="right" @selected(old('header_logo_position_en', $setting->header_logo_position_en ?: 'left') === 'right')>Right</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Arabic Logo Position</label>
                <select class="form-select" name="header_logo_position_ar">
                    <option value="right" @selected(old('header_logo_position_ar', $setting->header_logo_position_ar ?: 'right') === 'right')>Right</option>
                    <option value="left" @selected(old('header_logo_position_ar', $setting->header_logo_position_ar ?: 'right') === 'left')>Left</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">English Menu Position</label>
                <select class="form-select" name="header_menu_position_en">
                    <option value="left" @selected(old('header_menu_position_en', $setting->header_menu_position_en ?: 'left') === 'left')>Left</option>
                    <option value="right" @selected(old('header_menu_position_en', $setting->header_menu_position_en ?: 'left') === 'right')>Right</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Arabic Menu Position</label>
                <select class="form-select" name="header_menu_position_ar">
                    <option value="right" @selected(old('header_menu_position_ar', $setting->header_menu_position_ar ?: 'right') === 'right')>Right</option>
                    <option value="left" @selected(old('header_menu_position_ar', $setting->header_menu_position_ar ?: 'right') === 'left')>Left</option>
                </select>
                <div class="form-text">These controls apply the navbar alignment separately for English and Arabic on desktop and mobile.</div>
            </div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check pb-2"><input type="hidden" name="header_logo_enabled" value="0"><input class="form-check-input" type="checkbox" name="header_logo_enabled" value="1" id="header_logo_enabled" @checked(old('header_logo_enabled', $setting->header_logo_enabled ?? true))><label class="form-check-label" for="header_logo_enabled">Show logo in header</label></div></div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check pb-2"><input type="hidden" name="header_is_sticky" value="0"><input class="form-check-input" type="checkbox" name="header_is_sticky" value="1" id="header_is_sticky" @checked(old('header_is_sticky', $setting->header_is_sticky ?? true))><label class="form-check-label" for="header_is_sticky">Enable sticky header</label></div></div>
                </div>
            </div>
        </div>
    </div>
    <button class="btn btn-primary px-4">Save Header Settings</button>
</form>
@endsection
