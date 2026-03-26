@extends('layouts.admin')

@section('title', __('admin.notifications_ui_page_title'))
@section('page_title', __('admin.notifications_ui_page_title'))
@section('page_description', __('admin.notifications_ui_page_desc'))

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.notifications_ui_total') }}</div>
                    <div class="fs-4 fw-bold">{{ $summary['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-warning">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.notifications_ui_unread') }}</div>
                    <div class="fs-4 fw-bold text-warning">{{ $summary['unread'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-danger">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.notifications_ui_urgent_unread') }}</div>
                    <div class="fs-4 fw-bold text-danger">{{ $summary['urgent_unread'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-primary">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.notifications_ui_actionable') }}</div>
                    <div class="fs-4 fw-bold text-primary">{{ $summary['actionable_unread'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_status') }}</label>
                    <select name="state" class="form-select">
                        <option value="all" @selected(($filters['state'] ?? 'all') === 'all')>{{ __('admin.notifications_ui_all') }}</option>
                        <option value="unread" @selected(($filters['state'] ?? null) === 'unread')>{{ __('admin.notifications_ui_state_unread') }}</option>
                        <option value="read" @selected(($filters['state'] ?? null) === 'read')>{{ __('admin.notifications_ui_state_read') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_type') }}</label>
                    <select name="type" class="form-select">
                        <option value="">{{ __('admin.notifications_ui_all') }}</option>
                        @foreach($typeOptions as $type)
                            <option value="{{ $type }}" @selected(($filters['type'] ?? null) === $type)>{{ $notificationCenterService->localizedTypeLabel($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_severity') }}</label>
                    <select name="severity" class="form-select">
                        <option value="">{{ __('admin.notifications_ui_all') }}</option>
                        @foreach($severityOptions as $severity)
                            <option value="{{ $severity }}" @selected(($filters['severity'] ?? null) === $severity)>{{ $notificationCenterService->localizedSeverityLabel($severity) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_module') }}</label>
                    <select name="module" class="form-select">
                        <option value="">{{ __('admin.notifications_ui_all') }}</option>
                        @foreach($moduleOptions as $module)
                            <option value="{{ $module }}" @selected(($filters['module'] ?? null) === $module)>{{ $notificationCenterService->localizedModuleLabel($module) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_from_date') }}</label>
                    <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.notifications_ui_to_date') }}</label>
                    <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="actionable_only" name="actionable" value="1" @checked(($filters['actionable'] ?? null) === '1')>
                        <label class="form-check-label" for="actionable_only">{{ __('admin.notifications_ui_actionable_only') }}</label>
                    </div>
                </div>
                <div class="col-md-9 d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">{{ __('admin.notifications_ui_reset') }}</a>
                    <button class="btn btn-primary" type="submit">{{ __('admin.notifications_ui_filter') }}</button>
                    @if(($summary['unread'] ?? 0) > 0)
                        <button type="button" class="btn btn-outline-dark" data-notifications-read-all="{{ route('admin.notifications.read-all') }}">{{ __('admin.notifications_ui_mark_all_read') }}</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($items as $notification)
                    <div class="list-group-item {{ $notification['is_read'] ? '' : 'bg-light' }}" data-notification-item="{{ $notification['id'] }}" data-notification-state="{{ $notification['is_read'] ? 'read' : 'unread' }}">
                        <div class="d-flex flex-wrap justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                    <strong>{{ $notification['title'] }}</strong>
                                    <span class="badge text-bg-{{ $notification['severity'] }}">{{ $notification['severity_label'] }}</span>
                                    <span class="badge text-bg-light">{{ $notification['type_label'] }}</span>
                                    @if(! $notification['is_read'])
                                        <span class="badge text-bg-primary">{{ __('admin.notifications_ui_state_unread') }}</span>
                                    @endif
                                </div>
                                @if($notification['message'])
                                    <div class="text-muted mb-2">{{ $notification['message'] }}</div>
                                @endif
                                <div class="small text-muted">
                                    {{ optional($notification['created_at'])->translatedFormat('Y-m-d h:i A') }}
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                @if(! $notification['is_read'])
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-notification-read="{{ route('admin.notifications.read', $notification['id']) }}">{{ __('admin.notifications_ui_mark_read') }}</button>
                                @endif
                                @if($notification['is_actionable'])
                                    <a href="{{ $notification['url'] }}" class="btn btn-sm btn-primary">{{ $notification['action_label'] }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">{{ __('admin.notifications_ui_empty') }}</div>
                @endforelse
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection
