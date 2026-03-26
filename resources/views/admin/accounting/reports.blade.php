@extends('layouts.admin')

@section('page_title', __('admin.accounting_reports'))
@section('page_description', __('admin.accounting_reports_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <form method="GET" action="{{ route('admin.accounting.reports') }}" class="row g-3">
        <div class="col-md-3"><label class="form-label">{{ __('admin.from') }}</label><input type="date" name="from" value="{{ $filters['from'] }}" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.to') }}</label><input type="date" name="to" value="{{ $filters['to'] }}" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.crm_salesman') }}</label><select name="seller_id" class="form-select"><option value="">{{ __('admin.all') }}</option>@foreach($sellers as $seller)<option value="{{ $seller->id }}" @selected((int) ($filters['seller_id'] ?? 0) === (int) $seller->id)>{{ $seller->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.accounting_payment_status') }}</label><select name="payment_status" class="form-select"><option value="">{{ __('admin.all') }}</option>@foreach($paymentStatusOptions as $value => $label)<option value="{{ $value }}" @selected(($filters['payment_status'] ?? null) === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-12 d-flex gap-2"><button class="btn btn-primary">{{ __('admin.search') }}</button><a href="{{ route('admin.accounting.reports') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a></div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_total_revenue') }}</div><div class="h3 mb-0">{{ number_format($summary['total_revenue'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_total_collected') }}</div><div class="h3 mb-0">{{ number_format($summary['total_collected'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_total_remaining') }}</div><div class="h3 mb-0">{{ number_format($summary['total_remaining'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_net_after_general_expenses') }}</div><div class="h3 mb-0">{{ number_format($summary['net_after_general_expenses'], 2) }}</div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_customer_accounting_report') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.full_name') }}</th><th>{{ __('admin.crm_salesman') }}</th><th>{{ __('admin.accounting_total_amount') }}</th><th>{{ __('admin.accounting_paid_amount') }}</th><th>{{ __('admin.accounting_remaining_amount') }}</th><th>{{ __('admin.accounting_final_company_profit') }}</th></tr></thead>
                    <tbody>
                        @forelse($customerAccounts as $account)
                            <tr>
                                <td>{{ $account->customer_name }}</td>
                                <td>{{ $account->assignedUser?->name ?: '-' }}</td>
                                <td>{{ number_format($account->total_amount, 2) }}</td>
                                <td>{{ number_format($account->paid_amount, 2) }}</td>
                                <td>{{ number_format($account->remaining_amount, 2) }}</td>
                                <td>{{ number_format($account->final_company_profit, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_employee_finance_report') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.employee') }}</th><th>{{ __('admin.accounting_salary') }}</th><th>{{ __('admin.accounting_advance') }}</th><th>{{ __('admin.accounting_commission') }}</th><th>{{ __('admin.accounting_net_paid') }}</th></tr></thead>
                    <tbody>
                        @forelse($employeeFinance as $row)
                            <tr>
                                <td>{{ $row['user']->name }}</td>
                                <td>{{ number_format($row['salary'], 2) }}</td>
                                <td>{{ number_format($row['advance'], 2) }}</td>
                                <td>{{ number_format($row['commission'], 2) }}</td>
                                <td>{{ number_format($row['net_paid'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_seller_profit_report') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.crm_salesman') }}</th><th>{{ __('admin.accounting_customer_count') }}</th><th>{{ __('admin.accounting_total_revenue') }}</th><th>{{ __('admin.accounting_seller_profit') }}</th></tr></thead>
                    <tbody>
                        @forelse($sellerReport as $row)
                            <tr>
                                <td>{{ $row['user']->name }}</td>
                                <td>{{ $row['customers'] }}</td>
                                <td>{{ number_format($row['total_amount'], 2) }}</td>
                                <td>{{ number_format($row['seller_profit'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_general_expenses') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.category') }}</th><th>{{ __('admin.amount') }}</th><th>{{ __('admin.date') }}</th></tr></thead>
                    <tbody>
                        @forelse($generalExpenses as $expense)
                            <tr>
                                <td>{{ $expense->category?->localizedName() ?: '-' }}</td>
                                <td>{{ number_format($expense->amount, 2) }}</td>
                                <td>{{ optional($expense->expense_date)->format('Y-m-d') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
