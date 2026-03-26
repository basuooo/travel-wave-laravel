<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingTreasury extends Model
{
    use HasFactory;

    public const TYPE_CASH = 'cash';
    public const TYPE_INSTAPAY = 'instapay';
    public const TYPE_VODAFONE_CASH = 'vodafone_cash';
    public const TYPE_BANK = 'bank';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'name',
        'type',
        'identifier',
        'opening_balance',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'created_by' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(AccountingTreasuryTransaction::class, 'accounting_treasury_id')->latest('transaction_date');
    }

    public function incomingTransactions()
    {
        return $this->transactions()->where('direction', AccountingTreasuryTransaction::DIRECTION_IN);
    }

    public function outgoingTransactions()
    {
        return $this->transactions()->where('direction', AccountingTreasuryTransaction::DIRECTION_OUT);
    }

    public function currentBalance(): float
    {
        $incoming = (float) ($this->incoming_total ?? $this->incomingTransactions()->sum('amount'));
        $outgoing = (float) ($this->outgoing_total ?? $this->outgoingTransactions()->sum('amount'));

        return round((float) $this->opening_balance + $incoming - $outgoing, 2);
    }

    public function localizedType(): string
    {
        return __('admin.accounting_treasury_type_' . $this->type);
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_CASH => __('admin.accounting_treasury_type_cash'),
            self::TYPE_INSTAPAY => __('admin.accounting_treasury_type_instapay'),
            self::TYPE_VODAFONE_CASH => __('admin.accounting_treasury_type_vodafone_cash'),
            self::TYPE_BANK => __('admin.accounting_treasury_type_bank'),
            self::TYPE_OTHER => __('admin.accounting_treasury_type_other'),
        ];
    }
}
