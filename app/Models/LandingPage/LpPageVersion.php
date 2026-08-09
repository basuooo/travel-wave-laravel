<?php

namespace App\Models\LandingPage;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpPageVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_id',
        'version_number',
        'label',
        'structure',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'structure' => 'array',
        'settings' => 'array',
        'version_number' => 'integer',
    ];

    public function landingPage()
    {
        return $this->belongsTo(LpLandingPage::class, 'landing_page_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
