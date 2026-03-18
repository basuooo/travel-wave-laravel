<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'description_en',
        'description_ar',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(BlogPost::class)->latest('published_at');
    }
}
