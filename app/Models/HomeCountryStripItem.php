<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeCountryStripItem extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'visa_country_id',
        'name_en',
        'name_ar',
        'subtitle_en',
        'subtitle_ar',
        'image_path',
        'flag_image_path',
        'custom_url',
        'sort_order',
        'is_active',
        'show_on_homepage',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_homepage' => 'boolean',
    ];

    public function visaCountry()
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }

    public function resolvedUrl(): string
    {
        if ($this->visaCountry) {
            return route('visas.country', $this->visaCountry);
        }

        return $this->custom_url ?: '#';
    }

    public function displayName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');

        return $this->{"name_{$locale}"}
            ?: $this->{"name_{$fallbackLocale}"}
            ?: $this->visaCountry?->{"name_{$locale}"}
            ?: $this->visaCountry?->{"name_{$fallbackLocale}"}
            ?: 'Destination';
    }

    public function displaySubtitle(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');

        return $this->{"subtitle_{$locale}"}
            ?: $this->{"subtitle_{$fallbackLocale}"}
            ?: $this->visaCountry?->{"visa_type_{$locale}"}
            ?: $this->visaCountry?->{"visa_type_{$fallbackLocale}"}
            ?: null;
    }

    public function displayImagePath(): ?string
    {
        return $this->image_path ?: $this->visaCountry?->hero_image;
    }

    public function displayFlagPath(): ?string
    {
        return $this->flag_image_path ?: $this->visaCountry?->flag_image;
    }
}
