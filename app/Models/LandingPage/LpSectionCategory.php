<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpSectionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'icon',
        'sort_order',
    ];

    public function sections()
    {
        return $this->hasMany(LpSection::class, 'section_category_id');
    }
}
