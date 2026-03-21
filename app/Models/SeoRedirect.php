<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoRedirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_path',
        'destination_url',
        'redirect_type',
        'notes',
        'is_active',
        'hit_count',
        'last_hit_at',
    ];

    protected $casts = [
        'redirect_type' => 'integer',
        'is_active' => 'boolean',
        'hit_count' => 'integer',
        'last_hit_at' => 'datetime',
    ];
}
