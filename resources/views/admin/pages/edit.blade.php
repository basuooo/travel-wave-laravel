@extends('layouts.admin')

@section('page_title', 'Edit Page: ' . $page->key)
@section('page_description', 'Manage bilingual hero content, repeatable sections, FAQs, CTA blocks, and publishing status.')

@section('content')
    @include('admin.pages.form', [
        'page' => $page,
        'formAction' => route('admin.pages.update', $page),
        'formMethod' => 'PUT',
        'submitLabel' => 'Save Page',
    ])
@endsection
