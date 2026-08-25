<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppSequenceSubscriber extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_sequence_subscribers';

    protected $fillable = [
        'sequence_id',
        'whatsapp_contact_id',
        'current_step',
        'status',
        'next_execution_at',
    ];

    protected $casts = [
        'next_execution_at' => 'datetime',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(WhatsAppSequence::class, 'sequence_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(WhatsAppContact::class, 'whatsapp_contact_id');
    }
}
