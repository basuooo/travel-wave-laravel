@extends('layouts.admin')

@section('page_title', __('admin.seo_redirects_manager'))
@section('page_description', __('admin.seo_redirects_desc'))

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.seo.redirects.create') }}" class="btn btn-primary">{{ __('admin.create') }}</a>
</div>
<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>{{ __('admin.seo_source_url') }}</th>
                    <th>{{ __('admin.seo_destination_url') }}</th>
                    <th>{{ __('admin.type') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.seo_hits') }}</th>
                    <th>{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->source_path }}</td>
                        <td>{{ $item->destination_url }}</td>
                        <td>{{ $item->redirect_type }}</td>
                        <td>{{ $item->is_active ? __('admin.active') : __('admin.inactive') }}</td>
                        <td>{{ $item->hit_count }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.seo.redirects.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                            <form method="post" action="{{ route('admin.seo.redirects.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('admin.confirm_delete') }}')">{{ __('admin.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">{{ __('admin.seo_no_redirects') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
</div>
@endsection
