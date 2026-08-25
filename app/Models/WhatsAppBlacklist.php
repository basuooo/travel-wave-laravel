<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppBlacklist extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_blacklist';

    protected $fillable = [
        'phone',
        'normalized_phone',
        'reason',
        'added_by_user_id',
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}
