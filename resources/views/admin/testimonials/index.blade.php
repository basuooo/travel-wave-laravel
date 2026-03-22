@extends('layouts.admin')

@section('page_title', 'Testimonials')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.testimonials.trash') }}" class="btn btn-outline-secondary">Trash</a>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">Add Testimonial</a>
</div>
<div class="card admin-card p-4">
    <table class="table align-middle">
        <thead><tr><th>Name</th><th>Rating</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->client_name }}</td>
                <td>{{ $item->rating }}/5</td>
                <td><span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active ? 'Active' : 'Draft' }}</span></td>
                <td class="text-end">
                    <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('admin.testimonials.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                        @if($item->frontendUrl())
                            <a href="{{ $item->frontendUrl() }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer">View</a>
                        @endif
                        <form method="post" action="{{ route('admin.testimonials.duplicate', $item) }}">@csrf<button class="btn btn-sm btn-outline-primary">Duplicate</button></form>
                        <form method="post" action="{{ route('admin.testimonials.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Move this testimonial to Trash?')">Delete</button></form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
</div>
@endsection
