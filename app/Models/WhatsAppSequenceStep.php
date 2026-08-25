<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppSequenceStep extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_sequence_steps';

    protected $fillable = [
        'sequence_id',
        'step_number',
        'delay_days',
        'template_id',
        'message_content',
        'media_type',
        'media_url',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(WhatsAppSequence::class, 'sequence_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'template_id');
    }
}
