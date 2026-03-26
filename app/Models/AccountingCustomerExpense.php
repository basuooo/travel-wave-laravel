<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingCustomerExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_customer_account_id',
        'accounting_expense_category_id',
        'accounting_expense_subcategory_id',
        'accounting_treasury_id',
        'created_by',
        'amount',
        'expense_date',
        'note',
    ];

    protected $casts = [
        'accounting_customer_account_id' => 'integer',
        'accounting_expense_category_id' => 'integer',
        'accounting_expense_subcategory_id' => 'integer',
        'accounting_treasury_id' => 'integer',
        'created_by' => 'integer',
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(AccountingCustomerAccount::class, 'accounting_customer_account_id');
    }

    public function category()
    {
        return $this->belongsTo(AccountingExpenseCategory::class, 'accounting_expense_category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(AccountingExpenseSubcategory::class, 'accounting_expense_subcategory_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function treasury()
    {
        return $this->belongsTo(AccountingTreasury::class, 'accounting_treasury_id');
    }

    public function treasuryTransactions()
    {
        return $this->morphMany(AccountingTreasuryTransaction::class, 'related');
    }
}
