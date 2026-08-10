<?php

namespace App\Models\PopupManager;

use App\Models\LeadForm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Popup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'popups';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'is_active',
        'priority',
        'layout',
        'size_settings',
        'overlay_settings',
        'close_button_settings',
        'animation_settings',
        'trigger_settings',
        'condition_settings',
        'frequency_settings',
        'schedule_settings',
        'structure',
        'assigned_lead_form_id',
        'tracking_settings',
        'custom_css',
        'custom_js',
        'views_count',
        'clicks_count',
        'conversions_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'size_settings' => 'array',
        'overlay_settings' => 'array',
        'close_button_settings' => 'array',
        'animation_settings' => 'array',
        'trigger_settings' => 'array',
        'condition_settings' => 'array',
        'frequency_settings' => 'array',
        'schedule_settings' => 'array',
        'structure' => 'array',
        'tracking_settings' => 'array',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
        'conversions_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::ensureTableSchema();
    }

    public static function ensureTableSchema(): void
    {
        if (Schema::hasTable('popups')) {
            return;
        }

        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default(self::STATUS_DRAFT);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->string('layout')->default('center');
            $table->json('size_settings')->nullable();
            $table->json('overlay_settings')->nullable();
            $table->json('close_button_settings')->nullable();
            $table->json('animation_settings')->nullable();
            $table->json('trigger_settings')->nullable();
            $table->json('condition_settings')->nullable();
            $table->json('frequency_settings')->nullable();
            $table->json('schedule_settings')->nullable();
            $table->json('structure')->nullable();
            $table->foreignId('assigned_lead_form_id')->nullable()->constrained('lead_forms')->nullOnDelete();
            $table->json('tracking_settings')->nullable();
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->unsignedBigInteger('conversions_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public static function makeUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        if (empty($base)) {
            $base = 'popup-' . Str::random(5);
        }

        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function assignedForm(): BelongsTo
    {
        return $this->belongsTo(LeadForm::class, 'assigned_lead_form_id');
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(PopupAnalytic::class, 'popup_id');
    }
}
