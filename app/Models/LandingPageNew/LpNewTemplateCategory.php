<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LpNewTemplateCategory extends Model
{
    use HasFactory;

    protected $table = 'lp_new_template_categories';

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'icon',
        'sort_order',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(LpNewTemplate::class, 'template_category_id');
    }
}
