<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('form_category')->nullable();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('subtitle_en')->nullable();
            $table->text('subtitle_ar')->nullable();
            $table->string('submit_text_en')->nullable();
            $table->string('submit_text_ar')->nullable();
            $table->text('success_message_en')->nullable();
            $table->text('success_message_ar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_form_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->string('type')->default('text');
            $table->string('label_en')->nullable();
            $table->string('label_ar')->nullable();
            $table->string('placeholder_en')->nullable();
            $table->string('placeholder_ar')->nullable();
            $table->text('help_text_en')->nullable();
            $table->text('help_text_ar')->nullable();
            $table->text('validation_rule')->nullable();
            $table->text('default_value')->nullable();
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lead_form_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_form_id')->constrained()->cascadeOnDelete();
            $table->string('assignment_type');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_key')->nullable();
            $table->string('display_position')->default('bottom');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['assignment_type', 'target_id']);
            $table->index(['assignment_type', 'target_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_form_assignments');
        Schema::dropIfExists('lead_form_fields');
        Schema::dropIfExists('lead_forms');
    }
};
