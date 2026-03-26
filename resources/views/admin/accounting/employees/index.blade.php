@extends('layouts.admin')

@section('page_title', __('admin.accounting_employees'))
@section('page_description', __('admin.accounting_employees_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">{{ __('admin.accounting_add_employee_transaction') }}</h2>
    <form method="POST" action="{{ route('admin.accounting.employees.transactions.store') }}" class="row g-3">
        @csrf
        <div class="col-md-3"><label class="form-label">{{ __('admin.employee') }}</label><select name="user_id" class="form-select" required><option value="">-</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected((int) old('user_id') === (int) $employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.type') }}</label><select name="transaction_type" class="form-select" required>@foreach($typeOptions as $value => $label)<option value="{{ $value }}" @selected(old('transaction_type') === $value)>{{ __('admin.accounting_type_' . $label) }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.accounting_paid_from_treasury') }}</label><select name="accounting_treasury_id" class="form-select"><option value="">{{ __('admin.accounting_choose_treasury_optional') }}</option>@foreach($treasuries as $treasury)<option value="{{ $treasury->id }}" @selected((int) old('accounting_treasury_id') === (int) $treasury->id)>{{ $treasury->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.amount') }}</label><input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="form-control" required></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.date') }}</label><input type="date" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}" class="form-control" required></div>
        <div class="col-12"><label class="form-label">{{ __('admin.notes') }}</label><input name="note" value="{{ old('note') }}" class="form-control"></div>
        <div class="col-12"><div class="form-text">{{ __('admin.accounting_treasury_employee_hint') }}</div></div>
        <div class="col-12"><button class="btn btn-primary">{{ __('admin.save') }}</button></div>
    </form>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_employee_statement') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.employee') }}</th><th>{{ __('admin.accounting_net_paid') }}</th></tr></thead>
                    <tbody>
                    @forelse($employeeSummary as $row)
                        <tr>
                            <td>{{ $row->user?->name ?: '-' }}</td>
                            <td>{{ number_format(($row->salary_total + $row->advance_total + $row->commission_total + $row->bonus_total - $row->deduction_total), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.accounting_employee_transactions') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.employee') }}</th><th>{{ __('admin.type') }}</th><th>{{ __('admin.accounting_treasury') }}</th><th>{{ __('admin.amount') }}</th><th>{{ __('admin.date') }}</th><th>{{ __('admin.actions') }}</th></tr></thead>
                    <tbody>
                        @forelse($items as $transaction)
                            <tr>
                                <td>{{ $transaction->user?->name ?: '-' }}</td>
                                <td>{{ __('admin.accounting_type_' . ($typeOptions[$transaction->transaction_type] ?? $transaction->transaction_type)) }}</td>
                                <td>{{ $transaction->treasury?->name ?: '-' }}</td>
                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ optional($transaction->transaction_date)->format('Y-m-d') ?: '-' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.accounting.employees.transactions.destroy', $transaction) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div>
</div>
@endsection
