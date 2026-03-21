@extends('layouts.admin')

@section('page_title', __('admin.seo_meta_manager'))
@section('page_description', __('admin.seo_meta_desc'))

@section('content')
<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>{{ __('admin.name') }}</th>
                    <th>{{ __('admin.type') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($targets as $target)
                    @php($entry = $entries[$target['type'] . ':' . $target['id']] ?? null)
                    <tr>
                        <td>{{ $target['label'] }}</td>
                        <td>{{ $target['type'] }}</td>
                        <td>{{ $entry?->is_active ? __('admin.active') : __('admin.inactive') }}</td>
                        <td><a href="{{ route('admin.seo.meta.edit', ['targetType' => $target['type'], 'targetId' => $target['id']]) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
