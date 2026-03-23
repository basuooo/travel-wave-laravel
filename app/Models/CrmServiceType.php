<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'destination_label_en',
        'destination_label_ar',
        'requires_subtype',
        'is_default',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'requires_subtype' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function leads()
    {
        return $this->hasMany(Inquiry::class, 'crm_service_type_id');
    }

    public function subtypes()
    {
        return $this->hasMany(CrmServiceSubtype::class, 'crm_service_type_id')->orderBy('sort_order');
    }

    public function activeSubtypes()
    {
        return $this->subtypes()->where('is_active', true);
    }

    public function localizedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'ar'
            ? ($this->name_ar ?: $this->name_en)
            : ($this->name_en ?: $this->name_ar);
    }

    public function localizedDestinationLabel(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return $locale === 'ar'
            ? ($this->destination_label_ar ?: $this->destination_label_en)
            : ($this->destination_label_en ?: $this->destination_label_ar);
    }
}
