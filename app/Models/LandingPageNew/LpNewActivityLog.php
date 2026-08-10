<?php

namespace App\Models\LandingPageNew;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpNewActivityLog extends Model
{
    use HasFactory;

    protected $table = 'lp_new_activity_logs';

    protected $fillable = [
        'user_id',
        'landing_page_id',
        'action',
        'entity_type',
        'entity_id',
        'before_state',
        'after_state',
        'ip_address',
    ];

    protected $casts = [
        'before_state' => 'array',
        'after_state' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LpNewLandingPage::class, 'landing_page_id');
    }

    public static function log(string $action, ?LpNewLandingPage $page = null, ?array $before = null, ?array $after = null, ?string $entityType = null, ?int $entityId = null): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'landing_page_id' => $page?->id,
            'action' => $action,
            'entity_type' => $entityType ?: ($page ? get_class($page) : null),
            'entity_id' => $entityId ?: $page?->id,
            'before_state' => $before,
            'after_state' => $after,
            'ip_address' => request()->ip(),
        ]);
    }
}
