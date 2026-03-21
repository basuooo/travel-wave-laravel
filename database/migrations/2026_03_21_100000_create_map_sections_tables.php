<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('subtitle_en')->nullable();
            $table->text('subtitle_ar')->nullable();
            $table->text('address_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->string('button_text_en')->nullable();
            $table->string('button_text_ar')->nullable();
            $table->string('button_link')->nullable();
            $table->longText('embed_code')->nullable();
            $table->text('map_url')->nullable();
            $table->string('layout_type')->default('split');
            $table->unsignedInteger('height')->default(380);
            $table->string('background_style')->default('default');
            $table->string('spacing_preset')->default('normal');
            $table->boolean('rounded_corners')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('map_section_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_section_id')->constrained()->cascadeOnDelete();
            $table->string('assignment_type');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_key')->nullable();
            $table->string('display_position')->default('bottom');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_section_assignments');
        Schema::dropIfExists('map_sections');
    }
};
