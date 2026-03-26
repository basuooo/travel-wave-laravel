@extends('layouts.admin')

@section('title', __('admin.audit_log_details'))
@section('page_title', __('admin.audit_log_details'))
@section('page_description', $auditLog->target_label ?: __('admin.audit_log'))

@section('content')
<div class="container-fluid px-4 pb-4 audit-log-page {{ app()->getLocale() === 'ar' ? 'audit-log-page--rtl' : '' }}">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge audit-log-badge text-bg-{{ $auditLog->moduleBadgeClass() }}">{{ $auditLog->localizedModule() }}</span>
                <span class="badge audit-log-badge text-bg-{{ $auditLog->actionBadgeClass() }}">{{ $auditLog->localizedAction() }}</span>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ __('admin.audit_filter_user') }}</dt>
                        <dd class="col-sm-8">{{ $auditLog->actor?->name ?: __('admin.audit_system_label') }}</dd>

                        <dt class="col-sm-4">{{ __('admin.audit_log_date') }}</dt>
                        <dd class="col-sm-8">{{ optional($auditLog->created_at)->format('Y-m-d H:i:s') }}</dd>

                        <dt class="col-sm-4">{{ __('admin.audit_target') }}</dt>
                        <dd class="col-sm-8 text-break">{{ $auditLog->target_label ?: '-' }}</dd>

                        <dt class="col-sm-4">{{ __('admin.audit_entity') }}</dt>
                        <dd class="col-sm-8">{{ \App\Support\AuditLogService::auditableTypeOptions()[$auditLog->auditable_type] ?? class_basename((string) $auditLog->auditable_type) }}</dd>
                    </dl>
                </div>
                <div class="col-lg-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ __('admin.audit_title_label') }}</dt>
                        <dd class="col-sm-8 text-break">{{ $auditLog->title ?: '-' }}</dd>

                        <dt class="col-sm-4">{{ __('admin.audit_description_label') }}</dt>
                        <dd class="col-sm-8 text-break">{{ $auditLog->description ?: '-' }}</dd>

                        <dt class="col-sm-4">{{ __('admin.audit_ip_address') }}</dt>
                        <dd class="col-sm-8">{{ $auditLog->ip_address ?: '-' }}</dd>

                        <dt class="col-sm-4">{{ __('admin.audit_user_agent') }}</dt>
                        <dd class="col-sm-8 text-break">{{ $auditLog->user_agent ?: '-' }}</dd>
                    </dl>
                </div>
            </div>

            @if($contextualUrl)
                <div class="mt-3">
                    <a href="{{ $contextualUrl }}" class="btn btn-outline-primary text-nowrap">{{ __('admin.audit_open_related_record') }}</a>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h5 mb-3">{{ __('admin.audit_changed_fields') }}</h3>
                    @if(! empty($auditLog->changed_fields))
                        <ul class="list-group list-group-flush">
                            @foreach($auditLog->changed_fields as $field)
                                <li class="list-group-item px-0">{{ $fieldLabelResolver($field) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">{{ __('admin.audit_no_data_available') }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h5 mb-3">{{ __('admin.audit_old_values') }}</h3>
                    @if(! empty($auditLog->old_values))
                        <ul class="list-group list-group-flush">
                            @foreach($auditLog->old_values as $field => $value)
                                <li class="list-group-item px-0">
                                    <div class="small text-muted">{{ $fieldLabelResolver($field) }}</div>
                                    @if(is_array($value))
                                        <pre class="audit-log-json">{{ json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        <div class="fw-semibold text-break">{{ $value === null || $value === '' ? '-' : $value }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">{{ __('admin.audit_no_data_available') }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h5 mb-3">{{ __('admin.audit_new_values') }}</h3>
                    @if(! empty($auditLog->new_values))
                        <ul class="list-group list-group-flush">
                            @foreach($auditLog->new_values as $field => $value)
                                <li class="list-group-item px-0">
                                    <div class="small text-muted">{{ $fieldLabelResolver($field) }}</div>
                                    @if(is_array($value))
                                        <pre class="audit-log-json">{{ json_encode($value, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        <div class="fw-semibold text-break">{{ $value === null || $value === '' ? '-' : $value }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">{{ __('admin.audit_no_data_available') }}</div>
                    @endif
                </div>
            </div>
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

.audit-log-page .audit-log-json {
    margin: .25rem 0 0;
    padding: .65rem .8rem;
    border-radius: .75rem;
    background: #f8f9fa;
    font-size: .82rem;
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
    direction: ltr;
    text-align: left;
}

.audit-log-page--rtl .card-body,
.audit-log-page--rtl .list-group-item,
.audit-log-page--rtl dt,
.audit-log-page--rtl dd {
    text-align: right;
}
</style>
@endsection
