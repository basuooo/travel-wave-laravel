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

    public function getOrderedHomeSections(): array
    {
        $defaultSections = [
            'hero_slider'           => ['enabled' => true, 'sort_order' => 1,  'name_ar' => 'البانر والسلايدر الرئيسي', 'name_en' => 'Hero Banner & Slider'],
            'why_choose_travel_wave'=> ['enabled' => true, 'sort_order' => 2,  'name_ar' => 'مميزات ترافل ويف', 'name_en' => 'Why Choose Travel Wave'],
            'search_box'            => ['enabled' => true, 'sort_order' => 3,  'name_ar' => 'شريط البحث وتصفح الخدمات', 'name_en' => 'Services Search Bar'],
            'country_strip'         => ['enabled' => true, 'sort_order' => 4,  'name_ar' => 'شريط التأشيرات المتميزة', 'name_en' => 'Featured Visas Strip'],
            'services'              => ['enabled' => true, 'sort_order' => 5,  'name_ar' => 'خدمات الصفحة الرئيسية', 'name_en' => 'Homepage Services'],
            'popular_destinations'  => ['enabled' => true, 'sort_order' => 6,  'name_ar' => 'أبرز التأشيرات والوجهات الموصى بها', 'name_en' => 'Popular Visas & Destinations'],
            'featured_categories'   => ['enabled' => true, 'sort_order' => 7,  'name_ar' => 'أقسام وتصنيفات التأشيرات', 'name_en' => 'Visa Categories'],
            'why_choose_us'         => ['enabled' => true, 'sort_order' => 8,  'name_ar' => 'لماذا ترافل ويف وكيف نعمل', 'name_en' => 'Why Us & How It Works'],
            'featured_destinations' => ['enabled' => true, 'sort_order' => 9,  'name_ar' => 'السياحة الداخلية والبرامج', 'name_en' => 'Domestic Tourism Destinations'],
            'promo'                 => ['enabled' => true, 'sort_order' => 10, 'name_ar' => 'شريط العروض والترويج', 'name_en' => 'Promo Banner'],
            'testimonials'          => ['enabled' => true, 'sort_order' => 11, 'name_ar' => 'آراء العملاء والتقييمات', 'name_en' => 'Testimonials'],
            'blog'                  => ['enabled' => true, 'sort_order' => 12, 'name_ar' => 'أحدث المقالات والنصائح', 'name_en' => 'Latest Articles / Blog'],
            'final_cta'             => ['enabled' => true, 'sort_order' => 13, 'name_ar' => 'شريط الدعوة للتواصل النهائي', 'name_en' => 'Final CTA Banner'],
        ];

        $saved = data_get($this->sections, 'section_order', []);
        if (empty($saved) || !is_array($saved)) {
            return $defaultSections;
        }

        $merged = [];
        $i = 1;
        foreach ($defaultSections as $key => $meta) {
            $savedItem = $saved[$key] ?? [];
            $merged[$key] = [
                'enabled' => array_key_exists('enabled', $savedItem) ? (bool) $savedItem['enabled'] : $meta['enabled'],
                'sort_order' => (int) ($savedItem['sort_order'] ?? $i),
                'name_ar' => $meta['name_ar'],
                'name_en' => $meta['name_en'],
            ];
            $i++;
        }

        uasort($merged, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        return $merged;
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
