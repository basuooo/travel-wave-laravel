<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

    protected $fillable = [
        'key',
        'title_en',
        'title_ar',
        'slug',
        'hero_badge_en',
        'hero_badge_ar',
        'hero_title_en',
        'hero_title_ar',
        'hero_subtitle_en',
        'hero_subtitle_ar',
        'hero_primary_cta_text_en',
        'hero_primary_cta_text_ar',
        'hero_primary_cta_url',
        'hero_secondary_cta_text_en',
        'hero_secondary_cta_text_ar',
        'hero_secondary_cta_url',
        'hero_image',
        'intro_title_en',
        'intro_title_ar',
        'intro_body_en',
        'intro_body_ar',
        'sections',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'is_active',
    ];

    protected $casts = [
        'sections' => 'array',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public const CORE_KEYS = ['home', 'visas', 'domestic', 'flights', 'hotels', 'about', 'contact', 'blog'];

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function isCorePage(): bool
    {
        return in_array($this->key, self::CORE_KEYS, true);
    }

    public function frontendUrl(): ?string
    {
        if (! $this->is_active || $this->trashed()) {
            return null;
        }

        return match ($this->key) {
            'home' => route('home'),
            'visas' => route('visas.index'),
            'domestic' => route('destinations.index'),
            'flights' => route('flights'),
            'hotels' => route('hotels'),
            'about' => route('about'),
            'contact' => route('contact'),
            'blog' => route('blog.index'),
            default => $this->slug ? route('pages.show', $this) : null,
        };
    }

    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::slug($base) ?: 'page';
        $candidate = $base;
        $counter = 2;

        while (static::query()
            ->withTrashed()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $candidate)
            ->exists()) {
            $candidate = $base . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
