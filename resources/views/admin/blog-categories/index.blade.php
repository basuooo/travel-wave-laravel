@extends('layouts.admin')
@section('page_title', 'Blog Categories')
@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">Add Category</a></div>
<div class="card admin-card p-4"><table class="table"><thead><tr><th>Name</th><th>Slug</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->name_en }}</td><td>{{ $item->slug }}</td><td class="text-end"><a href="{{ route('admin.blog-categories.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a></td></tr>@endforeach</tbody></table>{{ $items->links() }}</div>
@endsection
