<?php

namespace Tests\Feature;

use App\Models\CrmStatus;
use App\Models\EmbassyAppointment;
use App\Models\EmbassyAppointmentNotification;
use App\Models\EmbassyAvailabilityEvent;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\VisaCountry;
use App\Support\EmbassyAppointmentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EmbassyAppointmentTest extends TestCase
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
            'database/migrations/2026_08_24_160000_create_visa_records_and_pivot_tables.php',
            'database/migrations/2026_03_18_155649_create_inquiries_table.php',
            'database/migrations/2026_03_22_210000_create_crm_core_tables.php',
            'database/migrations/2026_03_22_220000_upgrade_crm_status_workflow.php',
            'database/migrations/2026_03_22_230000_simplify_crm_workflow.php',
            'database/migrations/2026_08_24_220000_create_embassy_appointments_tables.php',
        ] as $migrationPath) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--path' => $migrationPath]);
        }
        VisaCountry::ensureTableSchema();
    }

    public function test_can_create_and_update_embassy_appointment()
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $category = \App\Models\VisaCategory::firstOrCreate(
            ['slug' => 'europe-test'],
            ['name_ar' => 'أوروبا', 'name_en' => 'Europe']
        );

        $country = VisaCountry::firstOrCreate(
            ['slug' => 'spain-test'],
            ['visa_category_id' => $category->id, 'name_ar' => 'إسبانيا للاختبار', 'name_en' => 'Spain Test']
        );

        $response = $this->actingAs($admin)->post(route('admin.embassy-appointments.store'), [
            'visa_country_id' => $country->id,
            'visa_type' => 'سياحة',
            'appointment_center' => 'BLS',
            'appointment_type' => 'Regular',
            'status' => 'no_availability',
            'earliest_date' => '2026-09-28',
            'notes' => 'اختبار المواعيد',
        ]);

        $response->assertRedirect(route('admin.embassy-appointments.index'));

        $this->assertDatabaseHas('embassy_appointments', [
            'visa_country_id' => $country->id,
            'visa_type' => 'سياحة',
            'appointment_center' => 'BLS',
            'appointment_type' => 'Regular',
            'status' => 'no_availability',
        ]);
    }

    public function test_available_now_triggers_event_and_notifies_seller()
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $sellerA = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $sellerB = User::factory()->create(['is_admin' => true, 'is_active' => true]);

        $category = \App\Models\VisaCategory::firstOrCreate(
            ['slug' => 'europe-test'],
            ['name_ar' => 'أوروبا', 'name_en' => 'Europe']
        );

        $country = VisaCountry::firstOrCreate(
            ['slug' => 'spain-test-2'],
            ['visa_category_id' => $category->id, 'name_ar' => 'إسبانيا', 'name_en' => 'Spain']
        );

        $awaitingStatus = CrmStatus::firstOrCreate(
            ['slug' => 'awaiting-embassy-appointment'],
            ['name_ar' => 'انتظار فتح مواعيد السفارة', 'name_en' => 'Awaiting Embassy Appointment', 'is_active' => true]
        );

        // Lead for Seller A
        $leadA = Inquiry::create([
            'full_name' => 'محمد أحمد',
            'phone' => '01000000001',
            'country' => 'إسبانيا',
            'visa_country_id' => $country->id,
            'appointment_center' => 'BLS',
            'appointment_type' => 'Regular',
            'crm_status_id' => $awaitingStatus->id,
            'assigned_user_id' => $sellerA->id,
        ]);

        // Lead for Seller B
        $leadB = Inquiry::create([
            'full_name' => 'محمود علي',
            'phone' => '01000000002',
            'country' => 'إسبانيا',
            'visa_country_id' => $country->id,
            'appointment_center' => 'BLS',
            'appointment_type' => 'Regular',
            'crm_status_id' => $awaitingStatus->id,
            'assigned_user_id' => $sellerB->id,
        ]);

        $appt = EmbassyAppointment::create([
            'visa_country_id' => $country->id,
            'visa_type' => 'سياحة',
            'appointment_center' => 'BLS',
            'appointment_type' => 'Regular',
            'status' => 'no_availability',
        ]);

        // Trigger Available Now
        $service = app(EmbassyAppointmentService::class);
        $service->updateStatus($appt, EmbassyAppointment::STATUS_AVAILABLE_NOW, '2026-09-28', 'تم فتح المواعيد', $admin);

        $this->assertDatabaseHas('embassy_availability_events', [
            'embassy_appointment_id' => $appt->id,
        ]);

        // Verify notification for Seller A
        $this->assertDatabaseHas('embassy_appointment_notifications', [
            'inquiry_id' => $leadA->id,
            'seller_id' => $sellerA->id,
            'status' => 'pending',
        ]);

        // Verify notification for Seller B
        $this->assertDatabaseHas('embassy_appointment_notifications', [
            'inquiry_id' => $leadB->id,
            'seller_id' => $sellerB->id,
            'status' => 'pending',
        ]);

        // Verify API endpoint returns ONLY Seller A's notification for Seller A
        $responseA = $this->actingAs($sellerA)->getJson(route('admin.embassy-appointments.pending-popups'));
        $responseA->assertOk();
        $responseA->assertJsonCount(1, 'items');
        $responseA->assertJsonPath('items.0.lead_name', 'محمد أحمد');

        // Verify Seller B only sees Seller B's notification
        $responseB = $this->actingAs($sellerB)->getJson(route('admin.embassy-appointments.pending-popups'));
        $responseB->assertOk();
        $responseB->assertJsonCount(1, 'items');
        $responseB->assertJsonPath('items.0.lead_name', 'محمود علي');
    }

    public function test_seller_snooze_and_contacted_workflow()
    {
        $seller = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $category = \App\Models\VisaCategory::firstOrCreate(
            ['slug' => 'europe-test'],
            ['name_ar' => 'أوروبا', 'name_en' => 'Europe']
        );
        $country = VisaCountry::firstOrCreate(
            ['slug' => 'italy-test'],
            ['visa_category_id' => $category->id, 'name_ar' => 'إيطاليا', 'name_en' => 'Italy']
        );

        $awaitingStatus = CrmStatus::firstOrCreate(
            ['slug' => 'awaiting-embassy-appointment'],
            ['name_ar' => 'انتظار فتح مواعيد السفارة', 'name_en' => 'Awaiting Embassy Appointment', 'is_active' => true]
        );

        $lead = Inquiry::create([
            'full_name' => 'أحمد علي',
            'phone' => '01000000003',
            'country' => 'إيطاليا',
            'visa_country_id' => $country->id,
            'crm_status_id' => $awaitingStatus->id,
            'assigned_user_id' => $seller->id,
        ]);

        $appt = EmbassyAppointment::create([
            'visa_country_id' => $country->id,
            'visa_type' => 'سياحة',
            'appointment_center' => 'VFS',
            'appointment_type' => 'Regular',
            'status' => 'no_availability',
        ]);

        $service = app(EmbassyAppointmentService::class);
        $service->updateStatus($appt, EmbassyAppointment::STATUS_AVAILABLE_NOW, '2026-09-30', 'متاح', $seller);

        $notification = EmbassyAppointmentNotification::where('inquiry_id', $lead->id)->first();
        $this->assertNotNull($notification);

        // Snooze for 30 minutes
        $responseSnooze = $this->actingAs($seller)->postJson(route('admin.embassy-appointments.notifications.snooze', $notification->id), [
            'snooze_option' => '30',
        ]);
        $responseSnooze->assertOk();

        $notification->refresh();
        $this->assertEquals('snoozed', $notification->status);
        $this->assertNotNull($notification->snoozed_until);

        // During snooze period, pending popups should be empty
        $popupResponse = $this->actingAs($seller)->getJson(route('admin.embassy-appointments.pending-popups'));
        $popupResponse->assertJsonCount(0, 'items');

        // Mark as Contacted
        $responseContact = $this->actingAs($seller)->postJson(route('admin.embassy-appointments.notifications.contact', $notification->id), [
            'contact_result' => 'agreed',
            'contact_notes' => 'العميل متحمس للحجز',
        ]);
        $responseContact->assertOk();

        $notification->refresh();
        $this->assertEquals('contacted', $notification->status);
        $this->assertEquals('agreed', $notification->contact_result);

        // Verify CrmLeadNote was logged
        $this->assertDatabaseHas('crm_lead_notes', [
            'inquiry_id' => $lead->id,
            'user_id' => $seller->id,
        ]);
    }

    public function test_can_store_bulk_embassy_appointments()
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $category = \App\Models\VisaCategory::firstOrCreate(
            ['slug' => 'europe-test-bulk'],
            ['name_ar' => 'أوروبا', 'name_en' => 'Europe']
        );

        $c1 = VisaCountry::firstOrCreate(
            ['slug' => 'germany-bulk'],
            ['visa_category_id' => $category->id, 'name_ar' => 'ألمانيا', 'name_en' => 'Germany']
        );
        $c2 = VisaCountry::firstOrCreate(
            ['slug' => 'france-bulk'],
            ['visa_category_id' => $category->id, 'name_ar' => 'فرنسا', 'name_en' => 'France']
        );

        $response = $this->actingAs($admin)->post(route('admin.embassy-appointments.store-bulk'), [
            'appointments' => [
                [
                    'visa_country_id' => $c1->id,
                    'visa_type' => 'سياحة',
                    'appointment_center' => 'VFS',
                    'appointment_type' => 'Regular',
                    'status' => 'available_later',
                    'earliest_date' => 'شهر 12',
                ],
                [
                    'visa_country_id' => $c2->id,
                    'visa_type' => 'سياحة',
                    'appointment_center' => 'TLS',
                    'appointment_type' => 'Regular',
                    'status' => 'available_later',
                    'earliest_date' => 'شهر 1',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.embassy-appointments.index'));

        $this->assertDatabaseHas('embassy_appointments', [
            'visa_country_id' => $c1->id,
            'earliest_date' => 'شهر 12',
        ]);
        $this->assertDatabaseHas('embassy_appointments', [
            'visa_country_id' => $c2->id,
            'earliest_date' => 'شهر 1',
        ]);
    }
}
