@extends('layouts.admin')

@section('page_title', __('admin.accounting_customer_accounts'))
@section('page_description', __('admin.accounting_customer_accounts_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <form method="GET" action="{{ route('admin.accounting.customers.index') }}" class="row g-3">
        <div class="col-md-3"><label class="form-label">{{ __('admin.from') }}</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.to') }}</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.crm_salesman') }}</label><select name="seller_id" class="form-select"><option value="">{{ __('admin.all') }}</option>@foreach($sellers as $seller)<option value="{{ $seller->id }}" @selected((int) request('seller_id') === (int) $seller->id)>{{ $seller->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.accounting_payment_status') }}</label><select name="payment_status" class="form-select"><option value="">{{ __('admin.all') }}</option><option value="unpaid" @selected(request('payment_status') === 'unpaid')>{{ __('admin.accounting_unpaid') }}</option><option value="partially_paid" @selected(request('payment_status') === 'partially_paid')>{{ __('admin.accounting_partially_paid') }}</option><option value="fully_paid" @selected(request('payment_status') === 'fully_paid')>{{ __('admin.accounting_fully_paid') }}</option></select></div>
        <div class="col-12 d-flex gap-2"><button class="btn btn-primary">{{ __('admin.search') }}</button><a href="{{ route('admin.accounting.customers.index') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a></div>
    </form>
</div>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.full_name') }}</th>
                    <th>{{ __('admin.crm_salesman') }}</th>
                    <th>{{ __('admin.accounting_total_amount') }}</th>
                    <th>{{ __('admin.accounting_paid_amount') }}</th>
                    <th>{{ __('admin.accounting_remaining_amount') }}</th>
                    <th>{{ __('admin.accounting_total_customer_expenses') }}</th>
                    <th>{{ __('admin.accounting_final_company_profit') }}</th>
                    <th>{{ __('admin.accounting_payment_status') }}</th>
                    <th>{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td><div class="fw-semibold">{{ $item->customer_name }}</div><div class="small text-muted">{{ $item->phone ?: '-' }}</div></td>
                    <td>{{ $item->assignedUser?->name ?: '-' }}</td>
                    <td>{{ number_format($item->total_amount, 2) }}</td>
                    <td>{{ number_format($item->paid_amount, 2) }}</td>
                    <td>{{ number_format($item->remaining_amount, 2) }}</td>
                    <td>{{ number_format($item->total_customer_expenses, 2) }}</td>
                    <td>{{ number_format($item->final_company_profit, 2) }}</td>
                    <td><span class="badge {{ $item->payment_status === 'fully_paid' ? 'text-bg-success' : ($item->payment_status === 'partially_paid' ? 'text-bg-warning' : 'text-bg-danger') }}">{{ $item->inquiry?->localizedPaymentStatus() ?: __('admin.accounting_unpaid') }}</span></td>
                    <td><a href="{{ route('admin.accounting.customers.show', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a></td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
