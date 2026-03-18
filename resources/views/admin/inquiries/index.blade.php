@extends('layouts.admin')
@section('page_title', 'Inquiries')
@section('page_description', 'Track leads by type, status, date, and source page.')
@section('content')
<div class="row g-3 mb-4">
    @foreach($stats as $label => $count)
        <div class="col-md-3">
            <div class="card admin-card p-3">
                <div class="text-muted text-uppercase small">{{ $label }}</div>
                <div class="h3 mb-0">{{ $count }}</div>
            </div>
        </div>
    @endforeach
</div>
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label">Type</label><select class="form-select" name="type"><option value="">All</option><option value="general" @selected(request('type')==='general')>General</option><option value="visa" @selected(request('type')==='visa')>Visa</option><option value="destination" @selected(request('type')==='destination')>Destination</option><option value="flights" @selected(request('type')==='flights')>Flights</option><option value="hotels" @selected(request('type')==='hotels')>Hotels</option><option value="contact" @selected(request('type')==='contact')>Contact</option></select></div>
        <div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">All</option><option value="new" @selected(request('status')==='new')>New</option><option value="contacted" @selected(request('status')==='contacted')>Contacted</option><option value="closed" @selected(request('status')==='closed')>Closed</option></select></div>
        <div class="col-md-3"><label class="form-label">Date</label><input type="date" class="form-control" name="date" value="{{ request('date') }}"></div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary">Filter</button><a href="{{ route('admin.inquiries.index') }}" class="btn btn-outline-secondary">Reset</a></div>
    </div>
</form>
<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Name</th><th>Type</th><th>Destination</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td><div class="fw-semibold">{{ $item->full_name }}</div><div class="text-muted small">{{ $item->phone }}</div></td>
                        <td><span class="badge text-bg-light">{{ ucfirst($item->type) }}</span></td>
                        <td>{{ $item->destination ?: '-' }}</td>
                        <td><span class="badge {{ $item->status === 'new' ? 'text-bg-warning' : ($item->status === 'contacted' ? 'text-bg-info' : 'text-bg-success') }}">{{ ucfirst($item->status) }}</span></td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td class="text-end"><a href="{{ route('admin.inquiries.show', $item) }}" class="btn btn-sm btn-primary">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
