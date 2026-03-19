<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'site_name_en',
        'site_name_ar',
        'site_tagline_en',
        'site_tagline_ar',
        'logo_path',
        'footer_logo_path',
        'logo_width',
        'logo_height',
        'mobile_logo_width',
        'favicon_path',
        'contact_email',
        'phone',
        'secondary_phone',
        'whatsapp_number',
        'address_en',
        'address_ar',
        'working_hours_en',
        'working_hours_ar',
        'map_iframe',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'footer_text_en',
        'footer_text_ar',
        'copyright_text_en',
        'copyright_text_ar',
        'default_meta_title_en',
        'default_meta_title_ar',
        'default_meta_description_en',
        'default_meta_description_ar',
        'primary_color',
        'secondary_color',
        'accent_color',
        'button_color',
        'button_hover_color',
        'link_hover_color',
        'header_background_color',
        'header_text_color',
        'header_link_color',
        'header_hover_color',
        'header_active_link_color',
        'header_button_color',
        'header_button_text_color',
        'header_logo_enabled',
        'header_is_sticky',
        'header_vertical_padding',
        'footer_background_color',
        'footer_text_color',
        'footer_link_color',
        'footer_hover_color',
        'footer_heading_color',
        'footer_button_color',
        'footer_button_text_color',
        'footer_vertical_padding',
        'footer_quick_links',
        'home_country_strip_title_en',
        'home_country_strip_title_ar',
        'home_country_strip_subtitle_en',
        'home_country_strip_subtitle_ar',
        'home_country_strip_autoplay',
        'home_country_strip_speed',
        'home_destinations_autoplay',
        'home_destinations_interval',
        'home_destinations_speed',
        'home_destinations_pause_on_hover',
        'home_destinations_loop',
        'global_cta_title_en',
        'global_cta_title_ar',
        'global_cta_text_en',
        'global_cta_text_ar',
        'global_cta_button_en',
        'global_cta_button_ar',
        'global_cta_url',
        'hero_slider_autoplay',
        'hero_slider_interval',
        'hero_slider_overlay_opacity',
        'hero_slider_show_dots',
        'hero_slider_show_arrows',
        'hero_slider_content_alignment',
        'hero_slider_layout_mode',
    ];

    protected $casts = [
        'hero_slider_autoplay' => 'boolean',
        'hero_slider_show_dots' => 'boolean',
        'hero_slider_show_arrows' => 'boolean',
        'hero_slider_interval' => 'integer',
        'hero_slider_overlay_opacity' => 'float',
        'logo_width' => 'integer',
        'logo_height' => 'integer',
        'mobile_logo_width' => 'integer',
        'header_logo_enabled' => 'boolean',
        'header_is_sticky' => 'boolean',
        'header_vertical_padding' => 'integer',
        'footer_vertical_padding' => 'integer',
        'footer_quick_links' => 'array',
        'home_country_strip_autoplay' => 'boolean',
        'home_country_strip_speed' => 'integer',
        'home_destinations_autoplay' => 'boolean',
        'home_destinations_interval' => 'integer',
        'home_destinations_speed' => 'integer',
        'home_destinations_pause_on_hover' => 'boolean',
        'home_destinations_loop' => 'boolean',
    ];

    public function logoPathFor(string $variant = 'header'): ?string
    {
        return match ($variant) {
            'footer' => $this->footer_logo_path ?: $this->logo_path,
            default => $this->logo_path,
        };
    }

    public function normalizedMediaPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = trim($path);

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $parsedPath = parse_url($path, PHP_URL_PATH) ?: '';
            $path = trim($parsedPath, '/');
        }

        foreach (['storage/', 'public/'] as $prefix) {
            if (Str::startsWith($path, $prefix)) {
                $path = Str::after($path, $prefix);
            }
        }

        return ltrim($path, '/');
    }

    public function logoUrlFor(string $variant = 'header'): ?string
    {
        $path = $this->normalizedMediaPath($this->logoPathFor($variant));

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return '/storage/' . $path . '?v=' . ($this->updated_at?->timestamp ?: time());
    }
}
