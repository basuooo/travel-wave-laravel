@extends('layouts.admin')

@php
    $selectedTargets = old('visibility_targets', $item->visibility_targets ?? []);
@endphp

@section('page_title', $isEdit ? __('admin.edit_tracking') : __('admin.create_tracking'))
@section('page_description', __('admin.tracking_manager_desc'))

@section('content')
<form method="post" action="{{ $isEdit ? route('admin.tracking-integrations.update', $item) : route('admin.tracking-integrations.store') }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.core_settings') }}</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.name') }}</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.slug_key') }}</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.type') }}</label>
                <select name="integration_type" class="form-select">
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('integration_type', $item->integration_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.platform') }}</label>
                <input type="text" name="platform" class="form-control" value="{{ old('platform', $item->platform) }}" placeholder="Google / Meta / TikTok">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.tracking_code') }}</label>
                <input type="text" name="tracking_code" class="form-control" value="{{ old('tracking_code', $item->tracking_code) }}" placeholder="GTM-XXXX / G-XXXX / 123456789">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.placement') }}</label>
                <select name="placement" class="form-select">
                    @foreach($placementOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('placement', $item->placement) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.sort_order') }}</label>
                <input type="number" name="sort_order" class="form-control" min="0" max="9999" value="{{ old('sort_order', $item->sort_order) }}">
            </div>
            <div class="col-md-8 d-flex align-items-end">
                <div class="form-check mb-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="tracking-active" @checked(old('is_active', $item->is_active ?? true))>
                    <label class="form-check-label" for="tracking-active">{{ __('admin.active') }}</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.script_code') }}</label>
                <textarea name="script_code" class="form-control" rows="8" placeholder="{{ __('admin.script_code_placeholder') }}">{{ old('script_code', $item->script_code) }}</textarea>
                <div class="form-text">{{ __('admin.script_code_help') }}</div>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.notes') }}</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $item->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.visibility_rules') }}</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.visibility_mode') }}</label>
                <select name="visibility_mode" class="form-select">
                    @foreach($visibilityModes as $value => $label)
                        <option value="{{ $value }}" @selected(old('visibility_mode', $item->visibility_mode ?: 'all') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">{{ __('admin.selected_targets') }}</label>
                <select name="visibility_targets[]" class="form-select" multiple size="14">
                    @foreach($visibilityTargets as $groupLabel => $options)
                        <optgroup label="{{ $groupLabel }}">
                            @foreach($options as $value => $label)
                                <option value="{{ $value }}" @selected(in_array($value, $selectedTargets, true))>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <div class="form-text">{{ __('admin.tracking_visibility_help') }}</div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">{{ $isEdit ? __('admin.update') : __('admin.create') }}</button>
        <a href="{{ route('admin.tracking-integrations.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
    </div>
</form>
@endsection
