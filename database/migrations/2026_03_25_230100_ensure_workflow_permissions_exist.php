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
                'slug' => 'workflow_automations.view',
                'name' => 'View Workflow Automations',
                'module' => 'workflow_automations',
                'description' => 'View workflow automation rules and execution logs.',
            ],
            [
                'slug' => 'workflow_automations.manage',
                'name' => 'Manage Workflow Automations',
                'module' => 'workflow_automations',
                'description' => 'Create, update, enable, and disable workflow automation rules.',
            ],
        ];

        foreach ($permissions as $attributes) {
            Permission::query()->updateOrCreate(
                ['slug' => $attributes['slug']],
                $attributes
            );
        }

        $roles = Role::query()->where('slug', 'admin')->get();
        $permissionIds = Permission::query()
            ->whereIn('slug', collect($permissions)->pluck('slug'))
            ->pluck('id')
            ->all();

        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    public function down(): void
    {
        Permission::query()
            ->whereIn('slug', ['workflow_automations.view', 'workflow_automations.manage'])
            ->delete();
    }
};
