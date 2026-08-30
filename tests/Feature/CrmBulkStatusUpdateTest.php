<?php

namespace Tests\Feature;

use App\Models\CrmStatus;
use App\Models\Inquiry;
use App\Models\User;
use App\Support\CrmLeadTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CrmBulkStatusUpdateTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'database/migrations/2014_10_12_000000_create_users_table.php',
            'database/migrations/2026_03_18_155647_add_is_admin_to_users_table.php',
            'database/migrations/2026_03_18_155649_create_inquiries_table.php',
            'database/migrations/2026_03_21_190000_add_user_access_fields_and_create_rbac_tables.php',
            'database/migrations/2026_03_22_210000_create_crm_core_tables.php',
            'database/migrations/2026_03_22_220000_upgrade_crm_status_workflow.php',
            'database/migrations/2026_03_22_230000_simplify_crm_workflow.php',
            'database/migrations/2026_03_25_210000_create_audit_logs_table.php',
        ] as $migrationPath) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--path' => $migrationPath]);
        }

        \App\Support\AccessControl::syncPermissionsInDatabase();
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create(['is_admin' => true]);
        $role = \App\Models\Role::query()->firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator', 'description' => 'System Administrator']
        );
        $role->permissions()->sync(\App\Models\Permission::pluck('id'));
        $user->roles()->sync([$role->id]);
        return $user->fresh();
    }

    public function test_crm_bulk_status_update_mode_previews_existing_leads_and_omits_non_existing()
    {
        $admin = $this->createAdminUser();

        $oldStatus = CrmStatus::create([
            'slug' => 'new',
            'name_ar' => 'جديد',
            'name_en' => 'New',
            'status_group' => 'primary',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $newStatus = CrmStatus::create([
            'slug' => 'contacted',
            'name_ar' => 'تم الاتصال',
            'name_en' => 'Contacted',
            'status_group' => 'primary',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Create an existing lead in system assigned to admin seller
        $existingLead = Inquiry::create([
            'full_name' => 'علي حسن',
            'phone' => '01099998888',
            'crm_status_id' => $oldStatus->id,
            'status' => $oldStatus->slug,
            'assigned_user_id' => $admin->id,
        ]);

        // Upload CSV containing 2 rows: 1 existing, 1 non-existing
        $csvContent = "اسم العميل,رقم الموبايل\nعلي حسن,01099998888\nعميل جديد غريب,01200001111\n";
        $file = UploadedFile::fake()->createWithContent('leads.csv', $csvContent);

        $responsePreview = $this->actingAs($admin)
            ->post(route('admin.crm.leads.import.preview'), [
                'duplicate_mode' => CrmLeadTransferService::DUPLICATE_MODE_STATUS_UPDATE,
                'duplicate_detector' => CrmLeadTransferService::DUPLICATE_DETECTOR_PHONE,
                'import_file' => $file,
            ]);

        $responsePreview->assertRedirect(route('admin.crm.leads.transfer'));
        $responsePreview->assertSessionHas('crm_leads_import_preview');

        $preview = session('crm_leads_import_preview');
        $this->assertEquals(CrmLeadTransferService::DUPLICATE_MODE_STATUS_UPDATE, $preview['duplicate_mode']);

        // Only the existing lead should be in rows (1 row total)
        $this->assertCount(1, $preview['rows']);
        $this->assertEquals('01099998888', $preview['rows'][0]['mapped']['phone']);
        $this->assertEquals($oldStatus->id, $preview['rows'][0]['current_crm_status_id']);
        $this->assertEquals('جديد', $preview['rows'][0]['current_crm_status_name']);
        $this->assertEquals($admin->name, $preview['rows'][0]['assigned_user_name']);
        $this->assertEquals(1, $preview['summary']['omitted_rows']);

        // Now confirm the import with target status
        $responseImport = $this->actingAs($admin)
            ->post(route('admin.crm.leads.import'), [
                'target_crm_status_id' => $newStatus->id,
            ]);

        $responseImport->assertRedirect(route('admin.crm.leads.transfer'));
        $responseImport->assertSessionHas('success');

        // Verify existing lead status was updated
        $existingLead->refresh();
        $this->assertEquals($newStatus->id, $existingLead->crm_status_id);
        $this->assertEquals($newStatus->slug, $existingLead->status);
        $this->assertNotNull($existingLead->crm_status_updated_at);

        // Verify no new inquiry was created (total count remains 1)
        $this->assertEquals(1, Inquiry::count());
    }

    public function test_admin_can_bulk_force_delete_and_bulk_restore_trashed_leads()
    {
        $admin = $this->createAdminUser();

        $lead1 = Inquiry::create(['full_name' => 'ليد محذوف 1', 'phone' => '01011112222']);
        $lead2 = Inquiry::create(['full_name' => 'ليد محذوف 2', 'phone' => '01033334444']);
        $lead3 = Inquiry::create(['full_name' => 'ليد محذوف 3', 'phone' => '01055556666']);

        $lead1->delete();
        $lead2->delete();
        $lead3->delete();

        $this->assertEquals(3, Inquiry::onlyTrashed()->count());

        // Test bulk restore lead1 & lead2
        $responseRestore = $this->actingAs($admin)
            ->post(route('admin.crm.leads.trash.bulk-restore'), [
                'ids' => [$lead1->id, $lead2->id],
            ]);

        $responseRestore->assertRedirect(route('admin.crm.leads.trash'));
        $responseRestore->assertSessionHas('success');

        $this->assertEquals(1, Inquiry::onlyTrashed()->count());
        $this->assertEquals(2, Inquiry::query()->count());

        // Test bulk force delete lead3
        $responseForceDelete = $this->actingAs($admin)
            ->delete(route('admin.crm.leads.trash.bulk-force-destroy'), [
                'ids' => [$lead3->id],
            ]);

        $responseForceDelete->assertRedirect(route('admin.crm.leads.trash'));
        $responseForceDelete->assertSessionHas('success');

        $this->assertEquals(0, Inquiry::onlyTrashed()->count());
        $this->assertDatabaseMissing('inquiries', ['id' => $lead3->id]);
    }
}
