@extends('layouts.admin')

@section('page_title', 'Version History - ' . $landingPage->internal_name)
@section('page_description', 'View, compare, and restore previous version snapshots for this landing page.')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.landing-pages.builder', $landingPage) }}" class="btn btn-sm btn-outline-secondary mb-2">← Back to Builder</a>
    <h3 class="fw-bold mb-1">🕒 Version History for {{ $landingPage->internal_name }}</h3>
    <p class="text-muted font-monospace">/lp/{{ $landingPage->slug }}</p>
</div>

<div class="card admin-card p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Version #</th>
                    <th>Label</th>
                    <th>Created By</th>
                    <th>Date & Time</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($versions as $version)
                <tr>
                    <td>
                        <span class="badge bg-primary fs-6">v{{ $version->version_number }}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $version->label ?: 'Autosave Snapshot' }}</div>
                    </td>
                    <td>
                        <span class="small text-muted">{{ $version->creator?->name ?? 'System' }}</span>
                    </td>
                    <td class="small text-muted">
                        {{ $version->created_at->format('Y-m-d H:i:s') }} ({{ $version->created_at->diffForHumans() }})
                    </td>
                    <td class="text-end">
                        <form method="post" action="{{ route('admin.landing-pages.versions.restore', [$landingPage, $version]) }}" onsubmit="return confirm('Restore page to Version #{{ $version->version_number }}?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning fw-bold">
                                <i class="bi bi-arrow-counterclockwise"></i> Restore This Version
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No version history recorded yet. Snapshots are created automatically when saving in the Visual Builder.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
