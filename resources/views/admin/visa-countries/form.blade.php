@extends('layouts.admin')
@section('page_title', $item->exists ? 'Edit Visa Country' : 'Create Visa Country')
@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.visa-countries.update', $item) : route('admin.visa-countries.store') }}">@csrf @if($item->exists) @method('PUT') @endif
<div class="card admin-card p-4 mb-4"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Category</label><select class="form-select" name="visa_category_id">@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('visa_category_id', $item->visa_category_id) == $category->id)>{{ $category->name_en }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Name EN</label><input class="form-control" name="name_en" value="{{ old('name_en', $item->name_en) }}"></div>
<div class="col-md-4"><label class="form-label">Name AR</label><input class="form-control" name="name_ar" value="{{ old('name_ar', $item->name_ar) }}"></div>
<div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}"></div>
<div class="col-md-6"><label class="form-label">Hero Image</label><input type="file" class="form-control" name="hero_image"></div>
<div class="col-md-6"><label class="form-label">Excerpt EN</label><textarea class="form-control" name="excerpt_en" rows="3">{{ old('excerpt_en', $item->excerpt_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Excerpt AR</label><textarea class="form-control" name="excerpt_ar" rows="3">{{ old('excerpt_ar', $item->excerpt_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Overview EN</label><textarea class="form-control" name="overview_en" rows="4">{{ old('overview_en', $item->overview_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Overview AR</label><textarea class="form-control" name="overview_ar" rows="4">{{ old('overview_ar', $item->overview_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Processing Time EN</label><textarea class="form-control" name="processing_time_en" rows="2">{{ old('processing_time_en', $item->processing_time_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Processing Time AR</label><textarea class="form-control" name="processing_time_ar" rows="2">{{ old('processing_time_ar', $item->processing_time_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Fees EN</label><textarea class="form-control" name="fees_en" rows="3">{{ old('fees_en', $item->fees_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Fees AR</label><textarea class="form-control" name="fees_ar" rows="3">{{ old('fees_ar', $item->fees_ar) }}</textarea></div>
<div class="col-md-2"><label class="form-label">Order</label><input class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"></div>
<div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label">Active</label></div>
<div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item->is_featured))><label class="form-check-label">Featured</label></div>
</div></div><button class="btn btn-primary">Save</button></form>
@endsection
