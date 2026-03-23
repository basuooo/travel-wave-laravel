<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_service_types')) {
            Schema::create('crm_service_types', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->string('destination_label_en')->nullable();
                $table->string('destination_label_ar')->nullable();
                $table->boolean('requires_subtype')->default(false);
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('crm_service_subtypes')) {
            Schema::create('crm_service_subtypes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('crm_service_type_id')->constrained('crm_service_types')->cascadeOnDelete();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'crm_service_type_id')) {
                $table->foreignId('crm_service_type_id')->nullable()->after('crm_source_id')->constrained('crm_service_types')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'crm_service_subtype_id')) {
                $table->foreignId('crm_service_subtype_id')->nullable()->after('crm_service_type_id')->constrained('crm_service_subtypes')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'service_country_name')) {
                $table->string('service_country_name')->nullable()->after('service_type');
            }
            if (! Schema::hasColumn('inquiries', 'tourism_destination')) {
                $table->string('tourism_destination')->nullable()->after('service_country_name');
            }
            if (! Schema::hasColumn('inquiries', 'travel_destination')) {
                $table->string('travel_destination')->nullable()->after('tourism_destination');
            }
            if (! Schema::hasColumn('inquiries', 'hotel_destination')) {
                $table->string('hotel_destination')->nullable()->after('travel_destination');
            }
        });

        $now = now();

        $typeSeed = [
            [
                'name_en' => 'External Visas',
                'name_ar' => 'تأشيرات خارجية',
                'slug' => 'external-visas',
                'destination_label_en' => 'Country',
                'destination_label_ar' => 'الدولة',
                'requires_subtype' => true,
            ],
            [
                'name_en' => 'Domestic Tourism',
                'name_ar' => 'رحلات داخلية',
                'slug' => 'domestic-tourism',
                'destination_label_en' => 'Tourism Destination',
                'destination_label_ar' => 'الوجهة السياحية',
                'requires_subtype' => false,
            ],
            [
                'name_en' => 'Flight Tickets',
                'name_ar' => 'تذاكر طيران',
                'slug' => 'flight-tickets',
                'destination_label_en' => 'Travel Destination',
                'destination_label_ar' => 'جهة السفر',
                'requires_subtype' => false,
            ],
            [
                'name_en' => 'Hotel Booking',
                'name_ar' => 'حجز فنادق',
                'slug' => 'hotel-booking',
                'destination_label_en' => 'Hotel Destination',
                'destination_label_ar' => 'المدينة / الدولة',
                'requires_subtype' => false,
            ],
        ];

        foreach ($typeSeed as $index => $type) {
            $payload = $type + [
                'is_default' => $type['slug'] === 'external-visas',
                'is_active' => true,
                'sort_order' => $index + 1,
            ];

            $existing = DB::table('crm_service_types')->where('slug', $type['slug'])->first();

            if ($existing) {
                DB::table('crm_service_types')
                    ->where('id', $existing->id)
                    ->update($payload + ['updated_at' => $now]);
            } else {
                DB::table('crm_service_types')
                    ->insert($payload + ['created_at' => $now, 'updated_at' => $now]);
            }
        }

        $visaTypeId = DB::table('crm_service_types')->where('slug', 'external-visas')->value('id');

        $subtypeSeed = [
            ['name_en' => 'European Union', 'name_ar' => 'الاتحاد الأوروبي', 'slug' => 'european-union'],
            ['name_en' => 'Asian Countries', 'name_ar' => 'دول آسيا', 'slug' => 'asian-countries'],
            ['name_en' => 'Arab Countries', 'name_ar' => 'الدول العربية', 'slug' => 'arab-countries'],
            ['name_en' => 'USA, Canada, and Australia', 'name_ar' => 'أمريكا وكندا وأستراليا', 'slug' => 'usa-canada-australia'],
        ];

        foreach ($subtypeSeed as $index => $subtype) {
            $payload = $subtype + [
                'crm_service_type_id' => $visaTypeId,
                'is_active' => true,
                'sort_order' => $index + 1,
            ];

            $existing = DB::table('crm_service_subtypes')->where('slug', $subtype['slug'])->first();

            if ($existing) {
                DB::table('crm_service_subtypes')
                    ->where('id', $existing->id)
                    ->update($payload + ['updated_at' => $now]);
            } else {
                DB::table('crm_service_subtypes')
                    ->insert($payload + ['created_at' => $now, 'updated_at' => $now]);
            }
        }

        $serviceTypes = DB::table('crm_service_types')->get()->keyBy('slug');

        DB::table('inquiries')->orderBy('id')->chunkById(100, function ($rows) use ($serviceTypes) {
            foreach ($rows as $row) {
                $updates = [];
                $serviceTypeValue = mb_strtolower(trim((string) ($row->service_type ?? '')));

                if (! $row->crm_service_type_id && $serviceTypeValue !== '') {
                    $slug = Str::slug($serviceTypeValue);
                    $matched = $serviceTypes->get($slug);

                    if (! $matched && str_contains($serviceTypeValue, 'visa')) {
                        $matched = $serviceTypes->get('external-visas');
                    } elseif (! $matched && (str_contains($serviceTypeValue, 'طھط£ط´ظٹط±') || str_contains($serviceTypeValue, 'تأشيرة'))) {
                        $matched = $serviceTypes->get('external-visas');
                    }

                    if ($matched) {
                        $updates['crm_service_type_id'] = $matched->id;
                    }
                }

                if (! $row->service_country_name && $row->country) {
                    $updates['service_country_name'] = $row->country;
                }

                if (! $row->tourism_destination && $row->destination) {
                    $updates['tourism_destination'] = $row->destination;
                }

                if ($updates !== []) {
                    DB::table('inquiries')->where('id', $row->id)->update($updates);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            foreach (['crm_service_subtype_id', 'crm_service_type_id'] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach (['service_country_name', 'tourism_destination', 'travel_destination', 'hotel_destination'] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('crm_service_subtypes');
        Schema::dropIfExists('crm_service_types');
    }
};
