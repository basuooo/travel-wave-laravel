<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'floating_whatsapp_enabled',
        'floating_whatsapp_number',
        'floating_whatsapp_message_en',
        'floating_whatsapp_message_ar',
        'floating_whatsapp_button_text_en',
        'floating_whatsapp_button_text_ar',
        'floating_whatsapp_show_icon',
        'floating_whatsapp_position',
        'floating_whatsapp_animation_style',
        'floating_whatsapp_animation_speed',
        'floating_whatsapp_show_desktop',
        'floating_whatsapp_show_mobile',
        'floating_whatsapp_background_color',
        'floating_whatsapp_visibility_mode',
        'floating_whatsapp_visibility_targets',
        'meta_pixel_id',
        'meta_conversion_api_enabled',
        'meta_conversion_api_access_token',
        'meta_conversion_api_test_event_code',
        'meta_conversion_api_default_event_source_url',
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
        'floating_whatsapp_enabled' => 'boolean',
        'floating_whatsapp_show_icon' => 'boolean',
        'floating_whatsapp_animation_speed' => 'integer',
        'floating_whatsapp_show_desktop' => 'boolean',
        'floating_whatsapp_show_mobile' => 'boolean',
        'floating_whatsapp_visibility_targets' => 'array',
        'meta_conversion_api_enabled' => 'boolean',
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

    public function floatingWhatsappMessage(): ?string
    {
        return $this->localized('floating_whatsapp_message')
            ?: 'مرحبًا، أريد الاستفسار عن خدمات Travel Wave';
    }

    public function floatingWhatsappButtonText(): ?string
    {
        return $this->localized('floating_whatsapp_button_text');
    }

    public function floatingWhatsappNumberNormalized(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->floating_whatsapp_number);

        return $number !== '' ? $number : null;
    }

    public function floatingWhatsappUrl(): ?string
    {
        $number = $this->floatingWhatsappNumberNormalized();

        if (! $number) {
            return null;
        }

        $message = trim((string) $this->floatingWhatsappMessage());

        return 'https://wa.me/' . $number . ($message !== '' ? '?text=' . rawurlencode($message) : '');
    }

    public function metaPixelId(): ?string
    {
        $explicit = preg_replace('/\D+/', '', (string) $this->meta_pixel_id);

        if ($explicit !== '') {
            return $explicit;
        }

        $integration = TrackingIntegration::query()
            ->where('integration_type', TrackingIntegration::TYPE_META_PIXEL)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        $fallback = preg_replace('/\D+/', '', (string) ($integration?->tracking_code ?? ''));

        return $fallback !== '' ? $fallback : null;
    }

    public function metaConversionApiConfigured(): bool
    {
        return (bool) $this->meta_conversion_api_enabled
            && filled($this->meta_conversion_api_access_token)
            && filled($this->metaPixelId());
    }

    public function shouldRenderFloatingWhatsapp(?Request $request = null): bool
    {
        if (! $this->floating_whatsapp_enabled || ! $this->floatingWhatsappUrl()) {
            return false;
        }

        $request ??= request();
        $context = $this->floatingWhatsappContext($request);
        $targets = collect($this->floating_whatsapp_visibility_targets ?? [])->filter()->values()->all();
        $mode = $this->floating_whatsapp_visibility_mode ?: 'all';

        if ($mode === 'exclude_selected') {
            return ! $this->contextMatchesFloatingWhatsappTargets($context, $targets);
        }

        if ($mode === 'only_selected') {
            return $this->contextMatchesFloatingWhatsappTargets($context, $targets);
        }

        return true;
    }

    public function floatingWhatsappContext(?Request $request = null): array
    {
        $request ??= request();
        $route = $request->route();
        $routeName = $route?->getName();

        $pageKey = match ($routeName) {
            'home' => 'home',
            'visas.index' => 'visas.index',
            'destinations.index' => 'destinations.index',
            'flights' => 'flights',
            'hotels' => 'hotels',
            'about' => 'about',
            'contact' => 'contact',
            default => null,
        };

        $visaCountry = $route?->parameter('country');
        $destination = $route?->parameter('destination');

        return array_filter([
            'page_key' => $pageKey,
            'visa_country_id' => $visaCountry?->id,
            'visa_category_id' => $visaCountry?->visa_category_id,
            'destination_id' => $destination?->id,
            'destination_type' => $visaCountry ? 'visa' : ($destination?->destination_type ?: ($destination ? 'domestic' : null)),
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected function contextMatchesFloatingWhatsappTargets(array $context, array $targets): bool
    {
        foreach ($targets as $target) {
            if (! is_string($target) || ! str_contains($target, '|')) {
                continue;
            }

            [$type, $value] = array_pad(explode('|', $target, 2), 2, null);

            if (! $type || ! $value) {
                continue;
            }

            $matches = match ($type) {
                'page_key' => ($context['page_key'] ?? null) === $value,
                'page_group' => $this->matchesFloatingWhatsappPageGroup($value, $context),
                'visa_country' => (int) ($context['visa_country_id'] ?? 0) === (int) $value,
                'visa_category' => (int) ($context['visa_category_id'] ?? 0) === (int) $value,
                'destination' => (int) ($context['destination_id'] ?? 0) === (int) $value,
                'destination_type' => ($context['destination_type'] ?? null) === $value,
                default => false,
            };

            if ($matches) {
                return true;
            }
        }

        return false;
    }

    protected function matchesFloatingWhatsappPageGroup(string $group, array $context): bool
    {
        return match ($group) {
            'service-pages' => in_array($context['page_key'] ?? null, ['visas.index', 'destinations.index', 'flights', 'hotels'], true),
            'visa-destinations' => ! empty($context['visa_country_id']),
            'domestic-destinations' => ($context['destination_type'] ?? null) === 'domestic',
            default => false,
        };
    }
}
