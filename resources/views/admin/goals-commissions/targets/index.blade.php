@extends('layouts.admin')

@section('page_title', __('admin.goals_targets_ui_page_title'))
@section('page_description', __('admin.goals_targets_ui_page_desc'))

@section('content')
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.goals_targets_ui_employee') }}</label>
            <select name="user_id" class="form-select">
                <option value="">{{ __('admin.goals_targets_ui_all') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((int) ($filters['user_id'] ?? 0) === (int) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.goals_targets_ui_goal_type') }}</label>
            <select name="target_type" class="form-select">
                <option value="">{{ __('admin.goals_targets_ui_all') }}</option>
                @foreach($targetTypes as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['target_type'] ?? null) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.goals_targets_ui_period_type') }}</label>
            <select name="period_type" class="form-select">
                <option value="">{{ __('admin.goals_targets_ui_all') }}</option>
                @foreach($periodTypes as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['period_type'] ?? null) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.goals_targets_ui_status') }}</label>
            <select name="active_state" class="form-select">
                <option value="">{{ __('admin.goals_targets_ui_all') }}</option>
                <option value="active" @selected(($filters['active_state'] ?? null) === 'active')>{{ __('admin.goals_targets_ui_active') }}</option>
                <option value="inactive" @selected(($filters['active_state'] ?? null) === 'inactive')>{{ __('admin.goals_targets_ui_inactive') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.goals_targets_ui_achievement_state') }}</label>
            <select name="achievement_state" class="form-select">
                <option value="">{{ __('admin.goals_targets_ui_all') }}</option>
                <option value="achieved" @selected(($filters['achievement_state'] ?? null) === 'achieved')>{{ __('admin.goals_targets_ui_achieved') }}</option>
                <option value="not_achieved" @selected(($filters['achievement_state'] ?? null) === 'not_achieved')>{{ __('admin.goals_targets_ui_not_achieved') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.goals_targets_ui_from_date') }}</label>
            <input type="date" class="form-control" name="from" value="{{ $filters['from'] ?? '' }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.goals_targets_ui_to_date') }}</label>
            <input type="date" class="form-control" name="to" value="{{ $filters['to'] ?? '' }}">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary">{{ __('admin.goals_targets_ui_search') }}</button>
            <a href="{{ route('admin.goals-commissions.targets.index') }}" class="btn btn-outline-secondary">{{ __('admin.goals_targets_ui_reset') }}</a>
            @if(auth()->user()?->hasPermission('goals_commissions.manage'))
                <a href="{{ route('admin.goals-commissions.targets.create') }}" class="btn btn-outline-primary">{{ __('admin.goals_targets_ui_add') }}</a>
            @endif
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.goals_targets_ui_total_targets') }}</div><div class="fs-4 fw-semibold">{{ $summary['total_targets'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.goals_targets_ui_active_targets') }}</div><div class="fs-4 fw-semibold">{{ $summary['active_targets'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.goals_targets_ui_achieved_targets') }}</div><div class="fs-4 fw-semibold text-success">{{ $summary['achieved_targets'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.goals_targets_ui_achievement_percentage') }}</div><div class="fs-4 fw-semibold">{{ $summary['average_progress'] }}%</div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.goals_targets_ui_employee') }}</th>
                            <th>{{ __('admin.goals_targets_ui_goal_type') }}</th>
                            <th>{{ __('admin.goals_targets_ui_goal_value') }}</th>
                            <th>{{ __('admin.goals_targets_ui_achieved_value') }}</th>
                            <th>{{ __('admin.goals_targets_ui_achievement_percentage') }}</th>
                            <th>{{ __('admin.goals_targets_ui_goal_period') }}</th>
                            <th>{{ __('admin.goals_targets_ui_status') }}</th>
                            <th class="text-end">{{ __('admin.goals_targets_ui_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $row)
                        <tr>
                            <td>{{ $row['goal']->user?->name ?: '-' }}</td>
                            <td>{{ $targetTypes[$row['goal']->target_type] ?? $row['goal']->target_type }}</td>
                            <td>{{ number_format((float) $row['goal']->target_value, 2) }}</td>
                            <td>{{ number_format((float) $row['achieved_value'], 2) }}</td>
                            <td style="min-width: 180px;">
                                <div class="progress" role="progressbar" aria-valuenow="{{ $row['progress_percent'] }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar {{ $row['is_achieved'] ? 'bg-success' : 'bg-primary' }}" style="width: {{ min(100, $row['progress_percent']) }}%">{{ $row['progress_percent'] }}%</div>
                                </div>
                            </td>
                            <td>{{ optional($row['goal']->period_start)->format('Y-m-d') }} - {{ optional($row['goal']->period_end)->format('Y-m-d') }}</td>
                            <td><span class="badge text-bg-{{ $row['goal']->is_active ? 'success' : 'secondary' }}">{{ $row['goal']->is_active ? __('admin.goals_targets_ui_active') : __('admin.goals_targets_ui_inactive') }}</span></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.goals-commissions.targets.show', $row['goal']) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.goals_targets_ui_view') }}</a>
                                    @if(auth()->user()?->hasPermission('goals_commissions.manage'))
                                        <a href="{{ route('admin.goals-commissions.targets.edit', $row['goal']) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.goals_targets_ui_edit') }}</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">{{ __('admin.goals_targets_ui_no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.goals_targets_ui_ranking') }}</h2>
            <div class="d-grid gap-3">
                @forelse($ranking as $row)
                    <div class="border rounded-4 p-3">
                        <div class="fw-semibold">{{ $row['user']?->name ?: '-' }}</div>
                        <div class="small text-muted">{{ __('admin.goals_targets_ui_achievement_percentage') }}: {{ $row['average_progress'] }}%</div>
                        <div class="small text-muted">{{ __('admin.goals_targets_ui_achieved_targets') }}: {{ $row['achieved_goals'] }} / {{ $row['total_goals'] }}</div>
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.goals_targets_ui_no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
