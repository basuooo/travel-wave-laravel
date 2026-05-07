@extends('layouts.admin')

@section('page_title', __('admin.edit_integration') ?? 'Edit Integration')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card admin-card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="platform-badge me-3">
                        @if($integration->platform === 'meta')
                            <i class="bi bi-facebook text-primary fs-1"></i>
                        @elseif($integration->platform === 'tiktok')
                            <i class="bi bi-tiktok text-dark fs-1"></i>
                        @endif
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $integration->name }}</h4>
                        <span class="text-muted text-uppercase small">{{ $integration->platform }} Integration</span>
                    </div>
                </div>

                <form action="{{ route('admin.integrations.update', $integration) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ Lang::has('admin.integration_name') ? __('admin.integration_name') : (app()->getLocale() == 'ar' ? 'اسم التكامل' : 'Integration Name') }}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $integration->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ Lang::has('admin.status') ? __('admin.status') : (app()->getLocale() == 'ar' ? 'الحالة' : 'Status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $integration->is_active))>
                            <label class="form-check-label" for="isActive">{{ Lang::has('admin.active') ? __('admin.active') : (app()->getLocale() == 'ar' ? 'نشط' : 'Active') }}</label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-4"><i class="bi bi-shield-lock me-2 text-primary"></i> API Credentials</h5>
                    
                    @foreach($fields as $name => $config)
                        <div class="mb-3">
                            <label class="form-label">{{ $config['label'] }}</label>
                            @if($config['type'] === 'textarea')
                                <textarea name="credentials[{{ $name }}]" class="form-control" rows="3" required>{{ old("credentials.{$name}", $integration->credentials[$name] ?? '') }}</textarea>
                            @else
                                <input type="{{ $config['type'] }}" name="credentials[{{ $name }}]" class="form-control" value="{{ old("credentials.{$name}", $integration->credentials[$name] ?? '') }}" required>
                            @endif
                        </div>
                    @endforeach

                    @if($integration->platform === 'meta')
                        <div class="mb-3 mt-4 p-3 bg-light rounded border-start border-primary border-4">
                            <label class="form-label fw-bold text-primary">Webhook Verify Token</label>
                            <input type="text" name="webhook_verify_token" class="form-control" value="{{ old('webhook_verify_token', $integration->webhook_verify_token) }}">
                            <div class="form-text">Used for Meta's automated verification challenge.</div>
                        </div>

                        <div class="webhook-url-box mt-4 p-3 bg-dark text-white rounded">
                            <div class="small text-secondary mb-1">Webhook Callback URL</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <code class="text-info">{{ route('api.webhooks.meta') }}</code>
                                <button type="button" class="btn btn-sm btn-link text-white p-0" onclick="navigator.clipboard.writeText('{{ route('api.webhooks.meta') }}')">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="mt-5 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            {{ Lang::has('admin.update') ? __('admin.update') : (app()->getLocale() == 'ar' ? 'حفظ التحديثات' : 'Update') }}
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
@endsection
