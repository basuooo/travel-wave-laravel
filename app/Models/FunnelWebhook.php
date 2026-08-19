<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'funnel_id',
        'name',
        'event_trigger',
        'target_url',
        'secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function funnel()
    {
        return $this->belongsTo(Funnel::class, 'funnel_id');
    }
}
