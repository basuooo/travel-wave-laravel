@extends('layouts.admin')

@php
    $assignments = old('assignments', $assignments ?? []);
@endphp

@section('page_title', $isEdit ? 'Edit Map Section' : 'Create Map Section')
@section('page_description', 'Manage reusable page maps, appearance, and page assignments.')

@section('content')
<form method="post" action="{{ $isEdit ? route('admin.map-sections.update', $item) : route('admin.map-sections.store') }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Core Settings</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Map Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Slug / Key</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Layout Type</label>
                <select name="layout_type" class="form-select">
                    @foreach($layoutOptions as $key => $label)
                        <option value="{{ $key }}" @selected(old('layout_type', $item->layout_type) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Title EN</label>
                <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $item->title_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Title AR</label>
                <input type="text" name="title_ar" class="form-control text-end" dir="rtl" value="{{ old('title_ar', $item->title_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle / Description EN</label>
                <textarea name="subtitle_en" class="form-control" rows="3">{{ old('subtitle_en', $item->subtitle_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Subtitle / Description AR</label>
                <textarea name="subtitle_ar" class="form-control text-end" dir="rtl" rows="3">{{ old('subtitle_ar', $item->subtitle_ar) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Address EN</label>
                <textarea name="address_en" class="form-control" rows="3">{{ old('address_en', $item->address_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Address AR</label>
                <textarea name="address_ar" class="form-control text-end" dir="rtl" rows="3">{{ old('address_ar', $item->address_ar) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Button Text EN</label>
                <input type="text" name="button_text_en" class="form-control" value="{{ old('button_text_en', $item->button_text_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Button Text AR</label>
                <input type="text" name="button_text_ar" class="form-control text-end" dir="rtl" value="{{ old('button_text_ar', $item->button_text_ar) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Button Link</label>
                <input type="text" name="button_link" class="form-control" value="{{ old('button_link', $item->button_link) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Map Embed Code</label>
                <textarea name="embed_code" class="form-control" rows="8">{{ old('embed_code', $item->embed_code) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Map URL</label>
                <textarea name="map_url" class="form-control" rows="8">{{ old('map_url', $item->map_url) }}</textarea>
                <div class="form-text">Use this if you prefer a direct map URL instead of pasting full iframe embed code.</div>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Display Options</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Map Height (px)</label>
                <input type="number" name="height" class="form-control" min="200" max="1200" value="{{ old('height', $item->height ?: 380) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Background Style</label>
                <select name="background_style" class="form-select">
                    @foreach($backgroundOptions as $key => $label)
                        <option value="{{ $key }}" @selected(old('background_style', $item->background_style) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Spacing Preset</label>
                <select name="spacing_preset" class="form-select">
                    @foreach($spacingOptions as $key => $label)
                        <option value="{{ $key }}" @selected(old('spacing_preset', $item->spacing_preset) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input type="hidden" name="rounded_corners" value="0">
                    <input type="checkbox" name="rounded_corners" value="1" class="form-check-input" id="rounded_corners" @checked(old('rounded_corners', $item->rounded_corners ?? true))>
                    <label class="form-check-label" for="rounded_corners">Use rounded corners</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="map-active" @checked(old('is_active', $item->is_active ?? true))>
                    <label class="form-check-label" for="map-active">Active</label>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Page Assignments</h2>
                <p class="text-muted mb-0">Choose where this map appears and which pages or destinations use it.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-map-assignment">Add Assignment</button>
        </div>
        <div id="map-assignments-list" class="d-grid gap-3">
            @foreach($assignments as $index => $assignment)
                @include('admin.map-sections.partials.assignment-row', ['index' => $index, 'assignment' => $assignment, 'assignmentTargets' => $assignmentTargets, 'positionOptions' => $positionOptions])
            @endforeach
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">{{ $isEdit ? 'Update Map Section' : 'Create Map Section' }}</button>
        <a href="{{ route('admin.map-sections.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

@include('admin.map-sections.partials.templates')
@endsection
