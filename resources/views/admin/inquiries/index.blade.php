@extends('layouts.admin')
@section('page_title', 'Inquiries')
@section('content')
<div class="card admin-card p-4"><table class="table"><thead><tr><th>Name</th><th>Type</th><th>Status</th><th>Date</th><th></th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->full_name }}</td><td>{{ $item->type }}</td><td>{{ $item->status }}</td><td>{{ $item->created_at->format('d M Y') }}</td><td class="text-end"><a href="{{ route('admin.inquiries.show', $item) }}" class="btn btn-sm btn-primary">View</a></td></tr>@endforeach</tbody></table>{{ $items->links() }}</div>
@endsection
