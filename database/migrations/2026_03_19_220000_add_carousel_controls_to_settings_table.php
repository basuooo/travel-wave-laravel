<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('home_destinations_autoplay')->default(true)->after('home_country_strip_speed');
            $table->unsignedInteger('home_destinations_interval')->default(3200)->after('home_destinations_autoplay');
            $table->unsignedInteger('home_destinations_speed')->default(500)->after('home_destinations_interval');
            $table->boolean('home_destinations_pause_on_hover')->default(true)->after('home_destinations_speed');
            $table->boolean('home_destinations_loop')->default(true)->after('home_destinations_pause_on_hover');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_destinations_autoplay',
                'home_destinations_interval',
                'home_destinations_speed',
                'home_destinations_pause_on_hover',
                'home_destinations_loop',
            ]);
        });
    }
};
