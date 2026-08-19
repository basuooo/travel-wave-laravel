<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'funnel_id',
        'funnel_result_id',
        'crm_inquiry_id',
        'session_id',
        'visitor_ip',
        'user_agent',
        'score',
        'is_completed',
        'completed_at',
        'crm_sync_status',
        'last_sync_attempt',
        'sync_error',
        'utm_data',
    ];

    protected $casts = [
        'score' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'last_sync_attempt' => 'datetime',
        'utm_data' => 'array',
    ];

    public function funnel()
    {
        return $this->belongsTo(Funnel::class, 'funnel_id');
    }

    public function result()
    {
        return $this->belongsTo(FunnelResult::class, 'funnel_result_id');
    }

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'crm_inquiry_id');
    }

    public function answers()
    {
        return $this->hasMany(FunnelResponseAnswer::class, 'response_id');
    }
}
