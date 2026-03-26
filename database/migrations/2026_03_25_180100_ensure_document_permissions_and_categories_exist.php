<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            $permissions = [
                [
                    'slug' => 'documents.view',
                    'name' => 'View Documents',
                    'description' => 'View CRM and customer documents allowed for the current user.',
                    'module' => 'documents',
                ],
                [
                    'slug' => 'documents.manage',
                    'name' => 'Manage Documents',
                    'description' => 'Upload, update, download, and delete documents for allowed CRM records.',
                    'module' => 'documents',
                ],
                [
                    'slug' => 'documents.categories.manage',
                    'name' => 'Manage Document Categories',
                    'description' => 'Manage reusable document categories.',
                    'module' => 'documents',
                ],
            ];

            foreach ($permissions as $permission) {
                Permission::query()->updateOrCreate(
                    ['slug' => $permission['slug']],
                    $permission
                );
            }

            $permissionIds = Permission::query()
                ->whereIn('slug', [
                    'documents.view',
                    'documents.manage',
                    'documents.categories.manage',
                ])
                ->pluck('id')
                ->all();

            Role::query()
                ->whereIn('slug', ['admin', 'super-admin', 'sales-leads-manager'])
                ->get()
                ->each(function (Role $role) use ($permissionIds) {
                    $allowedIds = $role->slug === 'sales-leads-manager'
                        ? Permission::query()->whereIn('slug', ['documents.view', 'documents.manage'])->pluck('id')->all()
                        : $permissionIds;

                    $role->permissions()->syncWithoutDetaching($allowedIds);
                });
        }

        if (Schema::hasTable('crm_document_categories')) {
            $categories = [
                ['slug' => 'passport', 'name_ar' => 'جواز السفر', 'name_en' => 'Passport'],
                ['slug' => 'bank-statement', 'name_ar' => 'كشف حساب', 'name_en' => 'Bank Statement'],
                ['slug' => 'employment-letter', 'name_ar' => 'خطاب جهة العمل', 'name_en' => 'Employment Letter'],
                ['slug' => 'commercial-register', 'name_ar' => 'السجل التجاري', 'name_en' => 'Commercial Register'],
                ['slug' => 'tax-card', 'name_ar' => 'البطاقة الضريبية', 'name_en' => 'Tax Card'],
                ['slug' => 'family-registry', 'name_ar' => 'قيد عائلي', 'name_en' => 'Family Registry'],
                ['slug' => 'movement-certificate', 'name_ar' => 'شهادة تحركات', 'name_en' => 'Movement Certificate'],
                ['slug' => 'translation', 'name_ar' => 'ترجمة', 'name_en' => 'Translation'],
                ['slug' => 'invitation', 'name_ar' => 'دعوة', 'name_en' => 'Invitation'],
                ['slug' => 'insurance', 'name_ar' => 'تأمين', 'name_en' => 'Insurance'],
                ['slug' => 'flight-booking', 'name_ar' => 'حجز طيران', 'name_en' => 'Flight Booking'],
                ['slug' => 'hotel-booking', 'name_ar' => 'حجز فندق', 'name_en' => 'Hotel Booking'],
                ['slug' => 'national-id', 'name_ar' => 'بطاقة رقم قومي', 'name_en' => 'National ID'],
                ['slug' => 'other', 'name_ar' => 'أخرى', 'name_en' => 'Other'],
            ];

            foreach ($categories as $index => $category) {
                DB::table('crm_document_categories')->updateOrInsert(
                    ['slug' => $category['slug']],
                    $category + [
                        'sort_order' => $index + 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            Permission::query()->whereIn('slug', [
                'documents.view',
                'documents.manage',
                'documents.categories.manage',
            ])->delete();
        }
    }
};
