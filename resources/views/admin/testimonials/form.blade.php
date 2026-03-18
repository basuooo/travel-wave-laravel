@extends('layouts.admin')
@section('page_title', $item->exists ? 'Edit Testimonial' : 'Create Testimonial')
@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.testimonials.update', $item) : route('admin.testimonials.store') }}">@csrf @if($item->exists) @method('PUT') @endif
<div class="card admin-card p-4"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Client Name</label><input class="form-control" name="client_name" value="{{ old('client_name', $item->client_name) }}"></div>
<div class="col-md-3"><label class="form-label">Role EN</label><input class="form-control" name="client_role_en" value="{{ old('client_role_en', $item->client_role_en) }}"></div>
<div class="col-md-3"><label class="form-label">Role AR</label><input class="form-control" name="client_role_ar" value="{{ old('client_role_ar', $item->client_role_ar) }}"></div>
<div class="col-md-6"><label class="form-label">Testimonial EN</label><textarea class="form-control" name="testimonial_en" rows="4">{{ old('testimonial_en', $item->testimonial_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Testimonial AR</label><textarea class="form-control" name="testimonial_ar" rows="4">{{ old('testimonial_ar', $item->testimonial_ar) }}</textarea></div>
<div class="col-md-3"><label class="form-label">Rating</label><input class="form-control" name="rating" value="{{ old('rating', $item->rating ?: 5) }}"></div>
<div class="col-md-3"><label class="form-label">Order</label><input class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"></div>
<div class="col-md-6"><label class="form-label">Image</label><input type="file" class="form-control" name="image"></div>
<div class="col-md-3 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label">Active</label></div>
</div></div><button class="btn btn-primary mt-3">Save</button></form>
@endsection
