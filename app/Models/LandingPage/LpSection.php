<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_category_id',
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'preview_image',
        'structure',
        'is_active',
        'is_global',
    ];

    protected $casts = [
        'structure' => 'array',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(LpSectionCategory::class, 'section_category_id');
    }
}
