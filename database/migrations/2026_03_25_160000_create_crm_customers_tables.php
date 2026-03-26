<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_customers')) {
            Schema::create('crm_customers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('inquiry_id');
                $table->string('customer_code')->nullable()->unique();
                $table->string('full_name');
                $table->string('phone')->nullable();
                $table->string('whatsapp_number')->nullable();
                $table->string('email')->nullable();
                $table->string('nationality')->nullable();
                $table->string('country')->nullable();
                $table->string('destination')->nullable();
                $table->unsignedBigInteger('crm_source_id')->nullable();
                $table->unsignedBigInteger('crm_service_type_id')->nullable();
                $table->unsignedBigInteger('crm_service_subtype_id')->nullable();
                $table->unsignedBigInteger('assigned_user_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('stage', 40)->default('new_customer');
                $table->boolean('is_active')->default(true);
                $table->timestamp('converted_at')->nullable();
                $table->timestamp('appointment_at')->nullable();
                $table->timestamp('submission_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique('inquiry_id', 'crm_customers_inquiry_unique');
                $table->foreign('inquiry_id', 'crm_customers_inquiry_fk')->references('id')->on('inquiries')->cascadeOnDelete();
                $table->foreign('crm_source_id', 'crm_customers_source_fk')->references('id')->on('crm_lead_sources')->nullOnDelete();
                $table->foreign('crm_service_type_id', 'crm_customers_service_type_fk')->references('id')->on('crm_service_types')->nullOnDelete();
                $table->foreign('crm_service_subtype_id', 'crm_customers_service_subtype_fk')->references('id')->on('crm_service_subtypes')->nullOnDelete();
                $table->foreign('assigned_user_id', 'crm_customers_seller_fk')->references('id')->on('users')->nullOnDelete();
                $table->foreign('created_by', 'crm_customers_creator_fk')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('crm_customer_activities')) {
            Schema::create('crm_customer_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('crm_customer_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action_type', 80);
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('crm_customer_id', 'crm_customer_activities_customer_fk')->references('id')->on('crm_customers')->cascadeOnDelete();
                $table->foreign('user_id', 'crm_customer_activities_user_fk')->references('id')->on('users')->nullOnDelete();
            });
        }

        Schema::table('accounting_customer_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('accounting_customer_accounts', 'crm_customer_id')) {
                $table->unsignedBigInteger('crm_customer_id')->nullable()->after('inquiry_id');
                $table->unique('crm_customer_id', 'acct_customer_accounts_customer_unique');
                $table->foreign('crm_customer_id', 'acct_customer_accounts_customer_fk')->references('id')->on('crm_customers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounting_customer_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_customer_accounts', 'crm_customer_id')) {
                $table->dropForeign('acct_customer_accounts_customer_fk');
                $table->dropUnique('acct_customer_accounts_customer_unique');
                $table->dropColumn('crm_customer_id');
            }
        });

        Schema::dropIfExists('crm_customer_activities');
        Schema::dropIfExists('crm_customers');
    }
};
