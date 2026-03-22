@extends('layouts.admin')

@section('page_title', 'Blog Categories')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.blog-categories.trash') }}" class="btn btn-outline-secondary">Trash</a>
    <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">Add Category</a>
</div>
<div class="card admin-card p-4">
    <table class="table align-middle">
        <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->name_en }}</td>
                <td>{{ $item->slug }}</td>
                <td><span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active ? 'Active' : 'Draft' }}</span></td>
                <td class="text-end">
                    <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('admin.blog-categories.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                        @if($item->frontendUrl())
                            <a href="{{ $item->frontendUrl() }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer">View</a>
                        @endif
                        <form method="post" action="{{ route('admin.blog-categories.duplicate', $item) }}">@csrf<button class="btn btn-sm btn-outline-primary">Duplicate</button></form>
                        <form method="post" action="{{ route('admin.blog-categories.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Move this blog category to Trash?')">Delete</button></form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
</div>
@endsection
