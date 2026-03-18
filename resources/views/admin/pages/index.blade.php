@extends('layouts.admin')

@section('page_title', 'Pages')
@section('page_description', 'Edit singleton pages including home, about, flights, hotels, and contact.')

@section('content')
<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Key</th><th>Title EN</th><th>Title AR</th><th></th></tr></thead>
            <tbody>
                @foreach($pages as $page)
                    <tr>
                        <td>{{ $page->key }}</td>
                        <td>{{ $page->title_en }}</td>
                        <td>{{ $page->title_ar }}</td>
                        <td class="text-end"><a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-primary">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
