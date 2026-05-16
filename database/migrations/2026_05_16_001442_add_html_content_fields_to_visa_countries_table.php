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
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->string('content_mode', 20)->default('normal')->after('is_active');
            $table->longText('html_content_en')->nullable()->after('content_mode');
            $table->longText('html_content_ar')->nullable()->after('html_content_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->dropColumn(['content_mode', 'html_content_en', 'html_content_ar']);
        });
    }
};
