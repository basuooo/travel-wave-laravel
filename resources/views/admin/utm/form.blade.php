@extends('layouts.admin')

@section('page_title', $isEdit ? __('admin.utm_edit_campaign') : __('admin.utm_build_link'))
@section('page_description', __('admin.utm_builder_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="post" action="{{ $isEdit ? route('admin.utm.update', $item) : route('admin.utm.store') }}" class="row g-3">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="col-md-6">
            <label class="form-label">{{ __('admin.campaign_name') }}</label>
            <input type="text" name="display_name" value="{{ old('display_name', $item->display_name) }}" class="form-control @error('display_name') is-invalid @enderror" required>
            @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.base_url') }}</label>
            <input type="url" name="base_url" value="{{ old('base_url', $item->base_url) }}" class="form-control @error('base_url') is-invalid @enderror" required>
            @error('base_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4"><label class="form-label">UTM Source</label><input type="text" name="utm_source" value="{{ old('utm_source', $item->utm_source) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">UTM Medium</label><input type="text" name="utm_medium" value="{{ old('utm_medium', $item->utm_medium) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">UTM Campaign</label><input type="text" name="utm_campaign" value="{{ old('utm_campaign', $item->utm_campaign) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">UTM ID</label><input type="text" name="utm_id" value="{{ old('utm_id', $item->utm_id) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">UTM Term</label><input type="text" name="utm_term" value="{{ old('utm_term', $item->utm_term) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">UTM Content</label><input type="text" name="utm_content" value="{{ old('utm_content', $item->utm_content) }}" class="form-control"></div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.platform') }}</label>
            <input type="text" name="platform" value="{{ old('platform', $item->platform) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.owner') }}</label>
            <select name="owner_user_id" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" @selected((int) old('owner_user_id', $item->owner_user_id) === (int) $owner->id)>{{ $owner->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select name="status" class="form-select">
                @foreach($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" @selected(old('status', $item->status) === $statusValue)>{{ $statusLabel }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('admin.notes') }}</label>
            <textarea name="notes" rows="4" class="form-control">{{ old('notes', $item->notes) }}</textarea>
        </div>

        @if($generatedUrl)
            <div class="col-12">
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2 mb-0">
                    <div>
                        <div class="fw-semibold">{{ __('admin.final_url') }}</div>
                        <div class="small text-break">{{ $generatedUrl }}</div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-copy-text="{{ $generatedUrl }}">{{ __('admin.copy_link') }}</button>
                </div>
            </div>
        @endif

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">{{ $isEdit ? __('admin.save_changes') : __('admin.save') }}</button>
            <a href="{{ route('admin.utm.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-text]');
    if (!button) return;
    try {
        await navigator.clipboard.writeText(button.dataset.copyText || '');
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
    } catch (error) {
        console.error(error);
    }
});
</script>
@endsection
