@extends('layouts.admin')

@section('page_title', $item->exists ? 'Edit Homepage Country Item' : 'Create Homepage Country Item')
@section('page_description', 'Upload the country icon/card image, manage bilingual names, choose the destination link, and control order and homepage visibility.')

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.home-country-strip.update', $item) : route('admin.home-country-strip.store') }}">
    @csrf
    @if($item->exists)
        @method('PUT')
    @endif
    <div class="card admin-card p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Linked Visa Country Page</label>
                <select class="form-select" name="visa_country_id">
                    <option value="">Custom URL / no linked country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" @selected(old('visa_country_id', $item->visa_country_id) == $country->id)>{{ $country->name_en }}</option>
                    @endforeach
                </select>
                <div class="form-text">Choose an existing visa country to auto-use its page link. Names, subtitle, image, and flag can still be customized here.</div>
            </div>
            <div class="col-md-6"><label class="form-label">Custom URL</label><input class="form-control" name="custom_url" value="{{ old('custom_url', $item->custom_url) }}"></div>
            <div class="col-md-6"><label class="form-label">Country Name EN</label><input class="form-control" name="name_en" value="{{ old('name_en', $item->name_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Country Name AR</label><input class="form-control text-end" dir="rtl" name="name_ar" value="{{ old('name_ar', $item->name_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Subtitle EN</label><input class="form-control" name="subtitle_en" value="{{ old('subtitle_en', $item->subtitle_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Subtitle AR</label><input class="form-control text-end" dir="rtl" name="subtitle_ar" value="{{ old('subtitle_ar', $item->subtitle_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Destination Image</label><input type="file" class="form-control" name="image" accept="image/*"></div>
            <div class="col-md-6"><label class="form-label">Flag Image</label><input type="file" class="form-control" name="flag_image" accept="image/*,.svg"></div>
            <div class="col-md-3"><label class="form-label">Display Order</label><input class="form-control" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"></div>
            <div class="col-md-3 d-flex align-items-end"><div class="form-check pb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label" for="is_active">Active</label></div></div>
            <div class="col-md-3 d-flex align-items-end"><div class="form-check pb-2"><input class="form-check-input" type="checkbox" name="show_on_homepage" value="1" id="show_on_homepage" @checked(old('show_on_homepage', $item->show_on_homepage ?? true))><label class="form-check-label" for="show_on_homepage">Show on homepage</label></div></div>
            @if($item->image_path || $item->flag_image_path)
                <div class="col-12">
                    <div class="row g-3">
                        @if($item->image_path)
                            <div class="col-md-6">
                                <div class="small text-muted mb-2">Current destination image</div>
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->displayName('en') }}" class="img-fluid rounded-4 border" style="max-height: 140px; object-fit: cover;">
                            </div>
                        @endif
                        @if($item->flag_image_path)
                            <div class="col-md-6">
                                <div class="small text-muted mb-2">Current flag image</div>
                                <img src="{{ asset('storage/' . $item->flag_image_path) }}" alt="{{ $item->displayName('en') }} flag" class="img-fluid rounded-4 border" style="max-height: 140px; object-fit: contain; background:#fff;">
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    <button class="btn btn-primary mt-3 px-4">Save Country Item</button>
</form>
@endsection
