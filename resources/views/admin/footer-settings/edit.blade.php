@extends('layouts.admin')

@php($quickLinks = old('footer_quick_links', $setting->footer_quick_links ?: []))

@section('page_title', 'Footer Settings')
@section('page_description', 'Control footer appearance, description, contact information, quick links, social links, and copyright in one module.')

@section('content')
<form method="post" action="{{ route('admin.footer-settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Footer Styling</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Footer Logo</label>
                <input type="file" class="form-control" name="footer_logo" accept="image/*">
                @if($setting->logoUrlFor('footer'))
                    <div class="mt-3 p-3 border rounded-4 bg-light">
                        <img src="{{ $setting->logoUrlFor('footer') }}" alt="{{ $setting->localized('site_name') ?: 'Travel Wave' }}" class="img-fluid" style="max-height: 96px; object-fit: contain;">
                    </div>
                @else
                    <div class="mt-3 p-3 border rounded-4 bg-light text-muted small">
                        No footer logo uploaded yet. The footer will fall back to the main brand logo or brand text.
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <div class="row g-3">
            <div class="col-md-2"><label class="form-label">Background</label><input class="form-control form-control-color w-100" type="color" name="footer_background_color" value="{{ old('footer_background_color', $setting->footer_background_color ?: '#0d2438') }}"></div>
            <div class="col-md-2"><label class="form-label">Text</label><input class="form-control form-control-color w-100" type="color" name="footer_text_color" value="{{ old('footer_text_color', $setting->footer_text_color ?: '#d9e3ed') }}"></div>
            <div class="col-md-2"><label class="form-label">Link</label><input class="form-control form-control-color w-100" type="color" name="footer_link_color" value="{{ old('footer_link_color', $setting->footer_link_color ?: '#ffffff') }}"></div>
            <div class="col-md-2"><label class="form-label">Hover</label><input class="form-control form-control-color w-100" type="color" name="footer_hover_color" value="{{ old('footer_hover_color', $setting->footer_hover_color ?: '#ff8c32') }}"></div>
            <div class="col-md-2"><label class="form-label">Heading</label><input class="form-control form-control-color w-100" type="color" name="footer_heading_color" value="{{ old('footer_heading_color', $setting->footer_heading_color ?: '#ffffff') }}"></div>
            <div class="col-md-2"><label class="form-label">Button</label><input class="form-control form-control-color w-100" type="color" name="footer_button_color" value="{{ old('footer_button_color', $setting->footer_button_color ?: '#ff8c32') }}"></div>
            <div class="col-md-3"><label class="form-label">Button Text</label><input class="form-control form-control-color w-100" type="color" name="footer_button_text_color" value="{{ old('footer_button_text_color', $setting->footer_button_text_color ?: '#ffffff') }}"></div>
            <div class="col-md-3"><label class="form-label">Footer Logo Display Mode</label><select class="form-select" name="footer_logo_display_mode"><option value="original" @selected(old('footer_logo_display_mode', $setting->footer_logo_display_mode ?: $setting->logoDisplayModeFor('footer')) === 'original')>Original</option><option value="contain" @selected(old('footer_logo_display_mode', $setting->footer_logo_display_mode ?: $setting->logoDisplayModeFor('footer')) === 'contain')>Contain</option><option value="cover" @selected(old('footer_logo_display_mode', $setting->footer_logo_display_mode ?: $setting->logoDisplayModeFor('footer')) === 'cover')>Cover</option><option value="custom" @selected(old('footer_logo_display_mode', $setting->footer_logo_display_mode ?: $setting->logoDisplayModeFor('footer')) === 'custom')>Custom Size</option></select><div class="form-text">Original keeps the footer logo as uploaded.</div></div>
            <div class="col-md-3"><label class="form-label">Footer Logo Width</label><input class="form-control" type="number" name="footer_logo_width" value="{{ old('footer_logo_width', $setting->footer_logo_width ?: 200) }}"><div class="form-text">Leave empty to keep the saved width or fallback to 200px.</div></div>
            <div class="col-md-3"><label class="form-label">Footer Logo Height</label><input class="form-control" type="number" name="footer_logo_height" value="{{ old('footer_logo_height', $setting->footer_logo_height) }}"><div class="form-text">Optional. Leave empty for automatic height.</div></div>
            <div class="col-md-3 d-flex align-items-end"><div class="form-check pb-2"><input type="hidden" name="footer_logo_keep_aspect_ratio" value="0"><input class="form-check-input" type="checkbox" name="footer_logo_keep_aspect_ratio" value="1" id="footer_logo_keep_aspect_ratio" @checked(old('footer_logo_keep_aspect_ratio', $setting->footer_logo_keep_aspect_ratio ?? true))><label class="form-check-label" for="footer_logo_keep_aspect_ratio">Keep aspect ratio</label></div></div>
            <div class="col-md-3"><label class="form-label">Footer Padding</label><input class="form-control" type="number" name="footer_vertical_padding" value="{{ old('footer_vertical_padding', $setting->footer_vertical_padding ?: 80) }}"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Footer Content</h2>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" name="footer_text_en" rows="4">{{ old('footer_text_en', $setting->footer_text_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" name="footer_text_ar" rows="4">{{ old('footer_text_ar', $setting->footer_text_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Copyright EN</label><input class="form-control" name="copyright_text_en" value="{{ old('copyright_text_en', $setting->copyright_text_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Copyright AR</label><input class="form-control text-end" dir="rtl" name="copyright_text_ar" value="{{ old('copyright_text_ar', $setting->copyright_text_ar) }}"></div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Footer Contact and Social</h2>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}"></div>
            <div class="col-md-4"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone', $setting->phone) }}"></div>
            <div class="col-md-4"><label class="form-label">Secondary Phone</label><input class="form-control" name="secondary_phone" value="{{ old('secondary_phone', $setting->secondary_phone) }}"></div>
            <div class="col-md-4"><label class="form-label">WhatsApp</label><input class="form-control" name="whatsapp_number" value="{{ old('whatsapp_number', $setting->whatsapp_number) }}"></div>
            <div class="col-md-4"><label class="form-label">Facebook URL</label><input class="form-control" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}"></div>
            <div class="col-md-4"><label class="form-label">Instagram URL</label><input class="form-control" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}"></div>
            <div class="col-md-4"><label class="form-label">X / Twitter URL</label><input class="form-control" name="twitter_url" value="{{ old('twitter_url', $setting->twitter_url) }}"></div>
            <div class="col-md-4"><label class="form-label">YouTube URL</label><input class="form-control" name="youtube_url" value="{{ old('youtube_url', $setting->youtube_url) }}"></div>
            <div class="col-md-4"><label class="form-label">TikTok URL</label><input class="form-control" name="tiktok_url" value="{{ old('tiktok_url', $setting->tiktok_url) }}"></div>
            <div class="col-md-4"><label class="form-label">LinkedIn URL</label><input class="form-control" name="linkedin_url" value="{{ old('linkedin_url', $setting->linkedin_url) }}"></div>
            <div class="col-md-4"><label class="form-label">Snapchat URL</label><input class="form-control" name="snapchat_url" value="{{ old('snapchat_url', $setting->snapchat_url) }}"></div>
            <div class="col-md-4"><label class="form-label">Telegram URL</label><input class="form-control" name="telegram_url" value="{{ old('telegram_url', $setting->telegram_url) }}"></div>
            <div class="col-md-6"><label class="form-label">Address EN</label><textarea class="form-control" name="address_en" rows="3">{{ old('address_en', $setting->address_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Address AR</label><textarea class="form-control text-end" dir="rtl" name="address_ar" rows="3">{{ old('address_ar', $setting->address_ar) }}</textarea></div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Footer Quick Links</h2>
                <p class="text-muted mb-0">Manage the quick links shown in the footer without leaving the footer module.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addFooterQuickLink">Add link</button>
        </div>
        <div id="footerQuickLinksList" class="row gy-3">
            @foreach($quickLinks as $index => $link)
                <div class="col-12 footer-link-row">
                    <div class="border rounded-4 p-3">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" name="footer_quick_links[{{ $index }}][title_en]" value="{{ $link['title_en'] ?? '' }}"></div>
                            <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" name="footer_quick_links[{{ $index }}][title_ar]" value="{{ $link['title_ar'] ?? '' }}"></div>
                            <div class="col-md-3"><label class="form-label">URL</label><input class="form-control" name="footer_quick_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}"></div>
                            <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100 remove-footer-link">Remove</button></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button class="btn btn-primary px-4">Save Footer Settings</button>
</form>

<template id="footerQuickLinkTemplate">
    <div class="col-12 footer-link-row">
        <div class="border rounded-4 p-3">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" data-field="title_en"></div>
                <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" data-field="title_ar"></div>
                <div class="col-md-3"><label class="form-label">URL</label><input class="form-control" data-field="url"></div>
                <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-outline-danger w-100 remove-footer-link">Remove</button></div>
            </div>
        </div>
    </div>
</template>

<script>
const footerList = document.getElementById('footerQuickLinksList');
const footerTemplate = document.getElementById('footerQuickLinkTemplate');
const addFooterLinkButton = document.getElementById('addFooterQuickLink');

function syncFooterLinkNames() {
    footerList.querySelectorAll('.footer-link-row').forEach((row, index) => {
        row.querySelectorAll('[data-field], input[name*="footer_quick_links"]').forEach((input) => {
            const field = input.getAttribute('data-field') || input.name.match(/\[(.*?)\]$/)?.[1];
            if (field) {
                input.name = `footer_quick_links[${index}][${field}]`;
            }
        });
    });
}

addFooterLinkButton.addEventListener('click', () => {
    const row = footerTemplate.content.firstElementChild.cloneNode(true);
    footerList.appendChild(row);
    syncFooterLinkNames();
});

document.addEventListener('click', (event) => {
    const removeButton = event.target.closest('.remove-footer-link');
    if (!removeButton) {
        return;
    }

    removeButton.closest('.footer-link-row')?.remove();
    syncFooterLinkNames();
});

syncFooterLinkNames();
</script>
@endsection
