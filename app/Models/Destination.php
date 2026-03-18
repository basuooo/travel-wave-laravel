<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'title_en',
        'title_ar',
        'slug',
        'excerpt_en',
        'excerpt_ar',
        'hero_badge_en',
        'hero_badge_ar',
        'hero_title_en',
        'hero_title_ar',
        'hero_subtitle_en',
        'hero_subtitle_ar',
        'hero_image',
        'overview_en',
        'overview_ar',
        'highlights',
        'packages',
        'included_items',
        'excluded_items',
        'itinerary',
        'gallery',
        'faqs',
        'cta_title_en',
        'cta_title_ar',
        'cta_text_en',
        'cta_text_ar',
        'cta_button_en',
        'cta_button_ar',
        'cta_url',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'highlights' => 'array',
        'packages' => 'array',
        'included_items' => 'array',
        'excluded_items' => 'array',
        'itinerary' => 'array',
        'gallery' => 'array',
        'faqs' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];
}
