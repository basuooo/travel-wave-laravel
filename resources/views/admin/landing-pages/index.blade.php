@extends('layouts.admin')

@section('page_title', __('admin.all_landing_pages'))
@section('page_description', 'Manage and edit all landing pages, active toggles, templates, and lead exports.')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1">📄 {{ __('admin.landing_pages') }}</h3>
        <p class="text-muted mb-0">Total Pages: {{ $items->total() }}</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary px-3" data-bs-toggle="modal" data-bs-target="#importPackageModal">
            <i class="bi bi-upload me-1"></i> Import Package
        </button>
        <a href="{{ route('admin.landing-pages.create') }}" class="btn btn-primary fw-bold px-3">
            <i class="bi bi-plus-lg me-1"></i> {{ __('admin.create_landing_page') }}
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card admin-card p-3 mb-4">
    <form method="get" action="{{ route('admin.landing-pages.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search page name, title, or slug..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold">Active State</label>
            <select name="active" class="form-select">
                <option value="">All States</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active ON</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive OFF</option>
            </select>
        </div>
        <div class="col-md-2 text-end">
            <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-filter"></i> Filter</button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card admin-card p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Internal Name / Slug</th>
                    <th>Status</th>
                    <th>Active Toggle</th>
                    <th>Form</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ $item->internal_name }}</div>
                        <div class="small text-muted font-monospace">/lp/{{ $item->slug }}</div>
                    </td>
                    <td>
                        @if($item->status === 'published')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Published</span>
                        @elseif($item->status === 'draft')
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Draft</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Archived</span>
                        @endif
                    </td>
                    <td>
                        <form method="post" action="{{ route('admin.landing-pages.toggle-active', $item) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()" {{ $item->is_active ? 'checked' : '' }}>
                            </div>
                        </form>
                    </td>
                    <td>
                        <span class="small text-muted">{{ $item->leadForm?->name ?? 'Default Form' }}</span>
                    </td>
                    <td class="small text-muted">
                        {{ $item->created_at->format('Y-m-d') }}
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.landing-pages.builder', $item) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square"></i> Builder
                            </a>
                            <a href="{{ $item->publicUrl() }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.landing-pages.leads', $item) }}">
                                        <i class="bi bi-people me-2"></i> View Leads
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.landing-pages.versions', $item) }}">
                                        <i class="bi bi-clock-history me-2"></i> Version History
                                    </a>
                                </li>
                                <li>
                                    <form method="post" action="{{ route('admin.landing-pages.duplicate', $item) }}">
                                        @csrf
                                        <button class="dropdown-item" type="submit">
                                            <i class="bi bi-files me-2"></i> Duplicate Page
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.landing-pages.export', $item) }}">
                                        <i class="bi bi-download me-2"></i> Export Package
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="post" action="{{ route('admin.landing-pages.destroy', $item) }}" onsubmit="return confirm('Are you sure you want to delete this page?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No landing pages found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="p-3 border-top">
        {{ $items->links() }}
    </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.landing-pages.import') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Import Landing Page Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Select a previously exported Landing Page JSON package file.</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Package File (.json)</label>
                    <input type="file" name="package_file" class="form-control" accept=".json" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-upload me-1"></i> Import Now</button>
            </div>
        </form>
    </div>
</div>
@endsection
