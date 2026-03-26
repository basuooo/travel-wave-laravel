<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtmVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'utm_campaign_id',
        'session_key',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_id',
        'utm_term',
        'utm_content',
        'landing_page',
        'referrer',
        'request_path',
        'ip_address',
        'user_agent',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(UtmCampaign::class, 'utm_campaign_id');
    }
}
