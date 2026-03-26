<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_base_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('status', 30)->default('draft');
            $table->string('visibility_scope', 30)->default('all_staff');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'visibility_scope']);
            $table->index(['knowledge_base_category_id', 'status']);
        });

        $now = now();
        $categories = [
            ['slug' => 'embassy-rules', 'name_ar' => 'السفارات', 'name_en' => 'Embassy Rules', 'description' => 'Embassy rules and country-specific instructions.', 'sort_order' => 10],
            ['slug' => 'visa-procedures', 'name_ar' => 'التأشيرات', 'name_en' => 'Visa Procedures', 'description' => 'Visa flows, process notes, and requirements.', 'sort_order' => 20],
            ['slug' => 'required-documents', 'name_ar' => 'الأوراق المطلوبة', 'name_en' => 'Required Documents', 'description' => 'Required document checklists for services and countries.', 'sort_order' => 30],
            ['slug' => 'pricing-guidance', 'name_ar' => 'الأسعار', 'name_en' => 'Pricing', 'description' => 'Pricing references and internal guidance.', 'sort_order' => 40],
            ['slug' => 'internal-procedures', 'name_ar' => 'الإجراءات الداخلية', 'name_en' => 'Internal Procedures', 'description' => 'SOPs and internal operational workflows.', 'sort_order' => 50],
            ['slug' => 'faq', 'name_ar' => 'الأسئلة الشائعة', 'name_en' => 'FAQ', 'description' => 'Frequently asked internal questions and answers.', 'sort_order' => 60],
            ['slug' => 'sales-scripts', 'name_ar' => 'سكربتات البيع', 'name_en' => 'Sales Scripts', 'description' => 'Seller scripts and response templates.', 'sort_order' => 70],
            ['slug' => 'accounting-notes', 'name_ar' => 'المحاسبة', 'name_en' => 'Accounting Notes', 'description' => 'Accounting and collections reference notes.', 'sort_order' => 80],
            ['slug' => 'operations', 'name_ar' => 'التشغيل', 'name_en' => 'Operations', 'description' => 'Operations handling notes and best practices.', 'sort_order' => 90],
        ];

        foreach ($categories as $category) {
            DB::table('knowledge_base_categories')->insert($category + [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_articles');
        Schema::dropIfExists('knowledge_base_categories');
    }
};
