<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'image_path',
        'mobile_image_path',
        'headline_en',
        'headline_ar',
        'subtitle_en',
        'subtitle_ar',
        'cta_text_en',
        'cta_text_ar',
        'cta_link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
