<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoMetaEntry extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'target_type',
        'target_id',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'canonical_url',
        'robots_meta',
        'og_title_en',
        'og_title_ar',
        'og_description_en',
        'og_description_ar',
        'og_image',
        'twitter_title_en',
        'twitter_title_ar',
        'twitter_description_en',
        'twitter_description_ar',
        'twitter_image',
        'schema_enabled',
        'schema_type',
        'hreflang_en_url',
        'hreflang_ar_url',
        'is_active',
    ];

    protected $casts = [
        'schema_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];
}
