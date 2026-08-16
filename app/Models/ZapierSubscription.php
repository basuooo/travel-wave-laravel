<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZapierSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event',
        'target_url',
        'is_active',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
