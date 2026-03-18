@extends('layouts.admin')

@section('page_title', $item->exists ? 'Edit Hero Slide' : 'Create Hero Slide')
@section('page_description', 'Upload the banner image and manage bilingual text, CTA, order, and active state.')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.hero-slides.update', $item) : route('admin.hero-slides.store') }}">
    @csrf
    @if($item->exists) @method('PUT') @endif
    <div class="card admin-card p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Headline EN</label>
                <input class="form-control" name="headline_en" value="{{ old('headline_en', $item->headline_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Headline AR</label>
                <input class="form-control text-end" dir="rtl" name="headline_ar" value="{{ old('headline_ar', $item->headline_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle EN</label>
                <textarea class="form-control" name="subtitle_en" rows="3">{{ old('subtitle_en', $item->subtitle_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle AR</label>
                <textarea class="form-control text-end" dir="rtl" name="subtitle_ar" rows="3">{{ old('subtitle_ar', $item->subtitle_ar) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">CTA Text EN</label>
                <input class="form-control" name="cta_text_en" value="{{ old('cta_text_en', $item->cta_text_en) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">CTA Text AR</label>
                <input class="form-control text-end" dir="rtl" name="cta_text_ar" value="{{ old('cta_text_ar', $item->cta_text_ar) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">CTA Link</label>
                <input class="form-control" name="cta_link" value="{{ old('cta_link', $item->cta_link) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Slide Image</label>
                <input type="file" class="form-control" name="image">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort Order</label>
                <input class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}">
            </div>
            <div class="col-md-3 form-check mt-4 pt-2">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
                <label class="form-check-label">Active</label>
            </div>
            @if($item->image_path)
                <div class="col-12">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->headline_en }}" class="img-fluid rounded-4">
                </div>
            @endif
        </div>
    </div>
    <button class="btn btn-primary mt-3">Save Slide</button>
</form>
@endsection
