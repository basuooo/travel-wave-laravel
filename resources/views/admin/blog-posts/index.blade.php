@extends('layouts.admin')
@section('page_title', 'Blog Posts')
@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">Add Post</a></div>
<div class="card admin-card p-4"><table class="table"><thead><tr><th>Title</th><th>Category</th><th>Published</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->title_en }}</td><td>{{ $item->category?->name_en }}</td><td>{{ $item->is_published ? 'Yes' : 'No' }}</td><td class="text-end"><a href="{{ route('admin.blog-posts.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a></td></tr>@endforeach</tbody></table>{{ $items->links() }}</div>
@endsection
