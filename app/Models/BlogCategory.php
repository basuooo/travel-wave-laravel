<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

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
        'deleted_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(BlogPost::class)->latest('published_at');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function frontendUrl(): ?string
    {
        return ($this->is_active && ! $this->trashed()) ? route('blog.index') : null;
    }

    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::slug($base) ?: 'blog-category';
        $candidate = $base;
        $counter = 2;

        while (static::query()->withTrashed()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
