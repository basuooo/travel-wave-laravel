<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_lead_whatsapps')) {
            Schema::create('crm_lead_whatsapps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('inquiry_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->integer('log_number')->default(1);
                $table->string('whatsapp_status');
                $table->text('comment')->nullable();
                $table->timestamps();

                $table->foreign('inquiry_id')->references('id')->on('inquiries')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_whatsapps');
    }
};
