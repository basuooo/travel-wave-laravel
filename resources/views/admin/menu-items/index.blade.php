@extends('layouts.admin')

@section('page_title', 'Navigation')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.menu-items.trash') }}" class="btn btn-outline-secondary">Trash</a>
    <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary">Add Menu Item</a>
</div>
<div class="card admin-card p-4">
    <table class="table align-middle">
        <thead><tr><th>Location</th><th>Title</th><th>URL</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->location }}</td>
                <td>{{ $item->title_en }}</td>
                <td>{{ $item->url ?: $item->route_name }}</td>
                <td><span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active ? 'Active' : 'Draft' }}</span></td>
                <td class="text-end">
                    <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('admin.menu-items.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                        @if($item->frontendUrl())
                            <a href="{{ $item->frontendUrl() }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer">View</a>
                        @endif
                        <form method="post" action="{{ route('admin.menu-items.duplicate', $item) }}">@csrf<button class="btn btn-sm btn-outline-primary">Duplicate</button></form>
                        <form method="post" action="{{ route('admin.menu-items.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Move this navigation item to Trash?')">Delete</button></form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
</div>
@endsection
