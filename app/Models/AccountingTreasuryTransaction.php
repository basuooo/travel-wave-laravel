<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingTreasuryTransaction extends Model
{
    use HasFactory;

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';
    public const TYPE_CUSTOMER_PAYMENT = 'customer_payment';
    public const TYPE_CUSTOMER_EXPENSE = 'customer_expense';
    public const TYPE_GENERAL_EXPENSE = 'general_expense';
    public const TYPE_EMPLOYEE_PAYOUT = 'employee_payout';

    protected $fillable = [
        'accounting_treasury_id',
        'direction',
        'transaction_type',
        'amount',
        'transaction_date',
        'description',
        'created_by',
    ];

    protected $casts = [
        'accounting_treasury_id' => 'integer',
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'created_by' => 'integer',
    ];

    public function treasury()
    {
        return $this->belongsTo(AccountingTreasury::class, 'accounting_treasury_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function localizedDirection(): string
    {
        return __('admin.accounting_treasury_direction_' . $this->direction);
    }

    public function localizedTransactionType(): string
    {
        return __('admin.accounting_treasury_transaction_' . $this->transaction_type);
    }
}
