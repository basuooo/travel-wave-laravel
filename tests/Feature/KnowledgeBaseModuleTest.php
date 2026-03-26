<?php

namespace Tests\Feature;

use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KnowledgeBaseModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_view_knowledge_base_article(): void
    {
        $admin = User::query()->create([
            'name' => 'KB Admin',
            'email' => 'kb-admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $category = KnowledgeBaseCategory::query()->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.knowledge-base.store'), [
            'knowledge_base_category_id' => $category->id,
            'title' => 'France Business Visa Procedure',
            'summary' => 'Internal process note for France business visa handling.',
            'content' => 'Step one: collect required documents. Step two: verify appointment flow.',
            'status' => KnowledgeBaseArticle::STATUS_PUBLISHED,
            'visibility_scope' => KnowledgeBaseArticle::SCOPE_ALL_STAFF,
            'is_featured' => 1,
        ]);

        $article = KnowledgeBaseArticle::query()->where('title', 'France Business Visa Procedure')->firstOrFail();

        $response->assertRedirect(route('admin.knowledge-base.show', $article));
        $this->actingAs($admin)
            ->get(route('admin.knowledge-base.show', $article))
            ->assertOk()
            ->assertSee('France Business Visa Procedure');
    }

    public function test_sales_user_can_read_published_article_but_not_draft(): void
    {
        $seller = User::query()->create([
            'name' => 'Seller Knowledge',
            'email' => 'seller-kb@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        $dashboardAccess = Permission::query()->firstOrCreate([
            'slug' => 'dashboard.access',
        ], [
            'name' => 'Dashboard Access',
            'module' => 'dashboard',
        ]);

        $kbView = Permission::query()->firstOrCreate([
            'slug' => 'knowledge_base.view',
        ], [
            'name' => 'View Knowledge Base',
            'module' => 'knowledge_base',
        ]);

        $leadsView = Permission::query()->firstOrCreate([
            'slug' => 'leads.view',
        ], [
            'name' => 'View Leads',
            'module' => 'leads',
        ]);

        $role = Role::query()->create([
            'name' => 'Knowledge Seller',
            'slug' => 'knowledge-seller',
            'is_system' => false,
        ]);
        $role->permissions()->sync([$dashboardAccess->id, $kbView->id, $leadsView->id]);
        $seller->roles()->sync([$role->id]);

        $category = KnowledgeBaseCategory::query()->firstOrFail();

        $published = KnowledgeBaseArticle::query()->create([
            'knowledge_base_category_id' => $category->id,
            'title' => 'Published Seller Script',
            'slug' => 'published-seller-script',
            'summary' => 'Published',
            'content' => 'Use this script for first contact.',
            'status' => KnowledgeBaseArticle::STATUS_PUBLISHED,
            'visibility_scope' => KnowledgeBaseArticle::SCOPE_SELLERS,
        ]);

        $draft = KnowledgeBaseArticle::query()->create([
            'knowledge_base_category_id' => $category->id,
            'title' => 'Draft Internal Article',
            'slug' => 'draft-internal-article',
            'summary' => 'Draft',
            'content' => 'Draft content.',
            'status' => KnowledgeBaseArticle::STATUS_DRAFT,
            'visibility_scope' => KnowledgeBaseArticle::SCOPE_ALL_STAFF,
        ]);

        $this->actingAs($seller)
            ->get(route('admin.knowledge-base.index'))
            ->assertOk()
            ->assertSee('Published Seller Script')
            ->assertDontSee('Draft Internal Article');

        $this->actingAs($seller)
            ->get(route('admin.knowledge-base.show', $published))
            ->assertOk()
            ->assertSee('Published Seller Script');

        $this->actingAs($seller)
            ->get(route('admin.knowledge-base.show', $draft))
            ->assertForbidden();
    }
}
