<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppSequence extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_sequences';

    protected $fillable = [
        'name',
        'description',
        'whatsapp_account_id',
        'trigger_event',
        'smart_stop_on_reply',
        'smart_stop_on_convert',
        'is_active',
    ];

    protected $casts = [
        'smart_stop_on_reply'   => 'boolean',
        'smart_stop_on_convert' => 'boolean',
        'is_active'             => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhatsAppAccount::class, 'whatsapp_account_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WhatsAppSequenceStep::class, 'sequence_id')->orderBy('step_number');
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(WhatsAppSequenceSubscriber::class, 'sequence_id');
    }
}
