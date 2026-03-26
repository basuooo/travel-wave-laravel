<?php

use App\Models\Permission;
use App\Models\Role;
use App\Support\AccessControl;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (AccessControl::flatPermissions() as $permissionData) {
            Permission::query()->updateOrCreate(
                ['slug' => $permissionData['slug']],
                [
                    'name' => $permissionData['name'],
                    'module' => $permissionData['module'],
                    'description' => $permissionData['description'],
                ]
            );
        }

        $admin = Role::query()->where('slug', 'admin')->first();
        $superAdmin = Role::query()->where('slug', 'super-admin')->first();

        $permissionIds = Permission::query()
            ->whereIn('slug', ['accounting.view', 'accounting.manage', 'accounting.reports.view'])
            ->pluck('id')
            ->all();

        if ($admin) {
            $admin->permissions()->syncWithoutDetaching($permissionIds);
        }

        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    public function down(): void
    {
    }
};
