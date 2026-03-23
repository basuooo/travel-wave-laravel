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
        if (! Schema::hasTable('crm_lead_sources')) {
            Schema::create('crm_lead_sources', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_ar');
                $table->string('slug')->unique();
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'crm_source_id')) {
                $table->foreignId('crm_source_id')->nullable()->after('lead_source')->constrained('crm_lead_sources')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'additional_notes')) {
                $table->longText('additional_notes')->nullable()->after('admin_notes');
            }
            if (! Schema::hasColumn('inquiries', 'total_price')) {
                $table->decimal('total_price', 12, 2)->nullable()->after('estimated_budget');
            }
            if (! Schema::hasColumn('inquiries', 'expenses')) {
                $table->decimal('expenses', 12, 2)->nullable()->after('total_price');
            }
            if (! Schema::hasColumn('inquiries', 'net_price')) {
                $table->decimal('net_price', 12, 2)->nullable()->after('expenses');
            }
            if (! Schema::hasColumn('inquiries', 'crm_status_updated_at')) {
                $table->timestamp('crm_status_updated_at')->nullable()->after('crm_status_id');
            }
            if (! Schema::hasColumn('inquiries', 'crm_status_updated_by')) {
                $table->foreignId('crm_status_updated_by')->nullable()->after('crm_status_updated_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'deleted_by')) {
                $table->foreignId('deleted_by')->nullable()->after('updated_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'deleted_at')) {
                $table->softDeletes()->after('deleted_by');
            }
        });

        Schema::table('crm_status_updates', function (Blueprint $table) {
            if (! Schema::hasColumn('crm_status_updates', 'note')) {
                $table->text('note')->nullable()->after('changed_at');
            }
        });

        $statusSeed = [
            ['name_en' => 'New Lead', 'name_ar' => 'ليد جديد', 'slug' => 'new-lead', 'color' => 'warning'],
            ['name_en' => 'No Answer', 'name_ar' => 'لم يتم الرد', 'slug' => 'no-answer', 'color' => 'secondary'],
            ['name_en' => 'Closed', 'name_ar' => 'مغلق', 'slug' => 'closed', 'color' => 'dark'],
            ['name_en' => 'Unavailable', 'name_ar' => 'غير متاح', 'slug' => 'unavailable', 'color' => 'secondary'],
            ['name_en' => 'Busy', 'name_ar' => 'مشغول', 'slug' => 'busy', 'color' => 'secondary'],
            ['name_en' => 'Dedicated Number', 'name_ar' => 'الرقم مخصص', 'slug' => 'dedicated-number', 'color' => 'secondary'],
            ['name_en' => 'Lazy Lead', 'name_ar' => 'كسلان', 'slug' => 'lazy-lead', 'color' => 'secondary'],
            ['name_en' => 'Not Interested', 'name_ar' => 'غير مهتم', 'slug' => 'not-interested', 'color' => 'danger'],
            ['name_en' => 'Waiting for Bank Account', 'name_ar' => 'منتظر حساب بنكي', 'slug' => 'waiting-bank-account', 'color' => 'warning'],
            ['name_en' => 'Bank Account Less Than 120K', 'name_ar' => 'موجود حساب بنكي أقل من 120 ألف جنيه', 'slug' => 'bank-account-less-than-120k', 'color' => 'warning'],
            ['name_en' => 'Will Be Contacted', 'name_ar' => 'سيتم التواصل', 'slug' => 'will-be-contacted', 'color' => 'info'],
            ['name_en' => 'Work Not Tourism', 'name_ar' => 'عاوز عنده عمل مش سياحة', 'slug' => 'work-not-tourism', 'color' => 'danger'],
            ['name_en' => 'Wrong Number', 'name_ar' => 'الرقم غلط', 'slug' => 'wrong-number', 'color' => 'danger'],
            ['name_en' => 'Far Location', 'name_ar' => 'خارج القاهرة المكان بعيد عليه', 'slug' => 'far-location', 'color' => 'secondary'],
            ['name_en' => 'International Number', 'name_ar' => 'الرقم دولي', 'slug' => 'international-number', 'color' => 'secondary'],
            ['name_en' => 'Documents Complete', 'name_ar' => 'الأوراق مكتملة', 'slug' => 'documents-complete', 'color' => 'success'],
            ['name_en' => 'Documents Need Commercial Register or Employment Letter', 'name_ar' => 'الأوراق مكملة (اخلص سجل تجاري أو خطاب عمل)', 'slug' => 'documents-needs-followup', 'color' => 'warning'],
            ['name_en' => 'Duplicate', 'name_ar' => 'مكرر', 'slug' => 'duplicate', 'color' => 'dark'],
        ];

        $now = now();

        foreach ($statusSeed as $index => $status) {
            $payload = $status + [
                'status_group' => 'lead',
                'sort_order' => $index + 1,
                'is_default' => $status['slug'] === 'new-lead',
                'is_system' => true,
                'is_active' => true,
            ];

            $existing = DB::table('crm_statuses')->where('slug', $status['slug'])->first();

            if ($existing) {
                DB::table('crm_statuses')
                    ->where('id', $existing->id)
                    ->update($payload + ['updated_at' => $now]);
            } else {
                DB::table('crm_statuses')->insert($payload + ['created_at' => $now, 'updated_at' => $now]);
            }
        }

        DB::table('crm_statuses')
            ->where('is_system', true)
            ->whereNotIn('slug', collect($statusSeed)->pluck('slug')->all())
            ->update([
                'is_active' => false,
                'updated_at' => $now,
            ]);

        $sourceSeed = [
            ['name_en' => 'Facebook (lead Generation)', 'name_ar' => 'فيسبوك (ليد جنريشن)', 'slug' => 'facebook-lead-generation'],
            ['name_en' => 'Facebook (message)', 'name_ar' => 'فيسبوك (رسائل)', 'slug' => 'facebook-message'],
            ['name_en' => 'Whatsapp (message)', 'name_ar' => 'واتساب (رسائل)', 'slug' => 'whatsapp-message'],
            ['name_en' => 'twitter', 'name_ar' => 'تويتر', 'slug' => 'twitter'],
            ['name_en' => 'instgram', 'name_ar' => 'انستجرام', 'slug' => 'instagram'],
            ['name_en' => 'linkedIn', 'name_ar' => 'لينكدإن', 'slug' => 'linkedin'],
            ['name_en' => 'E-mail Marketing', 'name_ar' => 'التسويق عبر البريد الإلكتروني', 'slug' => 'email-marketing'],
            ['name_en' => 'SMS', 'name_ar' => 'رسائل SMS', 'slug' => 'sms'],
            ['name_en' => 'other', 'name_ar' => 'أخرى', 'slug' => 'other'],
        ];

        foreach ($sourceSeed as $index => $source) {
            $existing = DB::table('crm_lead_sources')->where('slug', $source['slug'])->first();
            $payload = $source + [
                'sort_order' => $index + 1,
                'is_default' => $source['slug'] === 'other',
                'is_active' => true,
            ];

            if ($existing) {
                DB::table('crm_lead_sources')->where('id', $existing->id)->update($payload + ['updated_at' => $now]);
            } else {
                DB::table('crm_lead_sources')->insert($payload + ['created_at' => $now, 'updated_at' => $now]);
            }
        }

        $existingLeadSources = DB::table('inquiries')
            ->whereNotNull('lead_source')
            ->distinct()
            ->pluck('lead_source')
            ->filter()
            ->values();

        foreach ($existingLeadSources as $sourceName) {
            $slug = Str::slug((string) $sourceName) ?: ('source-' . Str::random(6));
            $baseSlug = $slug;
            $counter = 1;

            while (DB::table('crm_lead_sources')->where('slug', $slug)->exists()) {
                $match = DB::table('crm_lead_sources')
                    ->where('slug', $slug)
                    ->where('name_en', $sourceName)
                    ->orWhere('name_ar', $sourceName)
                    ->exists();

                if ($match) {
                    break;
                }

                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $existing = DB::table('crm_lead_sources')
                ->where('name_en', $sourceName)
                ->orWhere('name_ar', $sourceName)
                ->first();

            if (! $existing) {
                DB::table('crm_lead_sources')->insert([
                    'name_en' => $sourceName,
                    'name_ar' => $sourceName,
                    'slug' => $slug,
                    'is_default' => false,
                    'is_active' => true,
                    'sort_order' => 100,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $sourceMap = DB::table('crm_lead_sources')->get()->keyBy(function ($source) {
            return mb_strtolower(trim((string) $source->name_en));
        });

        DB::table('inquiries')->orderBy('id')->chunkById(100, function ($rows) use ($sourceMap, $now) {
            foreach ($rows as $row) {
                $statusUpdatedAt = $row->crm_status_updated_at
                    ?? $row->status_1_updated_at
                    ?? $row->updated_at
                    ?? $row->created_at
                    ?? $now;

                $statusUpdatedBy = $row->crm_status_updated_by
                    ?? $row->status_1_updated_by
                    ?? null;

                $sourceId = $row->crm_source_id;
                if (! $sourceId && filled($row->lead_source)) {
                    $match = $sourceMap->get(mb_strtolower(trim((string) $row->lead_source)));
                    $sourceId = $match?->id;
                }

                DB::table('inquiries')
                    ->where('id', $row->id)
                    ->update(array_filter([
                        'crm_status_updated_at' => $statusUpdatedAt,
                        'crm_status_updated_by' => $statusUpdatedBy,
                        'crm_source_id' => $sourceId,
                    ], fn ($value) => $value !== null));
            }
        });
    }

    public function down(): void
    {
        Schema::table('crm_status_updates', function (Blueprint $table) {
            if (Schema::hasColumn('crm_status_updates', 'note')) {
                $table->dropColumn('note');
            }
        });

        Schema::table('inquiries', function (Blueprint $table) {
            foreach (['crm_source_id', 'crm_status_updated_by', 'deleted_by'] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach ([
                'additional_notes',
                'total_price',
                'expenses',
                'net_price',
                'crm_status_updated_at',
                'deleted_at',
            ] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('crm_lead_sources');
    }
};
