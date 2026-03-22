<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class VisaCategory extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'short_description_en',
        'short_description_ar',
        'icon',
        'image',
        'sort_order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function countries()
    {
        return $this->hasMany(VisaCountry::class)->orderBy('sort_order');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function frontendUrl(): ?string
    {
        return ($this->is_active && ! $this->trashed()) ? route('visas.category', $this) : null;
    }

    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::slug($base) ?: 'visa-category';
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
