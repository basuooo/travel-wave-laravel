<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmInformationRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_information_id',
        'user_id',
        'delivered_at',
        'seen_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'crm_information_id' => 'integer',
        'user_id' => 'integer',
        'delivered_at' => 'datetime',
        'seen_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function information()
    {
        return $this->belongsTo(CrmInformation::class, 'crm_information_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAcknowledged(): bool
    {
        return ! is_null($this->acknowledged_at);
    }
}
