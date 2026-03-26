@extends('layouts.admin')

@section('page_title', __('admin.commission_statement_details'))
@section('page_description', __('admin.goals_commissions_desc'))

@section('content')
@php($snapshot = $statement->calculation_snapshot ?? [])
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">{{ $statement->user?->name ?: '-' }}</h2>
                    <div class="text-muted">{{ optional($statement->period_start)->format('Y-m-d') }} - {{ optional($statement->period_end)->format('Y-m-d') }}</div>
                </div>
                <span class="badge text-bg-{{ $statement->payment_status === 'fully_paid' ? 'success' : ($statement->payment_status === 'partially_paid' ? 'warning' : 'secondary') }}">{{ $statement->localizedPaymentStatus() }}</span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.commission_earned') }}</div><div class="fs-4 fw-semibold">{{ number_format((float) $statement->earned_amount, 2) }}</div></div></div>
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.commission_paid') }}</div><div class="fs-4 fw-semibold text-success">{{ number_format((float) $statement->paid_amount, 2) }}</div></div></div>
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.commission_remaining') }}</div><div class="fs-4 fw-semibold text-danger">{{ number_format((float) $statement->remaining_amount, 2) }}</div></div></div>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('admin.commission_basis') }}</dt><dd class="col-sm-8">{{ $statement->localizedBasisType() }}</dd>
                <dt class="col-sm-4">{{ __('admin.commission_payment_status') }}</dt><dd class="col-sm-8">{{ $statement->localizedPaymentStatus() }}</dd>
                <dt class="col-sm-4">{{ __('admin.notes') }}</dt><dd class="col-sm-8">{{ $statement->note ?: '-' }}</dd>
            </dl>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.commission_calculation_snapshot') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody>
                        <tr><th>{{ __('admin.collected_amount') }}</th><td>{{ number_format((float) ($snapshot['collected_amount'] ?? 0), 2) }}</td></tr>
                        <tr><th>{{ __('admin.total_customer_expenses') }}</th><td>{{ number_format((float) ($snapshot['customer_expenses'] ?? 0), 2) }}</td></tr>
                        <tr><th>{{ __('admin.company_profit_before_seller') }}</th><td>{{ number_format((float) ($snapshot['company_profit_before_seller'] ?? 0), 2) }}</td></tr>
                        <tr><th>{{ __('admin.commission_rate') }}</th><td>{{ number_format(((float) ($snapshot['commission_rate'] ?? 0)) * 100, 2) }}%</td></tr>
                        <tr><th>{{ __('admin.final_net_profit_contribution') }}</th><td>{{ number_format((float) ($snapshot['final_net_profit_contribution'] ?? 0), 2) }}</td></tr>
                        <tr><th>{{ __('admin.commission_paid') }}</th><td>{{ number_format((float) ($snapshot['paid_commission_transactions'] ?? 0), 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.quick_actions') }}</h2>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.goals-commissions.commissions.index') }}" class="btn btn-outline-secondary">{{ __('admin.commission_statements') }}</a>
                <a href="{{ route('admin.accounting.employees.index', ['user_id' => $statement->user_id, 'transaction_type' => 'commission', 'from' => optional($statement->period_start)->format('Y-m-d'), 'to' => optional($statement->period_end)->format('Y-m-d')]) }}" class="btn btn-outline-primary">{{ __('admin.accounting_employees') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
