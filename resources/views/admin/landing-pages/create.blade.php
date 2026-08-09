@extends('layouts.admin')

@section('page_title', __('admin.create_landing_page'))
@section('page_description', 'Choose between starting from scratch with a blank canvas or cloning a pre-built template.')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-sm btn-outline-secondary mb-2">← Back to All Pages</a>
    <h3 class="fw-bold mb-1">✨ {{ __('admin.create_landing_page') }}</h3>
    <p class="text-muted">Select how you want to start building your landing page.</p>
</div>

<div class="row g-4 mb-5">
    <!-- From Scratch -->
    <div class="col-md-6">
        <div class="card admin-card p-4 h-100 border-2 border-primary shadow-sm hover-shadow">
            <div class="text-primary fs-1 mb-3"><i class="bi bi-file-earmark-plus"></i></div>
            <h4 class="fw-bold text-dark mb-2">1. Start From Scratch</h4>
            <p class="text-muted small mb-4">Begin with a completely empty canvas and build your layout using components and sections library.</p>
            <form method="post" action="{{ route('admin.landing-pages.store') }}">
                @csrf
                <input type="hidden" name="creation_mode" value="scratch">
                <div class="mb-3">
                    <label class="form-label fw-bold">Internal Page Name</label>
                    <input type="text" name="internal_name" class="form-control" placeholder="e.g. Summer Visa Campaign 2026" required>
                </div>
                <button type="submit" class="btn btn-primary fw-bold w-100 py-2">
                    Start Blank Canvas →
                </button>
            </form>
        </div>
    </div>

    <!-- From Template -->
    <div class="col-md-6">
        <div class="card admin-card p-4 h-100 border-2 border-warning shadow-sm hover-shadow">
            <div class="text-warning fs-1 mb-3"><i class="bi bi-grid-1x2"></i></div>
            <h4 class="fw-bold text-dark mb-2">2. Choose From Template</h4>
            <p class="text-muted small mb-4">Clone a pre-designed, high-converting template snapshot without affecting the original master template.</p>
            <form method="post" action="{{ route('admin.landing-pages.store') }}">
                @csrf
                <input type="hidden" name="creation_mode" value="template">
                <div class="mb-3">
                    <label class="form-label fw-bold">Internal Page Name</label>
                    <input type="text" name="internal_name" class="form-control" placeholder="e.g. France Visa Promo Page" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Template</label>
                    <select name="template_id" class="form-select" required>
                        <option value="">-- Choose Template --</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name_en }} ({{ $template->category?->name_en ?? 'General' }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-warning text-dark fw-bold w-100 py-2">
                    Clone Template & Edit →
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Template Library Preview Grid -->
<div class="card admin-card p-4">
    <h5 class="fw-bold mb-3">🎨 Available Master Templates</h5>
    <div class="row g-3">
        @forelse($templates as $template)
        <div class="col-md-4">
            <div class="card border rounded p-3 h-100">
                <div class="bg-light rounded p-4 text-center mb-3">
                    <i class="bi bi-window fs-1 text-muted"></i>
                </div>
                <h6 class="fw-bold mb-1">{{ $template->name_en }}</h6>
                <span class="badge bg-secondary-subtle text-secondary w-fit mb-2">{{ $template->category?->name_en ?? 'General' }}</span>
                <p class="text-muted small mb-0">{{ $template->description_en ?: 'Pre-built high converting travel template.' }}</p>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-4 text-muted">No pre-built templates available. Starting from scratch will automatically generate default high-converting sections!</div>
        @endforelse
    </div>
</div>
@endsection
