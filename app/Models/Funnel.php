<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funnel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'template_id',
        'name',
        'slug',
        'status',
        'design_settings',
        'tracking_settings',
        'crm_settings',
        'seo_settings',
        'published_at',
    ];

    protected $casts = [
        'design_settings' => 'array',
        'tracking_settings' => 'array',
        'crm_settings' => 'array',
        'seo_settings' => 'array',
        'published_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(FunnelTemplate::class, 'template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function steps()
    {
        return $this->hasMany(FunnelStep::class, 'funnel_id')->orderBy('sort_order');
    }

    public function results()
    {
        return $this->hasMany(FunnelResult::class, 'funnel_id')->orderBy('sort_order');
    }

    public function conditions()
    {
        return $this->hasMany(FunnelCondition::class, 'funnel_id');
    }

    public function responses()
    {
        return $this->hasMany(FunnelResponse::class, 'funnel_id');
    }

    public function webhooks()
    {
        return $this->hasMany(FunnelWebhook::class, 'funnel_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function publicUrl(): string
    {
        return url('/f/' . $this->slug);
    }
}
