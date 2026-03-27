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
        'header_logo_path',
        'footer_logo_path',
        'logo_width',
        'logo_height',
        'logo_keep_aspect_ratio',
        'mobile_logo_width',
        'header_logo_width',
        'header_logo_height',
        'header_logo_keep_aspect_ratio',
        'header_mobile_logo_width',
        'header_logo_display_mode',
        'footer_logo_width',
        'footer_logo_height',
        'footer_logo_keep_aspect_ratio',
        'footer_logo_display_mode',
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
        'twitter_url',
        'youtube_url',
        'linkedin_url',
        'snapchat_url',
        'telegram_url',
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
        'header_logo_position_en',
        'header_logo_position_ar',
        'header_menu_position_en',
        'header_menu_position_ar',
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
        'chatbot_enabled',
        'chatbot_bot_name_en',
        'chatbot_bot_name_ar',
        'chatbot_welcome_message_en',
        'chatbot_welcome_message_ar',
        'chatbot_fallback_message_en',
        'chatbot_fallback_message_ar',
        'chatbot_primary_language',
        'chatbot_suggested_questions_en',
        'chatbot_suggested_questions_ar',
        'chatbot_show_whatsapp_handoff',
        'chatbot_show_contact_handoff',
        'chatbot_content_sources',
    ];

    protected $casts = [
        'hero_slider_autoplay' => 'boolean',
        'hero_slider_show_dots' => 'boolean',
        'hero_slider_show_arrows' => 'boolean',
        'hero_slider_interval' => 'integer',
        'hero_slider_overlay_opacity' => 'float',
        'logo_width' => 'integer',
        'logo_height' => 'integer',
        'logo_keep_aspect_ratio' => 'boolean',
        'mobile_logo_width' => 'integer',
        'header_logo_width' => 'integer',
        'header_logo_height' => 'integer',
        'header_logo_keep_aspect_ratio' => 'boolean',
        'header_mobile_logo_width' => 'integer',
        'footer_logo_width' => 'integer',
        'footer_logo_height' => 'integer',
        'footer_logo_keep_aspect_ratio' => 'boolean',
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
        'chatbot_enabled' => 'boolean',
        'chatbot_suggested_questions_en' => 'array',
        'chatbot_suggested_questions_ar' => 'array',
        'chatbot_show_whatsapp_handoff' => 'boolean',
        'chatbot_show_contact_handoff' => 'boolean',
        'chatbot_content_sources' => 'array',
    ];

    public function logoPathFor(string $variant = 'header'): ?string
    {
        $candidates = match ($variant) {
            'footer' => [$this->footer_logo_path, $this->header_logo_path, $this->logo_path],
            default => [$this->header_logo_path, $this->logo_path],
        };

        $normalizedCandidates = collect($candidates)
            ->map(fn ($path) => $this->normalizedMediaPath($path))
            ->filter()
            ->values();

        $disk = Storage::disk('public');

        foreach ($normalizedCandidates as $candidate) {
            if ($disk->exists($candidate)) {
                return $candidate;
            }
        }

        return $normalizedCandidates->first();
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
        $path = $this->logoPathFor($variant);

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/' . $path) . '?v=' . ($this->updated_at?->timestamp ?: time());
    }

    public function logoWidthFor(string $variant = 'header'): int
    {
        return match ($variant) {
            'mobile' => (int) ($this->header_mobile_logo_width ?: $this->mobile_logo_width ?: $this->header_logo_width ?: $this->logo_width ?: 168),
            'footer' => (int) ($this->footer_logo_width ?: 200),
            default => (int) ($this->header_logo_width ?: $this->logo_width ?: 220),
        };
    }

    public function logoHeightFor(string $variant = 'header'): ?int
    {
        return match ($variant) {
            'footer' => $this->footer_logo_height ?: null,
            default => $this->header_logo_height ?: $this->logo_height ?: null,
        };
    }

    public function logoKeepsAspectRatio(string $variant = 'header'): bool
    {
        return match ($variant) {
            'footer' => $this->footer_logo_keep_aspect_ratio ?? true,
            default => $this->header_logo_keep_aspect_ratio ?? $this->logo_keep_aspect_ratio ?? true,
        };
    }

    public function logoDisplayModeFor(string $variant = 'header'): string
    {
        $mode = match ($variant) {
            'footer' => $this->footer_logo_display_mode,
            default => $this->header_logo_display_mode,
        };

        $allowed = ['original', 'contain', 'cover', 'custom'];

        if (is_string($mode) && in_array($mode, $allowed, true)) {
            return $mode;
        }

        // Backward compatibility: old configured dimensions imply custom rendering.
        if ($variant === 'footer') {
            if ($this->footer_logo_width || $this->footer_logo_height) {
                return 'custom';
            }
        } else {
            if ($this->header_logo_width || $this->header_logo_height || $this->header_mobile_logo_width) {
                return 'custom';
            }
        }

        return 'original';
    }

    public function headerLogoPositionForLocale(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $position = $locale === 'ar' ? $this->header_logo_position_ar : $this->header_logo_position_en;

        return in_array($position, ['left', 'right'], true)
            ? $position
            : ($locale === 'ar' ? 'right' : 'left');
    }

    public function headerMenuPositionForLocale(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $position = $locale === 'ar' ? $this->header_menu_position_ar : $this->header_menu_position_en;

        return in_array($position, ['left', 'right'], true)
            ? $position
            : ($locale === 'ar' ? 'right' : 'left');
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

    public function whatsappNumberNormalized(?string $number = null): ?string
    {
        $number = preg_replace('/\D+/', '', (string) ($number ?? $this->whatsapp_number));

        return $number !== '' ? $number : null;
    }

    public function whatsappChatUrl(?string $number = null, ?string $message = null): ?string
    {
        $normalized = $this->whatsappNumberNormalized($number);

        if (! $normalized) {
            return null;
        }

        $message = trim((string) ($message ?? ''));

        return 'https://wa.me/' . $normalized . ($message !== '' ? '?text=' . rawurlencode($message) : '');
    }

    public function normalizedPhoneNumber(?string $number = null): ?string
    {
        $value = trim((string) ($number ?? $this->phone));

        if ($value === '') {
            return null;
        }

        $hasPlus = str_starts_with($value, '+');
        $digits = preg_replace('/\D+/', '', $value);

        if ($digits === '') {
            return null;
        }

        return $hasPlus ? '+' . $digits : $digits;
    }

    public function phoneCallUrl(?string $number = null): ?string
    {
        $normalized = $this->normalizedPhoneNumber($number);

        return $normalized ? 'tel:' . $normalized : null;
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

    public function chatbotBotName(): string
    {
        return (string) ($this->localized('chatbot_bot_name') ?: 'Travel Wave AI');
    }

    public function chatbotWelcomeMessage(): string
    {
        return (string) ($this->localized('chatbot_welcome_message')
            ?: __('ui.chatbot_default_welcome'));
    }

    public function chatbotFallbackMessage(): string
    {
        return (string) ($this->localized('chatbot_fallback_message')
            ?: __('ui.chatbot_default_fallback'));
    }

    public function chatbotSuggestedQuestions(): array
    {
        $locale = app()->getLocale();
        $field = "chatbot_suggested_questions_{$locale}";
        $fallbackField = 'chatbot_suggested_questions_' . config('app.fallback_locale', 'en');
        $questions = $this->{$field} ?? $this->{$fallbackField} ?? null;

        if (is_array($questions) && ! empty($questions)) {
            return collect($questions)->filter(fn ($item) => filled($item))->values()->all();
        }

        return [
            __('ui.chatbot_suggested_1'),
            __('ui.chatbot_suggested_2'),
            __('ui.chatbot_suggested_3'),
        ];
    }

    public function chatbotContentSources(): array
    {
        $sources = collect($this->chatbot_content_sources ?: [])->filter()->values()->all();

        return $sources !== [] ? $sources : [
            'pages',
            'service_pages',
            'visa_countries',
            'destinations',
            'faqs',
            'blog_posts',
            'contact_details',
        ];
    }

    public function shouldRenderChatbot(): bool
    {
        return (bool) $this->chatbot_enabled;
    }
}
