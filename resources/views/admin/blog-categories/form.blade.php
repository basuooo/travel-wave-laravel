@extends('layouts.admin')
@section('page_title', $item->exists ? 'Edit Blog Category' : 'Create Blog Category')
@section('content')
<form method="post" action="{{ $item->exists ? route('admin.blog-categories.update', $item) : route('admin.blog-categories.store') }}">@csrf @if($item->exists) @method('PUT') @endif
<div class="card admin-card p-4"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Name EN</label><input class="form-control" name="name_en" value="{{ old('name_en', $item->name_en) }}"></div>
<div class="col-md-6"><label class="form-label">Name AR</label><input class="form-control" name="name_ar" value="{{ old('name_ar', $item->name_ar) }}"></div>
<div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}"></div>
<div class="col-md-6"><label class="form-label">Order</label><input class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"></div>
<div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" name="description_en" rows="3">{{ old('description_en', $item->description_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control" name="description_ar" rows="3">{{ old('description_ar', $item->description_ar) }}</textarea></div>
<div class="col-md-3 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label">Active</label></div>
</div></div><button class="btn btn-primary mt-3">Save</button></form>
@endsection
