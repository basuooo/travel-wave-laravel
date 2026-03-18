<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'blog_category_id',
        'title_en',
        'title_ar',
        'slug',
        'excerpt_en',
        'excerpt_ar',
        'content_en',
        'content_ar',
        'featured_image',
        'tags_en',
        'tags_ar',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'is_published',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
