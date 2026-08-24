<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmbassyAppointment extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE_NOW = 'available_now';
    public const STATUS_AVAILABLE_LATER = 'available_later';
    public const STATUS_NO_AVAILABILITY = 'no_availability';
    public const STATUS_UNKNOWN = 'unknown';

    protected static function booted(): void
    {
        static::ensureTableSchema();
    }

    public static function ensureTableSchema(): void
    {
        try {
            if (! Schema::hasTable('embassy_appointments')) {
                Schema::create('embassy_appointments', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('visa_country_id')->constrained('visa_countries')->cascadeOnDelete();
                    $table->foreignId('visa_record_id')->nullable()->constrained('visa_records')->nullOnDelete();
                    $table->string('visa_type')->default('سياحة');
                    $table->string('appointment_center')->default('BLS');
                    $table->string('appointment_type')->default('Regular');
                    $table->string('status', 30)->default('unknown');
                    $table->string('earliest_date', 255)->nullable();
                    $table->timestamp('last_updated_at')->nullable();
                    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                    $table->text('notes')->nullable();
                    $table->text('booking_link')->nullable();
                    $table->timestamps();

                    $table->unique(['visa_country_id', 'visa_type', 'appointment_center', 'appointment_type'], 'embassy_appts_unique_combo');
                });
            }

            if (! Schema::hasTable('embassy_availability_events')) {
                Schema::create('embassy_availability_events', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('embassy_appointment_id')->constrained('embassy_appointments')->cascadeOnDelete();
                    $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
                    $table->string('status', 30)->default('active');
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            }

            if (! Schema::hasTable('embassy_appointment_notifications')) {
                Schema::create('embassy_appointment_notifications', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('embassy_availability_event_id')->constrained('embassy_availability_events')->cascadeOnDelete();
                    $table->foreignId('embassy_appointment_id')->constrained('embassy_appointments')->cascadeOnDelete();
                    $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
                    $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                    $table->string('status', 30)->default('pending');
                    $table->timestamp('snoozed_until')->nullable();
                    $table->timestamp('contacted_at')->nullable();
                    $table->string('contact_result', 50)->nullable();
                    $table->text('contact_notes')->nullable();
                    $table->timestamps();

                    $table->unique(['embassy_availability_event_id', 'inquiry_id'], 'embassy_notif_unique_event_lead');
                });
            }

            if (! Schema::hasTable('embassy_appointment_logs')) {
                Schema::create('embassy_appointment_logs', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('embassy_appointment_id')->constrained('embassy_appointments')->cascadeOnDelete();
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->string('user_name')->nullable();
                    $table->string('action');
                    $table->string('old_status', 30)->nullable();
                    $table->string('new_status', 30)->nullable();
                    $table->string('old_earliest_date', 255)->nullable();
                    $table->string('new_earliest_date', 255)->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            }

            if (Schema::hasTable('inquiries')) {
                Schema::table('inquiries', function (Blueprint $table) {
                    if (! Schema::hasColumn('inquiries', 'visa_country_id')) {
                        $table->foreignId('visa_country_id')->nullable()->after('country')->constrained('visa_countries')->nullOnDelete();
                    }
                    if (! Schema::hasColumn('inquiries', 'appointment_center')) {
                        $table->string('appointment_center')->nullable()->after('visa_country_id');
                    }
                    if (! Schema::hasColumn('inquiries', 'appointment_type')) {
                        $table->string('appointment_type')->nullable()->after('appointment_center');
                    }
                });
            }

            if (Schema::hasTable('crm_statuses')) {
                $existing = DB::table('crm_statuses')->where('slug', 'awaiting-embassy-appointment')->first();
                $now = now();
                if (! $existing) {
                    DB::table('crm_statuses')->insert([
                        'slug' => 'awaiting-embassy-appointment',
                        'name_ar' => 'انتظار فتح مواعيد السفارة',
                        'name_en' => 'Awaiting Embassy Appointment',
                        'status_group' => 'secondary',
                        'color' => 'warning',
                        'sort_order' => 25,
                        'is_default' => false,
                        'is_system' => true,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            static::autoSeedAppointmentsFromImage();
        } catch (\Throwable $e) {
            logger()->error('EmbassyAppointment ensureTableSchema error: ' . $e->getMessage());
        }
    }

    public static function autoSeedAppointmentsFromImage(): void
    {
        try {
            if (! Schema::hasTable('embassy_appointments') || ! Schema::hasTable('visa_countries')) {
                return;
            }

            $category = VisaCategory::firstOrCreate(
                ['slug' => 'schengen'],
                ['name_ar' => 'شنغن (Schengen)', 'name_en' => 'Schengen']
            );

            $appointmentsData = [
                ['name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'slug' => 'germany', 'dates' => 'شهر 12', 'center' => 'VFS'],
                ['name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'slug' => 'spain', 'dates' => 'شهر 9 و 10', 'center' => 'BLS'],
                ['name_ar' => 'اليونان', 'name_en' => 'Greece', 'slug' => 'greece', 'dates' => 'شهر 9 و 10', 'center' => 'VFS'],
                ['name_ar' => 'المجر', 'name_en' => 'Hungary', 'slug' => 'hungary', 'dates' => 'شهر 9 و 10', 'center' => 'iOM'],
                ['name_ar' => 'هولندا', 'name_en' => 'Netherlands', 'slug' => 'netherlands', 'dates' => 'شهر 11 و 12', 'center' => 'VFS'],
                ['name_ar' => 'اليونان', 'name_en' => 'Greece', 'slug' => 'greece', 'dates' => 'شهر 9 (إسكندرية)', 'center' => 'VFS (إسكندرية)'],
                ['name_ar' => 'البرتغال', 'name_en' => 'Portugal', 'slug' => 'portugal', 'dates' => 'شهر 9 و 10', 'center' => 'VFS'],
                ['name_ar' => 'السويد', 'name_en' => 'Sweden', 'slug' => 'sweden', 'dates' => 'شهر 9', 'center' => 'VFS'],
                ['name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'slug' => 'italy', 'dates' => 'شهر 9', 'center' => 'VFS'],
                ['name_ar' => 'سويسرا', 'name_en' => 'Switzerland', 'slug' => 'switzerland', 'dates' => 'شهر 9', 'center' => 'VFS'],
                ['name_ar' => 'كرواتيا', 'name_en' => 'Croatia', 'slug' => 'croatia', 'dates' => 'شهر 10', 'center' => 'VFS'],
                ['name_ar' => 'بلجيكا', 'name_en' => 'Belgium', 'slug' => 'belgium', 'dates' => 'شهر 11 و 12', 'center' => 'TLS'],
                ['name_ar' => 'فرنسا', 'name_en' => 'France', 'slug' => 'france', 'dates' => 'شهر 1', 'center' => 'TLS'],
                ['name_ar' => 'النمسا', 'name_en' => 'Austria', 'slug' => 'austria', 'dates' => 'شهر 10 و 11', 'center' => 'VFS'],
                ['name_ar' => 'النرويج', 'name_en' => 'Norway', 'slug' => 'norway', 'dates' => 'شهر 9', 'center' => 'VFS'],
            ];

            foreach ($appointmentsData as $item) {
                $country = VisaCountry::where('slug', $item['slug'])
                    ->orWhere('name_ar', $item['name_ar'])
                    ->first();

                if (! $country) {
                    $country = VisaCountry::create([
                        'visa_category_id' => $category->id,
                        'name_ar' => $item['name_ar'],
                        'name_en' => $item['name_en'],
                        'slug' => $item['slug'],
                        'status' => 'active',
                    ]);
                }

                static::updateOrCreate([
                    'visa_country_id' => $country->id,
                    'visa_type' => 'سياحة',
                    'appointment_center' => $item['center'],
                    'appointment_type' => 'Regular',
                ], [
                    'status' => self::STATUS_AVAILABLE_LATER,
                    'earliest_date' => $item['dates'],
                    'last_updated_at' => now(),
                    'notes' => '🟡 مواعيد متاحة بتاريخ مستقبلي',
                ]);
            }
        } catch (\Throwable $e) {
            logger()->error('autoSeedAppointmentsFromImage error: ' . $e->getMessage());
        }
    }

    protected $fillable = [
        'visa_country_id',
        'visa_record_id',
        'visa_type',
        'appointment_center',
        'appointment_type',
        'status',
        'earliest_date',
        'last_updated_at',
        'updated_by',
        'notes',
        'booking_link',
    ];

    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }

    public function visaRecord(): BelongsTo
    {
        return $this->belongsTo(VisaRecord::class, 'visa_record_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(EmbassyAvailabilityEvent::class, 'embassy_appointment_id')->latest();
    }

    public function latestEvent(): BelongsTo
    {
        return $this->belongsTo(EmbassyAvailabilityEvent::class, 'id', 'embassy_appointment_id')->latestOfMany();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(EmbassyAppointmentNotification::class, 'embassy_appointment_id')->latest();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EmbassyAppointmentLog::class, 'embassy_appointment_id')->latest();
    }

    public function getCountryNameAttribute(): string
    {
        return $this->country ? ($this->country->name_ar ?: $this->country->name_en) : 'غير محدد';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE_NOW => 'مواعيد متاحة الآن',
            self::STATUS_AVAILABLE_LATER => 'مواعيد متاحة بتاريخ مستقبلي',
            self::STATUS_NO_AVAILABILITY => 'لا توجد مواعيد حاليًا',
            default => 'غير معروف / لم يتم التحديث',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE_NOW => 'bg-success',
            self::STATUS_AVAILABLE_LATER => 'bg-warning text-dark',
            self::STATUS_NO_AVAILABILITY => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE_NOW => '🟢',
            self::STATUS_AVAILABLE_LATER => '🟡',
            self::STATUS_NO_AVAILABILITY => '🔴',
            default => '⚪',
        };
    }

    public function getFormattedLastUpdatedAttribute(): string
    {
        if (! $this->last_updated_at) {
            return 'لم يتم التحديث بعد';
        }

        return $this->last_updated_at->format('Y-m-d — h:i A') . ' (' . $this->last_updated_at->diffForHumans() . ')';
    }
}
