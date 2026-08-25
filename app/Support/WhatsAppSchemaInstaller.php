<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class WhatsAppSchemaInstaller
{
    public static function install(): void
    {
        try {
            // 1. whatsapp_accounts
            if (!Schema::hasTable('whatsapp_accounts')) {
                Schema::create('whatsapp_accounts', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('phone_number')->nullable();
                    $table->string('status')->default('disconnected');
                    $table->timestamp('last_connected_at')->nullable();
                    $table->string('usage_type')->default('both');
                    $table->unsignedBigInteger('assigned_user_id')->nullable();
                    $table->string('department_branch')->nullable();
                    $table->unsignedBigInteger('sent_count')->default(0);
                    $table->unsignedBigInteger('failed_count')->default(0);
                    $table->unsignedBigInteger('conversations_count')->default(0);
                    $table->json('connection_settings')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
            }

            // 2. whatsapp_conversations upgrades
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

            // 3. whatsapp_contacts
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
                    $table->string('status_in_crm')->nullable();
                    $table->string('service')->nullable();
                    $table->string('country')->nullable();
                    $table->string('lead_source')->nullable();
                    $table->string('opt_out_status')->default('opted_in');
                    $table->json('tags')->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamp('first_contact_at')->nullable();
                    $table->timestamp('last_contact_at')->nullable();
                    $table->unsignedBigInteger('conversation_count')->default(0);
                    $table->timestamps();
                });
            }

            // 4. whatsapp_templates
            if (!Schema::hasTable('whatsapp_templates')) {
                Schema::create('whatsapp_templates', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('category')->default('general');
                    $table->text('content');
                    $table->string('media_type')->nullable();
                    $table->string('media_url')->nullable();
                    $table->json('variables')->nullable();
                    $table->unsignedBigInteger('created_by_user_id')->nullable();
                    $table->timestamp('last_used_at')->nullable();
                    $table->timestamps();
                });
            }

            // 5. whatsapp_campaigns
            if (!Schema::hasTable('whatsapp_campaigns')) {
                Schema::create('whatsapp_campaigns', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('type')->default('bulk');
                    $table->unsignedBigInteger('whatsapp_account_id');
                    $table->unsignedBigInteger('created_by_user_id')->nullable();
                    $table->string('status')->default('draft');
                    $table->string('audience_source')->nullable();
                    $table->json('audience_filters')->nullable();
                    $table->text('message_content')->nullable();
                    $table->string('media_type')->nullable();
                    $table->string('media_url')->nullable();
                    $table->unsignedBigInteger('template_id')->nullable();
                    $table->string('schedule_type')->default('now');
                    $table->timestamp('scheduled_at')->nullable();
                    $table->time('sending_window_start')->nullable()->default('08:00:00');
                    $table->time('sending_window_end')->nullable()->default('20:00:00');
                    $table->json('allowed_days')->nullable();
                    $table->string('interval_type')->default('fixed');
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
                });
            }

            // 6. whatsapp_campaign_recipients
            if (!Schema::hasTable('whatsapp_campaign_recipients')) {
                Schema::create('whatsapp_campaign_recipients', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('campaign_id');
                    $table->unsignedBigInteger('whatsapp_account_id');
                    $table->string('phone');
                    $table->string('normalized_phone')->index();
                    $table->string('contact_name')->nullable();
                    $table->string('contact_status')->default('not_previously_contacted');
                    $table->boolean('is_selected')->default(true);
                    $table->unsignedBigInteger('whatsapp_contact_id')->nullable();
                    $table->unsignedBigInteger('lead_id')->nullable();
                    $table->unsignedBigInteger('customer_id')->nullable();
                    $table->json('custom_variables')->nullable();
                    $table->string('status')->default('pending');
                    $table->timestamp('sent_at')->nullable();
                    $table->text('error_message')->nullable();
                    $table->integer('retry_count')->default(0);
                    $table->timestamps();
                });
            }

            // 7. whatsapp_sequences
            if (!Schema::hasTable('whatsapp_sequences')) {
                Schema::create('whatsapp_sequences', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->text('description')->nullable();
                    $table->unsignedBigInteger('whatsapp_account_id')->nullable();
                    $table->string('trigger_event')->default('manual');
                    $table->boolean('smart_stop_on_reply')->default(true);
                    $table->boolean('smart_stop_on_convert')->default(true);
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
            }

            // 8. whatsapp_sequence_steps
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
                });
            }

            // 9. whatsapp_sequence_subscribers
            if (!Schema::hasTable('whatsapp_sequence_subscribers')) {
                Schema::create('whatsapp_sequence_subscribers', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('sequence_id');
                    $table->unsignedBigInteger('whatsapp_contact_id');
                    $table->integer('current_step')->default(1);
                    $table->string('status')->default('active');
                    $table->timestamp('next_execution_at')->nullable();
                    $table->timestamps();
                });
            }

            // 10. whatsapp_blacklist
            if (!Schema::hasTable('whatsapp_blacklist')) {
                Schema::create('whatsapp_blacklist', function (Blueprint $table) {
                    $table->id();
                    $table->string('phone');
                    $table->string('normalized_phone')->unique();
                    $table->string('reason')->nullable();
                    $table->unsignedBigInteger('added_by_user_id')->nullable();
                    $table->timestamps();
                });
            }

            // 11. whatsapp_import_history
            if (!Schema::hasTable('whatsapp_import_history')) {
                Schema::create('whatsapp_import_history', function (Blueprint $table) {
                    $table->id();
                    $table->string('file_name');
                    $table->string('campaign_type')->default('bulk');
                    $table->integer('total_numbers')->default(0);
                    $table->integer('valid_count')->default(0);
                    $table->integer('invalid_count')->default(0);
                    $table->integer('duplicate_count')->default(0);
                    $table->integer('imported_count')->default(0);
                    $table->integer('rejected_count')->default(0);
                    $table->unsignedBigInteger('uploaded_by_user_id')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            Log::error('WhatsAppSchemaInstaller Exception: ' . $e->getMessage());
        }
    }
}
