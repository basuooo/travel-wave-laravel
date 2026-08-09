<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpGlobalComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'name_en',
        'name_ar',
        'component_type',
        'structure',
        'settings',
    ];

    protected $casts = [
        'structure' => 'array',
        'settings' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
