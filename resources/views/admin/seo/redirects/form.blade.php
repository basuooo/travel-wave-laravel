@extends('layouts.admin')

@section('page_title', __('admin.seo_redirects_manager'))
@section('page_description', $isEdit ? __('admin.edit') : __('admin.create'))

@section('content')
<form method="post" action="{{ $isEdit ? route('admin.seo.redirects.update', $item) : route('admin.seo.redirects.store') }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="card admin-card p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">{{ __('admin.seo_source_url') }}</label><input type="text" name="source_path" class="form-control" value="{{ old('source_path', $item->source_path) }}" placeholder="/old-path"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.seo_destination_url') }}</label><input type="text" name="destination_url" class="form-control" value="{{ old('destination_url', $item->destination_url) }}" placeholder="/new-path-or-url"></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.type') }}</label><select name="redirect_type" class="form-select"><option value="301" @selected(old('redirect_type', $item->redirect_type) == 301)>301</option><option value="302" @selected(old('redirect_type', $item->redirect_type) == 302)>302</option></select></div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $item->is_active))>
                    <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                </div>
            </div>
            <div class="col-12"><label class="form-label">{{ __('admin.notes') }}</label><textarea name="notes" class="form-control" rows="4">{{ old('notes', $item->notes) }}</textarea></div>
        </div>
    </div>
    <button class="btn btn-primary">{{ $isEdit ? __('admin.update') : __('admin.create') }}</button>
</form>
@endsection
