<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'funnel_id',
        'title',
        'subtitle',
        'step_type',
        'sort_order',
        'is_hidden',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_hidden' => 'boolean',
    ];

    public function funnel()
    {
        return $this->belongsTo(Funnel::class, 'funnel_id');
    }

    public function elements()
    {
        return $this->hasMany(FunnelElement::class, 'step_id')->orderBy('sort_order');
    }
}
