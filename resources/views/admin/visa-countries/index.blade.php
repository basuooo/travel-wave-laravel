@extends('layouts.admin')
@section('page_title', 'Visa Countries')
@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('admin.visa-countries.create') }}" class="btn btn-primary">Add Country</a></div>
<div class="card admin-card p-4"><table class="table"><thead><tr><th>Country</th><th>Category</th><th>Featured</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->name_en }}</td><td>{{ $item->category?->name_en }}</td><td>{{ $item->is_featured ? 'Yes' : 'No' }}</td><td class="text-end"><a href="{{ route('admin.visa-countries.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a></td></tr>@endforeach</tbody></table>{{ $items->links() }}</div>
@endsection
