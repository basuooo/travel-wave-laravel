<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingCustomerAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'crm_customer_id',
        'assigned_user_id',
        'created_by',
        'customer_name',
        'phone',
        'whatsapp_number',
        'email',
        'service_label',
        'service_destination',
        'lead_source',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'total_customer_expenses',
        'company_profit_before_seller',
        'seller_profit',
        'final_company_profit',
        'payment_status',
        'notes',
        'last_payment_at',
    ];

    protected $casts = [
        'inquiry_id' => 'integer',
        'crm_customer_id' => 'integer',
        'assigned_user_id' => 'integer',
        'created_by' => 'integer',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'total_customer_expenses' => 'decimal:2',
        'company_profit_before_seller' => 'decimal:2',
        'seller_profit' => 'decimal:2',
        'final_company_profit' => 'decimal:2',
        'last_payment_at' => 'datetime',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function customer()
    {
        return $this->belongsTo(CrmCustomer::class, 'crm_customer_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(AccountingCustomerPayment::class)->latest('payment_date');
    }

    public function expenses()
    {
        return $this->hasMany(AccountingCustomerExpense::class)->latest('expense_date');
    }

    public function documents()
    {
        return $this->morphMany(CrmDocument::class, 'documentable')->latest('uploaded_at');
    }

    public function getRemainingAmountAttribute($value): float
    {
        $totalAmount = round((float) ($this->attributes['total_amount'] ?? 0), 2);
        $paidAmount = round((float) ($this->attributes['paid_amount'] ?? 0), 2);

        return max(0, round($totalAmount - $paidAmount, 2));
    }

    public function getCompanyProfitBeforeSellerAttribute($value): float
    {
        $paidAmount = round((float) ($this->attributes['paid_amount'] ?? 0), 2);
        $expensesTotal = round((float) ($this->attributes['total_customer_expenses'] ?? 0), 2);

        return round($paidAmount - $expensesTotal, 2);
    }

    public function getSellerProfitAttribute($value): float
    {
        return round($this->getCompanyProfitBeforeSellerAttribute(null) * 0.10, 2);
    }

    public function getFinalCompanyProfitAttribute($value): float
    {
        $companyProfitBeforeSeller = $this->getCompanyProfitBeforeSellerAttribute(null);
        $sellerProfit = $this->getSellerProfitAttribute(null);

        return round($companyProfitBeforeSeller - $sellerProfit, 2);
    }
}
