<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpNewSection extends Model
{
    use HasFactory;

    protected $table = 'lp_new_sections';

    protected $fillable = [
        'section_category_id',
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'preview_image',
        'structure',
        'custom_html',
        'custom_css',
        'custom_js',
        'is_active',
        'is_global',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(LpNewSectionCategory::class, 'section_category_id');
    }
}
