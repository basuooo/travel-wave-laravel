<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicCatalogSetting extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::ensureTableSchema();
    }

    public static function ensureTableSchema(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('public_catalog_settings')) {
                \Illuminate\Support\Facades\Schema::create('public_catalog_settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->boolean('show_price')->default(true);
                    $table->boolean('show_embassy_fee')->default(true);
                    $table->boolean('show_working_days')->default(true);
                    $table->boolean('show_biometrics')->default(true);
                    $table->boolean('show_interview')->default(true);
                    $table->boolean('show_notes')->default(true);
                    $table->boolean('show_preview_button')->default(true);
                    $table->string('logo_path')->nullable();
                    $table->integer('logo_width')->default(180);
                    $table->integer('logo_height')->default(50);
                    $table->boolean('logo_keep_aspect_ratio')->default(true);
                    $table->string('whatsapp_phone')->nullable();
                    $table->text('whatsapp_message_template')->nullable();
                    $table->boolean('floating_whatsapp_enabled')->default(true);
                    $table->json('custom_buttons')->nullable();
                    $table->unsignedBigInteger('selected_lead_form_id')->nullable();
                    $table->unsignedBigInteger('selected_map_section_id')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            logger()->error('PublicCatalogSetting ensureTableSchema error: ' . $e->getMessage());
        }
    }

    protected $fillable = [
        'show_price',
        'show_embassy_fee',
        'show_working_days',
        'show_biometrics',
        'show_interview',
        'show_notes',
        'show_preview_button',
        'logo_path',
        'logo_width',
        'logo_height',
        'logo_keep_aspect_ratio',
        'whatsapp_phone',
        'whatsapp_message_template',
        'floating_whatsapp_enabled',
        'custom_buttons',
        'selected_lead_form_id',
        'selected_map_section_id',
    ];

    protected $casts = [
        'show_price' => 'boolean',
        'show_embassy_fee' => 'boolean',
        'show_working_days' => 'boolean',
        'show_biometrics' => 'boolean',
        'show_interview' => 'boolean',
        'show_notes' => 'boolean',
        'show_preview_button' => 'boolean',
        'logo_width' => 'integer',
        'logo_height' => 'integer',
        'logo_keep_aspect_ratio' => 'boolean',
        'floating_whatsapp_enabled' => 'boolean',
        'custom_buttons' => 'array',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }

        $siteSetting = Setting::first();
        if ($siteSetting && $siteSetting->header_logo_path) {
            return asset('storage/' . $siteSetting->header_logo_path);
        }
        if ($siteSetting && $siteSetting->logo_path) {
            return asset('storage/' . $siteSetting->logo_path);
        }

        return null;
    }

    public static function getSettings(): self
    {
        static::ensureTableSchema();

        return static::firstOrCreate([], [
            'show_price' => true,
            'show_embassy_fee' => true,
            'show_working_days' => true,
            'show_biometrics' => true,
            'show_interview' => true,
            'show_notes' => true,
            'show_preview_button' => true,
            'whatsapp_phone' => Setting::first()?->phone ?: '201000000000',
            'whatsapp_message_template' => 'مرحباً ترافيل ويف، أود الاستفسار والتقديم على تأشيرة {country_name} ({visa_type})',
            'floating_whatsapp_enabled' => true,
            'custom_buttons' => [],
        ]);
    }

    public function leadForm(): BelongsTo
    {
        return $this->belongsTo(LeadForm::class, 'selected_lead_form_id');
    }

    public function mapSection(): BelongsTo
    {
        return $this->belongsTo(MapSection::class, 'selected_map_section_id');
    }

    public function formatWhatsappMessage(?VisaRecord $record = null): string
    {
        $template = $this->whatsapp_message_template ?: 'مرحباً ترافيل ويف، أود الاستفسار عن التأشيرات';

        if (! $record) {
            return str_replace(['{country_name}', '{visa_type}', '{price}'], ['كل الدول', 'تأشيرات', 'متاحة'], $template);
        }

        $countryName = $record->country ? ($record->country->name_ar ?: $record->country->name_en) : 'التأشيرة';
        $visaType = $record->visa_type ?: 'سياحة';
        $price = $record->price ? number_format($record->price, 0) . ' ' . ($record->currency ?: 'EGP') : 'تواصل للتحديد';

        return str_replace(
            ['{country_name}', '{visa_type}', '{price}'],
            [$countryName, $visaType, $price],
            $template
        );
    }
}
