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
        // 1. WhatsApp Accounts
        if (!Schema::hasTable('whatsapp_accounts')) {
            Schema::create('whatsapp_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone_number')->nullable();
                $table->string('status')->default('disconnected'); // connected, disconnected, disabled
                $table->timestamp('last_connected_at')->nullable();
                $table->string('usage_type')->default('both'); // retargeting, bulk, both
                $table->unsignedBigInteger('assigned_user_id')->nullable();
                $table->string('department_branch')->nullable();
                $table->unsignedBigInteger('sent_count')->default(0);
                $table->unsignedBigInteger('failed_count')->default(0);
                $table->unsignedBigInteger('conversations_count')->default(0);
                $table->json('connection_settings')->nullable(); // access_token, phone_number_id, business_account_id, qr_code_url, etc.
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 2. Upgrade existing whatsapp_conversations if needed
        if (Schema::hasTable('whatsapp_conversations')) {
            Schema::table('whatsapp_conversations', function (Blueprint $table) {
                if (!Schema::hasColumn('whatsapp_conversations', 'whatsapp_account_id')) {
                    $table->unsignedBigInteger('whatsapp_account_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'whatsapp_contact_id')) {
                    $table->unsignedBigInteger('whatsapp_contact_id')->nullable()->after('whatsapp_account_id');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'lead_id')) {
                    $table->unsignedBigInteger('lead_id')->nullable()->after('whatsapp_contact_id');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable()->after('lead_id');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'first_contact_at')) {
                    $table->timestamp('first_contact_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'message_count')) {
                    $table->unsignedBigInteger('message_count')->default(0)->after('last_message_at');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'tags')) {
                    $table->json('tags')->nullable()->after('message_count');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'notes')) {
                    $table->text('notes')->nullable()->after('tags');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'is_starred')) {
                    $table->boolean('is_starred')->default(false)->after('notes');
                }
                if (!Schema::hasColumn('whatsapp_conversations', 'is_archived')) {
                    $table->boolean('is_archived')->default(false)->after('is_starred');
                }
            });
        }

        // 3. WhatsApp Contacts
        if (!Schema::hasTable('whatsapp_contacts')) {
            Schema::create('whatsapp_contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('phone');
                $table->string('normalized_phone')->index();
                $table->unsignedBigInteger('whatsapp_account_id')->nullable();
                $table->unsignedBigInteger('assigned_user_id')->nullable();
                $table->unsignedBigInteger('lead_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('status_in_crm')->nullable(); // lead, customer, interested, hot_lead, cold_lead, no_response, replied
                $table->string('service')->nullable();
                $table->string('country')->nullable();
                $table->string('lead_source')->nullable();
                $table->string('opt_out_status')->default('opted_in'); // opted_in, opted_out, do_not_contact, blocked, suppressed
                $table->json('tags')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('first_contact_at')->nullable();
                $table->timestamp('last_contact_at')->nullable();
                $table->unsignedBigInteger('conversation_count')->default(0);
                $table->timestamps();

                $table->foreign('whatsapp_account_id')->references('id')->on('whatsapp_accounts')->onDelete('set null');
                $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('lead_id')->references('id')->on('crm_leads')->onDelete('set null');
                $table->foreign('customer_id')->references('id')->on('crm_customers')->onDelete('set null');
            });
        }

        // 4. WhatsApp Message Templates
        if (!Schema::hasTable('whatsapp_templates')) {
            Schema::create('whatsapp_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category')->default('general'); // follow-up, visa, offers, customer_service, reminder, existing_customer, lead_followup
                $table->text('content');
                $table->string('media_type')->nullable(); // text, image, video, document, audio
                $table->string('media_url')->nullable();
                $table->json('variables')->nullable(); // ['name', 'phone', 'country', 'service', 'employee', 'branch']
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();

                $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 5. WhatsApp Campaigns
        if (!Schema::hasTable('whatsapp_campaigns')) {
            Schema::create('whatsapp_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->default('bulk'); // retargeting, bulk
                $table->unsignedBigInteger('whatsapp_account_id');
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->string('status')->default('draft'); // draft, scheduled, running, paused, completed, failed, cancelled
                $table->string('audience_source')->nullable(); // previous_conversations, crm_contacts, upload_excel, upload_csv, paste_numbers, existing_contacts
                $table->json('audience_filters')->nullable();
                $table->text('message_content')->nullable();
                $table->string('media_type')->nullable();
                $table->string('media_url')->nullable();
                $table->unsignedBigInteger('template_id')->nullable();
                $table->string('schedule_type')->default('now'); // now, scheduled
                $table->timestamp('scheduled_at')->nullable();
                $table->time('sending_window_start')->nullable()->default('08:00:00');
                $table->time('sending_window_end')->nullable()->default('20:00:00');
                $table->json('allowed_days')->nullable(); // [1, 2, 3, 4, 5, 6, 7]
                $table->string('interval_type')->default('fixed'); // fixed, random
                $table->integer('interval_min_sec')->default(60);
                $table->integer('interval_max_sec')->default(90);
                $table->integer('daily_limit')->nullable();
                $table->integer('hourly_limit')->nullable();
                $table->integer('campaign_limit')->nullable();
                $table->integer('total_contacts')->default(0);
                $table->integer('previously_contacted_count')->default(0);
                $table->integer('not_previously_contacted_count')->default(0);
                $table->integer('sent_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->integer('pending_count')->default(0);
                $table->integer('reply_count')->default(0);
                $table->integer('opt_out_count')->default(0);
                $table->boolean('require_approval')->default(false);
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('approved_by_user_id')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('paused_at')->nullable();
                $table->timestamps();

                $table->foreign('whatsapp_account_id')->references('id')->on('whatsapp_accounts')->onDelete('cascade');
                $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('template_id')->references('id')->on('whatsapp_templates')->onDelete('set null');
                $table->foreign('approved_by_user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 6. WhatsApp Campaign Recipients
        if (!Schema::hasTable('whatsapp_campaign_recipients')) {
            Schema::create('whatsapp_campaign_recipients', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('campaign_id');
                $table->unsignedBigInteger('whatsapp_account_id');
                $table->string('phone');
                $table->string('normalized_phone')->index();
                $table->string('contact_name')->nullable();
                $table->string('contact_status')->default('not_previously_contacted'); // previously_contacted, not_previously_contacted
                $table->boolean('is_selected')->default(true);
                $table->unsignedBigInteger('whatsapp_contact_id')->nullable();
                $table->unsignedBigInteger('lead_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->json('custom_variables')->nullable();
                $table->string('status')->default('pending'); // pending, processing, sent, failed, retry, skipped_blacklist, skipped_optout, cancelled
                $table->timestamp('sent_at')->nullable();
                $table->text('error_message')->nullable();
                $table->integer('retry_count')->default(0);
                $table->timestamps();

                $table->foreign('campaign_id')->references('id')->on('whatsapp_campaigns')->onDelete('cascade');
                $table->foreign('whatsapp_account_id')->references('id')->on('whatsapp_accounts')->onDelete('cascade');
                $table->foreign('whatsapp_contact_id')->references('id')->on('whatsapp_contacts')->onDelete('set null');
                $table->foreign('lead_id')->references('id')->on('crm_leads')->onDelete('set null');
                $table->foreign('customer_id')->references('id')->on('crm_customers')->onDelete('set null');
            });
        }

        // 7. Follow-up Sequences
        if (!Schema::hasTable('whatsapp_sequences')) {
            Schema::create('whatsapp_sequences', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->unsignedBigInteger('whatsapp_account_id')->nullable();
                $table->string('trigger_event')->default('manual'); // manual, new_lead, tag_added, status_changed
                $table->boolean('smart_stop_on_reply')->default(true);
                $table->boolean('smart_stop_on_convert')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('whatsapp_account_id')->references('id')->on('whatsapp_accounts')->onDelete('set null');
            });
        }

        // 8. Sequence Steps
        if (!Schema::hasTable('whatsapp_sequence_steps')) {
            Schema::create('whatsapp_sequence_steps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sequence_id');
                $table->integer('step_number');
                $table->integer('delay_days')->default(0);
                $table->unsignedBigInteger('template_id')->nullable();
                $table->text('message_content')->nullable();
                $table->string('media_type')->nullable();
                $table->string('media_url')->nullable();
                $table->timestamps();

                $table->foreign('sequence_id')->references('id')->on('whatsapp_sequences')->onDelete('cascade');
                $table->foreign('template_id')->references('id')->on('whatsapp_templates')->onDelete('set null');
            });
        }

        // 9. Sequence Subscribers
        if (!Schema::hasTable('whatsapp_sequence_subscribers')) {
            Schema::create('whatsapp_sequence_subscribers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sequence_id');
                $table->unsignedBigInteger('whatsapp_contact_id');
                $table->integer('current_step')->default(1);
                $table->string('status')->default('active'); // active, completed, stopped_replied, stopped_converted, cancelled
                $table->timestamp('next_execution_at')->nullable();
                $table->timestamps();

                $table->foreign('sequence_id')->references('id')->on('whatsapp_sequences')->onDelete('cascade');
                $table->foreign('whatsapp_contact_id')->references('id')->on('whatsapp_contacts')->onDelete('cascade');
            });
        }

        // 10. WhatsApp Blacklist
        if (!Schema::hasTable('whatsapp_blacklist')) {
            Schema::create('whatsapp_blacklist', function (Blueprint $table) {
                $table->id();
                $table->string('phone');
                $table->string('normalized_phone')->unique();
                $table->string('reason')->nullable();
                $table->unsignedBigInteger('added_by_user_id')->nullable();
                $table->timestamps();

                $table->foreign('added_by_user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 11. WhatsApp Import History
        if (!Schema::hasTable('whatsapp_import_history')) {
            Schema::create('whatsapp_import_history', function (Blueprint $table) {
                $table->id();
                $table->string('file_name');
                $table->string('campaign_type')->default('bulk'); // retargeting, bulk
                $table->integer('total_numbers')->default(0);
                $table->integer('valid_count')->default(0);
                $table->integer('invalid_count')->default(0);
                $table->integer('duplicate_count')->default(0);
                $table->integer('imported_count')->default(0);
                $table->integer('rejected_count')->default(0);
                $table->unsignedBigInteger('uploaded_by_user_id')->nullable();
                $table->timestamps();

                $table->foreign('uploaded_by_user_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_import_history');
        Schema::dropIfExists('whatsapp_blacklist');
        Schema::dropIfExists('whatsapp_sequence_subscribers');
        Schema::dropIfExists('whatsapp_sequence_steps');
        Schema::dropIfExists('whatsapp_sequences');
        Schema::dropIfExists('whatsapp_campaign_recipients');
        Schema::dropIfExists('whatsapp_campaigns');
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('whatsapp_contacts');
        Schema::dropIfExists('whatsapp_accounts');
    }
};
