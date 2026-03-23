<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmFollowUp extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'inquiry_id',
        'crm_status_id',
        'assigned_user_id',
        'created_by',
        'completed_by',
        'status',
        'scheduled_at',
        'reminder_offset_minutes',
        'remind_at',
        'reminder_sent_at',
        'completed_at',
        'cancelled_at',
        'note',
        'completion_note',
    ];

    protected $casts = [
        'inquiry_id' => 'integer',
        'crm_status_id' => 'integer',
        'assigned_user_id' => 'integer',
        'created_by' => 'integer',
        'completed_by' => 'integer',
        'scheduled_at' => 'datetime',
        'remind_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_offset_minutes' => 'integer',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function statusModel()
    {
        return $this->belongsTo(CrmStatus::class, 'crm_status_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function visualStatus(): string
    {
        if ($this->status === self::STATUS_COMPLETED) {
            return 'completed';
        }

        if ($this->status === self::STATUS_CANCELLED) {
            return 'cancelled';
        }

        if ($this->scheduled_at && $this->scheduled_at->isPast()) {
            return 'overdue';
        }

        if ($this->scheduled_at && $this->scheduled_at->diffInMinutes(now(), false) >= -60) {
            return 'due_soon';
        }

        return 'pending';
    }

    public function reminderLabel(): string
    {
        $minutes = (int) $this->reminder_offset_minutes;

        return match (true) {
            $minutes === 15 => '15 minutes',
            $minutes === 30 => '30 minutes',
            $minutes === 60 => '1 hour',
            $minutes === 1440 => '1 day',
            default => $minutes . ' minutes',
        };
    }
}
