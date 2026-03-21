<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('meta_pixel_id')->nullable()->after('floating_whatsapp_visibility_targets');
            $table->boolean('meta_conversion_api_enabled')->default(false)->after('meta_pixel_id');
            $table->text('meta_conversion_api_access_token')->nullable()->after('meta_conversion_api_enabled');
            $table->string('meta_conversion_api_test_event_code')->nullable()->after('meta_conversion_api_access_token');
            $table->string('meta_conversion_api_default_event_source_url')->nullable()->after('meta_conversion_api_test_event_code');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'meta_pixel_id',
                'meta_conversion_api_enabled',
                'meta_conversion_api_access_token',
                'meta_conversion_api_test_event_code',
                'meta_conversion_api_default_event_source_url',
            ]);
        });
    }
};
