@extends('layouts.admin')
@section('page_title', 'Inquiry Details')
@section('content')
<div class="card admin-card p-4 mb-4">
    <dl class="row mb-0">
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $inquiry->full_name }}</dd>
        <dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $inquiry->phone }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $inquiry->email }}</dd>
        <dt class="col-sm-3">Type</dt><dd class="col-sm-9">{{ $inquiry->type }}</dd>
        <dt class="col-sm-3">Destination</dt><dd class="col-sm-9">{{ $inquiry->destination }}</dd>
        <dt class="col-sm-3">Message</dt><dd class="col-sm-9">{{ $inquiry->message }}</dd>
    </dl>
</div>
<form method="post" action="{{ route('admin.inquiries.update', $inquiry) }}">@csrf @method('PUT')
    <div class="card admin-card p-4">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status"><option value="new" @selected($inquiry->status === 'new')>New</option><option value="contacted" @selected($inquiry->status === 'contacted')>Contacted</option><option value="closed" @selected($inquiry->status === 'closed')>Closed</option></select></div>
            <div class="col-md-12"><label class="form-label">Admin Notes</label><textarea class="form-control" name="admin_notes" rows="4">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea></div>
        </div>
    </div>
    <button class="btn btn-primary mt-3">Update Inquiry</button>
</form>
@endsection
