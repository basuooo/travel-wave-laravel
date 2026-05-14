<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'wa_id',
        'contact_name',
        'locale',
        'status',
        'ai_active',
        'last_message_at',
        'assigned_user_id',
        'metadata',
    ];

    protected $casts = [
        'ai_active'       => 'boolean',
        'last_message_at' => 'datetime',
        'metadata'        => 'array',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function latestMessage(): HasMany
    {
        return $this->messages()->latest()->limit(1);
    }

    public function isAiActive(): bool
    {
        return $this->ai_active && $this->status === 'active';
    }

    public function enableAi(): void
    {
        $this->update(['ai_active' => true, 'status' => 'active']);
    }

    public function disableAi(): void
    {
        $this->update(['ai_active' => false, 'status' => 'human_handover']);
    }
}
