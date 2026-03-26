@extends('layouts.admin')

@section('page_title', __('admin.utm_saved_campaigns'))
@section('page_description', __('admin.utm_saved_campaigns_desc'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h2 class="h5 mb-1">{{ __('admin.utm_saved_campaigns') }}</h2>
        <p class="text-muted mb-0">{{ __('admin.utm_saved_campaigns_desc') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.utm.dashboard') }}" class="btn btn-outline-secondary">{{ __('admin.utm_analytics') }}</a>
        <a href="{{ route('admin.utm.create') }}" class="btn btn-primary">{{ __('admin.utm_build_link') }}</a>
    </div>
</div>

<div class="card admin-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.campaign_name') }}</th>
                    <th>{{ __('admin.base_url') }}</th>
                    <th>{{ __('admin.source') }}</th>
                    <th>{{ __('admin.medium') }}</th>
                    <th>{{ __('admin.owner') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th class="text-end">{{ __('admin.total_traffic') }}</th>
                    <th class="text-end">{{ __('admin.total_leads') }}</th>
                    <th>{{ __('admin.created_date') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php($conversionRate = $item->visits_count > 0 ? round(($item->inquiries_count / $item->visits_count) * 100, 2) : 0)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->display_name }}</div>
                            <div class="small text-muted">{{ $item->utm_campaign ?: '—' }}</div>
                        </td>
                        <td class="small">{{ $item->base_url }}</td>
                        <td>{{ $item->utm_source ?: '—' }}</td>
                        <td>{{ $item->utm_medium ?: '—' }}</td>
                        <td>{{ $item->owner?->name ?: '—' }}</td>
                        <td><span class="badge text-bg-{{ $item->status === 'active' ? 'success' : ($item->status === 'paused' ? 'warning' : 'secondary') }}">{{ $item->localizedStatus() }}</span></td>
                        <td class="text-end">{{ $item->visits_count }}</td>
                        <td class="text-end">
                            {{ $item->inquiries_count }}
                            <div class="small text-muted">{{ $conversionRate }}%</div>
                        </td>
                        <td>{{ $item->created_at?->format('d M Y') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-copy-text="{{ $item->generated_url }}">{{ __('admin.copy_link') }}</button>
                                <a href="{{ route('admin.utm.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                            </div>
                        </td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="10" class="small">
                            <strong>{{ __('admin.final_url') }}:</strong>
                            <a href="{{ $item->generated_url }}" target="_blank" rel="noopener">{{ $item->generated_url }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-5">{{ __('admin.no_data_available') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $items->links() }}
</div>

<script>
document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-text]');
    if (!button) return;
    try {
        await navigator.clipboard.writeText(button.dataset.copyText || '');
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
    } catch (error) {
        console.error(error);
    }
});
</script>
@endsection
