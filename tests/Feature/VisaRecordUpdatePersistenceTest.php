<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Models\VisaRecord;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VisaRecordUpdatePersistenceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'database/migrations/2014_10_12_000000_create_users_table.php',
            'database/migrations/2026_03_18_155647_add_is_admin_to_users_table.php',
            'database/migrations/2026_03_21_190000_add_user_access_fields_and_create_rbac_tables.php',
            'database/migrations/2026_03_18_155647_create_visa_categories_table.php',
            'database/migrations/2026_03_18_155647_create_visa_countries_table.php',
            'database/migrations/2026_08_30_180000_update_visa_categories_and_countries_structure.php',
        ] as $migrationPath) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--path' => $migrationPath]);
        }

        VisaCategory::ensureTableSchema();
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

    public function test_admin_can_update_price_and_categories_permanently()
    {
        $admin = $this->createAdminUser();

        $cat1 = VisaCategory::create([
            'name_ar' => 'تصنيف اختبار 1',
            'name_en' => 'Test Cat 1',
            'slug' => 'test-cat-1',
            'is_active' => true,
        ]);

        $cat2 = VisaCategory::create([
            'name_ar' => 'تصنيف اختبار 2',
            'name_en' => 'Test Cat 2',
            'slug' => 'test-cat-2',
            'is_active' => true,
        ]);

        $country = VisaCountry::create([
            'visa_category_id' => $cat1->id,
            'name_ar' => 'قطر',
            'name_en' => 'Qatar',
            'slug' => 'qatar',
            'is_active' => true,
        ]);

        $record = VisaRecord::create([
            'visa_country_id' => $country->id,
            'visa_type' => 'تأشيرة سياحية',
            'price' => 5000,
            'currency' => 'EGP',
            'status' => 'active',
        ]);

        // 1. Perform HTTP PUT update to edit price to 12500 and change category to cat2
        $response = $this->actingAs($admin)
            ->put(route('admin.visa-database.update', $record), [
                'visa_country_id' => $country->id,
                'visa_type' => 'تأشيرة سياحية',
                'price' => 12500,
                'currency' => 'EGP',
                'status' => 'active',
                'category_ids' => [$cat2->id],
            ]);

        $response->assertRedirect(route('admin.visa-database.index'));

        // 2. Refresh record and country
        $record->refresh();
        $country->refresh();

        // 3. Verify price is permanently 12500 and NOT reverted
        $this->assertEquals(12500, (float) $record->price);

        // 4. Trigger a model boot by querying VisaRecord again
        $freshRecord = VisaRecord::find($record->id);
        $this->assertEquals(12500, (float) $freshRecord->price);

        // 5. Verify category is updated to cat2 and NOT reverted
        $this->assertEquals($cat2->id, $country->visa_category_id);
        $this->assertTrue($country->categories->contains('id', $cat2->id));
    }
}
