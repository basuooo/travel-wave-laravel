<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandDomain extends Model
{
    use HasFactory;

    protected $table = 'lp_new_brand_domains';

    protected $fillable = [
        'brand_id',
        'domain',
        'is_primary',
        'is_verified',
        'ssl_status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
