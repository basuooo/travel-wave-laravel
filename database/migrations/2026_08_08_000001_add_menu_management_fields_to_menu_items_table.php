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
        Schema::table('menu_items', function (Blueprint $table) {
            if (! Schema::hasColumn('menu_items', 'type')) {
                $table->string('type')->default('custom')->after('parent_id');
            }
            if (! Schema::hasColumn('menu_items', 'page_id')) {
                $table->foreignId('page_id')->nullable()->after('type')->constrained('pages')->nullOnDelete();
            }
            if (! Schema::hasColumn('menu_items', 'icon')) {
                $table->string('icon')->nullable()->after('target');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            if (Schema::hasColumn('menu_items', 'icon')) {
                $table->dropColumn('icon');
            }
            if (Schema::hasColumn('menu_items', 'page_id')) {
                $table->dropForeign(['page_id']);
                $table->dropColumn('page_id');
            }
            if (Schema::hasColumn('menu_items', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
