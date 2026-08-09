<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpPageVariant extends Model
{
    use HasFactory;

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
        'structure' => 'array',
        'is_control' => 'boolean',
        'traffic_weight' => 'integer',
    ];

    public function experiment()
    {
        return $this->belongsTo(LpExperiment::class, 'experiment_id');
    }

    public function landingPage()
    {
        return $this->belongsTo(LpLandingPage::class, 'landing_page_id');
    }
}
