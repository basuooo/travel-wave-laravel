<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LpNewSectionCategory extends Model
{
    use HasFactory;

    protected $table = 'lp_new_section_categories';

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'icon',
        'sort_order',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(LpNewSection::class, 'section_category_id');
    }
}
