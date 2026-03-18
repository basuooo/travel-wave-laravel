@extends('layouts.admin')

@section('page_title', 'Homepage Hero Slider')
@section('page_description', 'Manage the 3-slide premium homepage banner plus autoplay, overlay, and navigation behavior.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <form method="post" action="{{ route('admin.hero-slides.settings') }}" class="card admin-card p-4 h-100">
            @csrf
            @method('PUT')
            <h2 class="h5 mb-3">Slider Settings</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Autoplay Interval (ms)</label>
                    <input class="form-control" name="hero_slider_interval" value="{{ old('hero_slider_interval', $setting->hero_slider_interval ?: 5000) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Overlay Opacity</label>
                    <input class="form-control" name="hero_slider_overlay_opacity" value="{{ old('hero_slider_overlay_opacity', $setting->hero_slider_overlay_opacity ?? 0.45) }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Banner Layout Mode</label>
                    <select class="form-select" name="hero_slider_layout_mode">
                        <option value="full-width" @selected(old('hero_slider_layout_mode', $setting->hero_slider_layout_mode) === 'full-width')>Full Width</option>
                        <option value="custom-1408" @selected(old('hero_slider_layout_mode', $setting->hero_slider_layout_mode ?? 'custom-1408') === 'custom-1408')>1408 x 656</option>
                        <option value="large-hero" @selected(old('hero_slider_layout_mode', $setting->hero_slider_layout_mode) === 'large-hero')>Large Hero</option>
                        <option value="medium-hero" @selected(old('hero_slider_layout_mode', $setting->hero_slider_layout_mode) === 'medium-hero')>Medium Hero</option>
                        <option value="compact-banner" @selected(old('hero_slider_layout_mode', $setting->hero_slider_layout_mode) === 'compact-banner')>Compact Banner</option>
                        <option value="fullscreen-hero" @selected(old('hero_slider_layout_mode', $setting->hero_slider_layout_mode) === 'fullscreen-hero')>Full Screen Hero</option>
                    </select>
                    <div class="form-text">Choose the overall banner width and height behavior used on the homepage.</div>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Content Alignment</label>
                    <select class="form-select" name="hero_slider_content_alignment">
                        <option value="start" @selected(old('hero_slider_content_alignment', $setting->hero_slider_content_alignment) === 'start')>Start</option>
                        <option value="center" @selected(old('hero_slider_content_alignment', $setting->hero_slider_content_alignment) === 'center')>Center</option>
                        <option value="end" @selected(old('hero_slider_content_alignment', $setting->hero_slider_content_alignment) === 'end')>End</option>
                    </select>
                </div>
                <div class="col-md-6 form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="hero_slider_autoplay" value="1" @checked(old('hero_slider_autoplay', $setting->hero_slider_autoplay ?? true))>
                    <label class="form-check-label">Enable Autoplay</label>
                </div>
                <div class="col-md-6 form-check mt-4 pt-2">
                    <input class="form-check-input" type="checkbox" name="hero_slider_show_dots" value="1" @checked(old('hero_slider_show_dots', $setting->hero_slider_show_dots ?? true))>
                    <label class="form-check-label">Show Dots</label>
                </div>
                <div class="col-md-6 form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="hero_slider_show_arrows" value="1" @checked(old('hero_slider_show_arrows', $setting->hero_slider_show_arrows ?? true))>
                    <label class="form-check-label">Show Arrows</label>
                </div>
            </div>
            <button class="btn btn-primary mt-4">Save Slider Settings</button>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="card admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h5 mb-1">Slides</h2>
                    <div class="text-muted small">Exactly 3 slides are seeded by default. You can edit, replace images, disable, or reorder them.</div>
                </div>
                <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-primary">Add Slide</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Preview</th><th>Headline</th><th>Order</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td style="width:130px">
                                    <img src="{{ asset('storage/' . $item->image_path) }}" class="img-fluid rounded-3" alt="{{ $item->headline_en }}">
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->headline_en }}</div>
                                    <div class="text-muted small" dir="rtl">{{ $item->headline_ar }}</div>
                                </td>
                                <td>{{ $item->sort_order }}</td>
                                <td><span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.hero-slides.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="post" action="{{ route('admin.hero-slides.destroy', $item) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this slide?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
