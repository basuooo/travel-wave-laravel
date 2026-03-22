@extends('layouts.admin')

@section('page_title', 'Create New Page')
@section('page_description', 'Create a new managed page with its own key, slug, content, and publish state.')

@section('content')
    @include('admin.pages.form', [
        'page' => $page,
        'formAction' => route('admin.pages.store'),
        'formMethod' => 'POST',
        'submitLabel' => 'Create Page',
    ])
@endsection
