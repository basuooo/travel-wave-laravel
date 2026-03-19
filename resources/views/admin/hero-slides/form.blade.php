@extends('layouts.admin')

@section('page_title', $item->exists ? 'Edit Hero Slide' : 'Create Hero Slide')
@section('page_description', 'Manage desktop and mobile banner images, bilingual content, CTA text, ordering, and activation state.')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.hero-slides.update', $item) : route('admin.hero-slides.store') }}">
    @csrf
    @if($item->exists)
        @method('PUT')
    @endif

    <div class="card admin-card p-4">
        <div class="row g-4">
            <div class="col-lg-6">
                <label class="form-label">Desktop Banner Image</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <div class="form-text">Used by default on desktop and as the fallback if no mobile image is uploaded.</div>
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->headline_en }}" class="img-fluid rounded-4 mt-3 border">
                @endif
            </div>
            <div class="col-lg-6">
                <label class="form-label">Mobile Banner Image</label>
                <input type="file" class="form-control" name="mobile_image" accept="image/*">
                <div class="form-text">Shown on mobile devices only. If empty, the desktop image will be used automatically.</div>
                @if($item->mobile_image_path)
                    <img src="{{ asset('storage/' . $item->mobile_image_path) }}" alt="{{ $item->headline_en }}" class="img-fluid rounded-4 mt-3 border" style="max-height: 420px; object-fit: cover;">
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">English Title</label>
                <input class="form-control" name="headline_en" value="{{ old('headline_en', $item->headline_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Arabic Title</label>
                <input class="form-control text-end" dir="rtl" name="headline_ar" value="{{ old('headline_ar', $item->headline_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">English Subtitle</label>
                <textarea class="form-control" name="subtitle_en" rows="4">{{ old('subtitle_en', $item->subtitle_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Arabic Subtitle</label>
                <textarea class="form-control text-end" dir="rtl" name="subtitle_ar" rows="4">{{ old('subtitle_ar', $item->subtitle_ar) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">English Button Text</label>
                <input class="form-control" name="cta_text_en" value="{{ old('cta_text_en', $item->cta_text_en) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Arabic Button Text</label>
                <input class="form-control text-end" dir="rtl" name="cta_text_ar" value="{{ old('cta_text_ar', $item->cta_text_ar) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Button Link</label>
                <input class="form-control" name="cta_link" value="{{ old('cta_link', $item->cta_link) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check pb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
    </div>
    <button class="btn btn-primary mt-3 px-4">Save Slide</button>
</form>
@endsection
