<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtmCampaign extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_ENDED = 'ended';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'display_name',
        'campaign_code',
        'base_url',
        'generated_url',
        'campaign_type',
        'objective',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_id',
        'utm_term',
        'utm_content',
        'platform',
        'external_campaign_id',
        'start_date',
        'end_date',
        'budget',
        'owner_user_id',
        'created_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visits()
    {
        return $this->hasMany(UtmVisit::class)->latest('visited_at');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'utm_campaign_id');
    }

    public function customers()
    {
        return $this->hasManyThrough(
            CrmCustomer::class,
            Inquiry::class,
            'utm_campaign_id',
            'inquiry_id',
            'id',
            'id'
        );
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => __('admin.marketing_campaign_status_draft'),
            self::STATUS_ACTIVE => __('admin.marketing_campaign_status_active'),
            self::STATUS_PAUSED => __('admin.marketing_campaign_status_paused'),
            self::STATUS_ENDED => __('admin.marketing_campaign_status_ended'),
            self::STATUS_ARCHIVED => __('admin.marketing_campaign_status_archived'),
        ];
    }

    public function localizedStatus(): string
    {
        return self::statusOptions()[$this->status] ?? __('admin.marketing_campaign_status_active');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PAUSED => 'warning',
            self::STATUS_ENDED => 'dark',
            self::STATUS_ARCHIVED => 'secondary',
            default => 'success',
        };
    }
}
