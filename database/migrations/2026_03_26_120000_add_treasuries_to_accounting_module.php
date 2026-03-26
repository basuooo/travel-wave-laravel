<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounting_treasuries')) {
            Schema::create('accounting_treasuries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type', 50);
                $table->string('identifier')->nullable();
                $table->decimal('opening_balance', 12, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('created_by', 'acct_treasuries_creator_fk')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('accounting_treasury_transactions')) {
            Schema::create('accounting_treasury_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('accounting_treasury_id');
                $table->string('direction', 10);
                $table->string('transaction_type', 50);
                $table->decimal('amount', 12, 2);
                $table->date('transaction_date');
                $table->nullableMorphs('related');
                $table->string('description')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('accounting_treasury_id', 'acct_treasury_transactions_treasury_fk')->references('id')->on('accounting_treasuries')->cascadeOnDelete();
                $table->foreign('created_by', 'acct_treasury_transactions_creator_fk')->references('id')->on('users')->nullOnDelete();
                $table->index(['accounting_treasury_id', 'transaction_date'], 'acct_treasury_transactions_treasury_date_idx');
            });
        }

        Schema::table('accounting_customer_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('accounting_customer_payments', 'accounting_treasury_id')) {
                $table->unsignedBigInteger('accounting_treasury_id')->nullable()->after('accounting_customer_account_id');
                $table->foreign('accounting_treasury_id', 'acct_customer_payments_treasury_fk')->references('id')->on('accounting_treasuries')->nullOnDelete();
            }
        });

        Schema::table('accounting_customer_expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('accounting_customer_expenses', 'accounting_treasury_id')) {
                $table->unsignedBigInteger('accounting_treasury_id')->nullable()->after('accounting_expense_subcategory_id');
                $table->foreign('accounting_treasury_id', 'acct_customer_expenses_treasury_fk')->references('id')->on('accounting_treasuries')->nullOnDelete();
            }
        });

        Schema::table('accounting_general_expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('accounting_general_expenses', 'accounting_treasury_id')) {
                $table->unsignedBigInteger('accounting_treasury_id')->nullable()->after('accounting_general_expense_category_id');
                $table->foreign('accounting_treasury_id', 'acct_general_expenses_treasury_fk')->references('id')->on('accounting_treasuries')->nullOnDelete();
            }
        });

        Schema::table('accounting_employee_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('accounting_employee_transactions', 'accounting_treasury_id')) {
                $table->unsignedBigInteger('accounting_treasury_id')->nullable()->after('user_id');
                $table->foreign('accounting_treasury_id', 'acct_employee_transactions_treasury_fk')->references('id')->on('accounting_treasuries')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounting_employee_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_employee_transactions', 'accounting_treasury_id')) {
                $table->dropForeign('acct_employee_transactions_treasury_fk');
                $table->dropColumn('accounting_treasury_id');
            }
        });

        Schema::table('accounting_general_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_general_expenses', 'accounting_treasury_id')) {
                $table->dropForeign('acct_general_expenses_treasury_fk');
                $table->dropColumn('accounting_treasury_id');
            }
        });

        Schema::table('accounting_customer_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_customer_expenses', 'accounting_treasury_id')) {
                $table->dropForeign('acct_customer_expenses_treasury_fk');
                $table->dropColumn('accounting_treasury_id');
            }
        });

        Schema::table('accounting_customer_payments', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_customer_payments', 'accounting_treasury_id')) {
                $table->dropForeign('acct_customer_payments_treasury_fk');
                $table->dropColumn('accounting_treasury_id');
            }
        });

        Schema::dropIfExists('accounting_treasury_transactions');
        Schema::dropIfExists('accounting_treasuries');
    }
};
