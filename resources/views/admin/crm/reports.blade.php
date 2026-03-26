@extends('layouts.admin')

@section('page_title', __('admin.crm_reports'))
@section('page_description', __('admin.crm_reports_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.crm_reports_filters_title') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.crm_reports_filters_desc') }}</p>
        </div>
        <a href="{{ route('admin.crm.reports') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
    </div>
    <form method="GET" action="{{ route('admin.crm.reports') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_employee') }}</label>
            <select name="employee_id" class="form-select">
                <option value="">{{ __('admin.crm_reports_all_employees') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((int) ($filters['employee_id'] ?? 0) === (int) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_day') }}</label>
            <input type="date" name="day" value="{{ $filters['day'] }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_from_date') }}</label>
            <input type="date" name="from_date" value="{{ $filters['from_date'] }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_to_date') }}</label>
            <input type="date" name="to_date" value="{{ $filters['to_date'] }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_current_status') }}</label>
            <select name="crm_status_id" class="form-select">
                <option value="">{{ __('admin.crm_reports_all_statuses') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @selected((int) ($filters['crm_status_id'] ?? 0) === (int) $status->id)>{{ $status->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_lead_source') }}</label>
            <select name="crm_source_id" class="form-select">
                <option value="">{{ __('admin.crm_reports_all_sources') }}</option>
                @foreach($sources as $source)
                    <option value="{{ $source->id }}" @selected((int) ($filters['crm_source_id'] ?? 0) === (int) $source->id)>{{ $source->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_owner') }}</label>
            <select name="assigned_user_id" class="form-select">
                <option value="">{{ __('admin.crm_reports_all') }}</option>
                <option value="unassigned" @selected(($filters['assigned_user_id'] ?? null) === 'unassigned')>{{ __('admin.crm_reports_unassigned') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) ($filters['assigned_user_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_assignment_state') }}</label>
            <select name="assignment_state" class="form-select">
                <option value="all" @selected(($filters['assignment_state'] ?? 'all') === 'all')>{{ __('admin.crm_reports_all') }}</option>
                <option value="assigned" @selected(($filters['assignment_state'] ?? 'all') === 'assigned')>{{ __('admin.crm_reports_assigned_only') }}</option>
                <option value="unassigned" @selected(($filters['assignment_state'] ?? 'all') === 'unassigned')>{{ __('admin.crm_reports_unassigned_only') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_reports_delay_state') }}</label>
            <select name="delayed_state" class="form-select">
                <option value="all" @selected(($filters['delayed_state'] ?? 'all') === 'all')>{{ __('admin.crm_reports_all') }}</option>
                <option value="delayed" @selected(($filters['delayed_state'] ?? 'all') === 'delayed')>{{ __('admin.crm_reports_delayed_only') }}</option>
                <option value="not_delayed" @selected(($filters['delayed_state'] ?? 'all') === 'not_delayed')>{{ __('admin.crm_reports_not_delayed_only') }}</option>
            </select>
        </div>
        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">{{ __('admin.crm_reports_apply') }}</button>
            <a href="{{ route('admin.crm.reports') }}" class="btn btn-outline-secondary">{{ __('admin.crm_reports_reset') }}</a>
        </div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_handled_leads') }}</div><div class="h3 mb-0">{{ $summary['handled_leads'] }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_total_actions') }}</div><div class="h3 mb-0">{{ $summary['total_actions'] }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_status_changes') }}</div><div class="h3 mb-0">{{ $summary['status_changes'] }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_followups_scheduled') }}</div><div class="h3 mb-0">{{ $summary['follow_ups_scheduled'] }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_converted') }}</div><div class="h3 mb-0 text-success">{{ $summary['converted'] }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_no_answer_rate') }}</div><div class="h3 mb-0 text-danger">{{ $summary['no_answer_rate'] }}%</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_conversion_rate') }}</div><div class="h3 mb-0 text-success">{{ $summary['conversion_rate'] }}%</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_avg_first_response') }}</div><div class="h3 mb-0">{{ $summary['avg_first_response_hours'] }} <small class="text-muted">{{ __('admin.crm_reports_hours_unit') }}</small></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_delayed_leads') }}</div><div class="h3 mb-0 text-danger">{{ $summary['delayed_leads'] }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_overdue_followups') }}</div><div class="h3 mb-0 text-danger">{{ $summary['overdue_follow_ups'] }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_average_overdue_days') }}</div><div class="h3 mb-0">{{ $summary['average_overdue_days'] }} <small class="text-muted">{{ __('admin.crm_reports_days_unit') }}</small></div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_delayed_actions') }}</div><div class="h3 mb-0">{{ $summary['delayed_leads_acted_on'] }}</div></div></div>
</div>

@if($selectedEmployee)
    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h2 class="h5 mb-1">{{ __('admin.crm_reports_employee_summary') }}</h2>
                <p class="text-muted mb-0">{{ $selectedEmployee->name }} {{ __('admin.crm_reports_employee_summary_period') }}</p>
            </div>
            <span class="badge bg-primary-subtle text-primary">{{ $filters['from_date'] }} - {{ $filters['to_date'] }}</span>
        </div>
        <div class="row g-3">
            <div class="col-md-3"><div class="border rounded-3 p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_employee_assigned') }}</div><div class="fs-4 fw-semibold">{{ $summary['assigned_leads'] }}</div></div></div>
            <div class="col-md-3"><div class="border rounded-3 p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_employee_notes') }}</div><div class="fs-4 fw-semibold">{{ $summary['notes_added'] }}</div></div></div>
            <div class="col-md-3"><div class="border rounded-3 p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_employee_tasks_completed') }}</div><div class="fs-4 fw-semibold">{{ $summary['tasks_completed'] }}</div></div></div>
            <div class="col-md-3"><div class="border rounded-3 p-3 h-100"><div class="text-muted small">{{ __('admin.crm_reports_employee_actions_per_lead') }}</div><div class="fs-4 fw-semibold">{{ $summary['actions_per_lead'] }}</div></div></div>
        </div>
    </div>
@else
    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h2 class="h5 mb-1">{{ __('admin.crm_reports_employee_comparison') }}</h2>
                <p class="text-muted mb-0">{{ __('admin.crm_reports_employee_comparison_desc') }}</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('admin.crm_reports_employee') }}</th>
                        <th>{{ __('admin.crm_reports_employee_assigned_short') }}</th>
                        <th>{{ __('admin.crm_reports_employee_handled_short') }}</th>
                        <th>{{ __('admin.crm_reports_employee_notes_short') }}</th>
                        <th>{{ __('admin.crm_reports_employee_status_changes_short') }}</th>
                        <th>{{ __('admin.crm_reports_employee_followups_short') }}</th>
                        <th>{{ __('admin.crm_reports_employee_delayed_short') }}</th>
                        <th>{{ __('admin.crm_reports_employee_no_answer_short') }}</th>
                        <th>{{ __('admin.crm_reports_employee_conversion_short') }}</th>
                        <th>{{ __('admin.crm_reports_employee_last_activity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employeeComparison as $row)
                        <tr>
                            <td>{{ $row['user']->name }}</td>
                            <td>{{ $row['assigned'] }}</td>
                            <td>{{ $row['handled'] }}</td>
                            <td>{{ $row['notes'] }}</td>
                            <td>{{ $row['status_changes'] }}</td>
                            <td>{{ $row['follow_ups'] }}</td>
                            <td><span class="badge bg-danger-subtle text-danger">{{ $row['delayed'] }}</span></td>
                            <td>{{ $row['no_answer'] }} <small class="text-danger">({{ $row['no_answer_rate'] }}%)</small></td>
                            <td>{{ $row['converted'] }} <small class="text-success">({{ $row['conversion_rate'] }}%)</small></td>
                            <td>{{ $row['last_activity_at'] ? \Illuminate\Support\Carbon::parse($row['last_activity_at'])->format('Y-m-d H:i') : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">{{ __('admin.crm_reports_employee_comparison_empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_reports_status_movement') }}</h2>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>{{ __('admin.crm_reports_status') }}</th><th class="text-end">{{ __('admin.crm_reports_count') }}</th></tr></thead>
                    <tbody>
                    @forelse($statusMovement as $row)
                        <tr>
                            <td><span class="badge text-bg-{{ $row['color'] }}">{{ $row['label'] }}</span></td>
                            <td class="text-end">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-4">{{ __('admin.crm_reports_status_movement_empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_reports_status_transitions') }}</h2>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>{{ __('admin.crm_reports_transition') }}</th><th class="text-end">{{ __('admin.crm_reports_count') }}</th></tr></thead>
                    <tbody>
                    @forelse($statusTransitions as $row)
                        <tr>
                            <td>{{ $row['from'] }} → {{ $row['to'] }}</td>
                            <td class="text-end">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-4">{{ __('admin.crm_reports_status_transitions_empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_reports_current_status_breakdown') }}</h2>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>{{ __('admin.crm_reports_status') }}</th><th class="text-end">{{ __('admin.crm_reports_count') }}</th></tr></thead>
                    <tbody>
                    @forelse($currentStatusBreakdown as $row)
                        <tr>
                            <td><span class="badge text-bg-{{ $row['color'] }}">{{ $row['label'] }}</span></td>
                            <td class="text-end">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-4">{{ __('admin.crm_reports_current_status_empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_reports_source_performance') }}</h2>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.crm_reports_source') }}</th>
                            <th>{{ __('admin.crm_reports_leads') }}</th>
                            <th>{{ __('admin.crm_reports_conversion') }}</th>
                            <th>{{ __('admin.crm_reports_no_answer') }}</th>
                            <th>{{ __('admin.crm_reports_delayed') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($sourceBreakdown as $row)
                        <tr>
                            <td>{{ $row['label'] }}</td>
                            <td>{{ $row['lead_count'] }}</td>
                            <td>{{ $row['converted'] }} <small class="text-success">({{ $row['conversion_rate'] }}%)</small></td>
                            <td>{{ $row['no_answer'] }} <small class="text-danger">({{ $row['no_answer_rate'] }}%)</small></td>
                            <td><span class="badge bg-danger-subtle text-danger">{{ $row['delayed'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('admin.crm_reports_source_performance_empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_reports_activity_details') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.crm_reports_lead') }}</th>
                            <th>{{ __('admin.crm_reports_employee') }}</th>
                            <th>{{ __('admin.crm_reports_action_type') }}</th>
                            <th>{{ __('admin.crm_reports_status') }}</th>
                            <th>{{ __('admin.crm_reports_note') }}</th>
                            <th>{{ __('admin.crm_reports_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($activityLog as $row)
                        <tr>
                            <td>
                                <a href="{{ $row['link'] }}" class="fw-semibold text-decoration-none">{{ $row['lead_name'] ?: ('#' . $row['lead_id']) }}</a>
                                @if($row['source'])
                                    <div class="small text-muted">{{ $row['source'] }}</div>
                                @endif
                            </td>
                            <td>{{ $row['employee_name'] ?: '—' }}</td>
                            <td>{{ $row['action_type'] }}</td>
                            <td class="small">
                                @if($row['old_status'] || $row['new_status'])
                                    <div>{{ $row['old_status'] ?: '—' }} → {{ $row['new_status'] ?: '—' }}</div>
                                @else
                                    —
                                @endif
                                @if($row['follow_up_at'])
                                    <div class="text-muted">{{ __('admin.crm_reports_follow_up_label') }}: {{ \Illuminate\Support\Carbon::parse($row['follow_up_at'])->format('Y-m-d H:i') }}</div>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $row['note'] ?: '—' }}</td>
                            <td>{{ $row['action_at'] ? \Illuminate\Support\Carbon::parse($row['action_at'])->format('Y-m-d H:i') : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">{{ __('admin.crm_reports_activity_empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_reports_daily_summary') }}</h2>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.crm_reports_date') }}</th>
                            <th>{{ __('admin.crm_reports_total') }}</th>
                            <th>{{ __('admin.crm_reports_statuses') }}</th>
                            <th>{{ __('admin.crm_reports_notes') }}</th>
                            <th>{{ __('admin.crm_reports_followups') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($dailyBreakdown as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['total_actions'] }}</td>
                            <td>{{ $row['status_changes'] }}</td>
                            <td>{{ $row['notes'] }}</td>
                            <td>{{ $row['follow_ups_scheduled'] + $row['follow_ups_completed'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('admin.crm_reports_daily_empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_reports_warning_insights') }}</h2>
            <div class="d-grid gap-3">
                <div class="border rounded-3 p-3">
                    <div class="small text-muted">{{ __('admin.crm_reports_warning_scheduled_untouched') }}</div>
                    <div class="fs-4 fw-semibold text-danger">{{ $warningInsights['scheduled_untouched'] }}</div>
                </div>
                <div class="border rounded-3 p-3">
                    <div class="small text-muted">{{ __('admin.crm_reports_warning_inactive_five_days') }}</div>
                    <div class="fs-4 fw-semibold text-danger">{{ $warningInsights['inactive_five_days'] }}</div>
                </div>
                <div class="border rounded-3 p-3">
                    <div class="small text-muted">{{ __('admin.crm_reports_warning_overdue_followups') }}</div>
                    <div class="fs-4 fw-semibold text-danger">{{ $warningInsights['overdue_follow_ups'] }}</div>
                </div>
                <div class="border rounded-3 p-3">
                    <div class="small text-muted">{{ __('admin.crm_reports_warning_average_overdue') }}</div>
                    <div class="fs-4 fw-semibold">{{ $warningInsights['average_overdue_days'] }} <small class="text-muted">{{ __('admin.crm_reports_days_unit') }}</small></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-7">
        <div class="card admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h2 class="h5 mb-0">{{ __('admin.crm_reports_delayed_samples') }}</h2>
                <a href="{{ route('admin.crm.leads.delayed') }}" class="btn btn-sm btn-outline-danger">{{ __('admin.crm_reports_open_delayed_page') }}</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.crm_reports_lead') }}</th><th>{{ __('admin.crm_reports_status') }}</th><th>{{ __('admin.crm_reports_delay_reason') }}</th><th>{{ __('admin.crm_reports_last_activity') }}</th></tr></thead>
                    <tbody>
                    @forelse($warningInsights['preview'] as $lead)
                        <tr>
                            <td><a href="{{ route('admin.crm.leads.show', $lead) }}" class="fw-semibold text-decoration-none">{{ $lead->full_name }}</a></td>
                            <td>{{ $lead->localizedEffectiveStatus() }}</td>
                            <td><span class="badge bg-danger-subtle text-danger">{{ $lead->delay_reason }}</span></td>
                            <td>{{ $lead->delay_last_action_at ? \Illuminate\Support\Carbon::parse($lead->delay_last_action_at)->format('Y-m-d H:i') : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">{{ __('admin.crm_reports_delayed_samples_empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card admin-card p-4">
    <h2 class="h5 mb-3">{{ __('admin.crm_reports_data_limits') }}</h2>
    <div class="row g-4">
        <div class="col-lg-6">
            <h3 class="h6">{{ __('admin.crm_reports_supported_metrics') }}</h3>
            <ul class="mb-0">
                @foreach($supportedMetrics as $metric)
                    <li>{{ $metric }}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-lg-6">
            <h3 class="h6">{{ __('admin.crm_reports_skipped_metrics') }}</h3>
            <ul class="mb-0">
                @foreach($skippedMetrics as $metric)
                    <li class="text-muted">{{ $metric }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
