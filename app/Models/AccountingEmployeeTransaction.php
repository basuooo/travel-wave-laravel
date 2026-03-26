<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingEmployeeTransaction extends Model
{
    use HasFactory;

    public const TYPE_SALARY = 'salary';
    public const TYPE_ADVANCE = 'advance';
    public const TYPE_COMMISSION = 'commission';
    public const TYPE_BONUS = 'bonus';
    public const TYPE_DEDUCTION = 'deduction';

    protected $fillable = [
        'user_id',
        'accounting_treasury_id',
        'created_by',
        'transaction_type',
        'amount',
        'transaction_date',
        'note',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'accounting_treasury_id' => 'integer',
        'created_by' => 'integer',
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public static function typeOptions(): array
    {
        return [
            self::TYPE_SALARY => 'salary',
            self::TYPE_ADVANCE => 'advance',
            self::TYPE_COMMISSION => 'commission',
            self::TYPE_BONUS => 'bonus',
            self::TYPE_DEDUCTION => 'deduction',
        ];
    }
}
