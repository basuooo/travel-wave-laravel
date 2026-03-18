<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.blog-posts.index', [
            'items' => BlogPost::with('category')->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.blog-posts.form', [
            'item' => new BlogPost(),
            'categories' => BlogCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data = $this->transformData($request, $data);
        BlogPost::create($data);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post created.');
    }

    public function show(BlogPost $blogPost)
    {
        return redirect()->route('admin.blog-posts.edit', $blogPost);
    }

    public function edit(BlogPost $blog_post)
    {
        return view('admin.blog-posts.form', [
            'item' => $blog_post,
            'categories' => BlogCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, BlogPost $blog_post)
    {
        $data = $this->validatedData($request, $blog_post->id);
        $data = $this->transformData($request, $data, $blog_post);
        $blog_post->update($data);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $blog_post)
    {
        $blog_post->delete();

        return back()->with('success', 'Blog post deleted.');
    }

    protected function validatedData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_posts,slug,' . $id],
            'excerpt_en' => ['nullable', 'string'],
            'excerpt_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'tags_en' => ['nullable', 'string'],
            'tags_ar' => ['nullable', 'string'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_title_ar' => ['nullable', 'string', 'max:255'],
            'meta_description_en' => ['nullable', 'string'],
            'meta_description_ar' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'image'],
        ]);
    }

    protected function transformData(Request $request, array $data, ?BlogPost $post = null): array
    {
        $data['featured_image'] = $this->uploadFile($request, 'featured_image', 'blog-posts', $post?->featured_image);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }
}
