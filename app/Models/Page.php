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

    public const CORE_KEYS = ['home', 'visas', 'domestic', 'flights', 'hotels', 'about', 'contact', 'blog', 'france-2'];

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function getOrderedSections(): array
    {
        $allDefaultSections = [
            'home' => [
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
            ],
            'visas' => [
                'hero'         => ['enabled' => true, 'sort_order' => 1,  'name_ar' => 'البانر الهيرو والتوجيه الرئيسي', 'name_en' => 'Hero Banner'],
                'search'       => ['enabled' => true, 'sort_order' => 2,  'name_ar' => 'شريط البحث والتصفية السريعة', 'name_en' => 'Search & Filter Bar'],
                'featured'     => ['enabled' => true, 'sort_order' => 3,  'name_ar' => 'شريط التأشيرات والدول البارزة', 'name_en' => 'Featured Visas Carousel'],
                'features'     => ['enabled' => true, 'sort_order' => 4,  'name_ar' => 'مميزات الخدمة (لماذا تختارنا)', 'name_en' => 'Key Features'],
                'cards'        => ['enabled' => true, 'sort_order' => 5,  'name_ar' => 'باقات وكروت الخدمة المتاحة', 'name_en' => 'Service Cards & Packages'],
                'steps'        => ['enabled' => true, 'sort_order' => 6,  'name_ar' => 'خطوات التقديم والحصول على التأشيرة', 'name_en' => 'Application Steps'],
                'grid'         => ['enabled' => true, 'sort_order' => 7,  'name_ar' => 'شبكة الخدمات والأنواع', 'name_en' => 'Services Grid'],
                'quick_info'   => ['enabled' => true, 'sort_order' => 8,  'name_ar' => 'معلومات سريعة وإحصائيات', 'name_en' => 'Quick Info'],
                'cta'          => ['enabled' => true, 'sort_order' => 9,  'name_ar' => 'شريط الدعوة للتقديم المباشر', 'name_en' => 'CTA Banner'],
                'faq'          => ['enabled' => true, 'sort_order' => 10, 'name_ar' => 'الأسئلة الشائعة والإجابات', 'name_en' => 'Frequently Asked Questions'],
            ],
            'domestic' => [
                'hero'         => ['enabled' => true, 'sort_order' => 1,  'name_ar' => 'البانر الهيرو والتوجيه الرئيسي', 'name_en' => 'Hero Banner'],
                'search'       => ['enabled' => true, 'sort_order' => 2,  'name_ar' => 'شريط البحث وتحديد الوجهة', 'name_en' => 'Destination Search Bar'],
                'featured'     => ['enabled' => true, 'sort_order' => 3,  'name_ar' => 'أبرز البرامج والوجهات السياحية', 'name_en' => 'Featured Destinations'],
                'features'     => ['enabled' => true, 'sort_order' => 4,  'name_ar' => 'مميزات برامجنا السياحية', 'name_en' => 'Features & Highlights'],
                'cards'        => ['enabled' => true, 'sort_order' => 5,  'name_ar' => 'باقات الرحلات والأنشطة', 'name_en' => 'Tour Packages & Cards'],
                'steps'        => ['enabled' => true, 'sort_order' => 6,  'name_ar' => 'خطوات الحجز والانطلاق', 'name_en' => 'Booking Steps'],
                'grid'         => ['enabled' => true, 'sort_order' => 7,  'name_ar' => 'شبكة الوجهات والأنواع', 'name_en' => 'Destinations Grid'],
                'quick_info'   => ['enabled' => true, 'sort_order' => 8,  'name_ar' => 'معلومات وتفاصيل السفر', 'name_en' => 'Travel Info'],
                'cta'          => ['enabled' => true, 'sort_order' => 9,  'name_ar' => 'شريط الحجز السريع', 'name_en' => 'Booking CTA Banner'],
                'faq'          => ['enabled' => true, 'sort_order' => 10, 'name_ar' => 'الأسئلة الشائعة عن الرحلات', 'name_en' => 'FAQs'],
            ],
            'flights' => [
                'hero'         => ['enabled' => true, 'sort_order' => 1,  'name_ar' => 'البانر الهيرو والتوجيه الرئيسي', 'name_en' => 'Hero Banner'],
                'search'       => ['enabled' => true, 'sort_order' => 2,  'name_ar' => 'شريط محرك بحث الطيران', 'name_en' => 'Flight Search Engine'],
                'featured'     => ['enabled' => true, 'sort_order' => 3,  'name_ar' => 'أشهر خطوط الطيران والوجهات', 'name_en' => 'Popular Flight Routes'],
                'features'     => ['enabled' => true, 'sort_order' => 4,  'name_ar' => 'مميزات حجز الطيران معنا', 'name_en' => 'Flight Booking Features'],
                'cards'        => ['enabled' => true, 'sort_order' => 5,  'name_ar' => 'أنواع التذاكر والدرجات', 'name_en' => 'Ticket Classes & Packages'],
                'steps'        => ['enabled' => true, 'sort_order' => 6,  'name_ar' => 'خطوات إصدار التذكرة', 'name_en' => 'Ticketing Steps'],
                'grid'         => ['enabled' => true, 'sort_order' => 7,  'name_ar' => 'شبكة الوجهات والعروض', 'name_en' => 'Routes Grid'],
                'quick_info'   => ['enabled' => true, 'sort_order' => 8,  'name_ar' => 'معلومات الوزن والأمتعة', 'name_en' => 'Baggage & Flight Info'],
                'cta'          => ['enabled' => true, 'sort_order' => 9,  'name_ar' => 'شريط طلب حجز تذكرة طيران', 'name_en' => 'Flight Request CTA'],
                'faq'          => ['enabled' => true, 'sort_order' => 10, 'name_ar' => 'الأسئلة الشائعة للطيران', 'name_en' => 'Flight FAQs'],
            ],
            'hotels' => [
                'hero'         => ['enabled' => true, 'sort_order' => 1,  'name_ar' => 'البانر الهيرو والتوجيه الرئيسي', 'name_en' => 'Hero Banner'],
                'search'       => ['enabled' => true, 'sort_order' => 2,  'name_ar' => 'شريط محرك بحث الفنادق', 'name_en' => 'Hotel Search Engine'],
                'featured'     => ['enabled' => true, 'sort_order' => 3,  'name_ar' => 'أفخم الفنادق والمنتجعات الموصى بها', 'name_en' => 'Featured Hotels & Resorts'],
                'features'     => ['enabled' => true, 'sort_order' => 4,  'name_ar' => 'مميزات حجز الإقامة معنا', 'name_en' => 'Hotel Booking Features'],
                'cards'        => ['enabled' => true, 'sort_order' => 5,  'name_ar' => 'أنواع الغرف والأجواء', 'name_en' => 'Room Types & Offers'],
                'steps'        => ['enabled' => true, 'sort_order' => 6,  'name_ar' => 'خطوات التأكيد والحجز', 'name_en' => 'Reservation Steps'],
                'grid'         => ['enabled' => true, 'sort_order' => 7,  'name_ar' => 'شبكة المدن والإقامات', 'name_en' => 'Hotels Grid'],
                'quick_info'   => ['enabled' => true, 'sort_order' => 8,  'name_ar' => 'سياسات الدخول والمغادرة', 'name_en' => 'Check-in Policies'],
                'cta'          => ['enabled' => true, 'sort_order' => 9,  'name_ar' => 'شريط طلب حجز فندق', 'name_en' => 'Hotel Booking CTA'],
                'faq'          => ['enabled' => true, 'sort_order' => 10, 'name_ar' => 'الأسئلة الشائعة للفنادق', 'name_en' => 'Hotel FAQs'],
            ],
            'about' => [
                'hero'            => ['enabled' => true, 'sort_order' => 1, 'name_ar' => 'البانر الهيرو الرئيسي (عن الشركة)', 'name_en' => 'Hero Banner'],
                'story'           => ['enabled' => true, 'sort_order' => 2, 'name_ar' => 'قصة ترافل ويف ورؤيتنا', 'name_en' => 'Our Story & Vision'],
                'mission'         => ['enabled' => true, 'sort_order' => 3, 'name_ar' => 'رسالتنا وقيمنا الجوهرية', 'name_en' => 'Mission & Core Values'],
                'why_choose'      => ['enabled' => true, 'sort_order' => 4, 'name_ar' => 'لماذا نحن الخيار الأفضل', 'name_en' => 'Why Choose Us'],
                'services'        => ['enabled' => true, 'sort_order' => 5, 'name_ar' => 'خدماتنا الشاملة للمسافرين', 'name_en' => 'Our Comprehensive Services'],
                'stats'           => ['enabled' => true, 'sort_order' => 6, 'name_ar' => 'أرقام وإحصائيات النجاح', 'name_en' => 'Success Statistics'],
                'professionalism' => ['enabled' => true, 'sort_order' => 7, 'name_ar' => 'الاحترافية والجودة في الخدمة', 'name_en' => 'Professionalism & Quality'],
                'faq'             => ['enabled' => true, 'sort_order' => 8, 'name_ar' => 'الأسئلة الشائعة والإجابات', 'name_en' => 'FAQs'],
                'cta'             => ['enabled' => true, 'sort_order' => 9, 'name_ar' => 'شريط التواصل معنا', 'name_en' => 'Contact CTA Banner'],
            ],
            'contact' => [
                'hero'         => ['enabled' => true, 'sort_order' => 1, 'name_ar' => 'البانر الهيرو الرئيسي (تواصل معنا)', 'name_en' => 'Hero Banner'],
                'contact_info' => ['enabled' => true, 'sort_order' => 2, 'name_ar' => 'معلومات الاتصال والفروع', 'name_en' => 'Contact Info & Branches'],
                'quick_help'   => ['enabled' => true, 'sort_order' => 3, 'name_ar' => 'قنوات الدعم السريع', 'name_en' => 'Quick Support Channels'],
                'faq'          => ['enabled' => true, 'sort_order' => 4, 'name_ar' => 'الأسئلة الشائعة والاستفسارات', 'name_en' => 'FAQs'],
                'cta'          => ['enabled' => true, 'sort_order' => 5, 'name_ar' => 'شريط البدء بالتقديم', 'name_en' => 'Start Now CTA'],
            ],
            'france-2' => [
                'hero'             => ['enabled' => true, 'sort_order' => 1,  'name_ar' => 'البانر الهيرو الرئيسي (فيزا فرنسا 2)', 'name_en' => 'Hero Banner'],
                'intro'            => ['enabled' => true, 'sort_order' => 2,  'name_ar' => 'نبذة بسيطة عن فيزا فرنسا', 'name_en' => 'About France Visa'],
                'requirements'     => ['enabled' => true, 'sort_order' => 3,  'name_ar' => 'المتطلبات الأساسية', 'name_en' => 'Key Requirements'],
                'steps'            => ['enabled' => true, 'sort_order' => 4,  'name_ar' => 'إجراءات التقديم ببساطة', 'name_en' => 'Application Steps'],
                'services'         => ['enabled' => true, 'sort_order' => 5,  'name_ar' => 'خدمات ترافل ويف', 'name_en' => 'Travel Wave Services'],
                'suitability'      => ['enabled' => true, 'sort_order' => 6,  'name_ar' => 'تقييم مناسبة الحالة للتقديم', 'name_en' => 'Suitability Assessment'],
                'pricing_duration' => ['enabled' => true, 'sort_order' => 7,  'name_ar' => 'مدة الإجراءات والرسوم', 'name_en' => 'Duration & Fees'],
                'faq'              => ['enabled' => true, 'sort_order' => 8,  'name_ar' => 'الأسئلة الشائعة والإجابات', 'name_en' => 'FAQs'],
                'notice'           => ['enabled' => true, 'sort_order' => 9,  'name_ar' => 'تنبيه مهم للعميل', 'name_en' => 'Important Notice'],
                'cta'              => ['enabled' => true, 'sort_order' => 10, 'name_ar' => 'شريط الدعوة للتواصل النهائي', 'name_en' => 'Final CTA Banner'],
            ],
            'default' => [
                'hero'           => ['enabled' => true, 'sort_order' => 1, 'name_ar' => 'البانر الهيرو الرئيسي', 'name_en' => 'Hero Banner'],
                'feature_blocks' => ['enabled' => true, 'sort_order' => 2, 'name_ar' => 'كوت المميزات والخدمات', 'name_en' => 'Feature Blocks'],
                'faqs'           => ['enabled' => true, 'sort_order' => 3, 'name_ar' => 'الأسئلة الشائعة', 'name_en' => 'FAQs'],
                'cta'            => ['enabled' => true, 'sort_order' => 4, 'name_ar' => 'شريط التوجيه والدعوة للعمل', 'name_en' => 'CTA Banner'],
            ],
        ];

        $pageKey = $this->key ?: 'default';
        $defaultSections = $allDefaultSections[$pageKey] ?? $allDefaultSections['default'];

        // Auto-discover any additional dynamic sections saved in $this->sections
        $sectionsData = is_array($this->sections) ? $this->sections : [];
        $sortIndex = count($defaultSections) + 1;

        foreach ($sectionsData as $sKey => $sVal) {
            if ($sKey === 'section_order' || empty($sKey)) {
                continue;
            }
            if (! isset($defaultSections[$sKey])) {
                $formattedName = Str::title(str_replace('_', ' ', $sKey));
                $defaultSections[$sKey] = [
                    'enabled' => true,
                    'sort_order' => $sortIndex,
                    'name_ar' => 'قسم: ' . $formattedName,
                    'name_en' => 'Section: ' . $formattedName,
                ];
                $sortIndex++;
            }
        }

        $saved = data_get($this->sections, 'section_order', []);
        if (empty($saved) || ! is_array($saved)) {
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

        uasort($merged, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        return $merged;
    }

    public function getOrderedHomeSections(): array
    {
        return $this->getOrderedSections();
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
            'umrah' => route('umrah'),
            'hajj' => route('hajj'),
            'france-2' => route('france-2'),
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
