<?php

namespace Tests\Feature;

use App\Models\CrmCustomer;
use App\Models\CrmDocumentCategory;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_lead_document_and_see_it_on_lead_page(): void
    {
        Storage::fake('local');

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Lead Owner',
            'phone' => '01000000000',
        ]);

        $category = CrmDocumentCategory::query()->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.documents.store'), [
            'documentable_type' => 'inquiry',
            'documentable_id' => $lead->id,
            'crm_document_category_id' => $category->id,
            'title' => 'Passport Copy',
            'file' => UploadedFile::fake()->create('passport.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('crm_documents', [
            'documentable_type' => Inquiry::class,
            'documentable_id' => $lead->id,
            'title' => 'Passport Copy',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.show', $lead))
            ->assertOk()
            ->assertSee('Passport Copy');
    }

    public function test_admin_can_upload_customer_document_and_see_it_on_customer_page(): void
    {
        Storage::fake('local');

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Converted Lead',
            'phone' => '01100000000',
        ]);

        $customer = CrmCustomer::query()->create([
            'inquiry_id' => $lead->id,
            'customer_code' => 'CUST-2026-0001',
            'full_name' => 'Converted Lead',
            'phone' => '01100000000',
            'stage' => CrmCustomer::STAGE_NEW,
            'is_active' => true,
            'converted_at' => now(),
            'created_by' => $admin->id,
        ]);

        $category = CrmDocumentCategory::query()->where('slug', 'passport')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.documents.store'), [
            'documentable_type' => 'customer',
            'documentable_id' => $customer->id,
            'crm_document_category_id' => $category->id,
            'title' => 'Customer Passport',
            'file' => UploadedFile::fake()->create('passport.jpg', 120, 'image/jpeg'),
        ])->assertRedirect();

        $document = $customer->documents()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.crm.customers.show', $customer))
            ->assertOk()
            ->assertSee('Customer Passport');

        $this->actingAs($admin)
            ->get(route('admin.documents.download', $document))
            ->assertOk();
    }
}
