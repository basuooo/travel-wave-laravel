<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('footer_logo_path')->nullable()->after('logo_path');
            $table->unsignedInteger('logo_width')->default(220)->after('footer_logo_path');
            $table->unsignedInteger('logo_height')->nullable()->after('logo_width');
            $table->unsignedInteger('mobile_logo_width')->default(168)->after('logo_height');
            $table->string('accent_color')->default('#ff8c32')->after('secondary_color');
            $table->string('button_color')->default('#ff8c32')->after('accent_color');
            $table->string('button_hover_color')->default('#ef5c00')->after('button_color');
            $table->string('link_hover_color')->default('#ff8c32')->after('button_hover_color');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'footer_logo_path',
                'logo_width',
                'logo_height',
                'mobile_logo_width',
                'accent_color',
                'button_color',
                'button_hover_color',
                'link_hover_color',
            ]);
        });
    }
};
