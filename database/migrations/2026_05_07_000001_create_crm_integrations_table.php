<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('platform'); // meta, tiktok, website, etc.
            $table->boolean('is_active')->default(true);
            $table->text('credentials'); // Encrypted JSON
            $table->string('webhook_token')->nullable();
            $table->string('webhook_verify_token')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->string('connection_status')->default('unknown');
            $table->text('error_log')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_integrations');
    }
};
