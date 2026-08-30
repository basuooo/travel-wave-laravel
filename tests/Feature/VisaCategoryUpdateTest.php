<?php

namespace Tests\Feature;

use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VisaCategoryUpdateTest extends TestCase
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
    }

    public function test_visa_categories_and_countries_updates()
    {
        // 1. Verify empty "شنغن (Schengen)" category is deleted / inactive
        $schengen = VisaCategory::withTrashed()->where('slug', 'schengen')->first();
        if ($schengen) {
            $this->assertTrue($schengen->trashed() || ! $schengen->is_active);
        }

        // 2. Verify "دول شنغن خارج الاتحاد الاوروبي" renamed to "دول اوربية خارج الاتحاد الاوروبي"
        $nonEu = VisaCategory::where('slug', 'schengen-non-eu')
            ->orWhere('name_ar', 'like', '%خارج الاتحاد%')
            ->first();

        $this->assertNotNull($nonEu);
        $this->assertEquals('دول اوربية خارج الاتحاد الاوروبي', $nonEu->name_ar);

        // 3. Verify Norway & Switzerland belong to EU category
        $euCat = VisaCategory::where('slug', 'eu')
            ->orWhere('name_ar', 'like', '%الاتحاد%')
            ->first();
        $this->assertNotNull($euCat);

        $norway = VisaCountry::where('slug', 'norway')->first();
        $switzerland = VisaCountry::where('slug', 'switzerland')->first();

        $this->assertNotNull($norway);
        $this->assertNotNull($switzerland);
        $this->assertEquals($euCat->id, $norway->visa_category_id);
        $this->assertEquals($euCat->id, $switzerland->visa_category_id);

        // 4. Verify Central America is removed
        $centralAmerica = VisaCategory::withTrashed()->where('slug', 'central-america')->first();
        if ($centralAmerica) {
            $this->assertTrue($centralAmerica->trashed() || ! $centralAmerica->is_active);
        }

        // 5. Verify Caribbean is removed
        $caribbean = VisaCategory::withTrashed()->where('slug', 'caribbean')->first();
        if ($caribbean) {
            $this->assertTrue($caribbean->trashed() || ! $caribbean->is_active);
        }
    }
}
