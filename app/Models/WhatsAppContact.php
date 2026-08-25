<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppContact extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_contacts';

    protected $fillable = [
        'name',
        'phone',
        'normalized_phone',
        'whatsapp_account_id',
        'assigned_user_id',
        'lead_id',
        'customer_id',
        'status_in_crm',
        'service',
        'country',
        'lead_source',
        'opt_out_status',
        'tags',
        'notes',
        'first_contact_at',
        'last_contact_at',
        'conversation_count',
    ];

    protected $casts = [
        'tags'             => 'array',
        'first_contact_at' => 'datetime',
        'last_contact_at'  => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhatsAppAccount::class, 'whatsapp_account_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CrmCustomer::class, 'customer_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(WhatsAppConversation::class, 'whatsapp_contact_id');
    }
}
