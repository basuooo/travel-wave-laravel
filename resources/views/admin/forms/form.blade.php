@extends('layouts.admin')

@php
    $fields = old('fields', $fields ?? []);
    $assignments = old('assignments', $assignments ?? []);
    $settings = old('settings', $item->settings ?? []);
    $infoItems = old('settings.info_items', $settings['info_items'] ?? []);
@endphp
@section('page_title', $isEdit ? 'Edit Form' : 'Create Form')
@section('page_description', 'Manage dynamic fields, assignments, and submission behavior for a reusable form.')

@section('content')
<form method="post" action="{{ $isEdit ? route('admin.forms.update', $item) : route('admin.forms.store') }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="core-tab" data-bs-toggle="tab" data-bs-target="#core" type="button" role="tab" aria-controls="core" aria-selected="true">
                <i class="fas fa-cog me-1"></i> Core Settings
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="layout-tab" data-bs-toggle="tab" data-bs-target="#layout" type="button" role="tab" aria-controls="layout" aria-selected="false">
                <i class="fas fa-layer-group me-1"></i> Layout & Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="fields-tab" data-bs-toggle="tab" data-bs-target="#fields" type="button" role="tab" aria-controls="fields" aria-selected="false">
                <i class="fas fa-list-ul me-1"></i> Dynamic Fields
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab" aria-controls="assignments" aria-selected="false">
                <i class="fas fa-map-marker-alt me-1"></i> Page Assignments
            </button>
        </li>
    </ul>

    <div class="tab-content" id="formTabsContent">
        <!-- Core Settings Tab -->
        <div class="tab-pane fade show active" id="core" role="tabpanel" aria-labelledby="core-tab">
            <div class="card admin-card p-4 mb-4 border-top-0 rounded-bottom-4 rounded-top-0 mt-[-1.5rem]">
                <h2 class="h5 mb-3 text-primary">Core Settings</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Form Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug / Key</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="form_category" class="form-select">
                            <option value="">General</option>
                            @foreach($categoryOptions as $key => $label)
                                <option value="{{ $key }}" @selected(old('form_category', $item->form_category) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Title EN</label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $item->title_en) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Title AR</label>
                        <input type="text" dir="rtl" name="title_ar" class="form-control text-end" value="{{ old('title_ar', $item->title_ar) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subtitle EN</label>
                        <textarea name="subtitle_en" class="form-control" rows="3">{{ old('subtitle_en', $item->subtitle_en) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subtitle AR</label>
                        <textarea name="subtitle_ar" class="form-control text-end" dir="rtl" rows="3">{{ old('subtitle_ar', $item->subtitle_ar) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Submit Button EN</label>
                        <input type="text" name="submit_text_en" class="form-control" value="{{ old('submit_text_en', $item->submit_text_en) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Submit Button AR</label>
                        <input type="text" name="submit_text_ar" class="form-control text-end" dir="rtl" value="{{ old('submit_text_ar', $item->submit_text_ar) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Success Message EN</label>
                        <textarea name="success_message_en" class="form-control" rows="3">{{ old('success_message_en', $item->success_message_en) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Success Message AR</label>
                        <textarea name="success_message_ar" class="form-control text-end" dir="rtl" rows="3">{{ old('success_message_ar', $item->success_message_ar) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check mt-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="form-active" @checked(old('is_active', $item->is_active ?? true))>
                            <label class="form-check-label fw-bold" for="form-active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layout and Details Tab -->
        <div class="tab-pane fade" id="layout" role="tabpanel" aria-labelledby="layout-tab">
            <div class="card admin-card p-4 mb-4 border-top-0 rounded-bottom-4 rounded-top-0 mt-[-1.5rem]">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h5 mb-1 text-primary">Layout and Details Side</h2>
                        <p class="text-muted mb-0">Choose how this managed form renders and configure the details panel that appears beside the form in split layouts.</p>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-form-info-item">
                        <i class="fas fa-plus me-1"></i> Add Info Card
                    </button>
                </div>
                <div class="row g-3 mb-4 p-3 bg-light rounded-3">
                    <div class="col-md-4">
                        <label class="form-label">Layout Type</label>
                        <select name="settings[layout_type]" class="form-select">
                            @foreach($layoutTypeOptions as $key => $label)
                                <option value="{{ $key }}" @selected(($settings['layout_type'] ?? $settings['layout_variant'] ?? 'standard') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Info Label EN</label>
                        <input type="text" name="settings[info_label_en]" class="form-control" value="{{ $settings['info_label_en'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Info Label AR</label>
                        <input type="text" name="settings[info_label_ar]" dir="rtl" class="form-control text-end" value="{{ $settings['info_label_ar'] ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Info Heading EN</label>
                        <input type="text" name="settings[info_heading_en]" class="form-control" value="{{ $settings['info_heading_en'] ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Info Heading AR</label>
                        <input type="text" name="settings[info_heading_ar]" dir="rtl" class="form-control text-end" value="{{ $settings['info_heading_ar'] ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Info Description EN</label>
                        <textarea name="settings[info_description_en]" class="form-control" rows="3">{{ $settings['info_description_en'] ?? '' }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Info Description AR</label>
                        <textarea name="settings[info_description_ar]" dir="rtl" class="form-control text-end" rows="3">{{ $settings['info_description_ar'] ?? '' }}</textarea>
                    </div>
                </div>
                <div id="form-info-items-list" class="d-grid gap-3">
                    @foreach($infoItems as $index => $infoItem)
                        @include('admin.forms.partials.info-item-row', ['index' => $index, 'item' => $infoItem])
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Dynamic Fields Tab -->
        <div class="tab-pane fade" id="fields" role="tabpanel" aria-labelledby="fields-tab">
            <div class="card admin-card p-4 mb-4 border-top-0 rounded-bottom-4 rounded-top-0 mt-[-1.5rem]">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="h5 mb-1 text-primary">Dynamic Fields</h2>
                        <p class="text-muted mb-0">Add, reorder, and control the fields shown in this form. Each field can be enabled or hidden, required or optional.</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="add-form-field">
                        <i class="fas fa-plus me-1"></i> Add New Field
                    </button>
                </div>
                <div id="form-fields-list" class="d-grid gap-4">
                    @foreach($fields as $index => $field)
                        @include('admin.forms.partials.field-row', ['index' => $index, 'field' => $field, 'fieldTypeOptions' => $fieldTypeOptions])
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Assignments Tab -->
        <div class="tab-pane fade" id="assignments" role="tabpanel" aria-labelledby="assignments-tab">
            <div class="card admin-card p-4 mb-4 border-top-0 rounded-bottom-4 rounded-top-0 mt-[-1.5rem]">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h2 class="h5 mb-1 text-primary">Page Assignments</h2>
                        <p class="text-muted mb-0">Choose where the form appears and which page or destination it belongs to.</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="add-form-assignment">
                        <i class="fas fa-plus me-1"></i> Add Assignment
                    </button>
                </div>
                <div id="form-assignments-list" class="d-grid gap-3">
                    @foreach($assignments as $index => $assignment)
                        @include('admin.forms.partials.assignment-row', ['index' => $index, 'assignment' => $assignment, 'assignmentTargets' => $assignmentTargets, 'positionOptions' => $positionOptions])
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 sticky-bottom bg-white p-3 border-top rounded-top-3 shadow-sm mt-4 z-3">
        <button class="btn btn-primary px-4 fw-bold">{{ $isEdit ? 'Update Form' : 'Create Form' }}</button>
        <a href="{{ route('admin.forms.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </div>
</form>
<datalist id="field-keys-list"></datalist>

@include('admin.forms.partials.templates')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Custom vanilla JS tab switcher just in case bootstrap.js is not loaded
    const tabButtons = document.querySelectorAll('#formTabs .nav-link');
    const tabPanes = document.querySelectorAll('#formTabsContent .tab-pane');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-bs-target');
            
            // Deactivate all
            tabButtons.forEach(b => {
                b.classList.remove('active', 'fw-bold');
                b.setAttribute('aria-selected', 'false');
            });
            tabPanes.forEach(p => p.classList.remove('show', 'active'));
            
            // Activate current
            btn.classList.add('active', 'fw-bold');
            btn.setAttribute('aria-selected', 'true');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
    });
});
</script>
@endpush
@endsection
