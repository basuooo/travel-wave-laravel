<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'visa_categories',
        'visa_countries',
        'destinations',
        'testimonials',
        'menu_items',
        'blog_categories',
        'blog_posts',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->softDeletes()->after('updated_at');
                }

                if (! Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->foreignId('deleted_by')->nullable()->after('deleted_at')->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'deleted_by')) {
                    $table->dropConstrainedForeignId('deleted_by');
                }

                if (Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
