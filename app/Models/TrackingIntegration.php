<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingIntegration extends Model
{
    use HasFactory;

    public const TYPE_GTM = 'gtm';
    public const TYPE_GA4 = 'ga4';
    public const TYPE_META_PIXEL = 'meta_pixel';
    public const TYPE_TIKTOK_PIXEL = 'tiktok_pixel';
    public const TYPE_SNAP_PIXEL = 'snap_pixel';
    public const TYPE_X_PIXEL = 'x_pixel';
    public const TYPE_LINKEDIN_INSIGHT = 'linkedin_insight';
    public const TYPE_PINTEREST_TAG = 'pinterest_tag';
    public const TYPE_GOOGLE_ADS = 'google_ads';
    public const TYPE_MICROSOFT_CLARITY = 'microsoft_clarity';
    public const TYPE_CUSTOM_SCRIPT = 'custom_script';

    protected $fillable = [
        'name',
        'slug',
        'integration_type',
        'platform',
        'tracking_code',
        'script_code',
        'settings',
        'placement',
        'notes',
        'visibility_mode',
        'visibility_targets',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'visibility_targets' => 'array',
        'settings' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
