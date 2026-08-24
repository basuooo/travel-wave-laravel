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

            // Ensure EU category exists
            $euCat = static::withTrashed()->where('slug', 'eu')->first()
                ?? static::withTrashed()->where('name_ar', 'like', '%الاتحاد%')->first();

            if (! $euCat) {
                $euCat = static::create([
                    'name_ar' => 'الاتحاد الاوروبي (European Union)',
                    'name_en' => 'European Union',
                    'slug' => 'eu',
                    'sort_order' => 1,
                    'is_active' => true,
                ]);
            } elseif ($euCat->trashed()) {
                $euCat->restore();
            }

            // Ensure all 27 EU countries exist and are attached to EU category
            $euCountries = [
                ['name_ar' => 'فرنسا', 'name_en' => 'France', 'slug' => 'france'],
                ['name_ar' => 'بلجيكا', 'name_en' => 'Belgium', 'slug' => 'belgium'],
                ['name_ar' => 'ايطاليا', 'name_en' => 'Italy', 'slug' => 'italy'],
                ['name_ar' => 'المانيا', 'name_en' => 'Germany', 'slug' => 'germany'],
                ['name_ar' => 'اليونان', 'name_en' => 'Greece', 'slug' => 'greece'],
                ['name_ar' => 'هولندا', 'name_en' => 'Netherlands', 'slug' => 'netherlands'],
                ['name_ar' => 'النمسا', 'name_en' => 'Austria', 'slug' => 'austria'],
                ['name_ar' => 'جمهورية التشيك', 'name_en' => 'Czech Republic', 'slug' => 'czech-republic'],
                ['name_ar' => 'الدنمارك', 'name_en' => 'Denmark', 'slug' => 'denmark'],
                ['name_ar' => 'المجر', 'name_en' => 'Hungary', 'slug' => 'hungary'],
                ['name_ar' => 'بولندا', 'name_en' => 'Poland', 'slug' => 'poland'],
                ['name_ar' => 'البرتغال', 'name_en' => 'Portugal', 'slug' => 'portugal'],
                ['name_ar' => 'السويد', 'name_en' => 'Sweden', 'slug' => 'sweden'],
                ['name_ar' => 'كرواتيا', 'name_en' => 'Croatia', 'slug' => 'croatia'],
                ['name_ar' => 'قبرص', 'name_en' => 'Cyprus', 'slug' => 'cyprus'],
                ['name_ar' => 'فنلندا', 'name_en' => 'Finland', 'slug' => 'finland'],
                ['name_ar' => 'مالطا', 'name_en' => 'Malta', 'slug' => 'malta'],
                ['name_ar' => 'رومانيا', 'name_en' => 'Romania', 'slug' => 'romania'],
                ['name_ar' => 'بلغاريا', 'name_en' => 'Bulgaria', 'slug' => 'bulgaria'],
                ['name_ar' => 'استونيا', 'name_en' => 'Estonia', 'slug' => 'estonia'],
                ['name_ar' => 'لاتفيا', 'name_en' => 'Latvia', 'slug' => 'latvia'],
                ['name_ar' => 'ليتوانيا', 'name_en' => 'Lithuania', 'slug' => 'lithuania'],
                ['name_ar' => 'لوكسمبورغ', 'name_en' => 'Luxembourg', 'slug' => 'luxembourg'],
                ['name_ar' => 'سلوفاكيا', 'name_en' => 'Slovakia', 'slug' => 'slovakia'],
                ['name_ar' => 'سلوفينيا', 'name_en' => 'Slovenia', 'slug' => 'slovenia'],
                ['name_ar' => 'ايرلندا', 'name_en' => 'Ireland', 'slug' => 'ireland'],
                ['name_ar' => 'اسبانيا', 'name_en' => 'Spain', 'slug' => 'spain'],
            ];

            foreach ($euCountries as $item) {
                $c = \App\Models\VisaCountry::withTrashed()->where('slug', $item['slug'])->first()
                    ?? \App\Models\VisaCountry::withTrashed()->where('name_en', $item['name_en'])->first();

                if (! $c) {
                    $c = \App\Models\VisaCountry::create([
                        'visa_category_id' => $euCat->id,
                        'name_ar' => $item['name_ar'],
                        'name_en' => $item['name_en'],
                        'slug' => $item['slug'],
                        'is_active' => true,
                    ]);
                } elseif ($c->trashed()) {
                    $c->restore();
                }

                try {
                    $c->categories()->syncWithoutDetaching([$euCat->id]);
                } catch (\Throwable $ex) {}
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
