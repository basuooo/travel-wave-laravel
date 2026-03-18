<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'client_name',
        'client_role_en',
        'client_role_ar',
        'testimonial_en',
        'testimonial_ar',
        'rating',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
