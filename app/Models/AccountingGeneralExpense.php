<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingGeneralExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_general_expense_category_id',
        'accounting_treasury_id',
        'created_by',
        'amount',
        'expense_date',
        'attachment_path',
        'note',
    ];

    protected $casts = [
        'accounting_general_expense_category_id' => 'integer',
        'accounting_treasury_id' => 'integer',
        'created_by' => 'integer',
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(AccountingGeneralExpenseCategory::class, 'accounting_general_expense_category_id');
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
