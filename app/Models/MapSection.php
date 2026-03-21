<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MapSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'title_en',
        'title_ar',
        'subtitle_en',
        'subtitle_ar',
        'address_en',
        'address_ar',
        'button_text_en',
        'button_text_ar',
        'button_link',
        'embed_code',
        'map_url',
        'layout_type',
        'height',
        'background_style',
        'spacing_preset',
        'rounded_corners',
        'is_active',
    ];

    protected $casts = [
        'rounded_corners' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(MapSectionAssignment::class)->orderBy('display_position')->orderBy('sort_order');
    }

    public function localized(string $base): ?string
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        return $this->getAttribute($base . '_' . $locale)
            ?: $this->getAttribute($base . '_' . $fallback)
            ?: null;
    }
}
