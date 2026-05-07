<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->nullable()->constrained('crm_integrations')->nullOnDelete();
            $table->string('external_id')->nullable()->index();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('platform')->nullable(); // meta, tiktok, etc.
            $table->string('campaign_name')->nullable();
            $table->string('adset_name')->nullable();
            $table->string('ad_name')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('new');
            $table->string('country')->nullable();
            $table->json('metadata')->nullable(); // Raw payload and extra fields
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('inquiry_id')->nullable()->constrained('inquiries')->nullOnDelete(); // Link to main CRM inquiries
            $table->timestamps();
            $table->softDeletes();

            $table->index(['platform', 'status']);
            $table->index('email');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
