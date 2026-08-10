<?php

namespace App\Models\LandingPageNew;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpNewAsset extends Model
{
    use HasFactory;

    protected $table = 'lp_new_assets';

    protected $fillable = [
        'landing_page_id',
        'filename',
        'original_path',
        'storage_path',
        'mime_type',
        'file_size',
        'asset_type',
        'usage_count',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LpNewLandingPage::class, 'landing_page_id');
    }

    public function storageUrl(): string
    {
        return asset('storage/' . $this->storage_path);
    }
}
