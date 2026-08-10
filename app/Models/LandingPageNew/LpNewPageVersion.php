<?php

namespace App\Models\LandingPageNew;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpNewPageVersion extends Model
{
    use HasFactory;

    protected $table = 'lp_new_page_versions';

    protected $fillable = [
        'landing_page_id',
        'version_number',
        'label',
        'structure',
        'custom_html_head',
        'custom_css',
        'custom_js',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LpNewLandingPage::class, 'landing_page_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
