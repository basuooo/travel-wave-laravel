@extends('layouts.admin')
@section('page_title', $item->exists ? 'Edit Menu Item' : 'Create Menu Item')
@section('content')
<form method="post" action="{{ $item->exists ? route('admin.menu-items.update', $item) : route('admin.menu-items.store') }}">@csrf @if($item->exists) @method('PUT') @endif
<div class="card admin-card p-4"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Location</label><select class="form-select" name="location"><option value="header" @selected(old('location', $item->location) === 'header')>Header</option><option value="footer" @selected(old('location', $item->location) === 'footer')>Footer</option></select></div>
<div class="col-md-4"><label class="form-label">Parent</label><select class="form-select" name="parent_id"><option value="">None</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected(old('parent_id', $item->parent_id) == $parent->id)>{{ $parent->title_en }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Order</label><input class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"></div>
<div class="col-md-6"><label class="form-label">Title EN</label><input class="form-control" name="title_en" value="{{ old('title_en', $item->title_en) }}"></div>
<div class="col-md-6"><label class="form-label">Title AR</label><input class="form-control" name="title_ar" value="{{ old('title_ar', $item->title_ar) }}"></div>
<div class="col-md-6"><label class="form-label">URL</label><input class="form-control" name="url" value="{{ old('url', $item->url) }}"></div>
<div class="col-md-6"><label class="form-label">Route Name</label><input class="form-control" name="route_name" value="{{ old('route_name', $item->route_name) }}"></div>
<div class="col-md-3 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label">Active</label></div>
</div></div><button class="btn btn-primary mt-3">Save</button></form>
@endsection
