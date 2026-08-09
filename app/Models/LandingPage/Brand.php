<?php

namespace App\Models\LandingPage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

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

    public function domains()
    {
        return $this->hasMany(BrandDomain::class);
    }

    public function landingPages()
    {
        return $this->hasMany(LpLandingPage::class);
    }

    public function templates()
    {
        return $this->hasMany(LpTemplate::class);
    }

    public function globalComponents()
    {
        return $this->hasMany(LpGlobalComponent::class);
    }
}
