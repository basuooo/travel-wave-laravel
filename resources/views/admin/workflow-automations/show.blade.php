@extends('layouts.admin')

@section('title', $automation->name)
@section('page_title', $automation->name)
@section('page_description', __('admin.workflow_ui_rule_details'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="badge {{ $automation->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $automation->is_active ? __('admin.workflow_ui_active') : __('admin.workflow_ui_inactive') }}</span>
            <span class="badge text-bg-light">{{ $workflowService->triggerLabel($automation->trigger_type) }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.workflow-automations.logs', ['workflow_automation_id' => $automation->id]) }}" class="btn btn-outline-primary">{{ __('admin.workflow_ui_execution_logs') }}</a>
            @if(auth()->user()?->hasPermission('workflow_automations.manage'))
                <a href="{{ route('admin.workflow-automations.edit', $automation) }}" class="btn btn-primary">{{ __('admin.workflow_ui_edit') }}</a>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">{{ __('admin.workflow_ui_trigger') }}</div><div class="fw-semibold">{{ $workflowService->triggerLabel($automation->trigger_type) }}</div></div></div></div>
        <div class="col-md-4"><div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">{{ __('admin.workflow_ui_actions') }}</div><div class="fw-semibold">{{ $workflowService->actionSummary($automation) }}</div></div></div></div>
        <div class="col-md-4"><div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">{{ __('admin.workflow_ui_last_execution') }}</div><div class="fw-semibold">{{ optional($automation->last_executed_at)->format('Y-m-d H:i') ?: __('admin.workflow_ui_not_available') }}</div></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white"><strong>{{ __('admin.workflow_ui_conditions') }}</strong></div>
                <div class="card-body"><pre class="mb-0 small">{{ json_encode($automation->conditions ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white"><strong>{{ __('admin.workflow_ui_actions') }}</strong></div>
                <div class="card-body"><pre class="mb-0 small">{{ json_encode($automation->actions ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white"><strong>{{ __('admin.workflow_ui_recent_executions') }}</strong></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>{{ __('admin.workflow_ui_date') }}</th><th>{{ __('admin.workflow_ui_status') }}</th><th>{{ __('admin.workflow_ui_item') }}</th><th>{{ __('admin.workflow_ui_details') }}</th></tr></thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr>
                            <td>{{ optional($log->executed_at)->format('Y-m-d H:i') }}</td>
                            <td><span class="badge text-bg-{{ $log->execution_status === 'success' ? 'success' : ($log->execution_status === 'failed' ? 'danger' : 'secondary') }}">{{ $log->execution_status === 'success' ? __('admin.workflow_ui_status_success') : ($log->execution_status === 'failed' ? __('admin.workflow_ui_status_failed') : __('admin.workflow_ui_status_skipped')) }}</span></td>
                            <td>{{ $log->target_label ?: ($log->entity_type . ' #' . $log->entity_id) }}</td>
                            <td class="small">{{ $log->result_summary ?: $log->error_message }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">{{ __('admin.workflow_ui_no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
