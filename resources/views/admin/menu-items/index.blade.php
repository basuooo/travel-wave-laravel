@extends('layouts.admin')
@section('page_title', 'Navigation')
@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary">Add Menu Item</a></div>
<div class="card admin-card p-4"><table class="table"><thead><tr><th>Location</th><th>Title</th><th>URL</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->location }}</td><td>{{ $item->title_en }}</td><td>{{ $item->url ?: $item->route_name }}</td><td class="text-end"><a href="{{ route('admin.menu-items.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a></td></tr>@endforeach</tbody></table>{{ $items->links() }}</div>
@endsection
