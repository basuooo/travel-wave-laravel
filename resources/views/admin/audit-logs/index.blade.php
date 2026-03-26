@extends('layouts.admin')

@section('title', __('admin.audit_log'))
@section('page_title', __('admin.audit_log'))
@section('page_description', __('admin.audit_log_desc'))

@section('content')
<div class="container-fluid px-4 pb-4 audit-log-page {{ app()->getLocale() === 'ar' ? 'audit-log-page--rtl' : '' }}">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.audit_summary_total') }}</div>
                    <div class="fs-3 fw-bold">{{ number_format($summary['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.audit_summary_today') }}</div>
                    <div class="fs-3 fw-bold">{{ number_format($summary['today']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.audit_critical_actions') }}</div>
                    <div class="fs-3 fw-bold text-danger">{{ number_format($summary['critical']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small">{{ __('admin.audit_finance_actions') }}</div>
                    <div class="fs-3 fw-bold text-warning">{{ number_format($summary['finance']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 audit-log-filters">
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.audit_filter_search') }}</label>
                    <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.audit_search_placeholder') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.audit_filter_from') }}</label>
                    <input type="date" class="form-control" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.audit_filter_to') }}</label>
                    <input type="date" class="form-control" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.audit_filter_user') }}</label>
                    <select class="form-select" name="user_id">
                        <option value="">{{ __('admin.audit_all_option') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.audit_filter_module') }}</label>
                    <select class="form-select" name="module">
                        <option value="">{{ __('admin.audit_all_option') }}</option>
                        @foreach($moduleOptions as $slug => $label)
                            <option value="{{ $slug }}" @selected(request('module') === $slug)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.audit_action') }}</label>
                    <select class="form-select" name="action_type">
                        <option value="">{{ __('admin.audit_all_option') }}</option>
                        @foreach($actionOptions as $slug => $label)
                            <option value="{{ $slug }}" @selected(request('action_type') === $slug)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('admin.audit_entity') }}</label>
                    <select class="form-select" name="auditable_type">
                        <option value="">{{ __('admin.audit_all_option') }}</option>
                        @foreach($auditableTypeOptions as $className => $label)
                            <option value="{{ $className }}" @selected(request('auditable_type') === $className)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('admin.audit_target_id') }}</label>
                    <input type="number" class="form-control" name="auditable_id" value="{{ request('auditable_id') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2 flex-wrap audit-log-filter-actions">
                    <button class="btn btn-primary text-nowrap">{{ __('admin.audit_filter_button') }}</button>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary text-nowrap">{{ __('admin.audit_reset_button') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th>{{ __('admin.audit_log_date') }}</th>
                    <th>{{ __('admin.audit_filter_user') }}</th>
                    <th>{{ __('admin.audit_filter_module') }}</th>
                    <th>{{ __('admin.audit_action') }}</th>
                    <th>{{ __('admin.audit_target') }}</th>
                    <th>{{ __('admin.details') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ optional($item->created_at)->format('Y-m-d H:i') }}</div>
                            <div class="small text-muted">{{ optional($item->created_at)->diffForHumans() }}</div>
                        </td>
                        <td>{{ $item->actor?->name ?: __('admin.audit_system_label') }}</td>
                        <td><span class="badge audit-log-badge text-bg-{{ $item->moduleBadgeClass() }}">{{ $item->localizedModule() }}</span></td>
                        <td><span class="badge audit-log-badge text-bg-{{ $item->actionBadgeClass() }}">{{ $item->localizedAction() }}</span></td>
                        <td class="audit-log-target-cell">
                            <div class="fw-semibold text-break">{{ $item->target_label ?: '-' }}</div>
                            @if($item->description)
                                <div class="small text-muted text-break">{{ \Illuminate\Support\Str::limit($item->description, 120) }}</div>
                            @elseif($item->title)
                                <div class="small text-muted text-break">{{ \Illuminate\Support\Str::limit($item->title, 120) }}</div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.audit-logs.show', $item) }}" class="btn btn-sm btn-outline-primary text-nowrap">{{ __('admin.audit_details_button') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">{{ __('admin.audit_log_empty') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            {{ $items->links() }}
        </div>
    </div>
</div>

<style>
.audit-log-page .audit-log-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .45rem .7rem;
    white-space: normal;
    line-height: 1.35;
    text-align: center;
    max-width: 13rem;
}

.audit-log-page .audit-log-target-cell {
    min-width: 14rem;
}

.audit-log-page--rtl .table th,
.audit-log-page--rtl .table td,
.audit-log-page--rtl .form-label,
.audit-log-page--rtl .card-body {
    text-align: right;
}

.audit-log-page--rtl .audit-log-filter-actions,
.audit-log-page--rtl .pagination {
    justify-content: flex-start;
}
</style>
@endsection
