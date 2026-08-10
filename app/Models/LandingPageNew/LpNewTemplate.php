<?php

namespace App\Models\LandingPageNew;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpNewTemplate extends Model
{
    use HasFactory;

    protected $table = 'lp_new_templates';

    protected $fillable = [
        'brand_id',
        'template_category_id',
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'slug',
        'preview_image',
        'structure',
        'settings',
        'package_path',
        'is_active',
        'is_global',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LpNewTemplateCategory::class, 'template_category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
