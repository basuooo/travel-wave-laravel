<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Inquiry;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_audit_log_index(): void
    {
        $admin = User::query()->create([
            'name' => 'Audit Admin',
            'email' => 'audit-admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        AuditLog::query()->create([
            'user_id' => $admin->id,
            'module' => 'crm_leads',
            'action_type' => 'created',
            'title' => 'Lead created',
            'target_label' => 'Lead #1',
            'created_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('Audit Log')
            ->assertSee('Lead #1');
    }

    public function test_updating_lead_creates_audit_log_entry(): void
    {
        $admin = User::query()->create([
            'name' => 'Audit Admin 2',
            'email' => 'audit-admin-2@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Audit Lead',
            'phone' => '01055555555',
        ]);

        $this->actingAs($admin)->put(route('admin.crm.leads.update', $lead), [
            'full_name' => 'Audit Lead Updated',
            'phone' => '01055555555',
        ])->assertRedirect(route('admin.crm.leads.show', $lead));

        $this->assertDatabaseHas('audit_logs', [
            'module' => 'crm_leads',
            'action_type' => 'updated',
            'auditable_type' => Inquiry::class,
            'auditable_id' => $lead->id,
        ]);
    }

    public function test_user_without_audit_permission_cannot_open_audit_log_page(): void
    {
        $user = User::query()->create([
            'name' => 'Limited User',
            'email' => 'limited-user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        $dashboardAccess = Permission::query()->firstOrCreate([
            'slug' => 'dashboard.access',
        ], [
            'name' => 'Dashboard Access',
            'module' => 'dashboard',
        ]);

        $role = Role::query()->create([
            'name' => 'Limited Role',
            'slug' => 'limited-role',
            'is_system' => false,
        ]);
        $role->permissions()->sync([$dashboardAccess->id]);
        $user->roles()->sync([$role->id]);

        $this->actingAs($user)
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }
}
