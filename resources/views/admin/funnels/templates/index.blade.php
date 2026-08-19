@extends('layouts.admin')

@section('title', __('admin.templates_library'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">🎨 {{ __('admin.templates_library') }}</h1>
            <p class="text-muted mb-0">Select from high-converting, pre-built funnel templates</p>
        </div>
        <a href="{{ route('admin.funnels.create') }}" class="btn btn-outline-secondary">
            ← Back to Funnels
        </a>
    </div>

    <!-- Category Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.funnels.templates.index') }}" class="btn btn-sm {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }}">
            All Templates
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.funnels.templates.index', ['category' => $cat]) }}" class="btn btn-sm {{ request('category') === $cat ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Templates Grid -->
    <div class="row g-4">
        @forelse($templates as $template)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="bg-primary-subtle text-primary p-4 text-center">
                        <iconify-icon icon="solar:magic-stick-3-bold-duotone" width="64"></iconify-icon>
                    </div>
                    <div class="card-body p-4 d-flex flex-direction-column justify-content-between">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-2">{{ $template->category }}</span>
                            <h5 class="fw-bold text-dark">{{ $template->name }}</h5>
                            <p class="text-muted small">{{ $template->description }}</p>
                        </div>
                        <form action="{{ route('admin.funnels.templates.use', $template) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                🚀 Use Template
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                No templates found in this category.
            </div>
        @endforelse
    </div>
</div>
@endsection
