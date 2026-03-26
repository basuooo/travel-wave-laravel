@extends('layouts.admin')

@section('page_title', __('admin.accounting_general_expenses'))
@section('page_description', __('admin.accounting_general_expenses_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">{{ __('admin.accounting_add_general_expense') }}</h2>
    <form method="POST" action="{{ route('admin.accounting.general-expenses.store') }}" class="row g-3">
        @csrf
        <div class="col-md-3"><label class="form-label">{{ __('admin.category') }}</label><select name="accounting_general_expense_category_id" class="form-select" required><option value="">-</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((int) old('accounting_general_expense_category_id') === (int) $category->id)>{{ $category->localizedName() }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.accounting_paid_from_treasury') }}</label><select name="accounting_treasury_id" class="form-select" required><option value="">{{ __('admin.accounting_choose_treasury') }}</option>@foreach($treasuries as $treasury)<option value="{{ $treasury->id }}" @selected((int) old('accounting_treasury_id') === (int) $treasury->id)>{{ $treasury->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.amount') }}</label><input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="form-control" required></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.date') }}</label><input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" class="form-control" required></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.notes') }}</label><input name="note" value="{{ old('note') }}" class="form-control"></div>
        <div class="col-12"><button class="btn btn-primary">{{ __('admin.save') }}</button></div>
    </form>
</div>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>{{ __('admin.category') }}</th><th>{{ __('admin.accounting_treasury') }}</th><th>{{ __('admin.amount') }}</th><th>{{ __('admin.date') }}</th><th>{{ __('admin.notes') }}</th><th>{{ __('admin.actions') }}</th></tr></thead>
            <tbody>
                @forelse($items as $expense)
                    <tr>
                        <td>{{ $expense->category?->localizedName() ?: '-' }}</td>
                        <td>{{ $expense->treasury?->name ?: '-' }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ optional($expense->expense_date)->format('Y-m-d') ?: '-' }}</td>
                        <td>{{ $expense->note ?: '-' }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.accounting.general-expenses.destroy', $expense) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
@endsection
