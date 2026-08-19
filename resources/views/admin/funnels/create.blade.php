@extends('layouts.admin')

@section('title', __('admin.create_new_funnel'))

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h3 fw-bold">🚀 Create Interactive Funnel</h1>
        <p class="text-muted">Select how you want to build your new funnel</p>
    </div>

    <form action="{{ route('admin.funnels.store') }}" method="POST">
        @csrf

        <div class="row g-4 mb-4">
            <!-- Scratch -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center option-card position-relative">
                    <input class="form-check-input position-absolute top-0 end-0 m-3" type="radio" name="creation_type" id="type_scratch" value="scratch" checked>
                    <label for="type_scratch" class="w-100 cursor-pointer">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle d-inline-flex mb-3">
                            <iconify-icon icon="solar:document-add-bold-duotone" width="40"></iconify-icon>
                        </div>
                        <h5 class="fw-bold">Start From Scratch</h5>
                        <p class="text-muted small mb-0">Build a completely blank funnel step by step with custom elements.</p>
                    </label>
                </div>
            </div>

            <!-- Template -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center option-card position-relative">
                    <input class="form-check-input position-absolute top-0 end-0 m-3" type="radio" name="creation_type" id="type_template" value="template">
                    <label for="type_template" class="w-100 cursor-pointer">
                        <div class="bg-success-subtle text-success p-3 rounded-circle d-inline-flex mb-3">
                            <iconify-icon icon="solar:magic-stick-3-bold-duotone" width="40"></iconify-icon>
                        </div>
                        <h5 class="fw-bold">Use Template</h5>
                        <p class="text-muted small mb-0">Pick from our pre-built high-converting Travel & Visa templates.</p>
                    </label>
                </div>
            </div>

            <!-- Duplicate -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center option-card position-relative">
                    <input class="form-check-input position-absolute top-0 end-0 m-3" type="radio" name="creation_type" id="type_duplicate" value="duplicate">
                    <label for="type_duplicate" class="w-100 cursor-pointer">
                        <div class="bg-warning-subtle text-warning p-3 rounded-circle d-inline-flex mb-3">
                            <iconify-icon icon="solar:copy-bold-duotone" width="40"></iconify-icon>
                        </div>
                        <h5 class="fw-bold">Duplicate Existing</h5>
                        <p class="text-muted small mb-0">Clone an existing active funnel with all steps, questions & logic.</p>
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Details -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Funnel Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="funnel_name_input" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="e.g. Schengen Visa Eligibility Quiz" value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Custom Slug (Public URL) <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light text-muted fw-semibold">/f/</span>
                    <input type="text" name="slug" id="funnel_slug_input" class="form-control @error('slug') is-invalid @enderror" placeholder="schengen-visa" value="{{ old('slug') }}" required>
                </div>
                @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Template Select (Hidden unless template option selected) -->
            <div class="mb-3 d-none" id="template_select_wrapper">
                <label class="form-label fw-bold">Select Template Preset</label>
                <select name="template_id" class="form-select form-select-lg">
                    @foreach($templates as $tpl)
                        <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->category }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Duplicate Select (Hidden unless duplicate option selected) -->
            <div class="mb-3 d-none" id="duplicate_select_wrapper">
                <label class="form-label fw-bold">Select Source Funnel to Duplicate</label>
                <select name="duplicate_funnel_id" class="form-select form-select-lg">
                    @foreach($existingFunnels as $ef)
                        <option value="{{ $ef->id }}">{{ $ef->name }} (/f/{{ $ef->slug }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.funnels.index') }}" class="btn btn-light btn-lg px-4">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">Create Funnel 🚀</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('funnel_name_input');
    const slugInput = document.getElementById('funnel_slug_input');
    const templateWrapper = document.getElementById('template_select_wrapper');
    const duplicateWrapper = document.getElementById('duplicate_select_wrapper');

    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.edited) {
            slugInput.value = nameInput.value.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        }
    });

    slugInput.addEventListener('input', function() {
        slugInput.dataset.edited = 'true';
    });

    document.querySelectorAll('input[name="creation_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            templateWrapper.classList.toggle('d-none', this.value !== 'template');
            duplicateWrapper.classList.toggle('d-none', this.value !== 'duplicate');
        });
    });
});
</script>
@endsection
