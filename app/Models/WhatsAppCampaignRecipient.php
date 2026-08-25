<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppCampaignRecipient extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_campaign_recipients';

    protected $fillable = [
        'campaign_id',
        'whatsapp_account_id',
        'phone',
        'normalized_phone',
        'contact_name',
        'contact_status',
        'is_selected',
        'whatsapp_contact_id',
        'lead_id',
        'customer_id',
        'custom_variables',
        'status',
        'sent_at',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'custom_variables' => 'array',
        'is_selected'      => 'boolean',
        'sent_at'          => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(WhatsAppCampaign::class, 'campaign_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhatsAppAccount::class, 'whatsapp_account_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(WhatsAppContact::class, 'whatsapp_contact_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CrmCustomer::class, 'customer_id');
    }
}
