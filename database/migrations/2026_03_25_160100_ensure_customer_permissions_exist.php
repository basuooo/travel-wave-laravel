<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            [
                'slug' => 'customers.view',
                'name' => 'View Customers',
                'module' => 'customers',
                'description' => 'View customer cases and customer details.',
            ],
            [
                'slug' => 'customers.manage',
                'name' => 'Manage Customers',
                'module' => 'customers',
                'description' => 'Convert leads to customers and manage customer case data.',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::query()->updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        $permissionIds = Permission::query()
            ->whereIn('slug', ['customers.view', 'customers.manage'])
            ->pluck('id')
            ->all();

        Role::query()
            ->whereIn('slug', ['admin', 'sales-leads-manager'])
            ->get()
            ->each(fn (Role $role) => $role->permissions()->syncWithoutDetaching($permissionIds));
    }

    public function down(): void
    {
        $permissionIds = Permission::query()
            ->whereIn('slug', ['customers.view', 'customers.manage'])
            ->pluck('id')
            ->all();

        Role::query()
            ->whereIn('slug', ['admin', 'sales-leads-manager'])
            ->get()
            ->each(fn (Role $role) => $role->permissions()->detach($permissionIds));

        Permission::query()->whereIn('slug', ['customers.view', 'customers.manage'])->delete();
    }
};
