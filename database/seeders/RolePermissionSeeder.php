<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
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

        $allPermissionIds = Permission::query()->pluck('id')->all();

        foreach (AccessControl::defaultRoles() as $roleData) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'is_system' => true,
                ]
            );

            if ($roleData['permissions'] === ['*']) {
                $role->permissions()->sync($allPermissionIds);
                continue;
            }

            $permissionIds = Permission::query()
                ->whereIn('slug', $roleData['permissions'])
                ->pluck('id')
                ->all();

            $role->permissions()->sync($permissionIds);
        }

        $admin = User::query()->where('email', 'admin@travelwave.test')->first();
        $superAdmin = Role::query()->where('slug', 'super-admin')->first();

        if ($admin && $superAdmin) {
            $admin->roles()->syncWithoutDetaching([$superAdmin->id]);
            $admin->is_admin = true;
            $admin->is_active = true;
            $admin->preferred_language = $admin->preferred_language ?: 'en';
            $admin->save();
        }
    }
}
