<?php

namespace App\Models\LandingPageNew;

use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\User;
use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LpNewLandingPage extends Model
{
    use HasFactory;
    use HasLocalizedContent;
    use SoftDeletes;

    protected $table = 'lp_new_landing_pages';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'brand_id',
        'assigned_lead_form_id',
        'header_mode',
        'custom_header_structure',
        'footer_mode',
        'custom_footer_structure',
        'status',
        'is_active',
        'slug',
        'internal_name',
        'title_en',
        'title_ar',
        'campaign_name',
        'ad_platform',
        'campaign_type',
        'traffic_source',
        'publish_at',
        'unpublish_at',
        'tracking_mode',
        'tracking_integration_ids',
        'custom_tracking_code',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
        'og_image',
        'canonical_url',
        'robots_meta',
        'schema_json',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'custom_html_head',
        'custom_css',
        'custom_js',
        'structure',
        'dependency_libraries',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_at' => 'datetime',
        'unpublish_at' => 'datetime',
        'tracking_integration_ids' => 'array',
        'schema_json' => 'array',
        'dependency_libraries' => 'array',
        'deleted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::ensureTableSchema();
    }

    public static function ensureTableSchema(): void
    {
        if (Schema::hasTable('lp_new_landing_pages')) {
            return;
        }

        // Run full migrations array safely if tables don't exist yet
        if (! Schema::hasTable('lp_new_brands')) {
            Schema::create('lp_new_brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('logo_path')->nullable();
                $table->string('favicon_path')->nullable();
                $table->string('primary_color', 50)->nullable()->default('#1e3a8a');
                $table->string('secondary_color', 50)->nullable()->default('#0284c7');
                $table->json('header_settings')->nullable();
                $table->json('footer_settings')->nullable();
                $table->json('default_tracking')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_brand_domains')) {
            Schema::create('lp_new_brand_domains', function (Blueprint $table) {
                $table->id();
                $table->foreignId('brand_id')->constrained('lp_new_brands')->cascadeOnDelete();
                $table->string('domain')->unique();
                $table->boolean('is_primary')->default(false);
                $table->boolean('is_verified')->default(true);
                $table->string('ssl_status', 50)->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_template_categories')) {
            Schema::create('lp_new_template_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->string('icon', 100)->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_templates')) {
            Schema::create('lp_new_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('brand_id')->nullable()->constrained('lp_new_brands')->nullOnDelete();
                $table->foreignId('template_category_id')->nullable()->constrained('lp_new_template_categories')->nullOnDelete();
                $table->string('name_en');
                $table->string('name_ar');
                $table->text('description_en')->nullable();
                $table->text('description_ar')->nullable();
                $table->string('slug')->unique();
                $table->string('preview_image')->nullable();
                $table->longText('structure')->nullable();
                $table->json('settings')->nullable();
                $table->string('package_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_global')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_section_categories')) {
            Schema::create('lp_new_section_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->string('icon', 100)->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_sections')) {
            Schema::create('lp_new_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('section_category_id')->nullable()->constrained('lp_new_section_categories')->nullOnDelete();
                $table->string('name_en');
                $table->string('name_ar');
                $table->text('description_en')->nullable();
                $table->text('description_ar')->nullable();
                $table->string('preview_image')->nullable();
                $table->longText('structure')->nullable();
                $table->longText('custom_html')->nullable();
                $table->longText('custom_css')->nullable();
                $table->longText('custom_js')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_global')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_global_components')) {
            Schema::create('lp_new_global_components', function (Blueprint $table) {
                $table->id();
                $table->foreignId('brand_id')->nullable()->constrained('lp_new_brands')->nullOnDelete();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('component_type', 100);
                $table->longText('structure')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_landing_pages')) {
            Schema::create('lp_new_landing_pages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('brand_id')->nullable()->constrained('lp_new_brands')->nullOnDelete();
                $table->foreignId('assigned_lead_form_id')->nullable()->constrained('lead_forms')->nullOnDelete();
                $table->string('header_mode', 50)->default('website');
                $table->longText('custom_header_structure')->nullable();
                $table->string('footer_mode', 50)->default('website');
                $table->longText('custom_footer_structure')->nullable();

                $table->string('status', 50)->default('draft');
                $table->boolean('is_active')->default(true);
                $table->string('slug')->unique();
                $table->string('internal_name');
                $table->string('title_en')->nullable();
                $table->string('title_ar')->nullable();

                $table->string('campaign_name')->nullable();
                $table->string('ad_platform', 100)->nullable();
                $table->string('campaign_type', 100)->nullable();
                $table->string('traffic_source')->nullable();

                $table->dateTime('publish_at')->nullable();
                $table->dateTime('unpublish_at')->nullable();

                $table->string('tracking_mode', 50)->default('brand');
                $table->json('tracking_integration_ids')->nullable();
                $table->text('custom_tracking_code')->nullable();

                $table->string('seo_title_en')->nullable();
                $table->string('seo_title_ar')->nullable();
                $table->text('seo_description_en')->nullable();
                $table->text('seo_description_ar')->nullable();
                $table->string('og_image')->nullable();
                $table->string('canonical_url', 1000)->nullable();
                $table->string('robots_meta', 100)->default('index, follow');
                $table->json('schema_json')->nullable();

                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_content')->nullable();
                $table->string('utm_term')->nullable();

                $table->longText('custom_html_head')->nullable();
                $table->longText('custom_css')->nullable();
                $table->longText('custom_js')->nullable();

                $table->longText('structure')->nullable();
                $table->json('dependency_libraries')->nullable();

                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('lp_new_page_versions')) {
            Schema::create('lp_new_page_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('landing_page_id')->constrained('lp_new_landing_pages')->cascadeOnDelete();
                $table->integer('version_number');
                $table->string('label')->nullable();
                $table->longText('structure')->nullable();
                $table->longText('custom_html_head')->nullable();
                $table->longText('custom_css')->nullable();
                $table->longText('custom_js')->nullable();
                $table->json('settings')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_assets')) {
            Schema::create('lp_new_assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('landing_page_id')->constrained('lp_new_landing_pages')->cascadeOnDelete();
                $table->string('filename');
                $table->string('original_path');
                $table->string('storage_path');
                $table->string('mime_type', 100)->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('asset_type', 50)->default('image');
                $table->integer('usage_count')->default(1);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_experiments')) {
            Schema::create('lp_new_experiments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('landing_page_id')->constrained('lp_new_landing_pages')->cascadeOnDelete();
                $table->string('name');
                $table->string('status', 50)->default('draft');
                $table->json('traffic_split_json')->nullable();
                $table->dateTime('started_at')->nullable();
                $table->dateTime('ended_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_page_variants')) {
            Schema::create('lp_new_page_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('experiment_id')->constrained('lp_new_experiments')->cascadeOnDelete();
                $table->foreignId('landing_page_id')->constrained('lp_new_landing_pages')->cascadeOnDelete();
                $table->string('variant_letter', 10);
                $table->string('name');
                $table->integer('traffic_weight')->default(50);
                $table->longText('structure')->nullable();
                $table->boolean('is_control')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lp_new_activity_logs')) {
            Schema::create('lp_new_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('landing_page_id')->nullable()->constrained('lp_new_landing_pages')->cascadeOnDelete();
                $table->string('action', 100);
                $table->string('entity_type', 100)->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->json('before_state')->nullable();
                $table->json('after_state')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function assignedForm(): BelongsTo
    {
        return $this->belongsTo(LeadForm::class, 'assigned_lead_form_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(LpNewPageVersion::class, 'landing_page_id')->orderByDesc('version_number');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(LpNewAsset::class, 'landing_page_id');
    }

    public function experiments(): HasMany
    {
        return $this->hasMany(LpNewExperiment::class, 'landing_page_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(LpNewActivityLog::class, 'landing_page_id')->orderByDesc('id');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->is_active
            && (! $this->publish_at || $this->publish_at->isPast())
            && (! $this->unpublish_at || $this->unpublish_at->isFuture());
    }

    public function publicUrl(): string
    {
        return route('landing-pages-new.public.show', $this->slug);
    }

    public static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::slug($base) ?: 'landing-page';
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
