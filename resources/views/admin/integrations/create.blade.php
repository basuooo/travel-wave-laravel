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
                        <label class="form-label fw-bold">{{ Lang::has('admin.integration_name') ? __('admin.integration_name') : (app()->getLocale() == 'ar' ? 'اسم التكامل' : 'Integration Name') }}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Meta Main Account" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ Lang::has('admin.platform') ? __('admin.platform') : (app()->getLocale() == 'ar' ? 'المنصة' : 'Platform') }}</label>
                        <select name="platform" id="platformSelect" class="form-select @error('platform') is-invalid @enderror" required onchange="renderPlatformFields()">
                            <option value="">-- {{ app()->getLocale() == 'ar' ? 'اختر المنصة' : 'Select Platform' }} --</option>
                            <option value="meta" @selected(old('platform') === 'meta')>Meta (Facebook/Instagram)</option>
                            <option value="tiktok" @selected(old('platform') === 'tiktok')>TikTok</option>
                        </select>
                        @error('platform') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ Lang::has('admin.status') ? __('admin.status') : (app()->getLocale() == 'ar' ? 'الحالة' : 'Status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                            <label class="form-check-label" for="isActive">{{ Lang::has('admin.active') ? __('admin.active') : (app()->getLocale() == 'ar' ? 'نشط' : 'Active') }}</label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-4"><i class="bi bi-shield-lock me-2 text-primary"></i> API Credentials</h5>
                    
                    <div id="credentialsFields" class="p-3 border rounded bg-light" style="min-height: 100px;">
                        <!-- Fields will be injected here -->
                        <p class="text-muted small m-0">{{ app()->getLocale() == 'ar' ? 'اختر المنصة أولاً لعرض حقول الربط المطلوبة.' : 'Select a platform first to see required credential fields.' }}</p>
                    </div>

                    <div class="mt-5 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            {{ Lang::has('admin.create') ? __('admin.create') : (app()->getLocale() == 'ar' ? 'إنشاء' : 'Create') }}
                        </button>
                        <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary px-4">
                            {{ Lang::has('admin.cancel') ? __('admin.cancel') : (app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function renderPlatformFields() {
        const platform = document.getElementById('platformSelect').value;
        const container = document.getElementById('credentialsFields');
        
        if (!platform) {
            container.innerHTML = '<p class="text-muted small m-0">{{ app()->getLocale() == "ar" ? "اختر المنصة أولاً لعرض حقول الربط المطلوبة." : "Select a platform first to see required credential fields." }}</p>';
            return;
        }

        const fieldsMap = {
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

        let html = '';
        if (fieldsMap[platform]) {
            fieldsMap[platform].forEach(field => {
                html += `<div class="mb-3">
                    <label class="form-label fw-bold small">${field.label}</label>
                    ${field.type === 'textarea' 
                        ? `<textarea name="credentials[${field.name}]" class="form-control" rows="3" required></textarea>`
                        : `<input type="${field.type}" name="credentials[${field.name}]" class="form-control" required>`
                    }
                </div>`;
            });

            if (platform === 'meta') {
                html += `<div class="mb-3 mt-4 p-3 bg-white border rounded shadow-sm">
                    <label class="form-label fw-bold text-primary small">Webhook Verify Token</label>
                    <input type="text" name="webhook_verify_token" class="form-control" placeholder="Create a unique token for Meta Webhook setup">
                    <div class="form-text x-small">You will use this token when configuring the Webhook in Meta Developers portal.</div>
                </div>`;
            }
            container.innerHTML = html;
        }
    }

    // Initialize on load
    window.addEventListener('load', renderPlatformFields);
    // Safety call
    setTimeout(renderPlatformFields, 500);
</script>
@endpush
@endsection
