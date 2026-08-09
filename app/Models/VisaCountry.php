<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VisaCountry extends Model
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
        if (! Schema::hasTable('visa_countries')) {
            return;
        }

        if (
            ! Schema::hasColumn('visa_countries', 'content_mode') ||
            ! Schema::hasColumn('visa_countries', 'html_content_en') ||
            ! Schema::hasColumn('visa_countries', 'html_content_ar') ||
            ! Schema::hasColumn('visa_countries', 'sections')
        ) {
            Schema::table('visa_countries', function (Blueprint $table) {
                if (! Schema::hasColumn('visa_countries', 'content_mode')) {
                    $table->string('content_mode', 20)->default('normal')->after('is_active');
                }
                if (! Schema::hasColumn('visa_countries', 'html_content_en')) {
                    $table->longText('html_content_en')->nullable()->after('content_mode');
                }
                if (! Schema::hasColumn('visa_countries', 'html_content_ar')) {
                    $table->longText('html_content_ar')->nullable()->after('html_content_en');
                }
                if (! Schema::hasColumn('visa_countries', 'sections')) {
                    $table->json('sections')->nullable()->after('html_content_ar');
                }
            });
        }
    }

    protected $fillable = [
        'visa_category_id',
        'name_en',
        'name_ar',
        'slug',
        'excerpt_en',
        'excerpt_ar',
        'hero_badge_en',
        'hero_badge_ar',
        'hero_title_en',
        'hero_title_ar',
        'hero_subtitle_en',
        'hero_subtitle_ar',
        'hero_cta_text_en',
        'hero_cta_text_ar',
        'hero_cta_url',
        'hero_overlay_opacity',
        'hero_image',
        'hero_mobile_image',
        'flag_image',
        'overview_en',
        'overview_ar',
        'visa_type_en',
        'visa_type_ar',
        'stay_duration_en',
        'stay_duration_ar',
        'quick_summary_destination_label_en',
        'quick_summary_destination_label_ar',
        'quick_summary_destination_icon',
        'quick_summary_items',
        'intro_image',
        'introduction_title_en',
        'introduction_title_ar',
        'introduction_badge_en',
        'introduction_badge_ar',
        'introduction_points',
        'detailed_title_en',
        'detailed_title_ar',
        'detailed_description_en',
        'detailed_description_ar',
        'best_time_badge_en',
        'best_time_badge_ar',
        'best_time_title_en',
        'best_time_title_ar',
        'best_time_description_en',
        'best_time_description_ar',
        'highlights_section_label_en',
        'highlights_section_label_ar',
        'highlights_section_title_en',
        'highlights_section_title_ar',
        'highlights',
        'required_documents',
        'application_steps',
        'services',
        'why_choose_title_en',
        'why_choose_title_ar',
        'why_choose_intro_en',
        'why_choose_intro_ar',
        'why_choose_items',
        'documents_title_en',
        'documents_title_ar',
        'documents_subtitle_en',
        'documents_subtitle_ar',
        'document_items',
        'steps_title_en',
        'steps_title_ar',
        'step_items',
        'processing_time_en',
        'processing_time_ar',
        'fees_en',
        'fees_ar',
        'fees_title_en',
        'fees_title_ar',
        'fee_items',
        'fees_notes_en',
        'fees_notes_ar',
        'faqs',
        'faq_title_en',
        'faq_title_ar',
        'support_title_en',
        'support_title_ar',
        'support_subtitle_en',
        'support_subtitle_ar',
        'support_button_en',
        'support_button_ar',
        'support_button_link',
        'support_is_active',
        'map_title_en',
        'map_title_ar',
        'map_description_en',
        'map_description_ar',
        'map_embed_code',
        'map_is_active',
        'inquiry_form_title_en',
        'inquiry_form_title_ar',
        'inquiry_form_subtitle_en',
        'inquiry_form_subtitle_ar',
        'inquiry_form_button_en',
        'inquiry_form_button_ar',
        'inquiry_form_success_en',
        'inquiry_form_success_ar',
        'inquiry_form_default_service_type',
        'inquiry_form_visible_fields',
        'inquiry_form_is_active',
        'inquiry_form_label_en',
        'inquiry_form_label_ar',
        'cta_title_en',
        'cta_title_ar',
        'cta_text_en',
        'cta_text_ar',
        'cta_button_en',
        'cta_button_ar',
        'cta_url',
        'final_cta_background_image',
        'final_cta_is_active',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'og_image',
        'is_featured',
        'is_active',
        'sort_order',
        'content_mode',
        'html_content_en',
        'html_content_ar',
        'sections',
    ];

    protected $casts = [
        'sections' => 'array',
        'highlights' => 'array',
        'required_documents' => 'array',
        'application_steps' => 'array',
        'services' => 'array',
        'quick_summary_items' => 'array',
        'introduction_points' => 'array',
        'why_choose_items' => 'array',
        'document_items' => 'array',
        'step_items' => 'array',
        'fee_items' => 'array',
        'faqs' => 'array',
        'inquiry_form_visible_fields' => 'array',
        'hero_overlay_opacity' => 'decimal:2',
        'map_is_active' => 'boolean',
        'inquiry_form_is_active' => 'boolean',
        'support_is_active' => 'boolean',
        'final_cta_is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VisaCategory::class, 'visa_category_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function frontendUrl(): ?string
    {
        return ($this->is_active && ! $this->trashed()) ? route('visas.country', $this) : null;
    }

    public function getOrderedSections(): array
    {
        $allDefaultSections = [
            'hero'             => ['enabled' => true, 'sort_order' => 1,  'name_ar' => 'البانر الهيرو الرئيسي', 'name_en' => 'Hero Banner'],
            'quick_summary'    => ['enabled' => true, 'sort_order' => 2,  'name_ar' => 'ملخص سريع عن فيزا فرنسا', 'name_en' => 'Quick Summary'],
            'intro'            => ['enabled' => true, 'sort_order' => 3,  'name_ar' => 'نبذة عن فيزا فرنسا', 'name_en' => 'About France Visa'],
            'best_time'        => ['enabled' => true, 'sort_order' => 4,  'name_ar' => 'متى تبدأ إجراءات فيزا فرنسا؟', 'name_en' => 'Best Time to Apply'],
            'requirements'     => ['enabled' => true, 'sort_order' => 5,  'name_ar' => 'المتطلبات الأساسية', 'name_en' => 'Key Requirements'],
            'services'         => ['enabled' => true, 'sort_order' => 6,  'name_ar' => 'خدمات ترافل ويف', 'name_en' => 'Travel Wave Services'],
            'steps'            => ['enabled' => true, 'sort_order' => 7,  'name_ar' => 'خطوات التقديم الـ 5', 'name_en' => 'Application Steps'],
            'why_choose'      => ['enabled' => true, 'sort_order' => 8,  'name_ar' => 'ليه تختار ترافل ويف؟', 'name_en' => 'Why Travel Wave'],
            'suitability'      => ['enabled' => true, 'sort_order' => 9,  'name_ar' => 'تقييم مناسبة الحالة للتقديم', 'name_en' => 'Suitability Assessment'],
            'pricing_duration' => ['enabled' => true, 'sort_order' => 10, 'name_ar' => 'الرسوم ومدة الإجراءات', 'name_en' => 'Duration & Fees'],
            'faq'              => ['enabled' => true, 'sort_order' => 11, 'name_ar' => 'الأسئلة الشائعة والإجابات', 'name_en' => 'FAQs'],
            'notice'           => ['enabled' => true, 'sort_order' => 12, 'name_ar' => 'تنبيه مهم للعميل', 'name_en' => 'Important Notice'],
            'cta'              => ['enabled' => true, 'sort_order' => 13, 'name_ar' => 'شريط الدعوة للتواصل النهائي', 'name_en' => 'Final CTA Banner'],
        ];

        $savedOrder = data_get($this->sections, 'section_order', []);
        $ordered = [];

        foreach ($allDefaultSections as $key => $default) {
            $userMeta = $savedOrder[$key] ?? [];
            $enabled = array_key_exists('enabled', $userMeta) ? (bool) $userMeta['enabled'] : (bool) $default['enabled'];
            $sortOrder = array_key_exists('sort_order', $userMeta) ? (int) $userMeta['sort_order'] : (int) $default['sort_order'];

            $ordered[$key] = array_merge($default, [
                'enabled' => $enabled,
                'sort_order' => $sortOrder,
            ]);
        }

        uasort($ordered, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        return $ordered;
    }

    public function displayFlagUrl(): ?string
    {
        if (! $this->flag_image) {
            return null;
        }

        if (str_starts_with($this->flag_image, 'http://') || str_starts_with($this->flag_image, 'https://') || str_starts_with($this->flag_image, 'data:')) {
            return $this->flag_image;
        }

        return asset('storage/' . $this->flag_image);
    }

    public function buildSectionsFromModel(): array
    {
        if (filled($this->sections) && is_array($this->sections) && ! empty(data_get($this->sections, 'hero'))) {
            return $this->sections;
        }

        return [
            'section_order' => [
                'hero' => ['sort_order' => 1, 'enabled' => true],
                'quick_summary' => ['sort_order' => 2, 'enabled' => true],
                'intro' => ['sort_order' => 3, 'enabled' => true],
                'best_time' => ['sort_order' => 4, 'enabled' => true],
                'requirements' => ['sort_order' => 5, 'enabled' => true],
                'services' => ['sort_order' => 6, 'enabled' => true],
                'steps' => ['sort_order' => 7, 'enabled' => true],
                'why_choose' => ['sort_order' => 8, 'enabled' => true],
                'suitability' => ['sort_order' => 9, 'enabled' => true],
                'pricing_duration' => ['sort_order' => 10, 'enabled' => true],
                'faq' => ['sort_order' => 11, 'enabled' => true],
                'notice' => ['sort_order' => 12, 'enabled' => true],
                'cta' => ['sort_order' => 13, 'enabled' => true],
            ],

            'hero' => [
                'eyebrow_en' => $this->hero_badge_en ?: 'Visa Destination',
                'eyebrow_ar' => $this->hero_badge_ar ?: 'وجهة تأشيرة',
                'title_en' => $this->hero_title_en ?: $this->name_en,
                'title_ar' => $this->hero_title_ar ?: $this->name_ar,
                'subtitle_en' => $this->hero_subtitle_en ?: $this->excerpt_en,
                'subtitle_ar' => $this->hero_subtitle_ar ?: $this->excerpt_ar,
                'primary_cta_text_en' => $this->hero_cta_text_en ?: 'Assess Your Case Now',
                'primary_cta_text_ar' => $this->hero_cta_text_ar ?: 'قيّم حالتك الآن',
                'primary_cta_url' => $this->hero_cta_url ?: '/contact#lead-form',
            ],

            'quick_summary' => [
                'title_en' => 'Quick Summary',
                'title_ar' => 'ملخص سريع عن الفيزا',
                'items' => is_array($this->quick_summary_items) && ! empty($this->quick_summary_items)
                    ? $this->quick_summary_items
                    : [
                        ['label_ar' => 'الدولة', 'label_en' => 'Country', 'value_ar' => $this->name_ar, 'value_en' => $this->name_en, 'icon' => '🌍', 'is_active' => true, 'sort_order' => 1],
                        ['label_ar' => 'نوع التأشيرة', 'label_en' => 'Visa Type', 'value_ar' => $this->visa_type_ar ?: 'تأشيرة دخول', 'value_en' => $this->visa_type_en ?: 'Entry Visa', 'icon' => '📄', 'is_active' => true, 'sort_order' => 2],
                        ['label_ar' => 'مدة الإقامة', 'label_en' => 'Stay Duration', 'value_ar' => $this->stay_duration_ar ?: 'حسب الشروط', 'value_en' => $this->stay_duration_en ?: 'Per terms', 'icon' => '📅', 'is_active' => true, 'sort_order' => 3],
                        ['label_ar' => 'مدة الإجراءات', 'label_en' => 'Processing Time', 'value_ar' => $this->processing_time_ar ?: 'حسب الحالة', 'value_en' => $this->processing_time_en ?: 'Varies by case', 'icon' => '⏱️', 'is_active' => true, 'sort_order' => 4],
                    ],
            ],

            'intro' => [
                'title_en' => $this->introduction_title_en ?: 'About Visa',
                'title_ar' => $this->introduction_title_ar ?: 'نبذة عن التأشيرة',
                'description_en' => $this->overview_en ?: $this->excerpt_en,
                'description_ar' => $this->overview_ar ?: $this->excerpt_ar,
            ],

            'best_time' => [
                'title_en' => $this->best_time_title_en ?: 'Best Time to Apply',
                'title_ar' => $this->best_time_title_ar ?: 'متى تبدأ الإجراءات؟',
                'text_en' => $this->best_time_description_en ?: 'It is recommended to apply early before travel date.',
                'text_ar' => $this->best_time_description_ar ?: 'يفضل البدء في إجراءات التأشيرة مبكراً قبل موعد السفر بوقت كافٍ.',
                'note_en' => 'Early preparation allows sufficient buffer to secure appointments.',
                'note_ar' => 'التجهيز المبكر يمنحك وقتاً كافياً لاستكمال الملف وحجز الموعد المناسب.',
            ],

            'requirements' => [
                'title_en' => $this->documents_title_en ?: 'Key Requirements',
                'title_ar' => $this->documents_title_ar ?: 'المتطلبات الأساسية',
                'note_en' => $this->documents_subtitle_en ?: 'Requirements depend on applicant status.',
                'note_ar' => $this->documents_subtitle_ar ?: 'تختلف المستندات المطلوبة حسب حالة كل متقدم.',
                'items' => is_array($this->document_items) ? $this->document_items : [],
            ],

            'services' => [
                'title_en' => 'Travel Wave Services',
                'title_ar' => 'خدمات ترافل ويف',
                'items' => is_array($this->services) ? $this->services : [],
            ],

            'steps' => [
                'title_en' => $this->steps_title_en ?: 'Application Steps',
                'title_ar' => $this->steps_title_ar ?: 'خطوات التقديم',
                'items' => is_array($this->step_items) ? $this->step_items : [],
            ],

            'why_choose' => [
                'title_en' => $this->why_choose_title_en ?: 'Why Choose Travel Wave?',
                'title_ar' => $this->why_choose_title_ar ?: 'ليه تختار ترافل ويف؟',
                'items' => is_array($this->why_choose_items) ? $this->why_choose_items : [],
            ],

            'suitability' => [
                'title_en' => 'Suitability Assessment',
                'title_ar' => 'تقييم مناسبة الحالة للتقديم',
                'description_en' => 'Reach out to Travel Wave team to assess your profile.',
                'description_ar' => 'تواصل معنا وفريق ترافل ويف يراجع حالتك ويوضح لك التقييم المباشر.',
                'button_text_en' => 'Assess Your Case Now',
                'button_text_ar' => 'قيّم حالتك الآن',
                'button_url' => '/contact#lead-form',
            ],

            'pricing_duration' => [
                'duration_title_en' => 'Processing Time',
                'duration_title_ar' => 'مدة الإجراءات',
                'duration_text_en' => $this->processing_time_en ?: 'Varies by case and appointment availability.',
                'duration_text_ar' => $this->processing_time_ar ?: 'تختلف حسب الحالة ومواعيد التقديم المتاحة.',
                'fees_title_en' => $this->fees_title_en ?: 'Fees',
                'fees_title_ar' => $this->fees_title_ar ?: 'الرسوم',
                'fees_text_en' => $this->fees_en ?: 'Varies by service requested.',
                'fees_text_ar' => $this->fees_ar ?: 'تختلف حسب نوع الخدمة المطلوبة.',
            ],

            'faq' => [
                'title_en' => $this->faq_title_en ?: 'Frequently Asked Questions',
                'title_ar' => $this->faq_title_ar ?: 'الأسئلة الشائعة والإجابات',
                'items' => is_array($this->faqs) ? $this->faqs : [],
            ],

            'notice' => [
                'title_en' => 'Important Notice',
                'title_ar' => 'تنبيه مهم للعميل',
                'text_en' => 'Travel Wave assists with file preparation. Final decisions rest solely with official authorities.',
                'text_ar' => 'ترافل ويف تساعدك في تجهيز ومراجعة ملف التأشيرة، والقرار النهائي بقبول أو رفض الطلب يكون من الجهة الرسمية المختصة.',
            ],

            'cta' => [
                'title_en' => $this->cta_title_en ?: 'Ready to Start?',
                'title_ar' => $this->cta_title_ar ?: 'جاهز تبدأ إجراءات التأشيرة؟',
                'description_en' => $this->cta_text_en ?: 'Leave your details and our team will get in touch.',
                'description_ar' => $this->cta_text_ar ?: 'سيب بياناتك وفريق ترافل ويف هيتواصل معاك ويوضح لك الخطوات والمستندات المناسبة.',
                'button_text_en' => $this->cta_button_en ?: 'Contact Us Now',
                'button_text_ar' => $this->cta_button_ar ?: 'تواصل معنا الآن',
                'button_url' => $this->cta_url ?: '/contact#lead-form',
            ],
        ];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: $this->getRouteKeyName();

        return $this->where($field, $value)->first();
    }

    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::slug($base) ?: 'visa-country';
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

    public function repeaterValue(array $item, string $field, ?string $locale = null, mixed $fallback = ''): mixed
    {
        $locale = $locale ?: app()->getLocale();

        return $item["{$field}_{$locale}"]
            ?? $item["{$field}_" . config('app.fallback_locale', 'en')]
            ?? $item[$field]
            ?? $fallback;
    }
}
