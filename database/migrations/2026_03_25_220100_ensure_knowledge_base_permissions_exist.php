<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permissions = [
            [
                'slug' => 'knowledge_base.view',
                'module' => 'knowledge_base',
                'name' => 'View Knowledge Base',
                'description' => 'Read published internal knowledge base articles.',
            ],
            [
                'slug' => 'knowledge_base.manage',
                'module' => 'knowledge_base',
                'name' => 'Manage Knowledge Base',
                'description' => 'Create, edit, publish, and archive knowledge base articles.',
            ],
            [
                'slug' => 'knowledge_base.categories.manage',
                'module' => 'knowledge_base',
                'name' => 'Manage Knowledge Base Categories',
                'description' => 'Create, edit, and delete knowledge base categories.',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        Permission::query()->whereIn('slug', [
            'knowledge_base.view',
            'knowledge_base.manage',
            'knowledge_base.categories.manage',
        ])->delete();
    }
};
