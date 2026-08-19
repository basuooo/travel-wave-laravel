<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'step_id',
        'element_type',
        'label',
        'question_key',
        'properties',
        'sort_order',
    ];

    protected $casts = [
        'properties' => 'array',
        'sort_order' => 'integer',
    ];

    public function step()
    {
        return $this->belongsTo(FunnelStep::class, 'step_id');
    }

    public function conditions()
    {
        return $this->hasMany(FunnelCondition::class, 'element_id');
    }
}
