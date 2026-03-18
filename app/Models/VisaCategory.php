<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaCategory extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'short_description_en',
        'short_description_ar',
        'icon',
        'image',
        'sort_order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function countries()
    {
        return $this->hasMany(VisaCountry::class)->orderBy('sort_order');
    }
}
