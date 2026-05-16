<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class VisaCountry extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

    protected $fillable = [
        'visa_category_id',
        'name_en',
        'name_ar',
        'slug',
        'excerpt_en',
        'excerpt_ar',
        'hero_badge_en',
        'hero_badge_ar',
        'hero_title_en',
        'hero_title_ar',
        'hero_subtitle_en',
        'hero_subtitle_ar',
        'hero_cta_text_en',
        'hero_cta_text_ar',
        'hero_cta_url',
        'hero_overlay_opacity',
        'hero_image',
        'hero_mobile_image',
        'flag_image',
        'overview_en',
        'overview_ar',
        'visa_type_en',
        'visa_type_ar',
        'stay_duration_en',
        'stay_duration_ar',
        'quick_summary_destination_label_en',
        'quick_summary_destination_label_ar',
        'quick_summary_destination_icon',
        'quick_summary_items',
        'intro_image',
        'introduction_title_en',
        'introduction_title_ar',
        'introduction_badge_en',
        'introduction_badge_ar',
        'introduction_points',
        'detailed_title_en',
        'detailed_title_ar',
        'detailed_description_en',
        'detailed_description_ar',
        'best_time_badge_en',
        'best_time_badge_ar',
        'best_time_title_en',
        'best_time_title_ar',
        'best_time_description_en',
        'best_time_description_ar',
        'highlights_section_label_en',
        'highlights_section_label_ar',
        'highlights_section_title_en',
        'highlights_section_title_ar',
        'highlights',
        'required_documents',
        'application_steps',
        'services',
        'why_choose_title_en',
        'why_choose_title_ar',
        'why_choose_intro_en',
        'why_choose_intro_ar',
        'why_choose_items',
        'documents_title_en',
        'documents_title_ar',
        'documents_subtitle_en',
        'documents_subtitle_ar',
        'document_items',
        'steps_title_en',
        'steps_title_ar',
        'step_items',
        'processing_time_en',
        'processing_time_ar',
        'fees_en',
        'fees_ar',
        'fees_title_en',
        'fees_title_ar',
        'fee_items',
        'fees_notes_en',
        'fees_notes_ar',
        'faqs',
        'faq_title_en',
        'faq_title_ar',
        'support_title_en',
        'support_title_ar',
        'support_subtitle_en',
        'support_subtitle_ar',
        'support_button_en',
        'support_button_ar',
        'support_button_link',
        'support_is_active',
        'map_title_en',
        'map_title_ar',
        'map_description_en',
        'map_description_ar',
        'map_embed_code',
        'map_is_active',
        'inquiry_form_title_en',
        'inquiry_form_title_ar',
        'inquiry_form_subtitle_en',
        'inquiry_form_subtitle_ar',
        'inquiry_form_button_en',
        'inquiry_form_button_ar',
        'inquiry_form_success_en',
        'inquiry_form_success_ar',
        'inquiry_form_default_service_type',
        'inquiry_form_visible_fields',
        'inquiry_form_is_active',
        'inquiry_form_label_en',
        'inquiry_form_label_ar',
        'cta_title_en',
        'cta_title_ar',
        'cta_text_en',
        'cta_text_ar',
        'cta_button_en',
        'cta_button_ar',
        'cta_url',
        'final_cta_background_image',
        'final_cta_is_active',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'og_image',
        'is_featured',
        'is_active',
        'sort_order',
        'content_mode',
        'html_content_en',
        'html_content_ar',
    ];

    protected $casts = [
        'highlights' => 'array',
        'required_documents' => 'array',
        'application_steps' => 'array',
        'services' => 'array',
        'quick_summary_items' => 'array',
        'introduction_points' => 'array',
        'why_choose_items' => 'array',
        'document_items' => 'array',
        'step_items' => 'array',
        'fee_items' => 'array',
        'faqs' => 'array',
        'inquiry_form_visible_fields' => 'array',
        'hero_overlay_opacity' => 'decimal:2',
        'map_is_active' => 'boolean',
        'inquiry_form_is_active' => 'boolean',
        'support_is_active' => 'boolean',
        'final_cta_is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VisaCategory::class, 'visa_category_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function frontendUrl(): ?string
    {
        return ($this->is_active && ! $this->trashed()) ? route('visas.country', $this) : null;
    }

    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::slug($base) ?: 'visa-country';
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
