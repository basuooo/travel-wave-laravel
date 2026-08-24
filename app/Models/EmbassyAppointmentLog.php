<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbassyAppointmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'embassy_appointment_id',
        'user_id',
        'user_name',
        'action',
        'old_status',
        'new_status',
        'old_earliest_date',
        'new_earliest_date',
        'notes',
    ];

    protected $casts = [];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(EmbassyAppointment::class, 'embassy_appointment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
