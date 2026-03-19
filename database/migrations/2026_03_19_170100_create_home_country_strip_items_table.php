<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('home_country_strip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_country_id')->nullable()->constrained('visa_countries')->nullOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('image_path')->nullable();
            $table->string('custom_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_homepage')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('home_country_strip_items');
    }
};
