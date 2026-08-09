<?php

namespace App\Models\LandingPage;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpTemplate extends Model
{
    use HasFactory;

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
        'is_active',
        'is_global',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'structure' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(LpTemplateCategory::class, 'template_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
