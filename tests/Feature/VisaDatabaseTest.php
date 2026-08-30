<?php

namespace Tests\Feature;

use App\Models\EmbassyAppointment;
use App\Models\User;
use App\Models\VisaCountry;
use App\Models\VisaRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VisaDatabaseTest extends TestCase
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
            'database/migrations/2026_03_18_155649_create_inquiries_table.php',
            'database/migrations/2026_03_18_155647_create_settings_table.php',
            'database/migrations/2026_08_24_160000_create_visa_records_and_pivot_tables.php',
            'database/migrations/2026_08_24_220000_create_embassy_appointments_tables.php',
            'database/migrations/2026_03_20_130000_create_lead_forms_tables.php',
            'database/migrations/2026_03_21_100000_create_map_sections_tables.php',
            'database/migrations/2026_08_25_150000_create_public_catalog_settings_table.php',
        ] as $migrationPath) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--path' => $migrationPath]);
        }

        VisaRecord::ensureTableSchema();
        EmbassyAppointment::ensureTableSchema();
        \App\Support\AccessControl::syncPermissionsInDatabase();
        app()->setLocale('ar');
        session(['locale' => 'ar']);
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

    public function test_admin_can_toggle_visa_record_status_and_it_syncs_with_public_catalog()
    {
        $admin = $this->createAdminUser();

        $cat = \App\Models\VisaCategory::firstOrCreate(
            ['slug' => 'europe-test'],
            ['name_ar' => 'تأشيرات أوروبا', 'name_en' => 'Europe Visas']
        );

        $country = VisaCountry::where('slug', 'germany')->first();
        if ($country) {
            $country->update(['is_active' => true]);
        } else {
            $country = VisaCountry::create([
                'visa_category_id' => $cat->id,
                'name_ar' => 'ألمانيا',
                'name_en' => 'Germany',
                'slug' => 'germany',
                'is_active' => true,
            ]);
        }

        VisaRecord::where('visa_country_id', $country->id)->delete();

        $record = VisaRecord::create([
            'visa_country_id' => $country->id,
            'visa_type' => 'سياحة',
            'price' => 5000,
            'status' => 'active',
        ]);

        // 1. Initially active -> visible in public catalog when searching for Germany
        $respCatalog = $this->get(route('visa-database.public-catalog', ['search' => 'ألمانيا']));
        $respCatalog->assertOk();
        $respCatalog->assertSee('ألمانيا');

        // 2. Admin toggles OFF via JSON / AJAX
        $responseToggleOff = $this->actingAs($admin)
            ->patchJson(route('admin.visa-database.toggle-status', $record), [
                'status' => 'inactive',
            ]);

        $responseToggleOff->assertOk();
        $responseToggleOff->assertJson([
            'success' => true,
            'status' => 'inactive',
            'is_active' => false,
        ]);

        $this->assertEquals('inactive', $record->fresh()->status);
        $this->assertFalse((bool) $country->fresh()->is_active);

        // 3. Hidden from public catalog when OFF
        $respCatalogOff = $this->get(route('visa-database.public-catalog', ['search' => 'ألمانيا']));
        $respCatalogOff->assertOk();
        $respCatalogOff->assertSeeText('لم يتم العثور على نتائج');

        // 4. Admin toggles ON again
        $responseToggleOn = $this->actingAs($admin)
            ->patchJson(route('admin.visa-database.toggle-status', $record), [
                'status' => 'active',
            ]);

        $responseToggleOn->assertOk();
        $responseToggleOn->assertJson([
            'success' => true,
            'status' => 'active',
            'is_active' => true,
        ]);

        $this->assertEquals('active', $record->fresh()->status);
        $this->assertTrue((bool) $country->fresh()->is_active);

        // 5. Reappears in public catalog
        $respCatalogOn = $this->get(route('visa-database.public-catalog', ['search' => 'ألمانيا']));
        $respCatalogOn->assertOk();
        $respCatalogOn->assertSee('ألمانيا');
    }

    public function test_visa_record_displays_latest_embassy_appointment_status_and_date()
    {
        $admin = $this->createAdminUser();

        $cat = \App\Models\VisaCategory::first() ?: \App\Models\VisaCategory::create([
            'name_ar' => 'تأشيرات أوروبا',
            'name_en' => 'Europe Visas',
            'slug' => 'europe',
        ]);

        $country = VisaCountry::where('slug', 'france')->first();
        if ($country) {
            $country->update(['is_active' => true]);
        } else {
            $country = VisaCountry::create([
                'visa_category_id' => $cat->id,
                'name_ar' => 'فرنسا',
                'name_en' => 'France',
                'slug' => 'france',
                'is_active' => true,
            ]);
        }

        $record = VisaRecord::create([
            'visa_country_id' => $country->id,
            'visa_type' => 'سياحة',
            'status' => 'active',
        ]);

        $appt = EmbassyAppointment::create([
            'visa_country_id' => $country->id,
            'visa_type' => 'سياحة',
            'appointment_center' => 'VFS',
            'appointment_type' => 'Regular',
            'status' => 'available_later',
            'earliest_date' => 'آخر شهر 12',
            'last_updated_at' => now(),
        ]);

        $this->assertNotNull($record->latest_embassy_appointment);
        $this->assertEquals('available_later', $record->latest_embassy_appointment->status);
        $this->assertEquals('آخر شهر 12', $record->latest_embassy_appointment->earliest_date);

        $response = $this->actingAs($admin)->get(route('admin.visa-database.index', ['country_id' => $country->id]));
        $response->assertOk();
        $response->assertSee('آخر شهر 12');
        $response->assertSee('بتاريخ مستقبلي');
    }

    public function test_admin_can_access_and_save_catalog_settings_and_public_catalog_respects_them()
    {
        $admin = $this->createAdminUser();

        // 1. Access Settings page
        $responseGet = $this->actingAs($admin)->get(route('admin.visa-database.catalog-settings'));
        $responseGet->assertOk();
        $responseGet->assertSee('مركز تحكم الدليل العام');

        // 2. Post new settings
        $responsePost = $this->actingAs($admin)->post(route('admin.visa-database.update-catalog-settings'), [
            'show_price' => '0',
            'show_embassy_fee' => '1',
            'show_working_days' => '1',
            'whatsapp_phone' => '201234567890',
            'whatsapp_message_template' => 'طلب جديد لتأشيرة {country_name}',
            'floating_whatsapp_enabled' => '1',
            'custom_buttons' => [
                [
                    'title_ar' => 'تحميل الشروط PDF',
                    'url' => 'https://example.com/file.pdf',
                    'icon' => 'bi-file-pdf',
                    'button_class' => 'btn-danger',
                    'is_active' => '1',
                ],
            ],
        ]);

        $responsePost->assertRedirect();
        $responsePost->assertSessionHas('success');

        $setting = \App\Models\PublicCatalogSetting::getSettings();
        $this->assertFalse((bool) $setting->show_price);
        $this->assertEquals('201234567890', $setting->whatsapp_phone);
        $this->assertCount(1, $setting->custom_buttons);

        // 3. Check public catalog page
        $responseCatalog = $this->get(route('visa-database.public-catalog'));
        $responseCatalog->assertOk();
        $responseCatalog->assertSee('201234567890');
        $responseCatalog->assertSee('تحميل الشروط PDF');
    }

    public function test_public_preview_auto_prefills_country_and_visa_type_and_supports_language_switcher()
    {
        $cat = \App\Models\VisaCategory::firstOrCreate(['slug' => 'gulf'], ['name_ar' => 'دول الخليج', 'name_en' => 'Gulf']);
        $country = VisaCountry::firstOrCreate(['slug' => 'qatar'], [
            'visa_category_id' => $cat->id,
            'name_ar' => 'قطر',
            'name_en' => 'Qatar',
            'is_active' => true,
        ]);

        $record = VisaRecord::create([
            'visa_country_id' => $country->id,
            'visa_type' => 'تأشيرة سياحية',
            'status' => 'active',
        ]);

        session(['locale' => 'ar']);
        app()->setLocale('ar');

        $form = \App\Models\LeadForm::create([
            'name' => 'نموذج قطر',
            'slug' => 'qatar-form-' . rand(1000, 9999),
            'form_category' => 'visa',
            'is_active' => true,
        ]);

        \App\Models\LeadFormField::create([
            'lead_form_id' => $form->id,
            'field_key' => 'country',
            'type' => 'text',
            'label_ar' => 'الدولة',
            'label_en' => 'Country',
            'is_enabled' => true,
        ]);

        \App\Models\LeadFormField::create([
            'lead_form_id' => $form->id,
            'field_key' => 'visa_type',
            'type' => 'select',
            'label_ar' => 'نوع التأشيرة',
            'label_en' => 'Visa Type',
            'options' => [['value' => 'تأشيرة سياحية', 'label_ar' => 'سياحية']],
            'is_enabled' => true,
        ]);

        $setting = \App\Models\PublicCatalogSetting::getSettings();
        $setting->update(['selected_lead_form_id' => $form->id]);

        // Test Qatar preview page pre-fills "قطر" and "تأشيرة سياحية"
        $respPreview = $this->get(route('visa-database.public-preview', $record->id));
        $respPreview->assertOk();
        $respPreview->assertSee('value="قطر"', false);
        $respPreview->assertSee('تأشيرة سياحية');

        // Test English Language Switcher
        $respSwitch = $this->get(route('locale.switch', 'en'));
        $respSwitch->assertRedirect();

        $respPreviewEn = $this->get(route('visa-database.public-preview', $record->id));
        $respPreviewEn->assertOk();
        $respPreviewEn->assertSee('Qatar');
        $respPreviewEn->assertSee('Visa Details for Qatar');
    }
}
