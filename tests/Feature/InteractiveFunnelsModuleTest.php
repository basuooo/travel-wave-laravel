<?php

namespace Tests\Feature;

use App\Models\Funnel;
use App\Models\FunnelResponse;
use App\Models\FunnelTemplate;
use App\Models\Inquiry;
use App\Models\User;
use App\Services\Funnels\FunnelCrmSyncService;
use App\Services\Funnels\FunnelExecutionEngine;
use Database\Seeders\FunnelTemplateSeeder;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InteractiveFunnelsModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create users table for testing if not present
        if (! Schema::hasTable('users')) {
            Schema::create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->boolean('is_admin')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create settings table for testing if not present
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function ($table) {
                $table->id();
                $table->string('key')->nullable();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // Create inquiries table for testing if not present
        if (! Schema::hasTable('inquiries')) {
            Schema::create('inquiries', function ($table) {
                $table->id();
                $table->string('full_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('whatsapp_number')->nullable();
                $table->string('email')->nullable();
                $table->string('country')->nullable();
                $table->string('destination')->nullable();
                $table->string('type')->nullable();
                $table->string('form_name')->nullable();
                $table->string('form_category')->nullable();
                $table->text('submitted_data')->nullable();
                $table->string('status')->default('new');
                $table->unsignedBigInteger('crm_source_id')->nullable();
                $table->unsignedBigInteger('crm_service_type_id')->nullable();
                $table->timestamp('crm_status_updated_at')->nullable();
                $table->timestamp('status_1_updated_at')->nullable();
                $table->string('lead_source')->nullable();
                $table->string('campaign_name')->nullable();
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_content')->nullable();
                $table->string('utm_term')->nullable();
                $table->string('priority')->default('normal');
                $table->text('additional_notes')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // Run Interactive Funnels migration
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2026_08_20_000000_create_interactive_funnels_tables.php',
        ]);
    }

    public function test_funnel_template_seeder_creates_8_production_templates()
    {
        $this->seed(FunnelTemplateSeeder::class);

        $this->assertDatabaseCount('funnel_templates', 8);
        $this->assertDatabaseHas('funnel_templates', [
            'slug' => 'schengen-visa-eligibility',
            'category' => 'Travel',
        ]);
    }

    public function test_funnel_creation_publishing_and_public_rendering()
    {
        $this->seed(FunnelTemplateSeeder::class);
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
            Schema::create('role_user', function ($table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
            });
        }

        $superRole = \App\Models\Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin']);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);
        $admin->roles()->attach($superRole);

        $template = FunnelTemplate::where('slug', 'schengen-visa-eligibility')->firstOrFail();

        // Instantiate funnel from template
        $response = $this->actingAs($admin)
            ->post(route('admin.funnels.templates.use', $template), [
                'name' => 'My Schengen Quiz',
            ]);

        $response->assertStatus(302);
        $funnel = Funnel::latest('id')->firstOrFail();
        $response->assertRedirect(route('admin.funnels.builder', $funnel));

        // Publish funnel
        $this->actingAs($admin)
            ->post(route('admin.funnels.publish', $funnel))
            ->assertRedirect();

        $this->assertTrue($funnel->fresh()->isPublished());

        // Public access test
        $publicRes = $this->get(route('funnels.public.show', $funnel->slug));
        $publicRes->assertStatus(200);
        $publicRes->assertSee('هل أنت جاهز للحصول على تأشيرة الشنغن؟');
    }

    public function test_logic_and_scoring_calculation()
    {
        $engine = new FunnelExecutionEngine();

        $funnel = Funnel::create([
            'name' => 'Scoring Test',
            'slug' => 'scoring-test',
        ]);

        $step = $funnel->steps()->create(['title' => 'Step 1', 'sort_order' => 1]);
        $element = $step->elements()->create([
            'element_type' => 'single_choice',
            'label' => 'Bank Account',
            'question_key' => 'bank_acc',
            'properties' => [
                'options' => [
                    ['label' => 'Yes', 'value' => 'yes', 'score' => 30],
                    ['label' => 'No', 'value' => 'no', 'score' => 0],
                ],
            ],
        ]);

        $score = $engine->calculateScore($funnel, ['bank_acc' => 'yes']);
        $this->assertEquals(30, $score);
    }

    public function test_funnel_submission_creates_crm_inquiry()
    {
        $this->seed(FunnelTemplateSeeder::class);
        $template = FunnelTemplate::where('slug', 'schengen-visa-eligibility')->firstOrFail();

        $funnelController = app(\App\Http\Controllers\Admin\FunnelController::class);
        $funnel = Funnel::create([
            'name' => 'CRM Schengen Funnel',
            'slug' => 'crm-schengen-funnel',
            'status' => 'published',
            'crm_settings' => ['enabled' => true],
        ]);
        $funnelController->importSchemaToFunnel($funnel, $template->schema_data);

        $answers = [
            'full_name' => 'Ahmad Hassan',
            'phone' => '0501234567',
            'email' => 'ahmad@example.com',
            'destination_country' => 'Spain',
            'bank_account' => 'Yes',
            'bank_balance' => 'Over 150k',
        ];

        $res = $this->postJson(route('funnels.public.submit', $funnel->slug), [
            'answers' => $answers,
        ]);

        $res->assertStatus(200);
        $res->assertJsonPath('success', true);

        // Check Inquiry CRM creation
        $this->assertDatabaseHas('inquiries', [
            'full_name' => 'Ahmad Hassan',
            'phone' => '0501234567',
            'email' => 'ahmad@example.com',
            'type' => 'interactive_funnel',
        ]);
    }
}
