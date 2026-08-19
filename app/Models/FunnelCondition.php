<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'funnel_id',
        'step_id',
        'element_id',
        'operator',
        'compare_value',
        'target_type',
        'target_id',
        'logical_operator',
    ];

    public function funnel()
    {
        return $this->belongsTo(Funnel::class, 'funnel_id');
    }

    public function step()
    {
        return $this->belongsTo(FunnelStep::class, 'step_id');
    }

    public function element()
    {
        return $this->belongsTo(FunnelElement::class, 'element_id');
    }
}
