<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpTemplateCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'icon',
        'sort_order',
    ];

    public function templates()
    {
        return $this->hasMany(LpTemplate::class, 'template_category_id');
    }
}
