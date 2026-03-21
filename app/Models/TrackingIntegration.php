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
    public const TYPE_CUSTOM_SCRIPT = 'custom_script';

    protected $fillable = [
        'name',
        'slug',
        'integration_type',
        'platform',
        'tracking_code',
        'script_code',
        'placement',
        'notes',
        'visibility_mode',
        'visibility_targets',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'visibility_targets' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
