<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppAccount extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_accounts';

    protected $fillable = [
        'name',
        'phone_number',
        'status',
        'last_connected_at',
        'usage_type',
        'assigned_user_id',
        'department_branch',
        'sent_count',
        'failed_count',
        'conversations_count',
        'connection_settings',
        'is_active',
    ];

    protected $casts = [
        'connection_settings' => 'array',
        'last_connected_at'   => 'datetime',
        'is_active'           => 'boolean',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(WhatsAppConversation::class, 'whatsapp_account_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(WhatsAppCampaign::class, 'whatsapp_account_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(WhatsAppContact::class, 'whatsapp_account_id');
    }
}
