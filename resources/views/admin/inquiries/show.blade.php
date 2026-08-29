@extends('layouts.admin')
@section('page_title', 'Inquiry Details')
@section('content')
<div class="card admin-card p-4 mb-4">
    <dl class="row mb-0">
        <dt class="col-sm-3">Form</dt><dd class="col-sm-9">{{ $inquiry->form_name ?: ($inquiry->form?->name ?: '-') }}</dd>
        <dt class="col-sm-3">Form Category</dt><dd class="col-sm-9">{{ $inquiry->form_category ?: $inquiry->type }}</dd>
        <dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $inquiry->full_name }}</dd>
        <dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $inquiry->phone }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $inquiry->email }}</dd>
        <dt class="col-sm-3">Type</dt><dd class="col-sm-9">{{ $inquiry->type }}</dd>
        <dt class="col-sm-3">Destination</dt><dd class="col-sm-9">{{ $inquiry->destination }}</dd>
        <dt class="col-sm-3">Source Page</dt>
        <dd class="col-sm-9">
            @if($inquiry->source_page)
                @php
                    $rawSource = $inquiry->source_page;
                    $sourceUrl = (str_starts_with($rawSource, 'http://') || str_starts_with($rawSource, 'https://'))
                        ? $rawSource
                        : url(ltrim($rawSource, '/'));
                @endphp
                <a href="{{ $sourceUrl }}" target="_blank" rel="noopener noreferrer" class="badge text-bg-light border text-decoration-none text-primary d-inline-flex align-items-center gap-1 fs-6" title="فتح صفحة المصدر في نافذة جديدة: {{ $sourceUrl }}">
                    <i class="bi bi-box-arrow-up-right me-1"></i>
                    <span>{{ ltrim($rawSource, '/') }}</span>
                </a>
            @else
                -
            @endif
        </dd>
        <dt class="col-sm-3">Display Position</dt><dd class="col-sm-9">{{ $inquiry->display_position ?: '-' }}</dd>
        <dt class="col-sm-3">Message</dt><dd class="col-sm-9">{{ $inquiry->message }}</dd>
    </dl>
</div>
@if(!empty($inquiry->submitted_data))
    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">Submitted Data</h2>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <tbody>
                    @foreach($inquiry->submitted_data as $key => $value)
                        <tr>
                            <th style="width: 220px;">{{ $key }}</th>
                            <td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
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
