<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmInformation extends Model
{
    use HasFactory;

    public const AUDIENCE_ALL = 'all';
    public const AUDIENCE_ADMINS = 'admins';
    public const AUDIENCE_SELLERS = 'sellers';
    public const AUDIENCE_SELECTED_USERS = 'selected_users';

    protected $table = 'crm_information';

    protected $fillable = [
        'title',
        'content',
        'category',
        'priority',
        'audience_type',
        'event_date',
        'expires_at',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'date',
        'expires_at' => 'datetime',
        'created_by' => 'integer',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients()
    {
        return $this->hasMany(CrmInformationRecipient::class, 'crm_information_id');
    }

    public function localizedAudience(): string
    {
        return match ($this->audience_type) {
            self::AUDIENCE_ALL => 'الجميع',
            self::AUDIENCE_ADMINS => 'الإدمن فقط',
            self::AUDIENCE_SELLERS => 'البائعين فقط',
            self::AUDIENCE_SELECTED_USERS => 'مستخدمون محددون',
            default => $this->audience_type,
        };
    }

    public function localizedPriority(): ?string
    {
        return match ($this->priority) {
            'normal' => 'عادي',
            'important' => 'مهم',
            'urgent' => 'عاجل',
            default => $this->priority,
        };
    }

    public function localizedCategory(): ?string
    {
        return match ($this->category) {
            'embassy_decision' => 'قرار سفارة',
            'price_update' => 'تعديل أسعار',
            'holiday' => 'إجازات',
            'internal_process' => 'إجراء داخلي',
            'general_notice' => 'تنبيه عام',
            default => $this->category,
        };
    }
}
