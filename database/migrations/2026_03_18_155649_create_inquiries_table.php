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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('general');
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('nationality')->nullable();
            $table->string('destination')->nullable();
            $table->string('service_type')->nullable();
            $table->date('travel_date')->nullable();
            $table->date('return_date')->nullable();
            $table->unsignedInteger('travelers_count')->nullable();
            $table->unsignedInteger('nights_count')->nullable();
            $table->string('accommodation_type')->nullable();
            $table->string('estimated_budget')->nullable();
            $table->string('preferred_language')->default('en');
            $table->string('source_page')->nullable();
            $table->longText('message')->nullable();
            $table->string('status')->default('new');
            $table->longText('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
};
