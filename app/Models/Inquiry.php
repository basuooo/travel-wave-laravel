<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'full_name',
        'phone',
        'email',
        'nationality',
        'destination',
        'service_type',
        'travel_date',
        'return_date',
        'travelers_count',
        'nights_count',
        'accommodation_type',
        'estimated_budget',
        'preferred_language',
        'source_page',
        'message',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
    ];
}
