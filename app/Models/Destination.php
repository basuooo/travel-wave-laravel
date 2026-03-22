<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Destination extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_ar',
        'slug',
        'destination_type',
        'excerpt_en',
        'excerpt_ar',
        'subtitle_en',
        'subtitle_ar',
        'hero_badge_en',
        'hero_badge_ar',
        'hero_title_en',
        'hero_title_ar',
        'hero_subtitle_en',
        'hero_subtitle_ar',
        'hero_cta_text_en',
        'hero_cta_text_ar',
        'hero_cta_url',
        'hero_secondary_cta_text_en',
        'hero_secondary_cta_text_ar',
        'hero_secondary_cta_url',
        'hero_overlay_opacity',
        'hero_image',
        'featured_image',
        'hero_mobile_image',
        'flag_image',
        'overview_en',
        'overview_ar',
        'quick_info_title_en',
        'quick_info_title_ar',
        'quick_info_items',
        'about_title_en',
        'about_title_ar',
        'about_description_en',
        'about_description_ar',
        'about_image',
        'about_points',
        'detailed_title_en',
        'detailed_title_ar',
        'detailed_description_en',
        'detailed_description_ar',
        'best_time_title_en',
        'best_time_title_ar',
        'best_time_description_en',
        'best_time_description_ar',
        'highlights_title_en',
        'highlights_title_ar',
        'highlight_items',
        'services_title_en',
        'services_title_ar',
        'services_intro_en',
        'services_intro_ar',
        'service_items',
        'documents_title_en',
        'documents_title_ar',
        'documents_subtitle_en',
        'documents_subtitle_ar',
        'document_items',
        'steps_title_en',
        'steps_title_ar',
        'step_items',
        'pricing_title_en',
        'pricing_title_ar',
        'pricing_notes_en',
        'pricing_notes_ar',
        'pricing_items',
        'faq_title_en',
        'faq_title_ar',
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
        'cta_secondary_button_en',
        'cta_secondary_button_ar',
        'cta_secondary_url',
        'cta_background_image',
        'form_title_en',
        'form_title_ar',
        'form_subtitle_en',
        'form_subtitle_ar',
        'form_submit_text_en',
        'form_submit_text_ar',
        'form_visible_fields',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'show_hero',
        'show_quick_info',
        'show_about',
        'show_detailed',
        'show_best_time',
        'show_highlights',
        'show_services',
        'show_documents',
        'show_steps',
        'show_pricing',
        'show_faq',
        'show_cta',
        'show_form',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'quick_info_items' => 'array',
        'about_points' => 'array',
        'highlight_items' => 'array',
        'service_items' => 'array',
        'document_items' => 'array',
        'step_items' => 'array',
        'pricing_items' => 'array',
        'highlights' => 'array',
        'packages' => 'array',
        'included_items' => 'array',
        'excluded_items' => 'array',
        'itinerary' => 'array',
        'gallery' => 'array',
        'faqs' => 'array',
        'form_visible_fields' => 'array',
        'hero_overlay_opacity' => 'decimal:2',
        'show_hero' => 'boolean',
        'show_quick_info' => 'boolean',
        'show_about' => 'boolean',
        'show_detailed' => 'boolean',
        'show_best_time' => 'boolean',
        'show_highlights' => 'boolean',
        'show_services' => 'boolean',
        'show_documents' => 'boolean',
        'show_steps' => 'boolean',
        'show_pricing' => 'boolean',
        'show_faq' => 'boolean',
        'show_cta' => 'boolean',
        'show_form' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function frontendUrl(): ?string
    {
        return ($this->is_active && ! $this->trashed()) ? route('destinations.show', $this) : null;
    }

    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::slug($base) ?: 'destination';
        $candidate = $base;
        $counter = 2;

        while (static::query()->withTrashed()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    public function repeaterValue(array $item, string $field, ?string $locale = null, mixed $fallback = ''): mixed
    {
        $locale = $locale ?: app()->getLocale();

        return $item["{$field}_{$locale}"]
            ?? $item["{$field}_" . config('app.fallback_locale', 'en')]
            ?? $item[$field]
            ?? $fallback;
    }
}
