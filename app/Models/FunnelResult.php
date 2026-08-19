<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'funnel_id',
        'title',
        'description',
        'image_url',
        'min_score',
        'max_score',
        'cta_label',
        'cta_type',
        'cta_url',
        'cta_whatsapp_number',
        'logic_conditions',
        'sort_order',
    ];

    protected $casts = [
        'min_score' => 'integer',
        'max_score' => 'integer',
        'logic_conditions' => 'array',
        'sort_order' => 'integer',
    ];

    public function funnel()
    {
        return $this->belongsTo(Funnel::class, 'funnel_id');
    }
}
