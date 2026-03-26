@extends('layouts.admin')

@section('page_title', $task->title)
@section('page_description', __('admin.crm_task_details'))

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card admin-card p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">{{ $task->title }}</h2>
                    <div class="text-muted d-flex flex-wrap gap-2">
                        <span class="badge text-bg-{{ $task->typeBadgeClass() }}">{{ $task->localizedType() }}</span>
                        @if($task->category)
                            <span class="badge text-bg-{{ $task->categoryBadgeClass() }}">{{ $task->localizedCategory() }}</span>
                        @endif
                    </div>
                </div>
                <span class="badge text-bg-{{ $task->visualStatus() }}">{{ $task->localizedStatus() }}</span>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-5">{{ __('admin.assigned_to') }}</dt><dd class="col-sm-7">{{ $task->assignedUser?->name ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.created_by') }}</dt><dd class="col-sm-7">{{ $task->creator?->name ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.priority') }}</dt><dd class="col-sm-7"><span class="badge" style="{{ $task->priorityBadgeStyle() }}">{{ $task->localizedPriority() }}</span></dd>
                <dt class="col-sm-5">{{ __('admin.status') }}</dt><dd class="col-sm-7">{{ $task->localizedWorkflowStatus() }}</dd>
                <dt class="col-sm-5">التأخير</dt><dd class="col-sm-7">{{ $task->overdueLabel() ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.crm_task_due_date') }}</dt><dd class="col-sm-7">{{ optional($task->due_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-5">اكتملت في</dt><dd class="col-sm-7">{{ optional($task->completed_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.crm_task_last_activity') }}</dt><dd class="col-sm-7">{{ optional($task->last_activity_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.crm_task_close_note') }}</dt><dd class="col-sm-7">{{ $task->closed_note ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.notes') }}</dt><dd class="col-sm-7">{{ $task->notes ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.description') }}</dt><dd class="col-sm-7">{{ $task->description ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.crm_related_lead') }}</dt>
                <dd class="col-sm-7">
                    @if($task->inquiry)
                        <a href="{{ route('admin.crm.leads.show', $task->inquiry) }}">{{ $task->inquiry->full_name }}</a>
                    @else
                        -
                    @endif
                </dd>
            </dl>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <a href="{{ route('admin.crm.tasks.edit', $task) }}" class="btn btn-primary">{{ __('admin.edit') }}</a>
                @if($task->inquiry)
                    <a href="{{ route('admin.crm.leads.show', $task->inquiry) }}" class="btn btn-outline-secondary">{{ __('admin.crm_popup_open_lead') }}</a>
                @endif
                <a href="{{ route('admin.crm.tasks.index') }}" class="btn btn-outline-dark">لوحة المهام</a>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_task_activity') }}</h2>
            <div class="d-grid gap-3">
                @forelse($task->activities as $activity)
                    <div class="border rounded-4 p-3">
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <strong>{{ $activity->user?->name ?: 'System' }}</strong>
                            <span class="text-muted small">{{ optional($activity->created_at)->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="fw-semibold small">{{ $activity->localizedAction() }}</div>
                        @if($activity->old_value || $activity->new_value)
                            <div class="text-muted small mt-1">{{ $activity->old_value ?: '-' }} -> {{ $activity->new_value ?: '-' }}</div>
                        @endif
                        @if($activity->note)
                            <div class="text-muted small mt-2">{{ $activity->note }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
