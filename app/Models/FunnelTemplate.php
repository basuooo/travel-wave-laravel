<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'thumbnail',
        'schema_data',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'schema_data' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function funnels()
    {
        return $this->hasMany(Funnel::class, 'template_id');
    }
}
