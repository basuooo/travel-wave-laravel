<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_leads';

    protected $fillable = [
        'integration_id',
        'external_id',
        'full_name',
        'phone',
        'email',
        'platform',
        'campaign_name',
        'adset_name',
        'ad_name',
        'source',
        'status',
        'country',
        'metadata',
        'assigned_user_id',
        'inquiry_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function integration()
    {
        return $this->belongsTo(CrmIntegration::class, 'integration_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }
}
