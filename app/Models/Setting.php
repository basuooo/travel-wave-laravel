<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'hero_slider_autoplay' => 'boolean',
        'hero_slider_show_dots' => 'boolean',
        'hero_slider_show_arrows' => 'boolean',
        'hero_slider_interval' => 'integer',
        'hero_slider_overlay_opacity' => 'float',
    ];
}
