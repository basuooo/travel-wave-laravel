<?php

namespace App\Models;

use App\Support\CrmLeadAccess;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCustomer extends Model
{
    use HasFactory;

    public const STAGE_NEW = 'new_customer';
    public const STAGE_WAITING_DOCUMENTS = 'waiting_documents';
    public const STAGE_UNDER_PROCESSING = 'under_processing';
    public const STAGE_APPOINTMENT_SCHEDULED = 'appointment_scheduled';
    public const STAGE_SUBMITTED = 'submitted';
    public const STAGE_ISSUED_COMPLETED = 'issued_completed';
    public const STAGE_CLOSED = 'closed';
    public const STAGE_CANCELLED = 'cancelled';

    protected $fillable = [
        'inquiry_id',
        'customer_code',
        'full_name',
        'phone',
        'whatsapp_number',
        'email',
        'nationality',
        'country',
        'destination',
        'crm_source_id',
        'crm_service_type_id',
        'crm_service_subtype_id',
        'assigned_user_id',
        'created_by',
        'stage',
        'is_active',
        'converted_at',
        'appointment_at',
        'submission_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'inquiry_id' => 'integer',
        'crm_source_id' => 'integer',
        'crm_service_type_id' => 'integer',
        'crm_service_subtype_id' => 'integer',
        'assigned_user_id' => 'integer',
        'created_by' => 'integer',
        'is_active' => 'boolean',
        'converted_at' => 'datetime',
        'appointment_at' => 'datetime',
        'submission_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function crmSource()
    {
        return $this->belongsTo(CrmLeadSource::class, 'crm_source_id');
    }

    public function crmServiceType()
    {
        return $this->belongsTo(CrmServiceType::class, 'crm_service_type_id');
    }

    public function crmServiceSubtype()
    {
        return $this->belongsTo(CrmServiceSubtype::class, 'crm_service_subtype_id');
    }

    public function activities()
    {
        return $this->hasMany(CrmCustomerActivity::class)->latest();
    }

    public function accountingAccount()
    {
        return $this->hasOne(AccountingCustomerAccount::class, 'crm_customer_id');
    }

    public function documents()
    {
        return $this->morphMany(CrmDocument::class, 'documentable')->latest('uploaded_at');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if (CrmLeadAccess::canViewAll($user)) {
            return $query;
        }

        return $query->where('assigned_user_id', $user->id);
    }

    public static function stageOptions(): array
    {
        return [
            self::STAGE_NEW => ['ar' => 'عميل جديد', 'en' => 'New Customer'],
            self::STAGE_WAITING_DOCUMENTS => ['ar' => 'بانتظار مستندات', 'en' => 'Waiting for Documents'],
            self::STAGE_UNDER_PROCESSING => ['ar' => 'قيد التنفيذ', 'en' => 'Under Processing'],
            self::STAGE_APPOINTMENT_SCHEDULED => ['ar' => 'تم تحديد موعد', 'en' => 'Appointment Scheduled'],
            self::STAGE_SUBMITTED => ['ar' => 'تم التقديم', 'en' => 'Submitted'],
            self::STAGE_ISSUED_COMPLETED => ['ar' => 'تم الإصدار', 'en' => 'Issued / Completed'],
            self::STAGE_CLOSED => ['ar' => 'مغلق', 'en' => 'Closed'],
            self::STAGE_CANCELLED => ['ar' => 'ملغي', 'en' => 'Cancelled'],
        ];
    }

    public function localizedStage(): string
    {
        $options = static::stageOptions();

        return $options[$this->stage][app()->getLocale() === 'ar' ? 'ar' : 'en']
            ?? ucfirst(str_replace('_', ' ', (string) $this->stage));
    }

    public function stageBadgeClass(): string
    {
        return match ($this->stage) {
            self::STAGE_ISSUED_COMPLETED => 'success',
            self::STAGE_WAITING_DOCUMENTS, self::STAGE_APPOINTMENT_SCHEDULED => 'warning',
            self::STAGE_CLOSED, self::STAGE_CANCELLED => 'secondary',
            default => 'primary',
        };
    }
}
