@extends('layouts.admin')

@section('page_title', __('admin.goals_targets_ui_details_title'))
@section('page_description', __('admin.goals_targets_ui_page_desc'))

@section('content')
@php($goal = $row['goal'])
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">{{ $goal->user?->name ?: '-' }}</h2>
                    <div class="text-muted">{{ __('admin.goals_targets_ui_goal_type') }}: {{ $targetTypes[$goal->target_type] ?? $goal->target_type }}</div>
                </div>
                <span class="badge text-bg-{{ $row['is_achieved'] ? 'success' : 'warning' }}">{{ $row['is_achieved'] ? __('admin.goals_targets_ui_achieved') : __('admin.goals_targets_ui_not_achieved') }}</span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.goals_targets_ui_goal_value') }}</div><div class="fs-4 fw-semibold">{{ number_format((float) $goal->target_value, 2) }}</div></div></div>
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.goals_targets_ui_achieved_value') }}</div><div class="fs-4 fw-semibold">{{ number_format((float) $row['achieved_value'], 2) }}</div></div></div>
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.goals_targets_ui_achievement_percentage') }}</div><div class="fs-4 fw-semibold">{{ $row['progress_percent'] }}%</div></div></div>
            </div>

            <div class="progress mb-4" role="progressbar" aria-valuenow="{{ $row['progress_percent'] }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar {{ $row['is_achieved'] ? 'bg-success' : 'bg-primary' }}" style="width: {{ min(100, $row['progress_percent']) }}%">{{ $row['progress_percent'] }}%</div>
            </div>

            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('admin.goals_targets_ui_period_type') }}</dt><dd class="col-sm-8">{{ $goal->localizedPeriodType() }}</dd>
                <dt class="col-sm-4">{{ __('admin.goals_targets_ui_goal_period') }}</dt><dd class="col-sm-8">{{ optional($goal->period_start)->format('Y-m-d') }} - {{ optional($goal->period_end)->format('Y-m-d') }}</dd>
                <dt class="col-sm-4">{{ __('admin.goals_targets_ui_status') }}</dt><dd class="col-sm-8">{{ $goal->is_active ? __('admin.goals_targets_ui_active') : __('admin.goals_targets_ui_inactive') }}</dd>
                <dt class="col-sm-4">{{ __('admin.goals_targets_ui_notes') }}</dt><dd class="col-sm-8">{{ $goal->note ?: '-' }}</dd>
            </dl>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.goals_targets_ui_actions') }}</h2>
            <div class="d-grid gap-2">
                @if(auth()->user()?->hasPermission('goals_commissions.manage'))
                    <a href="{{ route('admin.goals-commissions.targets.edit', $goal) }}" class="btn btn-primary">{{ __('admin.goals_targets_ui_edit') }}</a>
                @endif
                <a href="{{ route('admin.goals-commissions.targets.index') }}" class="btn btn-outline-secondary">{{ __('admin.goals_targets_ui_page_title') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
