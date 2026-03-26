<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseCategory;
use App\Support\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeBaseCategoryController extends Controller
{
    public function index()
    {
        return view('admin.knowledge-base.categories', [
            'categories' => KnowledgeBaseCategory::query()->withCount('articles')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, AuditLogService $auditLogService)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category = KnowledgeBaseCategory::query()->create([
            'slug' => $this->uniqueSlug($data['name_en'] ?: $data['name_ar']),
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        $auditLogService->log(
            $request->user(),
            'knowledge_base',
            'created',
            $category,
            [
                'title' => $category->localizedName(),
                'new_values' => [
                    'name_ar' => $category->name_ar,
                    'name_en' => $category->name_en,
                    'is_active' => $category->is_active,
                ],
                'changed_fields' => ['name_ar', 'name_en', 'is_active'],
            ]
        );

        return back()->with('success', __('admin.kb_category_created'));
    }

    public function update(Request $request, KnowledgeBaseCategory $category, AuditLogService $auditLogService)
    {
        $before = [
            'name_ar' => $category->name_ar,
            'name_en' => $category->name_en,
            'description' => $category->description,
            'is_active' => $category->is_active,
            'sort_order' => $category->sort_order,
        ];

        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'slug' => $this->uniqueSlug($data['name_en'] ?: $data['name_ar'], $category->id),
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $diff = $auditLogService->diff($before, [
            'name_ar' => $category->name_ar,
            'name_en' => $category->name_en,
            'description' => $category->description,
            'is_active' => $category->is_active,
            'sort_order' => $category->sort_order,
        ]);

        if ($diff['changed_fields'] !== []) {
            $auditLogService->log(
                $request->user(),
                'knowledge_base',
                'updated',
                $category,
                [
                    'title' => $category->localizedName(),
                    'old_values' => $diff['old_values'],
                    'new_values' => $diff['new_values'],
                    'changed_fields' => $diff['changed_fields'],
                ]
            );
        }

        return back()->with('success', __('admin.kb_category_updated'));
    }

    public function destroy(KnowledgeBaseCategory $category, AuditLogService $auditLogService)
    {
        if ($category->articles()->exists()) {
            return back()->withErrors([
                'category' => __('admin.kb_category_in_use'),
            ]);
        }

        $auditLogService->log(
            auth()->user(),
            'knowledge_base',
            'deleted',
            $category,
            [
                'title' => $category->localizedName(),
                'old_values' => [
                    'name_ar' => $category->name_ar,
                    'name_en' => $category->name_en,
                ],
                'changed_fields' => ['name_ar', 'name_en'],
            ]
        );

        $category->delete();

        return back()->with('success', __('admin.kb_category_deleted'));
    }

    protected function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base !== '' ? $base : 'knowledge-base-category';
        $counter = 1;

        while (
            KnowledgeBaseCategory::query()
                ->when($ignoreId, fn ($builder) => $builder->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
