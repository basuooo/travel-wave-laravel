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

    protected static function booted(): void
    {
        static::ensureTableSchema();
    }

    public static function ensureTableSchema(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('visa_categories')) {
            return;
        }

        if (
            ! \Illuminate\Support\Facades\Schema::hasColumn('visa_categories', 'deleted_at') ||
            ! \Illuminate\Support\Facades\Schema::hasColumn('visa_categories', 'deleted_by')
        ) {
            \Illuminate\Support\Facades\Schema::table('visa_categories', function (\Illuminate\Database\Schema\Blueprint $table) {
                if (! \Illuminate\Support\Facades\Schema::hasColumn('visa_categories', 'deleted_at')) {
                    $table->softDeletes();
                }
                if (! \Illuminate\Support\Facades\Schema::hasColumn('visa_categories', 'deleted_by')) {
                    $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }

        try {
            static::all()->each(function ($cat) {
                if ($cat->name_ar) {
                    $clean = preg_replace('/[أإآ]/u', 'ا', preg_replace('/[\x{064B}-\x{0652}\x{0670}]/u', '', $cat->name_ar));
                    if ($clean !== $cat->name_ar) {
                        $cat->update(['name_ar' => $clean]);
                    }
                }
            });

            // Force delete category Europe (slug: europe)
            $europeCats = static::withTrashed()->where(function ($q) {
                $q->where('slug', 'europe')
                    ->orWhere('name_ar', 'اوروبا')
                    ->orWhere('name_ar', 'أوروبا');
            })->get();

            foreach ($europeCats as $europeCat) {
                \Illuminate\Support\Facades\DB::table('country_visa_category')->where('visa_category_id', $europeCat->id)->delete();
                $europeCat->forceDelete();
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

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

    public function pivotCountries()
    {
        return $this->belongsToMany(VisaCountry::class, 'country_visa_category');
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
