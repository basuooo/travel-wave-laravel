<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_key',
        'locale',
        'question',
        'answer',
        'matched_sources',
        'was_answered',
        'used_handoff',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'matched_sources' => 'array',
        'was_answered' => 'boolean',
        'used_handoff' => 'boolean',
    ];
}
