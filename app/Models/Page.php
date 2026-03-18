<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'key',
        'title_en',
        'title_ar',
        'slug',
        'hero_badge_en',
        'hero_badge_ar',
        'hero_title_en',
        'hero_title_ar',
        'hero_subtitle_en',
        'hero_subtitle_ar',
        'hero_primary_cta_text_en',
        'hero_primary_cta_text_ar',
        'hero_primary_cta_url',
        'hero_secondary_cta_text_en',
        'hero_secondary_cta_text_ar',
        'hero_secondary_cta_url',
        'hero_image',
        'intro_title_en',
        'intro_title_ar',
        'intro_body_en',
        'intro_body_ar',
        'sections',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'is_active',
    ];

    protected $casts = [
        'sections' => 'array',
        'is_active' => 'boolean',
    ];
}
