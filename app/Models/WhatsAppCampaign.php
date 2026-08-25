<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppCampaign extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_campaigns';

    protected $fillable = [
        'name',
        'type',
        'whatsapp_account_id',
        'created_by_user_id',
        'status',
        'audience_source',
        'audience_filters',
        'message_content',
        'media_type',
        'media_url',
        'template_id',
        'schedule_type',
        'scheduled_at',
        'sending_window_start',
        'sending_window_end',
        'allowed_days',
        'interval_type',
        'interval_min_sec',
        'interval_max_sec',
        'daily_limit',
        'hourly_limit',
        'campaign_limit',
        'total_contacts',
        'previously_contacted_count',
        'not_previously_contacted_count',
        'sent_count',
        'failed_count',
        'pending_count',
        'reply_count',
        'opt_out_count',
        'require_approval',
        'approved_at',
        'approved_by_user_id',
        'started_at',
        'completed_at',
        'paused_at',
    ];

    protected $casts = [
        'audience_filters' => 'array',
        'allowed_days'     => 'array',
        'scheduled_at'     => 'datetime',
        'approved_at'      => 'datetime',
        'started_at'       => 'datetime',
        'completed_at'     => 'datetime',
        'paused_at'        => 'datetime',
        'require_approval' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhatsAppAccount::class, 'whatsapp_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'template_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(WhatsAppCampaignRecipient::class, 'campaign_id');
    }
}
