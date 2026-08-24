<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('country_visa_category')) {
            Schema::create('country_visa_category', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visa_country_id')->constrained('visa_countries')->cascadeOnDelete();
                $table->foreignId('visa_category_id')->constrained('visa_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['visa_country_id', 'visa_category_id']);
            });
        }

        if (! Schema::hasTable('visa_records')) {
            Schema::create('visa_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visa_country_id')->constrained('visa_countries')->cascadeOnDelete();
                $table->string('visa_type')->default('سياحة');
                $table->string('visa_type_slug')->nullable();
                $table->decimal('price', 12, 2)->nullable();
                $table->string('currency', 10)->default('EGP');
                $table->string('working_days')->nullable();
                $table->string('proposed_duration')->nullable();
                $table->string('stay_duration')->nullable();
                $table->string('entries_count')->nullable();
                $table->longText('required_documents')->nullable();
                $table->string('embassy_fee')->nullable();
                $table->string('embassy_fee_currency', 10)->default('EUR');
                $table->string('embassy_fee_payment_method')->nullable();
                $table->json('application_center')->nullable();
                $table->boolean('is_biometrics_required')->default(true);
                $table->boolean('is_interview_required')->default(true);
                $table->longText('notes')->nullable();
                $table->string('status', 30)->default('active'); // active, temporarily_unavailable, inactive
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('visa_activity_logs')) {
            Schema::create('visa_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visa_record_id')->nullable()->constrained('visa_records')->cascadeOnDelete();
                $table->foreignId('visa_country_id')->nullable()->constrained('visa_countries')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('user_name')->nullable();
                $table->string('action');
                $table->string('field_name')->nullable();
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_activity_logs');
        Schema::dropIfExists('visa_records');
        Schema::dropIfExists('country_visa_category');
    }
};
