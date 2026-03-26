@extends('layouts.admin')

@section('page_title', $item->name)
@section('page_description', __('admin.accounting_treasury_ledger'))

@section('content')
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_treasury_details') }}</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">{{ __('admin.accounting_treasury_name') }}</dt><dd class="col-sm-7">{{ $item->name }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_treasury_type') }}</dt><dd class="col-sm-7">{{ $item->localizedType() }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_treasury_identifier') }}</dt><dd class="col-sm-7">{{ $item->identifier ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_treasury_opening_balance') }}</dt><dd class="col-sm-7">{{ number_format((float) $item->opening_balance, 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_treasury_current_balance') }}</dt><dd class="col-sm-7">{{ number_format($summary['current_balance'], 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.status') }}</dt><dd class="col-sm-7">{{ $item->is_active ? __('admin.active') : __('admin.inactive') }}</dd>
                <dt class="col-sm-5">{{ __('admin.notes') }}</dt><dd class="col-sm-7">{{ $item->notes ?: '-' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row g-3">
            <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_treasury_incoming') }}</div><div class="h4 mb-0 text-success">{{ number_format($summary['incoming'], 2) }}</div></div></div>
            <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_treasury_outgoing') }}</div><div class="h4 mb-0 text-danger">{{ number_format($summary['outgoing'], 2) }}</div></div></div>
            <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.accounting_treasury_net_movement') }}</div><div class="h4 mb-0">{{ number_format($summary['net'], 2) }}</div></div></div>
        </div>
        <div class="card admin-card p-4 mt-4">
            <form method="GET" action="{{ route('admin.accounting.treasuries.show', $item) }}" class="row g-3">
                <div class="col-md-4"><label class="form-label">{{ __('admin.from') }}</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">{{ __('admin.to') }}</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary w-100">{{ __('admin.search') }}</button>
                    <a href="{{ route('admin.accounting.treasuries.show', $item) }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card admin-card p-4">
    <h2 class="h5 mb-3">{{ __('admin.accounting_treasury_ledger') }}</h2>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.date') }}</th>
                    <th>{{ __('admin.type') }}</th>
                    <th>{{ __('admin.accounting_treasury_direction') }}</th>
                    <th>{{ __('admin.amount') }}</th>
                    <th>{{ __('admin.description') }}</th>
                    <th>{{ __('admin.created_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ optional($transaction->transaction_date)->format('Y-m-d') ?: '-' }}</td>
                        <td>{{ $transaction->localizedTransactionType() }}</td>
                        <td>
                            <span class="badge {{ $transaction->direction === 'in' ? 'text-bg-success' : 'text-bg-danger' }}">
                                {{ $transaction->localizedDirection() }}
                            </span>
                        </td>
                        <td>{{ number_format((float) $transaction->amount, 2) }}</td>
                        <td>{{ $transaction->description ?: '-' }}</td>
                        <td>{{ $transaction->creator?->name ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection
