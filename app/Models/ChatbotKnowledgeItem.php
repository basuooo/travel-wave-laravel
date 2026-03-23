<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledgeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_type',
        'source_id',
        'source_key',
        'locale',
        'title',
        'summary',
        'content',
        'url',
        'metadata',
        'sort_order',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];
}
