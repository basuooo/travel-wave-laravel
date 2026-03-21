<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreignId('lead_form_id')->nullable()->after('id')->constrained('lead_forms')->nullOnDelete();
            $table->foreignId('lead_form_assignment_id')->nullable()->after('lead_form_id')->constrained('lead_form_assignments')->nullOnDelete();
            $table->string('form_name')->nullable()->after('type');
            $table->string('form_category')->nullable()->after('form_name');
            $table->string('display_position')->nullable()->after('source_page');
            $table->json('submitted_data')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_form_assignment_id');
            $table->dropConstrainedForeignId('lead_form_id');
            $table->dropColumn(['form_name', 'form_category', 'display_position', 'submitted_data']);
        });
    }
};
