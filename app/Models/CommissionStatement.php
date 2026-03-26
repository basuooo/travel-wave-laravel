<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionStatement extends Model
{
    use HasFactory;

    public const BASIS_SELLER_PROFIT_SHARE = 'seller_profit_share';

    protected $fillable = [
        'user_id',
        'basis_type',
        'period_start',
        'period_end',
        'earned_amount',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'calculation_snapshot',
        'note',
        'created_by',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'period_start' => 'date',
        'period_end' => 'date',
        'earned_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'calculation_snapshot' => 'array',
        'created_by' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function basisTypeOptions(): array
    {
        return [
            self::BASIS_SELLER_PROFIT_SHARE => __('admin.commission_basis_seller_profit_share'),
        ];
    }

    public function localizedBasisType(): string
    {
        return self::basisTypeOptions()[$this->basis_type] ?? $this->basis_type;
    }

    public function localizedPaymentStatus(): string
    {
        return match ($this->payment_status) {
            'fully_paid' => __('admin.commission_payment_status_fully_paid'),
            'partially_paid' => __('admin.commission_payment_status_partially_paid'),
            default => __('admin.commission_payment_status_unpaid'),
        };
    }
}
