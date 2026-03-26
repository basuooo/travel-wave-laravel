@extends('layouts.admin')

@section('title', __('admin.workflow_ui_page_title'))
@section('page_title', __('admin.workflow_ui_page_title'))
@section('page_description', __('admin.workflow_ui_page_desc'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">{{ __('admin.workflow_ui_rules_heading') }}</h2>
        @if(auth()->user()?->hasPermission('workflow_automations.manage'))
            <a href="{{ route('admin.workflow-automations.create') }}" class="btn btn-primary">{{ __('admin.workflow_ui_new_rule') }}</a>
        @endif
    </div>

    <form method="get" class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="{{ __('admin.workflow_ui_search_placeholder') }}" value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <select name="trigger_type" class="form-select">
                        <option value="">{{ __('admin.workflow_ui_all_triggers') }}</option>
                        @foreach($triggerOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('trigger_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="is_active" class="form-select">
                        <option value="">{{ __('admin.workflow_ui_all') }}</option>
                        <option value="1" @selected(request('is_active') === '1')>{{ __('admin.workflow_ui_active') }}</option>
                        <option value="0" @selected(request('is_active') === '0')>{{ __('admin.workflow_ui_inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-outline-primary flex-fill">{{ __('admin.workflow_ui_filter') }}</button>
                    <a href="{{ route('admin.workflow-automations.index') }}" class="btn btn-outline-secondary">{{ __('admin.workflow_ui_reset') }}</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('admin.workflow_ui_name') }}</th>
                        <th>{{ __('admin.workflow_ui_trigger') }}</th>
                        <th>{{ __('admin.workflow_ui_actions') }}</th>
                        <th>{{ __('admin.workflow_ui_status') }}</th>
                        <th>{{ __('admin.workflow_ui_priority') }}</th>
                        <th>{{ __('admin.workflow_ui_updated_at') }}</th>
                        <th class="text-end">{{ __('admin.workflow_ui_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->name }}</div>
                                @if($item->description)
                                    <div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->description, 90) }}</div>
                                @endif
                            </td>
                            <td>{{ $workflowService->triggerLabel($item->trigger_type) }}</td>
                            <td class="small">{{ $workflowService->actionSummary($item) }}</td>
                            <td><span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active ? __('admin.workflow_ui_active') : __('admin.workflow_ui_inactive') }}</span></td>
                            <td>{{ $item->priority }}</td>
                            <td>{{ optional($item->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.workflow-automations.show', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.workflow_ui_details') }}</a>
                                @if(auth()->user()?->hasPermission('workflow_automations.manage'))
                                    <a href="{{ route('admin.workflow-automations.edit', $item) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.workflow_ui_edit') }}</a>
                                    <form method="post" action="{{ route('admin.workflow-automations.toggle', $item) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-dark">{{ $item->is_active ? __('admin.workflow_ui_disable') : __('admin.workflow_ui_enable') }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">{{ __('admin.workflow_ui_no_results') }}</td></tr>
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
