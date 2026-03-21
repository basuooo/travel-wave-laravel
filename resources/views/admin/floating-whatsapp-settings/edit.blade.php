@extends('layouts.admin')

@section('page_title', 'Floating WhatsApp Settings')
@section('page_description', 'Control the global WhatsApp floating button, animation, page visibility, and device behavior.')

@php
    $selectedTargets = old('floating_whatsapp_visibility_targets', $setting->floating_whatsapp_visibility_targets ?? []);
@endphp

@section('content')
<form method="post" action="{{ route('admin.floating-whatsapp-settings.update') }}">
    @csrf
    @method('PUT')

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Core Settings</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check">
                    <input type="hidden" name="floating_whatsapp_enabled" value="0">
                    <input type="checkbox" name="floating_whatsapp_enabled" value="1" class="form-check-input" id="floating-whatsapp-enabled" @checked(old('floating_whatsapp_enabled', $setting->floating_whatsapp_enabled ?? true))>
                    <label class="form-check-label" for="floating-whatsapp-enabled">Enable floating WhatsApp button globally</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-check">
                    <input type="hidden" name="floating_whatsapp_show_icon" value="0">
                    <input type="checkbox" name="floating_whatsapp_show_icon" value="1" class="form-check-input" id="floating-whatsapp-icon" @checked(old('floating_whatsapp_show_icon', $setting->floating_whatsapp_show_icon ?? true))>
                    <label class="form-check-label" for="floating-whatsapp-icon">Show WhatsApp icon</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">WhatsApp Number</label>
                <input type="text" name="floating_whatsapp_number" class="form-control" value="{{ old('floating_whatsapp_number', $setting->floating_whatsapp_number ?: '201060500236') }}" placeholder="201060500236">
            </div>
            <div class="col-md-6">
                <label class="form-label">Button Text EN</label>
                <input type="text" name="floating_whatsapp_button_text_en" class="form-control" value="{{ old('floating_whatsapp_button_text_en', $setting->floating_whatsapp_button_text_en) }}" placeholder="Chat with us">
            </div>
            <div class="col-md-6">
                <label class="form-label">Button Text AR</label>
                <input type="text" name="floating_whatsapp_button_text_ar" dir="rtl" class="form-control text-end" value="{{ old('floating_whatsapp_button_text_ar', $setting->floating_whatsapp_button_text_ar ?: 'تواصل واتساب') }}" placeholder="تواصل واتساب">
            </div>
            <div class="col-md-6">
                <label class="form-label">Default Message EN</label>
                <textarea name="floating_whatsapp_message_en" class="form-control" rows="3" placeholder="Hello, I want to ask about Travel Wave services">{{ old('floating_whatsapp_message_en', $setting->floating_whatsapp_message_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Default Message AR</label>
                <textarea name="floating_whatsapp_message_ar" dir="rtl" class="form-control text-end" rows="3" placeholder="مرحبًا، أريد الاستفسار عن خدمات Travel Wave">{{ old('floating_whatsapp_message_ar', $setting->floating_whatsapp_message_ar ?: 'مرحبًا، أريد الاستفسار عن خدمات Travel Wave') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Display and Animation</h2>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Position</label>
                <select name="floating_whatsapp_position" class="form-select">
                    @foreach($positionOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('floating_whatsapp_position', $setting->floating_whatsapp_position ?: 'bottom_right') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Animation Style</label>
                <select name="floating_whatsapp_animation_style" class="form-select">
                    @foreach($animationOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('floating_whatsapp_animation_style', $setting->floating_whatsapp_animation_style ?: 'pulse') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Animation Speed (ms)</label>
                <input type="number" name="floating_whatsapp_animation_speed" class="form-control" min="1000" max="10000" value="{{ old('floating_whatsapp_animation_speed', $setting->floating_whatsapp_animation_speed ?: 3200) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Background Color</label>
                <input type="text" name="floating_whatsapp_background_color" class="form-control" value="{{ old('floating_whatsapp_background_color', $setting->floating_whatsapp_background_color ?: '#25D366') }}" placeholder="#25D366">
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input type="hidden" name="floating_whatsapp_show_desktop" value="0">
                    <input type="checkbox" name="floating_whatsapp_show_desktop" value="1" class="form-check-input" id="floating-whatsapp-desktop" @checked(old('floating_whatsapp_show_desktop', $setting->floating_whatsapp_show_desktop ?? true))>
                    <label class="form-check-label" for="floating-whatsapp-desktop">Show on desktop</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input type="hidden" name="floating_whatsapp_show_mobile" value="0">
                    <input type="checkbox" name="floating_whatsapp_show_mobile" value="1" class="form-check-input" id="floating-whatsapp-mobile" @checked(old('floating_whatsapp_show_mobile', $setting->floating_whatsapp_show_mobile ?? true))>
                    <label class="form-check-label" for="floating-whatsapp-mobile">Show on mobile</label>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Page Visibility Rules</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Visibility Mode</label>
                <select name="floating_whatsapp_visibility_mode" class="form-select">
                    @foreach($visibilityModes as $value => $label)
                        <option value="{{ $value }}" @selected(old('floating_whatsapp_visibility_mode', $setting->floating_whatsapp_visibility_mode ?: 'all') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Selected Pages / Groups</label>
                <select name="floating_whatsapp_visibility_targets[]" class="form-select" multiple size="14">
                    @foreach($visibilityTargets as $groupLabel => $options)
                        <optgroup label="{{ $groupLabel }}">
                            @foreach($options as $value => $label)
                                <option value="{{ $value }}" @selected(in_array($value, $selectedTargets, true))>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <div class="form-text">Use this with the selected visibility mode to include or exclude specific pages, groups, destinations, or categories.</div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-4">Save Floating WhatsApp Settings</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</form>
@endsection
