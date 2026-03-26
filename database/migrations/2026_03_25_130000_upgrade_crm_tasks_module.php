<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crm_tasks')) {
            DB::table('crm_tasks')->where('status', 'open')->update(['status' => 'new']);

            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $this->rebuildSqliteTasksTable();
            } else {
                try {
                    Schema::table('crm_tasks', function (Blueprint $table) {
                        $table->dropForeign(['inquiry_id']);
                    });
                } catch (\Throwable $exception) {
                    // Already dropped or database generated a different key name.
                }

                DB::statement('ALTER TABLE crm_tasks MODIFY inquiry_id BIGINT UNSIGNED NULL');

                Schema::table('crm_tasks', function (Blueprint $table) {
                    $table->foreign('inquiry_id', 'crm_tasks_inquiry_fk')->references('id')->on('inquiries')->nullOnDelete();

                    if (! Schema::hasColumn('crm_tasks', 'task_type')) {
                        $table->string('task_type', 20)->default('lead')->after('description');
                    }
                    if (! Schema::hasColumn('crm_tasks', 'priority')) {
                        $table->string('priority', 20)->default('medium')->after('task_type');
                    }
                    if (! Schema::hasColumn('crm_tasks', 'notes')) {
                        $table->text('notes')->nullable()->after('description');
                    }
                    if (! Schema::hasColumn('crm_tasks', 'last_activity_at')) {
                        $table->timestamp('last_activity_at')->nullable()->after('completed_at');
                    }
                    if (! Schema::hasColumn('crm_tasks', 'closed_by')) {
                        $table->unsignedBigInteger('closed_by')->nullable()->after('last_activity_at');
                    }
                    if (! Schema::hasColumn('crm_tasks', 'closed_note')) {
                        $table->text('closed_note')->nullable()->after('closed_by');
                    }
                });

                try {
                    Schema::table('crm_tasks', function (Blueprint $table) {
                        $table->foreign('closed_by', 'crm_tasks_closed_by_fk')->references('id')->on('users')->nullOnDelete();
                    });
                } catch (\Throwable $exception) {
                    // Ignore if already exists.
                }
            }

            DB::table('crm_tasks')
                ->whereNull('task_type')
                ->update(['task_type' => 'lead']);

            DB::table('crm_tasks')
                ->whereNull('priority')
                ->update(['priority' => 'medium']);

            DB::table('crm_tasks')
                ->whereNull('last_activity_at')
                ->update(['last_activity_at' => DB::raw('COALESCE(updated_at, created_at)')]);
        }

        if (! Schema::hasTable('crm_task_activities')) {
            Schema::create('crm_task_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('crm_task_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action_type', 40);
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('crm_task_id', 'crm_task_activities_task_fk')->references('id')->on('crm_tasks')->cascadeOnDelete();
                $table->foreign('user_id', 'crm_task_activities_user_fk')->references('id')->on('users')->nullOnDelete();
                $table->index(['action_type', 'created_at'], 'crm_task_activities_action_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_task_activities');

        if (Schema::hasTable('crm_tasks')) {
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $this->rebuildSqliteTasksTableForDown();

                return;
            }

            try {
                Schema::table('crm_tasks', function (Blueprint $table) {
                    $table->dropForeign('crm_tasks_closed_by_fk');
                    $table->dropForeign('crm_tasks_inquiry_fk');
                });
            } catch (\Throwable $exception) {
                // Ignore if already removed.
            }

            Schema::table('crm_tasks', function (Blueprint $table) {
                foreach (['task_type', 'priority', 'notes', 'last_activity_at', 'closed_by', 'closed_note'] as $column) {
                    if (Schema::hasColumn('crm_tasks', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });

            DB::statement('ALTER TABLE crm_tasks MODIFY inquiry_id BIGINT UNSIGNED NOT NULL');

            Schema::table('crm_tasks', function (Blueprint $table) {
                $table->foreign('inquiry_id', 'crm_tasks_inquiry_id_foreign')->references('id')->on('inquiries')->cascadeOnDelete();
            });

            DB::table('crm_tasks')->where('status', 'new')->update(['status' => 'open']);
        }
    }

    protected function rebuildSqliteTasksTable(): void
    {
        Schema::rename('crm_tasks', 'crm_tasks_legacy');

        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->nullable()->constrained('inquiries')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('task_type', 20)->default('lead');
            $table->string('priority', 20)->default('medium');
            $table->string('status', 20)->default('new');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('closed_note')->nullable();
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO crm_tasks (
                id, inquiry_id, assigned_user_id, created_by, title, description, notes, task_type, priority,
                status, due_at, completed_at, last_activity_at, closed_by, closed_note, created_at, updated_at
            )
            SELECT
                id, inquiry_id, assigned_user_id, created_by, title, description, NULL, 'lead', 'medium',
                CASE WHEN status = 'open' THEN 'new' ELSE status END,
                due_at, completed_at, COALESCE(updated_at, created_at), NULL, NULL, created_at, updated_at
            FROM crm_tasks_legacy
        ");

        Schema::drop('crm_tasks_legacy');
    }

    protected function rebuildSqliteTasksTableForDown(): void
    {
        Schema::rename('crm_tasks', 'crm_tasks_current');

        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('open');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO crm_tasks (id, inquiry_id, assigned_user_id, created_by, title, description, status, due_at, completed_at, created_at, updated_at)
            SELECT
                id, COALESCE(inquiry_id, 1), assigned_user_id, created_by, title, description,
                CASE WHEN status = 'new' THEN 'open' ELSE status END,
                due_at, completed_at, created_at, updated_at
            FROM crm_tasks_current
            WHERE inquiry_id IS NOT NULL
        ");

        Schema::drop('crm_tasks_current');
    }
};
