<?php

namespace Tests\Feature;

use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkflowAutomation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WorkflowAutomationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_workflow_automation_index(): void
    {
        $admin = $this->createUserWithPermissions('Workflow Admin', 'workflow-admin@example.com', [
            'dashboard.access',
            'workflow_automations.view',
            'workflow_automations.manage',
        ]);

        WorkflowAutomation::query()->create([
            'name' => 'Auto assign new manual leads',
            'trigger_type' => 'lead_created',
            'entity_type' => 'lead',
            'actions' => [
                'assign_user' => ['user_id' => $admin->id],
            ],
            'is_active' => true,
            'priority' => 100,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.workflow-automations.index'))
            ->assertOk()
            ->assertSee('Auto assign new manual leads');
    }

    public function test_lead_created_workflow_can_assign_user_and_create_task(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $admin = $this->createUserWithPermissions('Workflow Admin 2', 'workflow-admin-2@example.com', [
            'dashboard.access',
            'workflow_automations.view',
            'workflow_automations.manage',
            'leads.view',
            'leads.edit',
        ]);

        $seller = User::query()->create([
            'name' => 'Assigned Seller',
            'email' => 'assigned-seller@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        WorkflowAutomation::query()->create([
            'name' => 'New lead onboarding',
            'trigger_type' => 'lead_created',
            'entity_type' => 'lead',
            'conditions' => ['assigned_user_empty' => true],
            'actions' => [
                'assign_user' => ['user_id' => $seller->id],
                'create_task' => [
                    'title' => 'Initial follow-up for {lead_name}',
                    'priority' => CrmTask::PRIORITY_HIGH,
                    'category' => CrmTask::CATEGORY_CUSTOMER_FOLLOWUP,
                    'assign_to' => 'linked_owner',
                ],
            ],
            'is_active' => true,
            'priority' => 100,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->post(route('admin.crm.leads.store'), [
            'full_name' => 'Workflow Lead',
            'phone' => '01090000001',
        ])->assertRedirect(route('admin.crm.leads.index'));

        $lead = Inquiry::query()->where('full_name', 'Workflow Lead')->firstOrFail();

        $this->assertSame($seller->id, $lead->assigned_user_id);
        $this->assertDatabaseHas('crm_tasks', [
            'inquiry_id' => $lead->id,
            'assigned_user_id' => $seller->id,
            'title' => 'Initial follow-up for Workflow Lead',
        ]);
        $this->assertDatabaseHas('workflow_execution_logs', [
            'entity_type' => 'lead',
            'entity_id' => $lead->id,
            'execution_status' => 'success',
        ]);
    }

    public function test_scheduled_task_overdue_workflow_creates_execution_log(): void
    {
        $admin = $this->createUserWithPermissions('Workflow Admin 3', 'workflow-admin-3@example.com', [
            'dashboard.access',
            'workflow_automations.view',
            'workflow_automations.manage',
        ]);

        $seller = User::query()->create([
            'name' => 'Overdue Seller',
            'email' => 'overdue-seller@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        WorkflowAutomation::query()->create([
            'name' => 'Overdue task reminder',
            'trigger_type' => 'task_overdue',
            'entity_type' => 'task',
            'conditions' => ['min_overdue_days' => 1],
            'actions' => [
                'send_notification' => [
                    'recipient_mode' => 'linked_owner',
                    'severity' => 'warning',
                    'title_en' => 'Task is overdue',
                    'message_en' => 'Please review {task_title}',
                ],
            ],
            'is_active' => true,
            'priority' => 100,
            'created_by' => $admin->id,
        ]);

        $task = CrmTask::query()->create([
            'title' => 'Collect missing document',
            'assigned_user_id' => $seller->id,
            'created_by' => $admin->id,
            'task_type' => CrmTask::TYPE_GENERAL,
            'category' => CrmTask::CATEGORY_DOCUMENTS,
            'priority' => CrmTask::PRIORITY_MEDIUM,
            'status' => CrmTask::STATUS_NEW,
            'due_at' => now()->subDay(),
            'last_activity_at' => now()->subDay(),
        ]);

        Artisan::call('workflow:run-scheduled');

        $this->assertDatabaseHas('workflow_execution_logs', [
            'entity_type' => 'task',
            'entity_id' => $task->id,
            'execution_status' => 'success',
        ]);
        $this->assertSame(1, $seller->notifications()->count());
    }

    protected function createUserWithPermissions(string $name, string $email, array $permissionSlugs): User
    {
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $permissionIds = collect($permissionSlugs)->map(function (string $slug) {
            return Permission::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $slug, 'module' => 'tests']
            )->id;
        })->all();

        $role = Role::query()->create([
            'name' => $name . ' Role',
            'slug' => strtolower(str_replace(' ', '-', $name)) . '-role',
            'is_system' => false,
        ]);
        $role->permissions()->sync($permissionIds);
        $user->roles()->sync([$role->id]);

        return $user;
    }
}
