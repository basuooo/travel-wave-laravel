@extends('layouts.admin')
@section('page_title', $item->exists ? 'Edit Destination' : 'Create Destination')
@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.destinations.update', $item) : route('admin.destinations.store') }}">@csrf @if($item->exists) @method('PUT') @endif
<div class="card admin-card p-4"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" name="title_en" value="{{ old('title_en', $item->title_en) }}"></div>
<div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control" name="title_ar" value="{{ old('title_ar', $item->title_ar) }}"></div>
<div class="col-md-4"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}"></div>
<div class="col-md-6"><label class="form-label">Excerpt EN</label><textarea class="form-control" name="excerpt_en" rows="3">{{ old('excerpt_en', $item->excerpt_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Excerpt AR</label><textarea class="form-control" name="excerpt_ar" rows="3">{{ old('excerpt_ar', $item->excerpt_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Overview EN</label><textarea class="form-control" name="overview_en" rows="4">{{ old('overview_en', $item->overview_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Overview AR</label><textarea class="form-control" name="overview_ar" rows="4">{{ old('overview_ar', $item->overview_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Hero Image</label><input type="file" class="form-control" name="hero_image"></div>
<div class="col-md-6"><label class="form-label">Gallery Images</label><input type="file" class="form-control" name="gallery_files[]" multiple></div>
<div class="col-md-2"><label class="form-label">Order</label><input class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"></div>
<div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label">Active</label></div>
<div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item->is_featured))><label class="form-check-label">Featured</label></div>
</div></div><button class="btn btn-primary mt-3">Save</button></form>
@endsection
