<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmApiLog extends Model
{
    use HasFactory;

    protected $table = 'crm_api_logs';

    protected $fillable = [
        'integration_id',
        'endpoint',
        'method',
        'request_payload',
        'response_payload',
        'status_code',
        'duration_ms',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function integration()
    {
        return $this->belongsTo(CrmIntegration::class, 'integration_id');
    }
}
