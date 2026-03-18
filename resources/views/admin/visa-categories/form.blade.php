@extends('layouts.admin')
@section('page_title', $item->exists ? 'Edit Visa Category' : 'Create Visa Category')
@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.visa-categories.update', $item) : route('admin.visa-categories.store') }}">@csrf @if($item->exists) @method('PUT') @endif
<div class="card admin-card p-4"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Name EN</label><input class="form-control" name="name_en" value="{{ old('name_en', $item->name_en) }}"></div>
<div class="col-md-6"><label class="form-label">Name AR</label><input class="form-control" name="name_ar" value="{{ old('name_ar', $item->name_ar) }}"></div>
<div class="col-md-4"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}"></div>
<div class="col-md-4"><label class="form-label">Icon</label><input class="form-control" name="icon" value="{{ old('icon', $item->icon) }}"></div>
<div class="col-md-4"><label class="form-label">Order</label><input class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"></div>
<div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" name="short_description_en" rows="3">{{ old('short_description_en', $item->short_description_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control" name="short_description_ar" rows="3">{{ old('short_description_ar', $item->short_description_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Image</label><input type="file" class="form-control" name="image"></div>
<div class="col-md-3 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label">Active</label></div>
<div class="col-md-3 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item->is_featured))><label class="form-check-label">Featured</label></div>
</div></div><button class="btn btn-primary mt-3">Save</button></form>
@endsection
