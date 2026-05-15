<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_knowledge_entries', function (Blueprint $table) {
            $table->string('match_type')->default('fuzzy')->after('is_active'); // fuzzy, exact
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_knowledge_entries', function (Blueprint $table) {
            $table->dropColumn('match_type');
        });
    }
};
