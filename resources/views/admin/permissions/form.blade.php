@extends('layouts.admin')

@section('title', $isEdit ? __('admin.edit_permission') : __('admin.create_permission'))
@section('page_title', $isEdit ? __('admin.edit_permission') : __('admin.create_permission'))
@section('page_description', __('admin.permissions_form_desc'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <form method="post" action="{{ $isEdit ? route('admin.permissions.update', $item) : route('admin.permissions.store') }}">
                @csrf
                @if($isEdit)
                    @method('put')
                @endif
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.slug_key') }}</label>
                                <input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('admin.module') }}</label>
                                <input type="text" name="module" class="form-control" value="{{ old('module', $item->module) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('admin.description') }}</label>
                                <textarea name="description" class="form-control" rows="5">{{ old('description', $item->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
                        <button class="btn btn-primary">{{ $isEdit ? __('admin.update_permission') : __('admin.create_permission') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
