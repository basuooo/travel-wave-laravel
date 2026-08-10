<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpNewPageVariant extends Model
{
    use HasFactory;

    protected $table = 'lp_new_page_variants';

    protected $fillable = [
        'experiment_id',
        'landing_page_id',
        'variant_letter',
        'name',
        'traffic_weight',
        'structure',
        'is_control',
    ];

    protected $casts = [
        'is_control' => 'boolean',
    ];

    public function experiment(): BelongsTo
    {
        return $this->belongsTo(LpNewExperiment::class, 'experiment_id');
    }

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LpNewLandingPage::class, 'landing_page_id');
    }
}
