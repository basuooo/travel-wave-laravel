@extends('layouts.admin')
@section('page_title', $item->exists ? 'Edit Blog Post' : 'Create Blog Post')
@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.blog-posts.update', $item) : route('admin.blog-posts.store') }}">@csrf @if($item->exists) @method('PUT') @endif
<div class="card admin-card p-4"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Category</label><select class="form-select" name="blog_category_id"><option value="">None</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('blog_category_id', $item->blog_category_id) == $category->id)>{{ $category->name_en }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Published At</label><input type="date" class="form-control" name="published_at" value="{{ old('published_at', optional($item->published_at)->format('Y-m-d')) }}"></div>
<div class="col-md-4"><label class="form-label">Image</label><input type="file" class="form-control" name="featured_image"></div>
<div class="col-md-6"><label class="form-label">Title EN</label><input class="form-control" name="title_en" value="{{ old('title_en', $item->title_en) }}"></div>
<div class="col-md-6"><label class="form-label">Title AR</label><input class="form-control" name="title_ar" value="{{ old('title_ar', $item->title_ar) }}"></div>
<div class="col-md-6"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $item->slug) }}"></div>
<div class="col-md-6"><label class="form-label">Tags EN</label><input class="form-control" name="tags_en" value="{{ old('tags_en', $item->tags_en) }}"></div>
<div class="col-md-6"><label class="form-label">Excerpt EN</label><textarea class="form-control" name="excerpt_en" rows="3">{{ old('excerpt_en', $item->excerpt_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Excerpt AR</label><textarea class="form-control" name="excerpt_ar" rows="3">{{ old('excerpt_ar', $item->excerpt_ar) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Content EN</label><textarea class="form-control" name="content_en" rows="8">{{ old('content_en', $item->content_en) }}</textarea></div>
<div class="col-md-6"><label class="form-label">Content AR</label><textarea class="form-control" name="content_ar" rows="8">{{ old('content_ar', $item->content_ar) }}</textarea></div>
<div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published ?? true))><label class="form-check-label">Published</label></div>
<div class="col-md-2 form-check mt-4 pt-2"><input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item->is_featured))><label class="form-check-label">Featured</label></div>
</div></div><button class="btn btn-primary mt-3">Save</button></form>
@endsection
