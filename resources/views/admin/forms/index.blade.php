@extends('layouts.admin')

@section('page_title', 'Forms Manager')
@section('page_description', 'Create, duplicate, assign, and manage dynamic forms across the website.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-1">Forms</h2>
        <p class="text-muted mb-0">Standalone managed forms with flexible field sets and page assignments.</p>
    </div>
    <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">Create Form</a>
</div>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Form Name</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Assigned Pages</th>
                    <th>Submissions</th>
                    <th>Created Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->name }}</div>
                            <div class="text-muted small">{{ $item->slug }}</div>
                        </td>
                        <td>{{ ucfirst($item->form_category ?: 'general') }}</td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $item->assignments_count }}</td>
                        <td>{{ $item->inquiries_count }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.forms.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="post" action="{{ route('admin.forms.duplicate', $item) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary">Duplicate</button>
                                </form>
                                <form method="post" action="{{ route('admin.forms.destroy', $item) }}" onsubmit="return confirm('Delete this form?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">No forms created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
