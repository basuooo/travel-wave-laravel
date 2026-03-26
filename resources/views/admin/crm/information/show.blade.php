@extends('layouts.admin')

@section('page_title', $information->title)
@section('page_description', __('admin.crm_information_desc'))

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h2 class="h4 mb-1">{{ $information->title }}</h2>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge text-bg-secondary">{{ $information->localizedAudience() }}</span>
                        @if($information->localizedPriority())
                            <span class="badge {{ $information->priority === 'urgent' ? 'text-bg-danger' : ($information->priority === 'important' ? 'text-bg-warning' : 'text-bg-secondary') }}">
                                {{ $information->localizedPriority() }}
                            </span>
                        @endif
                        @if($information->localizedCategory())
                            <span class="badge text-bg-light">{{ $information->localizedCategory() }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.crm.information.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('admin.crm_information_back') }}</a>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="small text-muted">{{ __('admin.crm_information_date') }}</div>
                    <div>{{ $information->event_date?->format('Y-m-d') ?: '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">{{ __('admin.crm_information_expires_at') }}</div>
                    <div>{{ $information->expires_at?->format('Y-m-d H:i') ?: '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">{{ __('admin.created_by') }}</div>
                    <div>{{ $information->creator?->name ?: '—' }}</div>
                </div>
            </div>

            <div class="border rounded-3 p-3 bg-body-tertiary">
                {!! nl2br(e($information->content)) !!}
            </div>

            @if(!$canManageInformation && $recipient)
                <div class="mt-4">
                    @if($recipient->acknowledged_at)
                        <div class="alert alert-success mb-0">{{ __('admin.crm_information_ack_done') }} {{ $recipient->acknowledged_at->format('Y-m-d H:i') }}</div>
                    @else
                        <form method="POST" action="{{ route('admin.crm.information.acknowledge', $information) }}">
                            @csrf
                            <button type="submit" class="btn btn-success">{{ __('admin.crm_information_ack_button') }}</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        @if($canManageInformation)
            <div class="card admin-card p-4 h-100">
                <h2 class="h5 mb-3">{{ __('admin.crm_information_ack_review') }}</h2>
                <div class="d-grid gap-3 mb-4">
                    <div class="border rounded-3 p-3">
                        <div class="small text-muted">{{ __('admin.crm_information_recipients_count') }}</div>
                        <div class="fs-4 fw-semibold">{{ $information->recipients->count() }}</div>
                    </div>
                    <div class="border rounded-3 p-3">
                        <div class="small text-muted">{{ __('admin.crm_information_acknowledged_count') }}</div>
                        <div class="fs-4 fw-semibold text-success">{{ $information->recipients->whereNotNull('acknowledged_at')->count() }}</div>
                    </div>
                    <div class="border rounded-3 p-3">
                        <div class="small text-muted">{{ __('admin.crm_information_pending_count') }}</div>
                        <div class="fs-4 fw-semibold text-danger">{{ $information->recipients->whereNull('acknowledged_at')->count() }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('admin.name') }}</th>
                                <th>{{ __('admin.status') }}</th>
                                <th>{{ __('admin.crm_information_seen_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($information->recipients as $recipientRow)
                                <tr>
                                    <td>{{ $recipientRow->user?->name ?: '—' }}</td>
                                    <td>
                                        @if($recipientRow->acknowledged_at)
                                            <span class="badge text-bg-success">{{ __('admin.crm_information_acknowledged') }}</span>
                                        @elseif($recipientRow->seen_at)
                                            <span class="badge text-bg-warning">{{ __('admin.crm_information_seen_only') }}</span>
                                        @else
                                            <span class="badge text-bg-danger">{{ __('admin.crm_information_pending_ack') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $recipientRow->acknowledged_at?->format('Y-m-d H:i') ?: ($recipientRow->seen_at?->format('Y-m-d H:i') ?: '—') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card admin-card p-4">
                <h2 class="h5 mb-3">{{ __('admin.crm_information_ack_status') }}</h2>
                <div class="small text-muted mb-2">{{ __('admin.crm_information_seen_at') }}</div>
                <div class="mb-3">{{ $recipient?->seen_at?->format('Y-m-d H:i') ?: '—' }}</div>
                <div class="small text-muted mb-2">{{ __('admin.crm_information_acknowledged_at') }}</div>
                <div>{{ $recipient?->acknowledged_at?->format('Y-m-d H:i') ?: __('admin.crm_information_pending_ack') }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
