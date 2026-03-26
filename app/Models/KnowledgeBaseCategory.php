<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function articles()
    {
        return $this->hasMany(KnowledgeBaseArticle::class, 'knowledge_base_category_id');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $locale === 'ar'
            ? ($this->name_ar ?: ($this->name_en ?: $this->slug))
            : ($this->name_en ?: ($this->name_ar ?: $this->slug));
    }
}
