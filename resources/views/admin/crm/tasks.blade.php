@extends('layouts.admin')

@section('page_title', __('admin.crm_tasks'))
@section('page_description', __('admin.crm_tasks_desc'))

@section('content')
<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>{{ __('admin.title') }}</th><th>{{ __('admin.full_name') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.next_follow_up') }}</th><th></th></tr></thead>
            <tbody>
                @foreach($items as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td><a href="{{ route('admin.crm.leads.show', $task->inquiry) }}">{{ $task->inquiry?->full_name }}</a></td>
                        <td>{{ $task->assignedUser?->name ?: '—' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $task->status)) }}</td>
                        <td>{{ optional($task->due_at)->format('Y-m-d H:i') ?: '—' }}</td>
                        <td class="text-end"><a href="{{ route('admin.crm.leads.show', $task->inquiry) }}" class="btn btn-sm btn-primary">{{ __('admin.view') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
