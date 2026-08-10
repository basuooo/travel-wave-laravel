<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LpNewExperiment extends Model
{
    use HasFactory;

    protected $table = 'lp_new_experiments';

    protected $fillable = [
        'landing_page_id',
        'name',
        'status',
        'traffic_split_json',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'traffic_split_json' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LpNewLandingPage::class, 'landing_page_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(LpNewPageVariant::class, 'experiment_id');
    }
}
