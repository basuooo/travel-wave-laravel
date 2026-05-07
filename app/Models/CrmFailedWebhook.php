<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmFailedWebhook extends Model
{
    use HasFactory;

    protected $table = 'crm_failed_webhooks';

    protected $fillable = [
        'webhook_log_id',
        'retry_count',
        'last_retry_at',
        'error',
    ];

    protected $casts = [
        'last_retry_at' => 'datetime',
    ];

    public function webhookLog()
    {
        return $this->belongsTo(CrmWebhookLog::class, 'webhook_log_id');
    }
}
