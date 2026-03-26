@extends('layouts.admin')

@section('page_title', $account->customer_name)
@section('page_description', __('admin.accounting_customer_account_details'))

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.accounting_customer_account_details') }}</h2>
            <dl class="row mb-0">
                <dt class="col-sm-5">{{ __('admin.full_name') }}</dt><dd class="col-sm-7">{{ $account->customer_name }}</dd>
                <dt class="col-sm-5">{{ __('admin.phone') }}</dt><dd class="col-sm-7">{{ $account->phone ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.whatsapp_number') }}</dt><dd class="col-sm-7">{{ $account->whatsapp_number ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.crm_salesman') }}</dt><dd class="col-sm-7">{{ $account->assignedUser?->name ?: '-' }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_total_amount') }}</dt><dd class="col-sm-7">{{ number_format($account->total_amount, 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_paid_amount') }}</dt><dd class="col-sm-7">{{ number_format($account->paid_amount, 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_remaining_amount') }}</dt><dd class="col-sm-7">{{ number_format($account->remaining_amount, 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_total_customer_expenses') }}</dt><dd class="col-sm-7">{{ number_format($account->total_customer_expenses, 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_company_profit_before_seller') }}</dt><dd class="col-sm-7">{{ number_format($account->company_profit_before_seller, 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_seller_profit') }}</dt><dd class="col-sm-7">{{ number_format($account->seller_profit, 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_final_company_profit') }}</dt><dd class="col-sm-7">{{ number_format($account->final_company_profit, 2) }}</dd>
                <dt class="col-sm-5">{{ __('admin.accounting_payment_status') }}</dt><dd class="col-sm-7"><span class="badge {{ $account->payment_status === 'fully_paid' ? 'text-bg-success' : ($account->payment_status === 'partially_paid' ? 'text-bg-warning' : 'text-bg-danger') }}">{{ $account->inquiry?->localizedPaymentStatus() ?: __('admin.accounting_unpaid') }}</span></dd>
            </dl>
            <div class="mt-3">
                <a href="{{ route('admin.crm.leads.show', $account->inquiry_id) }}" class="btn btn-outline-secondary btn-sm">{{ __('admin.crm_popup_open_lead') }}</a>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.accounting_add_payment') }}</h2>
            <form method="POST" action="{{ route('admin.accounting.customers.payments.store', $account) }}" class="row g-3">
                @csrf
                <div class="col-md-3"><label class="form-label">{{ __('admin.amount') }}</label><input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">{{ __('admin.date') }}</label><input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">{{ __('admin.accounting_received_in_treasury') }}</label><select name="accounting_treasury_id" class="form-select" required><option value="">{{ __('admin.accounting_choose_treasury') }}</option>@foreach($treasuries as $treasury)<option value="{{ $treasury->id }}" @selected((int) old('accounting_treasury_id') === (int) $treasury->id)>{{ $treasury->name }}</option>@endforeach</select></div>
                <div class="col-md-3"><label class="form-label">{{ __('admin.notes') }}</label><input name="note" value="{{ old('note') }}" class="form-control"></div>
                <div class="col-12"><button class="btn btn-primary">{{ __('admin.save') }}</button></div>
            </form>
        </div>

        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.accounting_customer_expenses') }}</h2>
            <form method="POST" action="{{ route('admin.accounting.customers.expenses.store', $account) }}" class="row g-3 mb-4">
                @csrf
                <div class="col-md-4"><label class="form-label">{{ __('admin.category') }}</label><select class="form-select" name="accounting_expense_category_id" data-accounting-category-select required><option value="">-</option>@foreach($expenseCategories as $category)<option value="{{ $category->id }}" @selected((int) old('accounting_expense_category_id') === (int) $category->id)>{{ $category->localizedName() }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">{{ __('admin.subcategory') }}</label><select class="form-select" name="accounting_expense_subcategory_id" data-accounting-subcategory-select><option value="">-</option>@foreach($expenseCategories as $category)@foreach($category->subcategories as $subcategory)<option value="{{ $subcategory->id }}" data-parent="{{ $category->id }}" @selected((int) old('accounting_expense_subcategory_id') === (int) $subcategory->id)>{{ $subcategory->localizedName() }}</option>@endforeach @endforeach</select></div>
                <div class="col-md-4"><label class="form-label">{{ __('admin.accounting_paid_from_treasury') }}</label><select name="accounting_treasury_id" class="form-select" required><option value="">{{ __('admin.accounting_choose_treasury') }}</option>@foreach($treasuries as $treasury)<option value="{{ $treasury->id }}" @selected((int) old('accounting_treasury_id') === (int) $treasury->id)>{{ $treasury->name }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">{{ __('admin.amount') }}</label><input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">{{ __('admin.date') }}</label><input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">{{ __('admin.notes') }}</label><input name="note" value="{{ old('note') }}" class="form-control"></div>
                <div class="col-12"><button class="btn btn-primary">{{ __('admin.save') }}</button></div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.category') }}</th><th>{{ __('admin.subcategory') }}</th><th>{{ __('admin.accounting_treasury') }}</th><th>{{ __('admin.amount') }}</th><th>{{ __('admin.date') }}</th><th></th></tr></thead>
                    <tbody>
                        @forelse($account->expenses as $expense)
                            <tr>
                                <td>{{ $expense->category?->localizedName() ?: '-' }}</td>
                                <td>{{ $expense->subcategory?->localizedName() ?: '-' }}</td>
                                <td>{{ $expense->treasury?->name ?: '-' }}</td>
                                <td>{{ number_format($expense->amount, 2) }}</td>
                                <td>{{ optional($expense->expense_date)->format('Y-m-d') ?: '-' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.accounting.customers.expenses.destroy', $expense) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.accounting_payment_history') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.accounting_treasury') }}</th><th>{{ __('admin.amount') }}</th><th>{{ __('admin.date') }}</th><th>{{ __('admin.created_by') }}</th><th>{{ __('admin.notes') }}</th></tr></thead>
                    <tbody>
                        @forelse($account->payments as $payment)
                            <tr>
                                <td>{{ $payment->treasury?->name ?: '-' }}</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ optional($payment->payment_date)->format('Y-m-d') ?: '-' }}</td>
                                <td>{{ $payment->creator?->name ?: '-' }}</td>
                                <td>{{ $payment->note ?: '-' }}</td>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.querySelector('[data-accounting-category-select]');
    const subcategorySelect = document.querySelector('[data-accounting-subcategory-select]');
    if (!categorySelect || !subcategorySelect) return;
    const toggleOptions = () => {
        const value = categorySelect.value;
        Array.from(subcategorySelect.options).forEach((option) => {
            if (!option.value) {
                option.hidden = false;
                return;
            }
            option.hidden = option.dataset.parent !== value;
        });
        if (subcategorySelect.selectedOptions[0]?.hidden) {
            subcategorySelect.value = '';
        }
    };
    categorySelect.addEventListener('change', toggleOptions);
    toggleOptions();
});
</script>
@endsection
