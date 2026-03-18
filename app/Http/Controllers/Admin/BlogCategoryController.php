<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        return view('admin.blog-categories.index', [
            'items' => BlogCategory::orderBy('sort_order')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.blog-categories.form', ['item' => new BlogCategory()]);
    }

    public function store(Request $request)
    {
        BlogCategory::create($this->validatedData($request));

        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category created.');
    }

    public function show(BlogCategory $blogCategory)
    {
        return redirect()->route('admin.blog-categories.edit', $blogCategory);
    }

    public function edit(BlogCategory $blog_category)
    {
        return view('admin.blog-categories.form', ['item' => $blog_category]);
    }

    public function update(Request $request, BlogCategory $blog_category)
    {
        $blog_category->update($this->validatedData($request, $blog_category->id));

        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category updated.');
    }

    public function destroy(BlogCategory $blog_category)
    {
        $blog_category->delete();

        return back()->with('success', 'Blog category deleted.');
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_categories,slug,' . $id],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
