<?php

namespace App\Models\LandingPage;

use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\MarketingLandingPageEvent;
use App\Models\TrackingIntegration;
use App\Models\User;
use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LpLandingPage extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

    protected $table = 'lp_landing_pages';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'brand_id',
        'assigned_lead_form_id',
        'header_mode',
        'custom_header_structure',
        'footer_mode',
        'custom_footer_structure',
        'status',
        'is_active',
        'slug',
        'internal_name',
        'title_en',
        'title_ar',
        'campaign_name',
        'ad_platform',
        'campaign_type',
        'traffic_source',
        'publish_at',
        'unpublish_at',
        'tracking_mode',
        'tracking_integration_ids',
        'custom_tracking_code',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
        'og_image',
        'canonical_url',
        'robots_meta',
        'schema_json',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'custom_html_head',
        'custom_css',
        'custom_js',
        'structure',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_at' => 'datetime',
        'unpublish_at' => 'datetime',
        'tracking_integration_ids' => 'array',
        'schema_json' => 'array',
        'custom_header_structure' => 'array',
        'custom_footer_structure' => 'array',
        'structure' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function leadForm()
    {
        return $this->belongsTo(LeadForm::class, 'assigned_lead_form_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function versions()
    {
        return $this->hasMany(LpPageVersion::class, 'landing_page_id')->orderByDesc('version_number');
    }

    public function experiments()
    {
        return $this->hasMany(LpExperiment::class, 'landing_page_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(LpActivityLog::class, 'landing_page_id')->orderByDesc('created_at');
    }

    public function trackingIntegrations()
    {
        return TrackingIntegration::query()
            ->whereIn('id', $this->tracking_integration_ids ?? [])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function isPublished(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED || ! $this->is_active) {
            return false;
        }

        $now = now();
        if ($this->publish_at && $this->publish_at->isFuture()) {
            return false;
        }
        if ($this->unpublish_at && $this->unpublish_at->isPast()) {
            return false;
        }

        return true;
    }

    public function publicUrl(): string
    {
        return route('landing-pages.public.show', $this->slug);
    }
}
