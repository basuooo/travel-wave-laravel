<?php

namespace App\Models\LandingPage;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpActivityLog extends Model
{
    use HasFactory;

    protected $table = 'lp_activity_logs';

    protected $fillable = [
        'user_id',
        'landing_page_id',
        'action',
        'entity_type',
        'entity_id',
        'before_state',
        'after_state',
        'ip_address',
    ];

    protected $casts = [
        'before_state' => 'array',
        'after_state' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function landingPage()
    {
        return $this->belongsTo(LpLandingPage::class, 'landing_page_id');
    }
}
