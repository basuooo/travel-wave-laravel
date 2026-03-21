@extends('layouts.admin')

@section('page_title', 'Map Sections')
@section('page_description', 'Manage reusable map sections, assignments, and page positions.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h5 mb-1">Maps Manager</h2>
        <p class="text-muted mb-0">Create reusable map blocks and assign them to pages, destinations, and page groups.</p>
    </div>
    <a href="{{ route('admin.map-sections.create') }}" class="btn btn-primary">Create Map Section</a>
</div>

<div class="card admin-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Assignments</th>
                <th>Created</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $item->name }}</div>
                        <div class="small text-muted">{{ $item->localized('title') }}</div>
                    </td>
                    <td><code>{{ $item->slug }}</code></td>
                    <td>{!! $item->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                    <td>{{ $item->assignments_count }}</td>
                    <td>{{ optional($item->created_at)->format('Y-m-d') }}</td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-2">
                            <a href="{{ route('admin.map-sections.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="post" action="{{ route('admin.map-sections.duplicate', $item) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-secondary">Duplicate</button>
                            </form>
                            <form method="post" action="{{ route('admin.map-sections.destroy', $item) }}" onsubmit="return confirm('Delete this map section?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">No map sections created yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $items->links() }}
</div>
@endsection
