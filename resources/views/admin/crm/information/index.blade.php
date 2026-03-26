@extends('layouts.admin')

@section('page_title', __('admin.crm_information'))
@section('page_description', __('admin.crm_information_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.crm_information') }}</h2>
            <p class="text-muted mb-0">{{ $canManageInformation ? __('admin.crm_information_admin_hint') : __('admin.crm_information_user_hint') }}</p>
        </div>
        @if($canManageInformation)
            <a href="{{ route('admin.crm.information.create') }}" class="btn btn-primary">{{ __('admin.crm_information_create') }}</a>
        @endif
    </div>

    @if($canManageInformation)
        <form method="GET" action="{{ route('admin.crm.information.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.crm_information_audience_type') }}</label>
                <select name="audience_type" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach($audienceOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('audience_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.crm_information_priority') }}</label>
                <select name="priority" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach($priorityOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.created_by') }}</label>
                <select name="created_by" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((int) request('created_by') === (int) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.crm_information_date') }}</label>
                <input type="date" name="event_date" value="{{ request('event_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.crm_information_ack_filter') }}</label>
                <select name="ack_status" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    <option value="pending" @selected(request('ack_status') === 'pending')>{{ __('admin.crm_information_pending_ack') }}</option>
                    <option value="acknowledged" @selected(request('ack_status') === 'acknowledged')>{{ __('admin.crm_information_acknowledged') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.crm_information_user_filter') }}</label>
                <select name="user_id" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((int) request('user_id') === (int) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.crm_information_user_ack_filter') }}</label>
                <select name="user_ack_state" class="form-select">
                    <option value="">{{ __('admin.all') }}</option>
                    <option value="pending" @selected(request('user_ack_state') === 'pending')>{{ __('admin.crm_information_pending_ack') }}</option>
                    <option value="acknowledged" @selected(request('user_ack_state') === 'acknowledged')>{{ __('admin.crm_information_acknowledged') }}</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ __('admin.search') }}</button>
                <a href="{{ route('admin.crm.information.index') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
            </div>
        </form>
    @endif

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.crm_information_title') }}</th>
                    <th>{{ __('admin.crm_information_audience_type') }}</th>
                    <th>{{ __('admin.crm_information_date') }}</th>
                    <th>{{ __('admin.crm_information_priority') }}</th>
                    @if($canManageInformation)
                        <th>{{ __('admin.created_by') }}</th>
                        <th>{{ __('admin.crm_information_recipients_count') }}</th>
                        <th>{{ __('admin.crm_information_acknowledged_count') }}</th>
                        <th>{{ __('admin.crm_information_pending_count') }}</th>
                    @else
                        <th>{{ __('admin.status') }}</th>
                    @endif
                    <th>{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php($information = $canManageInformation ? $item : $item->information)
                    @php($recipient = $canManageInformation ? null : $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $information->title }}</div>
                            @if($information->localizedCategory())
                                <div class="small text-muted">{{ $information->localizedCategory() }}</div>
                            @endif
                        </td>
                        <td>{{ $information->localizedAudience() }}</td>
                        <td>{{ $information->event_date?->format('Y-m-d') ?: '—' }}</td>
                        <td>
                            @if($information->localizedPriority())
                                <span class="badge {{ $information->priority === 'urgent' ? 'text-bg-danger' : ($information->priority === 'important' ? 'text-bg-warning' : 'text-bg-secondary') }}">
                                    {{ $information->localizedPriority() }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        @if($canManageInformation)
                            <td>{{ $information->creator?->name ?: '—' }}</td>
                            <td>{{ $information->recipients_count }}</td>
                            <td class="text-success">{{ $information->acknowledged_count }}</td>
                            <td class="text-danger">{{ $information->pending_count }}</td>
                        @else
                            <td>
                                @if($recipient?->acknowledged_at)
                                    <span class="badge text-bg-success">{{ __('admin.crm_information_acknowledged') }}</span>
                                @elseif($recipient?->seen_at)
                                    <span class="badge text-bg-warning">{{ __('admin.crm_information_seen_only') }}</span>
                                @else
                                    <span class="badge text-bg-danger">{{ __('admin.crm_information_pending_ack') }}</span>
                                @endif
                            </td>
                        @endif
                        <td>
                            <a href="{{ route('admin.crm.information.show', $information) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManageInformation ? 9 : 6 }}" class="text-center text-muted py-4">{{ __('admin.crm_information_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
