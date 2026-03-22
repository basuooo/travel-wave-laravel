@extends('layouts.admin')
@section('page_title', 'Testimonials Trash')
@section('content')
<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Testimonials Trash</h2><a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">Back</a></div>
    <table class="table align-middle"><thead><tr><th>Name</th><th>Deleted At</th><th>Deleted By</th><th class="text-end">Actions</th></tr></thead><tbody>@forelse($items as $item)<tr><td>{{ $item->client_name }}</td><td>{{ optional($item->deleted_at)->format('Y-m-d H:i') }}</td><td>{{ $item->deletedBy?->name ?? 'System' }}</td><td class="text-end"><div class="d-inline-flex gap-2"><form method="post" action="{{ route('admin.testimonials.restore', $item->id) }}">@csrf<button class="btn btn-sm btn-outline-primary">Restore</button></form><form method="post" action="{{ route('admin.testimonials.force-destroy', $item->id) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this testimonial?')">Delete Permanently</button></form></div></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">Trash is empty.</td></tr>@endforelse</tbody></table>{{ $items->links() }}
</div>
@endsection
