<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.source_lead') }}</label>
        @if(($lead ?? null))
            <input type="hidden" name="inquiry_id" value="{{ $lead->id }}">
            <input class="form-control" value="{{ $lead->full_name }} - {{ $lead->phone }}" disabled>
        @else
            <select class="form-select" name="inquiry_id" @disabled(($formMethod ?? 'POST') !== 'POST')>
                <option value="">{{ __('admin.select_lead') }}</option>
                @foreach($availableLeads as $availableLead)
                    <option value="{{ $availableLead->id }}" @selected((int) old('inquiry_id', $customer->inquiry_id) === (int) $availableLead->id)>{{ $availableLead->full_name }} - {{ $availableLead->phone }}</option>
                @endforeach
            </select>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.assigned_to') }}</label>
        <select class="form-select" name="assigned_user_id">
            <option value="">{{ __('admin.crm_unassigned') }}</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((int) old('assigned_user_id', $customer->assigned_user_id) === (int) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.full_name') }}</label>
        <input class="form-control" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.phone') }}</label>
        <input class="form-control" name="phone" value="{{ old('phone', $customer->phone) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.whatsapp_number') }}</label>
        <input class="form-control" name="whatsapp_number" value="{{ old('whatsapp_number', $customer->whatsapp_number) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.email') }}</label>
        <input class="form-control" name="email" value="{{ old('email', $customer->email) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.nationality') }}</label>
        <input class="form-control" name="nationality" value="{{ old('nationality', $customer->nationality) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.country') }}</label>
        <input class="form-control" name="country" value="{{ old('country', $customer->country) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.destination') }}</label>
        <input class="form-control" name="destination" value="{{ old('destination', $customer->destination) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.customer_stage') }}</label>
        <select class="form-select" name="stage" required>
            @foreach($stageOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('stage', $customer->stage) === $value)>{{ $label[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.appointment_date') }}</label>
        <input type="datetime-local" class="form-control" name="appointment_at" value="{{ old('appointment_at', optional($customer->appointment_at)->format('Y-m-d\\TH:i')) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('admin.submission_date') }}</label>
        <input type="datetime-local" class="form-control" name="submission_at" value="{{ old('submission_at', optional($customer->submission_at)->format('Y-m-d\\TH:i')) }}">
    </div>
    @if(($formMethod ?? 'POST') !== 'POST')
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $customer->is_active))>
                <label class="form-check-label" for="is_active">{{ __('admin.active_customer') }}</label>
            </div>
        </div>
    @endif
    <div class="col-12">
        <label class="form-label">{{ __('admin.notes') }}</label>
        <textarea class="form-control" name="notes" rows="4">{{ old('notes', $customer->notes) }}</textarea>
    </div>
</div>
