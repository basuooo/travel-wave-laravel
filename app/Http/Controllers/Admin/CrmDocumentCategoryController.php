<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmDocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CrmDocumentCategoryController extends Controller
{
    public function index()
    {
        return view('admin.documents.categories', [
            'categories' => CrmDocumentCategory::query()->withCount('documents')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        CrmDocumentCategory::query()->create([
            'slug' => Str::slug($data['name_en'] ?: $data['name_ar']),
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', __('admin.document_category_created'));
    }

    public function update(Request $request, CrmDocumentCategory $category)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'slug' => Str::slug($data['name_en'] ?: $data['name_ar']),
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('admin.document_category_updated'));
    }

    public function destroy(CrmDocumentCategory $category)
    {
        if ($category->documents()->exists()) {
            return back()->withErrors([
                'category' => __('admin.document_category_in_use'),
            ]);
        }

        $category->delete();

        return back()->with('success', __('admin.document_category_deleted'));
    }
}
