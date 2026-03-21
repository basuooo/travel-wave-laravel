<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('integration_type', 50);
            $table->string('platform', 100)->nullable();
            $table->string('tracking_code', 255)->nullable();
            $table->longText('script_code')->nullable();
            $table->string('placement', 50)->default('standard');
            $table->text('notes')->nullable();
            $table->string('visibility_mode', 50)->default('all');
            $table->json('visibility_targets')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_integrations');
    }
};
