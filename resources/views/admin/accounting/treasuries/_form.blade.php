@csrf
@if($method === 'PUT')
    @method('PUT')
@endif

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.accounting_treasury_name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.accounting_treasury_type') }}</label>
        <select name="type" class="form-select" required>
            <option value="">{{ __('admin.accounting_choose_treasury') }}</option>
            @foreach($typeOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $item->type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.accounting_treasury_identifier') }}</label>
        <input type="text" name="identifier" class="form-control" value="{{ old('identifier', $item->identifier) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('admin.accounting_treasury_opening_balance') }}</label>
        <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $item->opening_balance ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label d-block">{{ __('admin.status') }}</label>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
            <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('admin.notes') }}</label>
        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $item->notes) }}</textarea>
    </div>
    <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary">{{ __('admin.save') }}</button>
        <a href="{{ route('admin.accounting.treasuries.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
    </div>
</div>
