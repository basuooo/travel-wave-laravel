<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->nullable()->after('net_price');
            }

            if (! Schema::hasColumn('inquiries', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->nullable()->after('total_amount');
            }

            if (! Schema::hasColumn('inquiries', 'remaining_amount')) {
                $table->decimal('remaining_amount', 12, 2)->nullable()->after('paid_amount');
            }

            if (! Schema::hasColumn('inquiries', 'payment_status')) {
                $table->string('payment_status', 30)->nullable()->after('remaining_amount');
            }
        });

        if (! Schema::hasTable('accounting_customer_accounts')) {
            Schema::create('accounting_customer_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('inquiry_id');
                $table->unsignedBigInteger('assigned_user_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('customer_name');
                $table->string('phone')->nullable();
                $table->string('whatsapp_number')->nullable();
                $table->string('email')->nullable();
                $table->string('service_label')->nullable();
                $table->string('service_destination')->nullable();
                $table->string('lead_source')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->decimal('remaining_amount', 12, 2)->default(0);
                $table->decimal('total_customer_expenses', 12, 2)->default(0);
                $table->decimal('company_profit_before_seller', 12, 2)->default(0);
                $table->decimal('seller_profit', 12, 2)->default(0);
                $table->decimal('final_company_profit', 12, 2)->default(0);
                $table->string('payment_status', 30)->default('unpaid');
                $table->text('notes')->nullable();
                $table->timestamp('last_payment_at')->nullable();
                $table->timestamps();

                $table->unique('inquiry_id', 'acct_customer_accounts_inquiry_unique');
                $table->foreign('inquiry_id', 'acct_customer_accounts_inquiry_fk')->references('id')->on('inquiries')->cascadeOnDelete();
                $table->foreign('assigned_user_id', 'acct_customer_accounts_seller_fk')->references('id')->on('users')->nullOnDelete();
                $table->foreign('created_by', 'acct_customer_accounts_creator_fk')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('accounting_customer_payments')) {
            Schema::create('accounting_customer_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('accounting_customer_account_id');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->decimal('amount', 12, 2);
                $table->date('payment_date');
                $table->string('payment_type', 30)->default('payment');
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('accounting_customer_account_id', 'acct_customer_payments_account_fk')->references('id')->on('accounting_customer_accounts')->cascadeOnDelete();
                $table->foreign('created_by', 'acct_customer_payments_creator_fk')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('accounting_expense_categories')) {
            Schema::create('accounting_expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        if (! Schema::hasTable('accounting_expense_subcategories')) {
            Schema::create('accounting_expense_subcategories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('accounting_expense_category_id');
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->string('slug')->unique();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('accounting_expense_category_id', 'acct_expense_subcategories_cat_fk')->references('id')->on('accounting_expense_categories')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('accounting_customer_expenses')) {
            Schema::create('accounting_customer_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('accounting_customer_account_id');
                $table->unsignedBigInteger('accounting_expense_category_id');
                $table->unsignedBigInteger('accounting_expense_subcategory_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->decimal('amount', 12, 2);
                $table->date('expense_date');
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('accounting_customer_account_id', 'acct_customer_expenses_account_fk')->references('id')->on('accounting_customer_accounts')->cascadeOnDelete();
                $table->foreign('accounting_expense_category_id', 'acct_customer_expenses_cat_fk')->references('id')->on('accounting_expense_categories')->cascadeOnDelete();
                $table->foreign('accounting_expense_subcategory_id', 'acct_customer_expenses_subcat_fk')->references('id')->on('accounting_expense_subcategories')->nullOnDelete();
                $table->foreign('created_by', 'acct_customer_expenses_creator_fk')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('accounting_general_expense_categories')) {
            Schema::create('accounting_general_expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        if (! Schema::hasTable('accounting_general_expenses')) {
            Schema::create('accounting_general_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('accounting_general_expense_category_id');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->decimal('amount', 12, 2);
                $table->date('expense_date');
                $table->string('attachment_path')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('accounting_general_expense_category_id', 'acct_general_expenses_cat_fk')->references('id')->on('accounting_general_expense_categories')->cascadeOnDelete();
                $table->foreign('created_by', 'acct_general_expenses_creator_fk')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('accounting_employee_transactions')) {
            Schema::create('accounting_employee_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('transaction_type', 30);
                $table->decimal('amount', 12, 2);
                $table->date('transaction_date');
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('user_id', 'acct_employee_transactions_user_fk')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('created_by', 'acct_employee_transactions_creator_fk')->references('id')->on('users')->nullOnDelete();
            });
        }

        $this->seedCategories();
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_employee_transactions');
        Schema::dropIfExists('accounting_general_expenses');
        Schema::dropIfExists('accounting_general_expense_categories');
        Schema::dropIfExists('accounting_customer_expenses');
        Schema::dropIfExists('accounting_expense_subcategories');
        Schema::dropIfExists('accounting_expense_categories');
        Schema::dropIfExists('accounting_customer_payments');
        Schema::dropIfExists('accounting_customer_accounts');

        Schema::table('inquiries', function (Blueprint $table) {
            foreach (['total_amount', 'paid_amount', 'remaining_amount', 'payment_status'] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    protected function seedCategories(): void
    {
        $customerCategories = [
            ['slug' => 'embassy-appointment', 'name_ar' => 'حجز معاد السفارة', 'name_en' => 'Embassy Appointment', 'sort_order' => 1],
            ['slug' => 'main-translations', 'name_ar' => 'الترجمات الرئيسية', 'name_en' => 'Main Translations', 'sort_order' => 2],
            ['slug' => 'conference-invitation', 'name_ar' => 'دعوة المؤتمر او المعرض', 'name_en' => 'Conference Invitation', 'sort_order' => 3],
            ['slug' => 'transportation', 'name_ar' => 'توصيل اوبر او جو باص', 'name_en' => 'Transportation', 'sort_order' => 4],
        ];

        foreach ($customerCategories as $category) {
            DB::table('accounting_expense_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category + ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $translationCategoryId = DB::table('accounting_expense_categories')->where('slug', 'main-translations')->value('id');

        if ($translationCategoryId) {
            $subcategories = [
                ['slug' => 'commercial-register', 'name_ar' => 'سجل تجاري', 'name_en' => 'Commercial Register', 'sort_order' => 1],
                ['slug' => 'tax-card', 'name_ar' => 'بطاقة ضريبية', 'name_en' => 'Tax Card', 'sort_order' => 2],
                ['slug' => 'employment-letter', 'name_ar' => 'خطاب جهة العمل', 'name_en' => 'Employment Letter', 'sort_order' => 3],
                ['slug' => 'movements-certificate', 'name_ar' => 'شهادة تحركات', 'name_en' => 'Movements Certificate', 'sort_order' => 4],
                ['slug' => 'family-record', 'name_ar' => 'قيد عائلي', 'name_en' => 'Family Record', 'sort_order' => 5],
                ['slug' => 'properties', 'name_ar' => 'الممتلكات', 'name_en' => 'Properties', 'sort_order' => 6],
            ];

            foreach ($subcategories as $subcategory) {
                DB::table('accounting_expense_subcategories')->updateOrInsert(
                    ['slug' => $subcategory['slug']],
                    $subcategory + [
                        'accounting_expense_category_id' => $translationCategoryId,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $generalCategories = [
            ['slug' => 'rent', 'name_ar' => 'ايجار', 'name_en' => 'Rent', 'sort_order' => 1],
            ['slug' => 'electricity', 'name_ar' => 'كهرباء', 'name_en' => 'Electricity', 'sort_order' => 2],
            ['slug' => 'water', 'name_ar' => 'مياة', 'name_en' => 'Water', 'sort_order' => 3],
            ['slug' => 'mobile', 'name_ar' => 'موبايل', 'name_en' => 'Mobile', 'sort_order' => 4],
            ['slug' => 'beverages', 'name_ar' => 'مشروبات', 'name_en' => 'Beverages', 'sort_order' => 5],
        ];

        foreach ($generalCategories as $category) {
            DB::table('accounting_general_expense_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category + ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
};
