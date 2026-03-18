@extends('layouts.admin')

@section('page_title', 'Edit Page: ' . $page->key)
@section('page_description', 'Manage bilingual content and editable sections for this page.')

@section('content')
@php($sections = $page->sections ?? [])
<form method="post" enctype="multipart/form-data" action="{{ route('admin.pages.update', $page) }}">
    @csrf
    @method('PUT')
    <div class="card admin-card p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Title EN</label><input class="form-control" name="title_en" value="{{ old('title_en', $page->title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Title AR</label><input class="form-control" name="title_ar" value="{{ old('title_ar', $page->title_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $page->slug) }}"></div>
            <div class="col-md-4"><label class="form-label">Hero Badge EN</label><input class="form-control" name="hero_badge_en" value="{{ old('hero_badge_en', $page->hero_badge_en) }}"></div>
            <div class="col-md-4"><label class="form-label">Hero Badge AR</label><input class="form-control" name="hero_badge_ar" value="{{ old('hero_badge_ar', $page->hero_badge_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Title EN</label><input class="form-control" name="hero_title_en" value="{{ old('hero_title_en', $page->hero_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Title AR</label><input class="form-control" name="hero_title_ar" value="{{ old('hero_title_ar', $page->hero_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Subtitle EN</label><textarea class="form-control" name="hero_subtitle_en" rows="3">{{ old('hero_subtitle_en', $page->hero_subtitle_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Hero Subtitle AR</label><textarea class="form-control" name="hero_subtitle_ar" rows="3">{{ old('hero_subtitle_ar', $page->hero_subtitle_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Intro Title EN</label><input class="form-control" name="intro_title_en" value="{{ old('intro_title_en', $page->intro_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Intro Title AR</label><input class="form-control" name="intro_title_ar" value="{{ old('intro_title_ar', $page->intro_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Intro Body EN</label><textarea class="form-control" name="intro_body_en" rows="4">{{ old('intro_body_en', $page->intro_body_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Intro Body AR</label><textarea class="form-control" name="intro_body_ar" rows="4">{{ old('intro_body_ar', $page->intro_body_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Hero Image</label><input type="file" class="form-control" name="hero_image"></div>
            <div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active))><label class="form-check-label">Active</label></div>
        </div>
    </div>
    <div class="card admin-card p-4">
        <p class="mb-0 text-muted">Use the seeded content structure first, then refine section-specific text as needed from the dashboard later.</p>
    </div>
    <button class="btn btn-primary mt-3">Save Page</button>
</form>
@endsection
