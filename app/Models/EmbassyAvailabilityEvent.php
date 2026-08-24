<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmbassyAvailabilityEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'embassy_appointment_id',
        'triggered_by',
        'status',
        'notes',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(EmbassyAppointment::class, 'embassy_appointment_id');
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(EmbassyAppointmentNotification::class, 'embassy_availability_event_id');
    }
}
