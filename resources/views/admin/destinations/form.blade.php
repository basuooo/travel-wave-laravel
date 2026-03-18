@extends('layouts.admin')
@section('page_title', $item->exists ? 'Edit Destination' : 'Create Destination')
@section('page_description', 'Manage destination highlights, packages, inclusions, itinerary, gallery, FAQs, and CTA content.')
@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.destinations.update', $item) : route('admin.destinations.store') }}">
@csrf @if($item->exists) @method('PUT') @endif
<div class="card admin-card p-4 mb-4"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" name="title_en" value="{{ old('title_en', $item->title_en) }}"></div>
<div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" name="title_ar" value="{{ old('title_ar', $item->title_ar) }}"></div>
<div class="col-md-4"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}"></div>
<div class="col-md-6"><label class="form-label">Hero Title EN</label><input class="form-control" name="hero_title_en" value="{{ old('hero_title_en', $item->hero_title_en) }}"></div>
<div class="col-md-6"><label class="form-label">Hero Title AR</label><input class="form-control text-end" dir="rtl" name="hero_title_ar" value="{{ old('hero_title_ar', $item->hero_title_ar) }}"></div>
<div class="col-md-6"><label class="form-label">Excerpt EN</label><textarea class="form-control" name="excerpt_en" rows="3">{{ old('excerpt_en', $item->excerpt_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Excerpt AR</label><textarea class="form-control text-end" dir="rtl" name="excerpt_ar" rows="3">{{ old('excerpt_ar', $item->excerpt_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Overview EN</label><textarea class="form-control" name="overview_en" rows="5">{{ old('overview_en', $item->overview_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Overview AR</label><textarea class="form-control text-end" dir="rtl" name="overview_ar" rows="5">{{ old('overview_ar', $item->overview_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Hero Image</label><input type="file" class="form-control" name="hero_image"></div>
<div class="col-md-6"><label class="form-label">Gallery Images</label><input type="file" class="form-control" name="gallery_files[]" multiple></div>
<div class="col-md-2"><label class="form-label">Order</label><input class="form-control" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"></div>
<div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label">Active</label></div>
<div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item->is_featured))><label class="form-check-label">Featured</label></div>
</div></div>
<div class="card admin-card p-4 mb-4">
<h2 class="h5 mb-3">Highlights, Packages, Included/Excluded, Itinerary, and FAQs</h2>
@for($i = 0; $i < 6; $i++)
<div class="row g-3 mb-4 border-bottom pb-3">
<div class="col-md-6"><label class="form-label">Highlight EN</label><input class="form-control" name="highlights_en[]" value="{{ $item->highlights[$i]['text_en'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Highlight AR</label><input class="form-control text-end" dir="rtl" name="highlights_ar[]" value="{{ $item->highlights[$i]['text_ar'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Package EN</label><input class="form-control" name="packages_en[]" value="{{ $item->packages[$i]['text_en'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Package AR</label><input class="form-control text-end" dir="rtl" name="packages_ar[]" value="{{ $item->packages[$i]['text_ar'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Included EN</label><input class="form-control" name="included_en[]" value="{{ $item->included_items[$i]['text_en'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Included AR</label><input class="form-control text-end" dir="rtl" name="included_ar[]" value="{{ $item->included_items[$i]['text_ar'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Excluded EN</label><input class="form-control" name="excluded_en[]" value="{{ $item->excluded_items[$i]['text_en'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Excluded AR</label><input class="form-control text-end" dir="rtl" name="excluded_ar[]" value="{{ $item->excluded_items[$i]['text_ar'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Itinerary EN</label><input class="form-control" name="itinerary_en[]" value="{{ $item->itinerary[$i]['text_en'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">Itinerary AR</label><input class="form-control text-end" dir="rtl" name="itinerary_ar[]" value="{{ $item->itinerary[$i]['text_ar'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">FAQ Question EN</label><input class="form-control" name="faq_question_en[]" value="{{ $item->faqs[$i]['question_en'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">FAQ Question AR</label><input class="form-control text-end" dir="rtl" name="faq_question_ar[]" value="{{ $item->faqs[$i]['question_ar'] ?? '' }}"></div>
<div class="col-md-6"><label class="form-label">FAQ Answer EN</label><textarea class="form-control" name="faq_answer_en[]" rows="2">{{ $item->faqs[$i]['answer_en'] ?? '' }}</textarea></div>
<div class="col-md-6"><label class="form-label">FAQ Answer AR</label><textarea class="form-control text-end" dir="rtl" name="faq_answer_ar[]" rows="2">{{ $item->faqs[$i]['answer_ar'] ?? '' }}</textarea></div>
</div>
@endfor
</div>
<button class="btn btn-primary">Save Destination</button>
</form>
@endsection
