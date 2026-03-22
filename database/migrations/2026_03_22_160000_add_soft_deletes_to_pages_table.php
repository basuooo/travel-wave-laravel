<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            if (! Schema::hasColumn('pages', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            if (! Schema::hasColumn('pages', 'deleted_by')) {
                $table->foreignId('deleted_by')->nullable()->after('deleted_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'deleted_by')) {
                $table->dropConstrainedForeignId('deleted_by');
            }

            if (Schema::hasColumn('pages', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
