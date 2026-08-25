<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_lead_calls')) {
            Schema::create('crm_lead_calls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('inquiry_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->integer('call_number')->default(1);
                $table->string('call_status');
                $table->text('comment')->nullable();
                $table->boolean('whatsapp_sent')->default(true);
                $table->timestamps();

                $table->foreign('inquiry_id')->references('id')->on('inquiries')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_calls');
    }
};
