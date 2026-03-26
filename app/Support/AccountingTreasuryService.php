<?php

namespace App\Support;

use App\Models\AccountingCustomerExpense;
use App\Models\AccountingCustomerPayment;
use App\Models\AccountingEmployeeTransaction;
use App\Models\AccountingGeneralExpense;
use App\Models\AccountingTreasury;
use App\Models\AccountingTreasuryTransaction;
use Illuminate\Database\Eloquent\Model;

class AccountingTreasuryService
{
    public function syncForCustomerPayment(AccountingCustomerPayment $payment): void
    {
        $payment->loadMissing(['account', 'account.inquiry', 'treasury']);

        $this->replaceTransaction(
            $payment,
            $payment->accounting_treasury_id,
            AccountingTreasuryTransaction::DIRECTION_IN,
            AccountingTreasuryTransaction::TYPE_CUSTOMER_PAYMENT,
            (float) $payment->amount,
            optional($payment->payment_date)->toDateString(),
            $payment->account?->customer_name ?: $payment->account?->inquiry?->full_name ?: __('admin.accounting_payment_added')
        );
    }

    public function syncForCustomerExpense(AccountingCustomerExpense $expense): void
    {
        $expense->loadMissing(['account', 'category']);

        $this->replaceTransaction(
            $expense,
            $expense->accounting_treasury_id,
            AccountingTreasuryTransaction::DIRECTION_OUT,
            AccountingTreasuryTransaction::TYPE_CUSTOMER_EXPENSE,
            (float) $expense->amount,
            optional($expense->expense_date)->toDateString(),
            $expense->account?->customer_name ?: $expense->category?->localizedName() ?: __('admin.accounting_customer_expenses')
        );
    }

    public function syncForGeneralExpense(AccountingGeneralExpense $expense): void
    {
        $expense->loadMissing('category');

        $this->replaceTransaction(
            $expense,
            $expense->accounting_treasury_id,
            AccountingTreasuryTransaction::DIRECTION_OUT,
            AccountingTreasuryTransaction::TYPE_GENERAL_EXPENSE,
            (float) $expense->amount,
            optional($expense->expense_date)->toDateString(),
            $expense->category?->localizedName() ?: __('admin.accounting_general_expenses')
        );
    }

    public function syncForEmployeeTransaction(AccountingEmployeeTransaction $transaction): void
    {
        $transaction->loadMissing('user');

        if (! $this->transactionAffectsTreasury($transaction)) {
            $this->deleteForRelated($transaction);

            return;
        }

        $this->replaceTransaction(
            $transaction,
            $transaction->accounting_treasury_id,
            AccountingTreasuryTransaction::DIRECTION_OUT,
            AccountingTreasuryTransaction::TYPE_EMPLOYEE_PAYOUT,
            (float) $transaction->amount,
            optional($transaction->transaction_date)->toDateString(),
            $transaction->user?->name ?: __('admin.accounting_employee_transactions')
        );
    }

    public function deleteForRelated(Model $related): void
    {
        $related->morphMany(AccountingTreasuryTransaction::class, 'related')->delete();
    }

    public function transactionAffectsTreasury(AccountingEmployeeTransaction $transaction): bool
    {
        return in_array($transaction->transaction_type, [
            AccountingEmployeeTransaction::TYPE_SALARY,
            AccountingEmployeeTransaction::TYPE_ADVANCE,
            AccountingEmployeeTransaction::TYPE_COMMISSION,
            AccountingEmployeeTransaction::TYPE_BONUS,
        ], true);
    }

    protected function replaceTransaction(
        Model $related,
        ?int $treasuryId,
        string $direction,
        string $transactionType,
        float $amount,
        ?string $transactionDate,
        ?string $description
    ): void {
        $this->deleteForRelated($related);

        if (! $treasuryId || $amount <= 0 || ! $transactionDate) {
            return;
        }

        $treasury = AccountingTreasury::query()->find($treasuryId);
        if (! $treasury) {
            return;
        }

        $related->morphMany(AccountingTreasuryTransaction::class, 'related')->create([
            'accounting_treasury_id' => $treasury->id,
            'direction' => $direction,
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'transaction_date' => $transactionDate,
            'description' => $description,
            'created_by' => $related->created_by ?? auth()->id(),
        ]);
    }
}
