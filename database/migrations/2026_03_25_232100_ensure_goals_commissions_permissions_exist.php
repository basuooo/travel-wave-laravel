<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            ['slug' => 'goals_commissions.view', 'name' => 'View Goals & Commissions', 'module' => 'goals_commissions'],
            ['slug' => 'goals_commissions.manage', 'name' => 'Manage Goals & Commissions', 'module' => 'goals_commissions'],
        ] as $permission) {
            Permission::query()->firstOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                    'description' => $permission['name'],
                ]
            );
        }
    }

    public function down(): void
    {
        Permission::query()->whereIn('slug', [
            'goals_commissions.view',
            'goals_commissions.manage',
        ])->delete();
    }
};
