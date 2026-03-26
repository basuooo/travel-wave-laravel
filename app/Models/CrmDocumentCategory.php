<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmDocumentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(CrmDocument::class, 'crm_document_category_id');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return $locale === 'ar'
            ? ($this->name_ar ?: ($this->name_en ?: $this->slug))
            : ($this->name_en ?: ($this->name_ar ?: $this->slug));
    }
}
