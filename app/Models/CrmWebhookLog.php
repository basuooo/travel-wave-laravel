<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmWebhookLog extends Model
{
    use HasFactory;

    protected $table = 'crm_webhook_logs';

    protected $fillable = [
        'integration_id',
        'platform',
        'payload',
        'status',
        'error_message',
        'request_ip',
        'headers',
    ];

    protected $casts = [
        'payload' => 'array',
        'headers' => 'array',
    ];

    public function integration()
    {
        return $this->belongsTo(CrmIntegration::class, 'integration_id');
    }

    public function failure()
    {
        return $this->hasOne(CrmFailedWebhook::class, 'webhook_log_id');
    }
}
