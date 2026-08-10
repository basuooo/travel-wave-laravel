<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpNewGlobalComponent extends Model
{
    use HasFactory;

    protected $table = 'lp_new_global_components';

    protected $fillable = [
        'brand_id',
        'name_en',
        'name_ar',
        'component_type',
        'structure',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
