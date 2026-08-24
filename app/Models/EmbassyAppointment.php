<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmbassyAppointment extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE_NOW = 'available_now';
    public const STATUS_AVAILABLE_LATER = 'available_later';
    public const STATUS_NO_AVAILABILITY = 'no_availability';
    public const STATUS_UNKNOWN = 'unknown';

    protected $fillable = [
        'visa_country_id',
        'visa_record_id',
        'visa_type',
        'appointment_center',
        'appointment_type',
        'status',
        'earliest_date',
        'last_updated_at',
        'updated_by',
        'notes',
        'booking_link',
    ];

    protected $casts = [
        'earliest_date' => 'date',
        'last_updated_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }

    public function visaRecord(): BelongsTo
    {
        return $this->belongsTo(VisaRecord::class, 'visa_record_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(EmbassyAvailabilityEvent::class, 'embassy_appointment_id')->latest();
    }

    public function latestEvent(): BelongsTo
    {
        return $this->belongsTo(EmbassyAvailabilityEvent::class, 'id', 'embassy_appointment_id')->latestOfMany();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(EmbassyAppointmentNotification::class, 'embassy_appointment_id')->latest();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EmbassyAppointmentLog::class, 'embassy_appointment_id')->latest();
    }

    public function getCountryNameAttribute(): string
    {
        return $this->country ? ($this->country->name_ar ?: $this->country->name_en) : 'غير محدد';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE_NOW => 'مواعيد متاحة الآن',
            self::STATUS_AVAILABLE_LATER => 'مواعيد متاحة بتاريخ مستقبلي',
            self::STATUS_NO_AVAILABILITY => 'لا توجد مواعيد حاليًا',
            default => 'غير معروف / لم يتم التحديث',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE_NOW => 'bg-success',
            self::STATUS_AVAILABLE_LATER => 'bg-warning text-dark',
            self::STATUS_NO_AVAILABILITY => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE_NOW => '🟢',
            self::STATUS_AVAILABLE_LATER => '🟡',
            self::STATUS_NO_AVAILABILITY => '🔴',
            default => '⚪',
        };
    }

    public function getFormattedLastUpdatedAttribute(): string
    {
        if (! $this->last_updated_at) {
            return 'لم يتم التحديث بعد';
        }

        return $this->last_updated_at->format('Y-m-d — h:i A') . ' (' . $this->last_updated_at->diffForHumans() . ')';
    }
}
