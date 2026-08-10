@extends('layouts.admin')

@section('page_title', 'مكتبة الأقسام Reusable Sections')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h4 class="fw-bold mb-1">📦 مكتبة الأقسام القابلة لإعادة الاستخدام</h4>
        <p class="text-muted mb-4">أقسام هيرو، مميزات، نماذج، وآراء عملاء جاهزة للجميع.</p>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border p-3 rounded-4 bg-light">
                    <h5 class="fw-bold">🚀 Hero Banners</h5>
                    <p class="text-muted small">أقسام البانر العلوي بمختلف التنسيقات والخلفيات.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border p-3 rounded-4 bg-light">
                    <h5 class="fw-bold">✨ Features & Benefits</h5>
                    <p class="text-muted small">بطاقات وأعمدة عرض المميزات والخدمات.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border p-3 rounded-4 bg-light">
                    <h5 class="fw-bold">📝 Lead Forms & CTA</h5>
                    <p class="text-muted small">نماذج التواصل وحث المستخدمين على الطلب.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
