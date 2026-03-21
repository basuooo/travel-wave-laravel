<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->string('inquiry_form_label_en')->nullable()->after('inquiry_form_is_active');
            $table->string('inquiry_form_label_ar')->nullable()->after('inquiry_form_label_en');
        });
    }

    public function down(): void
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->dropColumn([
                'inquiry_form_label_en',
                'inquiry_form_label_ar',
            ]);
        });
    }
};
