<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingLandingPage extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'internal_name',
        'title_en',
        'title_ar',
        'slug',
        'campaign_name',
        'ad_platform',
        'campaign_type',
        'traffic_source',
        'target_audience_note',
        'status',
        'assigned_lead_form_id',
        'tracking_integration_ids',
        'sections',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'final_url',
        'notes',
    ];

    protected $casts = [
        'tracking_integration_ids' => 'array',
        'sections' => 'array',
    ];

    public function leadForm()
    {
        return $this->belongsTo(LeadForm::class, 'assigned_lead_form_id');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function events()
    {
        return $this->hasMany(MarketingLandingPageEvent::class);
    }

    public function trackingIntegrations()
    {
        return TrackingIntegration::query()
            ->whereIn('id', $this->tracking_integration_ids ?? [])
            ->orderBy('sort_order')
            ->get();
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function localizedSectionText(string $path, ?string $locale = null, mixed $fallback = null): mixed
    {
        $locale = $locale ?: app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $base = data_get($this->sections ?? [], $path);

        if (! is_array($base)) {
            return $base ?? $fallback;
        }

        return $base[$locale]
            ?? $base[$fallbackLocale]
            ?? $base['value']
            ?? $fallback;
    }

    public function publicUrl(): string
    {
        return route('marketing.landing-pages.show', $this);
    }
}
