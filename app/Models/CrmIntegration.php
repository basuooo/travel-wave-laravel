<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class CrmIntegration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_integrations';

    protected $fillable = [
        'name',
        'platform',
        'is_active',
        'credentials',
        'webhook_token',
        'webhook_verify_token',
        'last_sync_at',
        'connection_status',
        'error_log',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * Encrypt credentials before saving.
     */
    public function setCredentialsAttribute($value)
    {
        $this->attributes['credentials'] = Crypt::encrypt($value);
    }

    /**
     * Decrypt credentials when retrieving.
     */
    public function getCredentialsAttribute($value)
    {
        try {
            return Crypt::decrypt($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function webhookLogs()
    {
        return $this->hasMany(CrmWebhookLog::class, 'integration_id');
    }

    public function apiLogs()
    {
        return $this->hasMany(CrmApiLog::class, 'integration_id');
    }

    public function leads()
    {
        return $this->hasMany(CrmLead::class, 'integration_id');
    }
}
