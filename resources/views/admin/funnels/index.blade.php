@extends('layouts.admin')

@section('title', __('admin.funnels_list'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">⚡ {{ __('admin.funnels_list') }}</h1>
            <p class="text-muted mb-0">Manage all interactive funnels, publication status, and custom slugs</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.funnels.templates.index') }}" class="btn btn-outline-primary">
                🎨 Templates
            </a>
            <a href="{{ route('admin.funnels.create') }}" class="btn btn-primary fw-bold">
                ➕ Create Funnel
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.funnels.index') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search funnel name or slug..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- All Statuses --</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="unpublished" {{ request('status') === 'unpublished' ? 'selected' : '' }}>Unpublished</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Funnels List -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Funnel Name</th>
                        <th>Slug / Public URL</th>
                        <th>Status</th>
                        <th>Template</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($funnels as $funnel)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $funnel->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border">
                                    /f/{{ $funnel->slug }}
                                </span>
                                <a href="{{ $funnel->publicUrl() }}" target="_blank" class="ms-1 text-decoration-none">🔗</a>
                            </td>
                            <td>
                                @if($funnel->status === 'published')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Published</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">Draft</span>
                                @endif
                            </td>
                            <td>{{ $funnel->template?->name ?: 'Scratch' }}</td>
                            <td class="text-muted small">{{ $funnel->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.funnels.builder', $funnel) }}" class="btn btn-sm btn-outline-primary">
                                        ✏️ Builder
                                    </a>
                                    <a href="{{ route('admin.funnels.analytics', $funnel) }}" class="btn btn-sm btn-outline-info">
                                        📊 Analytics
                                    </a>
                                    @if($funnel->status === 'published')
                                        <form action="{{ route('admin.funnels.unpublish', $funnel) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Unpublish</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.funnels.publish', $funnel) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">Publish</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.funnels.duplicate', $funnel) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Copy</button>
                                    </form>
                                    <form action="{{ route('admin.funnels.destroy', $funnel) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this funnel?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No funnels found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($funnels->hasPages())
            <div class="card-footer bg-transparent py-3">
                {{ $funnels->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
