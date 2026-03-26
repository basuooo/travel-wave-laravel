@extends('layouts.admin')

@section('title', __('admin.workflow_ui_execution_logs'))
@section('page_title', __('admin.workflow_ui_execution_logs'))
@section('page_description', __('admin.workflow_ui_execution_logs_desc'))

@section('content')
<div class="container-fluid py-4">
    <form method="get" class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="{{ __('admin.workflow_ui_search_placeholder') }}"></div>
                <div class="col-md-3">
                    <select name="workflow_automation_id" class="form-select">
                        <option value="">{{ __('admin.workflow_ui_all_rules') }}</option>
                        @foreach($automations as $automation)
                            <option value="{{ $automation->id }}" @selected((string) request('workflow_automation_id') === (string) $automation->id)>{{ $automation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="trigger_type" class="form-select">
                        <option value="">{{ __('admin.workflow_ui_all_triggers') }}</option>
                        @foreach($triggerOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('trigger_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="execution_status" class="form-select">
                        <option value="">{{ __('admin.workflow_ui_all') }}</option>
                        <option value="success" @selected(request('execution_status') === 'success')>{{ __('admin.workflow_ui_status_success') }}</option>
                        <option value="failed" @selected(request('execution_status') === 'failed')>{{ __('admin.workflow_ui_status_failed') }}</option>
                        <option value="skipped" @selected(request('execution_status') === 'skipped')>{{ __('admin.workflow_ui_status_skipped') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-primary flex-fill">{{ __('admin.workflow_ui_filter') }}</button>
                    <a href="{{ route('admin.workflow-automations.logs') }}" class="btn btn-outline-secondary">{{ __('admin.workflow_ui_reset') }}</a>
                </div>
                <div class="col-md-2"><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
                <div class="col-md-2"><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
                <div class="col-md-2"><input type="text" name="entity_type" class="form-control" value="{{ request('entity_type') }}" placeholder="{{ __('admin.workflow_ui_type') }}"></div>
                <div class="col-md-2"><input type="number" name="entity_id" class="form-control" value="{{ request('entity_id') }}" placeholder="{{ __('admin.workflow_ui_id') }}"></div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>{{ __('admin.workflow_ui_date') }}</th><th>{{ __('admin.workflow_ui_rule_name') }}</th><th>{{ __('admin.workflow_ui_trigger') }}</th><th>{{ __('admin.workflow_ui_status') }}</th><th>{{ __('admin.workflow_ui_item') }}</th><th>{{ __('admin.workflow_ui_details') }}</th></tr></thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ optional($item->executed_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $item->automation?->name }}</td>
                            <td>{{ $triggerOptions[$item->trigger_type] ?? $item->trigger_type }}</td>
                            <td><span class="badge text-bg-{{ $item->execution_status === 'success' ? 'success' : ($item->execution_status === 'failed' ? 'danger' : 'secondary') }}">{{ $item->execution_status === 'success' ? __('admin.workflow_ui_status_success') : ($item->execution_status === 'failed' ? __('admin.workflow_ui_status_failed') : __('admin.workflow_ui_status_skipped')) }}</span></td>
                            <td>{{ $item->target_label ?: ($item->entity_type . ' #' . $item->entity_id) }}</td>
                            <td class="small">{{ $item->result_summary ?: $item->error_message }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">{{ __('admin.workflow_ui_no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="card-footer bg-white">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection
