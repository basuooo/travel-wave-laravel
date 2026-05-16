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
    public function up(): void
    {
        Schema::table('lead_form_fields', function (Blueprint $table) {
            $table->string('depends_on_field')->nullable()->after('options');
            $table->string('depends_on_value')->nullable()->after('depends_on_field');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('lead_form_fields', function (Blueprint $table) {
            $table->dropColumn(['depends_on_field', 'depends_on_value']);
        });
    }
};
