<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpExperiment extends Model
{
    use HasFactory;

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

    public function landingPage()
    {
        return $this->belongsTo(LpLandingPage::class, 'landing_page_id');
    }

    public function variants()
    {
        return $this->hasMany(LpPageVariant::class, 'experiment_id');
    }
}
