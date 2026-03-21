<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingLandingPageEvent extends Model
{
    use HasFactory;

    public const TYPE_PAGE_VIEW = 'page_view';
    public const TYPE_CTA_CLICK = 'cta_click';
    public const TYPE_WHATSAPP_CLICK = 'whatsapp_click';
    public const TYPE_FORM_SUBMIT = 'form_submit';

    protected $fillable = [
        'marketing_landing_page_id',
        'event_type',
        'session_key',
        'source',
        'medium',
        'campaign',
        'content',
        'term',
        'referrer',
        'path',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function landingPage()
    {
        return $this->belongsTo(MarketingLandingPage::class, 'marketing_landing_page_id');
    }
}
