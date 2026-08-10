@extends('layouts.admin')

@section('page_title', 'تعديل إعدادات صفحة الهبوط: ' . $landingPage->internal_name)

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1">⚙️ إعدادات الصفحة والـ SEO: {{ $landingPage->internal_name }}</h4>
                <p class="text-muted mb-0">الرابط الحقيقي: <code>{{ $landingPage->publicUrl() }}</code></p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.landing-pages-new.builder', $landingPage) }}" class="btn btn-primary fw-bold rounded-pill px-4">
                    🛠️ الانتقال للبناء المرئي (Builder)
                </a>
                <a href="{{ route('admin.landing-pages-new.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    ← القائمة
                </a>
            </div>
        </div>

        <form action="{{ route('admin.landing-pages-new.update', $landingPage) }}" method="POST">
            @csrf
            @method('PUT')

            <ul class="nav nav-tabs nav-tabs-bordered mb-4" id="settingsTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-item nav-link active fw-bold" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button">📋 البيانات الأساسية</button>
                </li>
                <li class="nav-item">
                    <button class="nav-item nav-link fw-bold" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo-pane" type="button">🔍 محركات البحث (SEO & OpenGraph)</button>
                </li>
                <li class="nav-item">
                    <button class="nav-item nav-link fw-bold" id="code-tab" data-bs-toggle="tab" data-bs-target="#code-pane" type="button">💻 أكواد مخصصة (Custom CSS & JS)</button>
                </li>
            </ul>

            <div class="tab-content" id="settingsTabContent">
                <!-- GENERAL PANE -->
                <div class="tab-pane fade show active" id="general-pane">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الاسم الداخلي للصفحة</label>
                            <input type="text" name="internal_name" class="form-control" value="{{ old('internal_name', $landingPage->internal_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الـ Slug (الرابط)</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $landingPage->slug) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العنوان بالعربية (Title AR)</label>
                            <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $landingPage->title_ar) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العنوان بالإنجليزية (Title EN)</label>
                            <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $landingPage->title_en) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">العلامة التجارية (Brand)</label>
                            <select name="brand_id" class="form-select">
                                <option value="">ترافل ويف (الرئيسي)</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" @selected($landingPage->brand_id == $b->id)>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">حالة النشر (Status)</label>
                            <select name="status" class="form-select">
                                <option value="draft" @selected($landingPage->status === 'draft')>مسودة (Draft)</option>
                                <option value="published" @selected($landingPage->status === 'published')>منشورة (Published)</option>
                                <option value="archived" @selected($landingPage->status === 'archived')>مؤرشفة (Archived)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold d-block">التفعيل المباشر (Active Switch)</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active_switch" @checked($landingPage->is_active)>
                                <label class="form-check-label fw-bold" for="is_active_switch">تفعيل الصفحة أونلاين</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO PANE -->
                <div class="tab-pane fade" id="seo-pane">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">عنوان SEO بالعربية</label>
                            <input type="text" name="seo_title_ar" class="form-control" value="{{ old('seo_title_ar', $landingPage->seo_title_ar) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">عنوان SEO بالإنجليزية</label>
                            <input type="text" name="seo_title_en" class="form-control" value="{{ old('seo_title_en', $landingPage->seo_title_en) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">وصف Meta Description بالعربية</label>
                            <textarea name="seo_description_ar" class="form-control" rows="3">{{ old('seo_description_ar', $landingPage->seo_description_ar) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">وصف Meta Description بالإنجليزية</label>
                            <textarea name="seo_description_en" class="form-control" rows="3">{{ old('seo_description_en', $landingPage->seo_description_en) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- CUSTOM CODE PANE -->
                <div class="tab-pane fade" id="code-pane">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">أكواد Custom Head (توضع داخل &lt;head&gt;)</label>
                            <textarea name="custom_html_head" class="form-control font-monospace" rows="4" dir="ltr" placeholder="<!-- Pixel or meta tags -->">{{ old('custom_html_head', $landingPage->custom_html_head) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Custom CSS</label>
                            <textarea name="custom_css" class="form-control font-monospace" rows="8" dir="ltr" placeholder="/* CSS styles */">{{ old('custom_css', $landingPage->custom_css) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Custom JS</label>
                            <textarea name="custom_js" class="form-control font-monospace" rows="8" dir="ltr" placeholder="// Custom JavaScript">{{ old('custom_js', $landingPage->custom_js) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-navy rounded-pill px-5 fw-bold">💾 حفظ الإعدادات والتحديث</button>
            </div>
        </form>
    </div>
</div>
@endsection
