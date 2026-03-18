@extends('layouts.admin')
@section('page_title', 'Destinations')
@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('admin.destinations.create') }}" class="btn btn-primary">Add Destination</a></div>
<div class="card admin-card p-4"><table class="table"><thead><tr><th>Destination</th><th>Slug</th><th>Featured</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->title_en }}</td><td>{{ $item->slug }}</td><td>{{ $item->is_featured ? 'Yes' : 'No' }}</td><td class="text-end"><a href="{{ route('admin.destinations.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a></td></tr>@endforeach</tbody></table>{{ $items->links() }}</div>
@endsection
