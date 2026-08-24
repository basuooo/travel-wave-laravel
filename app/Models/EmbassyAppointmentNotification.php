<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbassyAppointmentNotification extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_NOTIFIED = 'notified';
    public const STATUS_SNOOZED = 'snoozed';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_DISMISSED = 'dismissed';

    protected $fillable = [
        'embassy_availability_event_id',
        'embassy_appointment_id',
        'inquiry_id',
        'seller_id',
        'status',
        'snoozed_until',
        'contacted_at',
        'contact_result',
        'contact_notes',
    ];

    protected $casts = [
        'snoozed_until' => 'datetime',
        'contacted_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(EmbassyAvailabilityEvent::class, 'embassy_availability_event_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(EmbassyAppointment::class, 'embassy_appointment_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getContactResultLabelAttribute(): ?string
    {
        return match ($this->contact_result) {
            'agreed' => 'العميل وافق',
            'no_answer' => 'العميل لم يرد',
            'call_later' => 'العميل طلب الاتصال لاحقًا',
            'not_ready' => 'العميل غير جاهز',
            'refused' => 'العميل رفض',
            'other' => 'أخرى',
            default => $this->contact_result,
        };
    }
}
