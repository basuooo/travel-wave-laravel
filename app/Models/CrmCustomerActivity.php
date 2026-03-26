<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCustomerActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_customer_id',
        'user_id',
        'action_type',
        'old_value',
        'new_value',
        'note',
    ];

    protected $casts = [
        'crm_customer_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(CrmCustomer::class, 'crm_customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function localizedAction(): string
    {
        $labels = [
            'converted_from_lead' => ['ar' => 'تم التحويل من ليد', 'en' => 'Converted from lead'],
            'updated_stage' => ['ar' => 'تم تغيير المرحلة', 'en' => 'Stage changed'],
            'updated_assigned_user_id' => ['ar' => 'تم تغيير البائع', 'en' => 'Seller changed'],
            'updated_notes' => ['ar' => 'تم تحديث الملاحظات', 'en' => 'Notes updated'],
            'updated_profile' => ['ar' => 'تم تحديث بيانات العميل', 'en' => 'Customer profile updated'],
        ];

        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';

        return $labels[$this->action_type][$locale]
            ?? str($this->action_type)->replace('_', ' ')->headline()->toString();
    }
}
