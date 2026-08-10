@extends('layouts.admin')

@section('page_title', 'إنشاء صفحة هبوط جديدة')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1">➕ إنشاء صفحة هبوط جديدة</h4>
                        <p class="text-muted mb-0">اختر طريقة البناء وسجل البيانات الأساسية للصفحة.</p>
                    </div>
                    <a href="{{ route('admin.landing-pages-new.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                        ← العودة للقائمة
                    </a>
                </div>

                <form action="{{ route('admin.landing-pages-new.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold">طريقة البناء (Creation Mode)</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="creation_mode" id="mode_scratch" value="scratch" checked>
                                <label class="btn btn-outline-primary w-100 p-3 text-start rounded-3" for="mode_scratch">
                                    <div class="fw-bold fs-6 mb-1">🧩 البدء من الصفر (Blank Canvas)</div>
                                    <div class="small text-muted">بناء صفحة هبوط من الهيكل الافتراضي الهجين.</div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="creation_mode" id="mode_template" value="template">
                                <label class="btn btn-outline-primary w-100 p-3 text-start rounded-3" for="mode_template">
                                    <div class="fw-bold fs-6 mb-1">🎨 من قالب جاهز (From Template)</div>
                                    <div class="small text-muted">استخدام هيكل قالب محدد مسبقاً في النظام.</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">الاسم الداخلي للصفحة <span class="text-danger">*</span></label>
                            <input type="text" name="internal_name" class="form-control form-control-lg" placeholder="مثال: حملة تأشيرات فرنسا - صيف 2026" required value="{{ old('internal_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العنوان بالعربية (Title AR)</label>
                            <input type="text" name="title_ar" class="form-control" placeholder="تأشيرة فرنسا السياحية" value="{{ old('title_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العنوان بالإنجليزية (Title EN)</label>
                            <input type="text" name="title_en" class="form-control" placeholder="France Tourist Visa" value="{{ old('title_en') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الرابط اللطيف (Slug)</label>
                            <input type="text" name="slug" class="form-control" placeholder="france-visa-summer-2026" value="{{ old('slug') }}">
                            <div class="form-text">سيصبح رابط الصفحة: <code>/lp-new/slug-name</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">العلامة التجارية (Brand)</label>
                            <select name="brand_id" class="form-select">
                                <option value="">ترافل ويف (الرئيسي)</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نموذج العملاء المرتبط (Assigned Lead Form)</label>
                            <select name="assigned_lead_form_id" class="form-select">
                                <option value="">-- اختياري --</option>
                                @foreach($forms as $form)
                                    <option value="{{ $form->id }}">{{ $form->name_ar ?: $form->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="template_select_wrapper">
                            <label class="form-label">اختر القالب (Template)</label>
                            <select name="template_id" class="form-select">
                                <option value="">-- اختر القالب --</option>
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->id }}">{{ $tpl->name_ar ?: $tpl->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.landing-pages-new.index') }}" class="btn btn-light rounded-pill px-4">إلغاء</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">🚀 متابعة للبناء المرئي (Builder)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
