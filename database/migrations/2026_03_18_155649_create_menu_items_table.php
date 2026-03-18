<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('location')->default('header');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->string('target')->default('_self');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
};
