@extends('layouts.admin')

@section('page_title', __('admin.accounting_dashboard'))
@section('page_description', __('admin.accounting_dashboard_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <form method="GET" action="{{ route('admin.accounting.dashboard') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.from') }}</label>
            <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.to') }}</label>
            <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.crm_salesman') }}</label>
            <select name="seller_id" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}" @selected((int) ($filters['seller_id'] ?? 0) === (int) $seller->id)>{{ $seller->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ __('admin.search') }}</button>
            <a href="{{ route('admin.accounting.dashboard') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
        </div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_total_revenue') }}</div><div class="h3 mb-0">{{ number_format($summary['total_revenue'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_total_collected') }}</div><div class="h3 mb-0 text-success">{{ number_format($summary['total_collected'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_total_remaining') }}</div><div class="h3 mb-0 text-danger">{{ number_format($summary['total_remaining'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_total_customer_expenses') }}</div><div class="h3 mb-0">{{ number_format($summary['total_customer_expenses'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_total_general_expenses') }}</div><div class="h3 mb-0">{{ number_format($summary['total_general_expenses'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_company_profit_before_seller') }}</div><div class="h3 mb-0">{{ number_format($summary['company_profit_before_seller'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_seller_profit_total') }}</div><div class="h3 mb-0">{{ number_format($summary['seller_profit_total'], 2) }}</div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_final_company_profit') }}</div><div class="h3 mb-0 text-primary">{{ number_format($summary['final_company_profit'], 2) }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_seller_profit_report') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.crm_salesman') }}</th><th>{{ __('admin.total') }}</th><th>{{ __('admin.accounting_seller_profit') }}</th><th>{{ __('admin.accounting_final_company_profit') }}</th></tr></thead>
                    <tbody>
                    @forelse($sellerReport as $row)
                        <tr>
                            <td>{{ $row['user']->name }}</td>
                            <td>{{ number_format($row['total_amount'], 2) }}</td>
                            <td>{{ number_format($row['seller_profit'], 2) }}</td>
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
    <div class="col-xl-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_daily_profit_summary') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.date') }}</th><th>{{ __('admin.accounting_total_revenue') }}</th><th>{{ __('admin.accounting_total_collected') }}</th><th>{{ __('admin.accounting_final_company_profit') }}</th></tr></thead>
                    <tbody>
                    @forelse($dailyProfit as $row)
                        <tr>
                            <td>{{ $row['day'] }}</td>
                            <td>{{ number_format($row['total_amount'], 2) }}</td>
                            <td>{{ number_format($row['total_paid'], 2) }}</td>
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
@endsection
