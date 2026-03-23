<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmServiceSubtype extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_service_type_id',
        'name_en',
        'name_ar',
        'slug',
        'is_active',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'crm_service_type_id' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function serviceType()
    {
        return $this->belongsTo(CrmServiceType::class, 'crm_service_type_id');
    }

    public function leads()
    {
        return $this->hasMany(Inquiry::class, 'crm_service_subtype_id');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'ar'
            ? ($this->name_ar ?: $this->name_en)
            : ($this->name_en ?: $this->name_ar);
    }
}
