@extends('layouts.admin')

@section('page_title', __('admin.create_integration') ?? 'Add Integration')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card admin-card">
            <div class="card-body">
                <form action="{{ route('admin.integrations.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('admin.integration_name') ?? 'Integration Name' }}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Meta Main Account" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('admin.platform') }}</label>
                        <select name="platform" class="form-select @error('platform') is-invalid @enderror" required>
                            <option value="">-- Select Platform --</option>
                            <option value="meta" @selected(old('platform') === 'meta')>Meta (Facebook/Instagram)</option>
                            <option value="tiktok" @selected(old('platform') === 'tiktok')>TikTok</option>
                        </select>
                        @error('platform') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('admin.status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                            <label class="form-check-label" for="isActive">{{ __('admin.active') }}</label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-4"><i class="bi bi-shield-lock me-2 text-primary"></i> API Credentials</h5>
                    
                    <div id="credentialsFields">
                        <p class="text-muted small">Select a platform first to see required credential fields.</p>
                    </div>

                    <div class="mt-5 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            {{ __('admin.create') }}
                        </button>
                        <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary px-4">
                            {{ __('admin.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const platformSelect = document.querySelector('select[name="platform"]');
    const credentialsDiv = document.getElementById('credentialsFields');

    const fields = {
        meta: [
            { name: 'app_id', label: 'App ID', type: 'text' },
            { name: 'app_secret', label: 'App Secret', type: 'password' },
            { name: 'access_token', label: 'Page Access Token', type: 'textarea' }
        ],
        tiktok: [
            { name: 'app_id', label: 'App ID', type: 'text' },
            { name: 'client_secret', label: 'Client Secret', type: 'password' },
            { name: 'access_token', label: 'Access Token', type: 'textarea' }
        ]
    };

    platformSelect.addEventListener('change', function() {
        const platform = this.value;
        credentialsDiv.innerHTML = '';

        if (fields[platform]) {
            fields[platform].forEach(field => {
                const div = document.createElement('div');
                div.className = 'mb-3';
                
                let input;
                if (field.type === 'textarea') {
                    input = `<textarea name="credentials[${field.name}]" class="form-control" rows="3" required></textarea>`;
                } else {
                    input = `<input type="${field.type}" name="credentials[${field.name}]" class="form-control" required>`;
                }

                div.innerHTML = `
                    <label class="form-label">${field.label}</label>
                    ${input}
                `;
                credentialsDiv.appendChild(div);
            });

            // Add Verify Token for Webhooks if platform supports it
            if (platform === 'meta') {
                const verifyTokenDiv = document.createElement('div');
                verifyTokenDiv.className = 'mb-3 mt-4 p-3 bg-light rounded';
                verifyTokenDiv.innerHTML = `
                    <label class="form-label fw-bold text-primary">Webhook Verify Token</label>
                    <input type="text" name="webhook_verify_token" class="form-control" placeholder="Create a unique token for Meta Webhook setup">
                    <div class="form-text">You will use this token when configuring the Webhook in Meta Developers portal.</div>
                `;
                credentialsDiv.appendChild(verifyTokenDiv);
            }
        } else {
            credentialsDiv.innerHTML = '<p class="text-muted small">Select a platform first to see required credential fields.</p>';
        }
    });

    // Trigger on page load if there's a pre-selected value
    if (platformSelect.value) {
        platformSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection
