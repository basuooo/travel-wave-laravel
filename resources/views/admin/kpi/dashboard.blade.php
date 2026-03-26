@extends('layouts.admin')

@section('page_title', __('admin.kpi_dashboard'))
@section('page_description', __('admin.kpi_dashboard_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <form method="GET" action="{{ route('admin.kpi.dashboard') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.from') }}</label>
            <input type="date" name="from_date" value="{{ $filters['from_date'] }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.to') }}</label>
            <input type="date" name="to_date" value="{{ $filters['to_date'] }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.kpi_employee_filter') }}</label>
            <select name="seller_id" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($availableUsers as $user)
                    <option value="{{ $user->id }}" @selected((int) ($filters['seller_id'] ?? 0) === (int) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">{{ __('admin.search') }}</button>
            <a href="{{ route('admin.kpi.dashboard') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
        </div>
    </form>
</div>

<div class="row g-3 mb-4">
    @foreach($summaryCards as $card)
        <div class="col-xl-3 col-md-6">
            <div class="card admin-card p-3 h-100 border-{{ in_array($card['tone'], ['danger', 'warning', 'success', 'primary'], true) ? $card['tone'] : 'light' }}">
                <div class="text-muted small">{{ $card['label'] }}</div>
                <div class="h3 mb-0 @if($card['tone'] === 'danger') text-danger @elseif($card['tone'] === 'success') text-success @endif">{{ $card['value'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">{{ __('admin.kpi_leads_overview') }}</h2>
                <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.view') }}</a>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_leads_today') }}</div><div class="fs-4 fw-semibold">{{ $crm['summary']['today'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_leads_this_week') }}</div><div class="fs-4 fw-semibold">{{ $crm['summary']['this_week'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_leads_this_month') }}</div><div class="fs-4 fw-semibold">{{ $crm['summary']['this_month'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_unassigned_leads') }}</div><div class="fs-4 fw-semibold">{{ $crm['summary']['unassigned'] }}</div></div></div>
            </div>
            <div class="table-responsive mb-4">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.status') }}</th><th>{{ __('admin.total') }}</th><th class="w-50">{{ __('admin.kpi_distribution') }}</th></tr></thead>
                    <tbody>
                    @php($leadStatusMax = max(1, $crm['status_breakdown']->max('count') ?? 1))
                    @forelse($crm['status_breakdown'] as $row)
                        <tr>
                            <td><span class="badge text-bg-{{ $row['color'] }}">{{ $row['label'] }}</span></td>
                            <td>{{ $row['count'] }}</td>
                            <td>
                                <div class="progress" style="height:8px;">
                                    <div class="progress-bar bg-{{ $row['color'] }}" style="width: {{ round(($row['count'] / $leadStatusMax) * 100, 1) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <h3 class="h6 mb-3">{{ __('admin.kpi_leads_trend') }}</h3>
                <div class="row g-2">
                    @php($leadTrendMax = max(1, $trends['leads']->max('count') ?? 1))
                    @foreach($trends['leads'] as $point)
                        <div class="col-md-3 col-6">
                            <div class="border rounded-3 p-2 h-100">
                                <div class="small text-muted">{{ $point['label'] }}</div>
                                <div class="fw-semibold">{{ $point['count'] }}</div>
                                <div class="progress mt-2" style="height:6px;">
                                    <div class="progress-bar" style="width: {{ round(($point['count'] / $leadTrendMax) * 100, 1) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">{{ __('admin.kpi_tasks_overview') }}</h2>
                <a href="{{ route('admin.crm.tasks.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.view') }}</a>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_open_tasks') }}</div><div class="fs-4 fw-semibold">{{ $tasks['summary']['open'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_tasks_due_today') }}</div><div class="fs-4 fw-semibold">{{ $tasks['summary']['due_today'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted text-danger">{{ __('admin.kpi_delayed_tasks') }}</div><div class="fs-4 fw-semibold text-danger">{{ $tasks['summary']['delayed'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted text-success">{{ __('admin.kpi_completed_tasks_period') }}</div><div class="fs-4 fw-semibold text-success">{{ $tasks['summary']['completed_period'] }}</div></div></div>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <h3 class="h6 mb-3">{{ __('admin.kpi_tasks_by_status') }}</h3>
                    @php($statusMax = max(1, $tasks['status_breakdown']->max('count') ?? 1))
                    @foreach($tasks['status_breakdown'] as $row)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $row['label'] }}</span>
                            <strong>{{ $row['count'] }}</strong>
                        </div>
                        <div class="progress mb-3" style="height:8px;">
                            <div class="progress-bar bg-{{ $row['color'] }}" style="width: {{ round(($row['count'] / $statusMax) * 100, 1) }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-6">
                    <h3 class="h6 mb-3">{{ __('admin.kpi_tasks_by_priority') }}</h3>
                    @php($priorityMax = max(1, $tasks['priority_breakdown']->max('count') ?? 1))
                    @foreach($tasks['priority_breakdown'] as $row)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $row['label'] }}</span>
                            <strong>{{ $row['count'] }}</strong>
                        </div>
                        <div class="progress mb-3" style="height:8px;">
                            <div class="progress-bar bg-{{ $row['color'] }}" style="width: {{ round(($row['count'] / $priorityMax) * 100, 1) }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@if($canViewFinance)
    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">{{ __('admin.kpi_finance_overview') }}</h2>
            <a href="{{ route('admin.accounting.dashboard') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.view') }}</a>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_total_amount') }}</div><div class="fs-4 fw-semibold">{{ number_format($finance['summary']['total_amount'], 2) }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted text-success">{{ __('admin.kpi_total_collected') }}</div><div class="fs-4 fw-semibold text-success">{{ number_format($finance['summary']['total_collected'], 2) }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted text-danger">{{ __('admin.kpi_remaining_amount') }}</div><div class="fs-4 fw-semibold text-danger">{{ number_format($finance['summary']['total_remaining'], 2) }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_total_expenses') }}</div><div class="fs-4 fw-semibold">{{ number_format($finance['summary']['total_expenses'], 2) }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_company_profit_before_seller') }}</div><div class="fs-4 fw-semibold">{{ number_format($finance['summary']['company_profit_before_seller'], 2) }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_seller_share_total') }}</div><div class="fs-4 fw-semibold">{{ number_format($finance['summary']['seller_profit_total'], 2) }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_company_profit_after_seller') }}</div><div class="fs-4 fw-semibold">{{ number_format($finance['summary']['final_company_profit'], 2) }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="border rounded-4 p-3 h-100 border-primary"><div class="small text-muted">{{ __('admin.kpi_final_net_profit') }}</div><div class="fs-4 fw-semibold text-primary">{{ number_format($finance['summary']['final_net_profit'], 2) }}</div></div></div>
        </div>
        <div class="row g-4">
            <div class="col-xl-6">
                <h3 class="h6 mb-3">{{ __('admin.kpi_finance_trend') }}</h3>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>{{ __('admin.date') }}</th><th>{{ __('admin.kpi_total_collected') }}</th><th>{{ __('admin.kpi_total_general_expenses') }}</th></tr></thead>
                        <tbody>
                        @php($expenseMap = $trends['general_expenses']->keyBy('day'))
                        @forelse($trends['collections'] as $row)
                            @php($expenseAmount = data_get($expenseMap->get($row['day']), 'amount', 0))
                            <tr>
                                <td>{{ $row['day'] }}</td>
                                <td class="text-success">{{ number_format($row['amount'], 2) }}</td>
                                <td>{{ number_format((float) $expenseAmount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-xl-6">
                <h3 class="h6 mb-3">{{ __('admin.kpi_payment_status_summary') }}</h3>
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.accounting_fully_paid') }}</div><div class="fs-4 fw-semibold text-success">{{ $finance['summary']['fully_paid'] }}</div></div></div>
                    <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.accounting_partially_paid') }}</div><div class="fs-4 fw-semibold text-warning">{{ $finance['summary']['partially_paid'] }}</div></div></div>
                    <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.accounting_unpaid') }}</div><div class="fs-4 fw-semibold text-danger">{{ $finance['summary']['unpaid'] }}</div></div></div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>{{ __('admin.crm_salesman') }}</th><th>{{ __('admin.total') }}</th><th>{{ __('admin.kpi_total_collected') }}</th><th>{{ __('admin.kpi_final_net_profit') }}</th></tr></thead>
                        <tbody>
                        @forelse($finance['seller_rows'] as $row)
                            <tr>
                                <td>{{ $row['user']->name }}</td>
                                <td>{{ number_format($row['total_amount'], 2) }}</td>
                                <td class="text-success">{{ number_format($row['total_paid'], 2) }}</td>
                                <td>{{ number_format($row['final_profit'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.kpi_team_performance') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.name') }}</th><th>{{ __('admin.kpi_total_leads_period') }}</th><th>{{ __('admin.kpi_delayed_leads') }}</th><th>{{ __('admin.kpi_open_tasks') }}</th><th>{{ __('admin.kpi_completed_tasks_period') }}</th>@if($canViewFinance)<th>{{ __('admin.kpi_total_collected') }}</th>@endif</tr></thead>
                    <tbody>
                    @forelse($teamPerformance as $row)
                        <tr>
                            <td>{{ $row['user']->name }}</td>
                            <td>{{ $row['lead_count'] }}</td>
                            <td><span class="badge text-bg-danger">{{ $row['delayed_leads'] }}</span></td>
                            <td>{{ $row['open_tasks'] }}</td>
                            <td>{{ $row['completed_tasks'] }}</td>
                            @if($canViewFinance)<td>{{ number_format($row['collections'], 2) }}</td>@endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $canViewFinance ? 6 : 5 }}" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.kpi_important_alerts') }}</h2>
            <div class="d-grid gap-3">
                <div class="border rounded-4 p-3">
                    <div class="fw-semibold text-danger mb-2">{{ __('admin.kpi_delayed_leads') }}</div>
                    @forelse($alerts['delayed_leads'] as $lead)
                        <div class="d-flex justify-content-between gap-3 small py-1">
                            <a href="{{ route('admin.crm.leads.show', $lead) }}">{{ $lead->full_name ?: ('#' . $lead->id) }}</a>
                            <span class="text-muted">{{ $lead->assignedUser?->name ?: '-' }}</span>
                        </div>
                    @empty
                        <div class="text-muted small">{{ __('admin.no_data') }}</div>
                    @endforelse
                </div>
                <div class="border rounded-4 p-3">
                    <div class="fw-semibold text-danger mb-2">{{ __('admin.kpi_delayed_tasks') }}</div>
                    @forelse($alerts['delayed_tasks'] as $task)
                        <div class="d-flex justify-content-between gap-3 small py-1">
                            <a href="{{ route('admin.crm.tasks.show', $task) }}">{{ $task->title }}</a>
                            <span class="text-muted">{{ optional($task->due_at)->format('Y-m-d') ?: '-' }}</span>
                        </div>
                    @empty
                        <div class="text-muted small">{{ __('admin.no_data') }}</div>
                    @endforelse
                </div>
                @if($canViewFinance)
                    <div class="border rounded-4 p-3">
                        <div class="fw-semibold text-warning mb-2">{{ __('admin.kpi_high_remaining_accounts') }}</div>
                        @forelse($alerts['high_remaining_accounts'] as $account)
                            <div class="d-flex justify-content-between gap-3 small py-1">
                                <a href="{{ route('admin.accounting.customers.show', $account) }}">{{ $account->customer_name }}</a>
                                <span class="text-muted">{{ number_format($account->remaining_amount, 2) }}</span>
                            </div>
                        @empty
                            <div class="text-muted small">{{ __('admin.no_data') }}</div>
                        @endforelse
                    </div>
                @endif
                @if($canViewInformation)
                    <div class="border rounded-4 p-3">
                        <div class="fw-semibold text-primary mb-2">{{ __('admin.kpi_urgent_information') }}</div>
                        @forelse($alerts['urgent_information'] as $recipient)
                            <div class="d-flex justify-content-between gap-3 small py-1">
                                <a href="{{ route('admin.crm.information.show', $recipient->information) }}">{{ $recipient->information?->title }}</a>
                                <span class="text-muted">{{ $recipient->user?->name ?: '-' }}</span>
                            </div>
                        @empty
                            <div class="text-muted small">{{ __('admin.no_data') }}</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @if($canViewDocuments)
        <div class="col-xl-6">
            <div class="card admin-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">{{ __('admin.kpi_documents_overview') }}</h2>
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.view') }}</a>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.total_documents') }}</div><div class="fs-4 fw-semibold">{{ $documents['summary']['total'] }}</div></div></div>
                    <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.documents_uploaded_this_week') }}</div><div class="fs-4 fw-semibold">{{ $documents['summary']['this_week'] }}</div></div></div>
                    <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.documents_uploaded_today') }}</div><div class="fs-4 fw-semibold">{{ $documents['summary']['today'] }}</div></div></div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>{{ __('admin.document_title') }}</th><th>{{ __('admin.document_category') }}</th><th>{{ __('admin.uploaded_at') }}</th></tr></thead>
                        <tbody>
                        @forelse($documents['recent'] as $document)
                            <tr>
                                <td><a href="{{ route('admin.documents.show', $document) }}">{{ $document->title }}</a></td>
                                <td>{{ $document->category?->localizedName() ?: '-' }}</td>
                                <td>{{ optional($document->uploaded_at)->format('Y-m-d H:i') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">{{ __('admin.no_documents') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    @if($canViewInformation)
        <div class="col-xl-6">
            <div class="card admin-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">{{ __('admin.kpi_operational_summary') }}</h2>
                    <a href="{{ route('admin.crm.information.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.view') }}</a>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.kpi_unacknowledged_information') }}</div><div class="fs-4 fw-semibold">{{ $information['summary']['unacknowledged'] }}</div></div></div>
                    <div class="col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted text-danger">{{ __('admin.kpi_urgent_information') }}</div><div class="fs-4 fw-semibold text-danger">{{ $information['summary']['urgent_unacknowledged'] }}</div></div></div>
                </div>
                <div class="small text-muted">{{ __('admin.kpi_skipped_metrics_note') }}</div>
            </div>
        </div>
    @endif
</div>
@endsection
