<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'lp_new_brands';

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'header_settings',
        'footer_settings',
        'default_tracking',
        'is_active',
    ];

    protected $casts = [
        'header_settings' => 'array',
        'footer_settings' => 'array',
        'default_tracking' => 'array',
        'is_active' => 'boolean',
    ];

    public function domains(): HasMany
    {
        return $this->hasMany(BrandDomain::class, 'brand_id');
    }

    public function landingPages(): HasMany
    {
        return $this->hasMany(LpNewLandingPage::class, 'brand_id');
    }

    public function primaryDomain(): ?BrandDomain
    {
        return $this->domains()->where('is_primary', true)->first();
    }
}
