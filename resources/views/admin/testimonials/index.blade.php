@extends('layouts.admin')
@section('page_title', 'Testimonials')
@section('content')
<div class="d-flex justify-content-end mb-3"><a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">Add Testimonial</a></div>
<div class="card admin-card p-4"><table class="table"><thead><tr><th>Name</th><th>Rating</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->client_name }}</td><td>{{ $item->rating }}/5</td><td class="text-end"><a href="{{ route('admin.testimonials.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a></td></tr>@endforeach</tbody></table>{{ $items->links() }}</div>
@endsection
