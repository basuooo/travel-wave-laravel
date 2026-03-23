@extends('layouts.admin')

@section('page_title', $lead->full_name ?: __('admin.crm_lead_details'))
@section('page_description', __('admin.crm_lead_details_desc'))

@section('content')
@php($pendingFollowUp = $lead->crmFollowUps->firstWhere('status', 'pending'))
@php($selectedServiceTypeId = (int) old('crm_service_type_id', $lead->crm_service_type_id))
@php($selectedServiceSubtypeId = (int) old('crm_service_subtype_id', $lead->crm_service_subtype_id))
@php($selectedStatusId = (int) old('crm_status_id', $lead->crm_status_id))
@php($callLaterSelected = $selectedStatusId === (int) optional($statuses->firstWhere('slug', 'call-later'))->id)

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">{{ __('admin.crm_lead_details') }}</h2>
                    <div class="text-muted">{{ $lead->full_name }}</div>
                </div>
                <span class="badge text-bg-primary">{{ $lead->localizedStatus() }}</span>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('admin.full_name') }}</dt><dd class="col-sm-8">{{ $lead->full_name ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.phone') }}</dt><dd class="col-sm-8">{{ $lead->phone ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.whatsapp_number') }}</dt><dd class="col-sm-8">@if($lead->whatsappChatUrl(auth()->user()?->name))<a href="{{ $lead->whatsappChatUrl(auth()->user()?->name) }}" target="_blank" rel="noopener noreferrer" class="admin-table-link">{{ $lead->whatsapp_number }}</a>@else{{ $lead->whatsapp_number ?: '-' }}@endif</dd>
                <dt class="col-sm-4">{{ __('admin.email') }}</dt><dd class="col-sm-8">{{ $lead->email ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.crm_service_type') }}</dt><dd class="col-sm-8">{{ $lead->localizedServiceType() ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.crm_service_subtype') }}</dt><dd class="col-sm-8">{{ $lead->localizedServiceSubtype() ?: '-' }}</dd>
                <dt class="col-sm-4">{{ $lead->localizedServiceDestinationLabel() ?: __('admin.destination') }}</dt><dd class="col-sm-8">{{ $lead->serviceDestinationValue() ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.country') }}</dt><dd class="col-sm-8">{{ $lead->country ?: $lead->nationality ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.crm_number_of_persons') }}</dt><dd class="col-sm-8">{{ $lead->travelers_count ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.source') }}</dt><dd class="col-sm-8">{{ $lead->crmSource?->localizedName() ?: ($lead->lead_source ?: '-') }}</dd>
                <dt class="col-sm-4">{{ __('admin.assigned_to') }}</dt><dd class="col-sm-8">{{ $lead->assignedUser?->name ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.crm_updated_by') }}</dt><dd class="col-sm-8">{{ $lead->crmStatusUpdatedBy?->name ?: '-' }} / {{ optional($lead->statusChangedAt())->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.created_date') }}</dt><dd class="col-sm-8">{{ optional($lead->created_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.message') }}</dt><dd class="col-sm-8">{{ $lead->message ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.comment') }}</dt><dd class="col-sm-8">{{ $lead->admin_notes ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.crm_additional_notes') }}</dt><dd class="col-sm-8">{{ $lead->additional_notes ?: '-' }}</dd>
            </dl>
        </div>

        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_assignment_history') }}</h2>
            <div class="d-grid gap-3">
                @forelse($lead->crmAssignments as $assignment)
                    <div class="border rounded-4 p-3">
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <strong>{{ $assignment->changedByUser?->name ?: 'System' }}</strong>
                            <span class="text-muted small">{{ optional($assignment->changed_at)->format('Y-m-d H:i') ?: '-' }}</span>
                        </div>
                        <div>{{ $assignment->oldAssignedUser?->name ?: __('admin.crm_unassigned') }} -> {{ $assignment->newAssignedUser?->name ?: __('admin.crm_unassigned') }}</div>
                        @if($assignment->note)
                            <div class="text-muted small mt-2">{{ $assignment->note }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.crm_no_assignment_history') }}</div>
                @endforelse
            </div>
        </div>

        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_status_history') }}</h2>
            <div class="d-grid gap-3">
                @forelse($lead->crmStatusUpdates as $update)
                    <div class="border rounded-4 p-3">
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <strong>{{ $update->user?->name ?: 'System' }}</strong>
                            <span class="text-muted small">{{ optional($update->changed_at)->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>{{ $update->oldStatus?->localizedName() ?: '-' }} -> {{ $update->newStatus?->localizedName() ?: '-' }}</div>
                        @if($update->note)
                            <div class="text-muted small mt-2">{{ $update->note }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.crm_no_status_history') }}</div>
                @endforelse
            </div>
        </div>

        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_followups') }}</h2>
            <div class="d-grid gap-3">
                @forelse($lead->crmFollowUps as $followUp)
                    <div class="border rounded-4 p-3">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="fw-semibold">{{ optional($followUp->scheduled_at)->format('Y-m-d H:i') ?: '-' }}</div>
                                <div class="text-muted small">{{ __('admin.crm_reminder_before') }}: {{ $followUp->reminderLabel() }}</div>
                            </div>
                            <span class="badge text-bg-light">{{ __('admin.crm_follow_up_' . $followUp->visualStatus()) }}</span>
                        </div>
                        <div class="small mb-2">{{ __('admin.assigned_to') }}: {{ $followUp->assignedUser?->name ?: '-' }}</div>
                        @if($followUp->note)
                            <div class="small text-muted mb-2">{{ $followUp->note }}</div>
                        @endif
                        @if($followUp->completion_note)
                            <div class="small text-muted mb-2">{{ $followUp->completion_note }}</div>
                        @endif
                        <form method="post" action="{{ route('admin.crm.follow-ups.update', $followUp) }}" class="row g-2 align-items-end">
                            @csrf
                            @method('PUT')
                            <div class="col-md-4">
                                <label class="form-label">{{ __('admin.crm_reschedule_to') }}</label>
                                <input type="datetime-local" class="form-control" name="scheduled_at" value="{{ optional($followUp->scheduled_at)->format('Y-m-d\\TH:i') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('admin.crm_reminder_before') }}</label>
                                <select class="form-select" name="reminder_offset_minutes">
                                    @foreach([15 => __('admin.crm_15_minutes'), 30 => __('admin.crm_30_minutes'), 60 => __('admin.crm_1_hour'), 1440 => __('admin.crm_1_day')] as $value => $label)
                                        <option value="{{ $value }}" @selected((int) $followUp->reminder_offset_minutes === (int) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('admin.notes') }}</label>
                                <input class="form-control" name="note" value="{{ $followUp->note }}">
                            </div>
                            <div class="col-md-2 d-grid gap-2">
                                <button class="btn btn-sm btn-outline-secondary" name="action" value="reschedule">{{ __('admin.reschedule') }}</button>
                                <button class="btn btn-sm btn-outline-success" name="action" value="complete">{{ __('admin.mark_completed') }}</button>
                                <button class="btn btn-sm btn-outline-danger" name="action" value="cancel">{{ __('admin.cancel') }}</button>
                            </div>
                            <div class="col-12">
                                <input class="form-control" name="completion_note" placeholder="{{ __('admin.crm_completion_note') }}">
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.crm_no_followups') }}</div>
                @endforelse
            </div>
        </div>

        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.comment') }}</h2>
            <form method="post" action="{{ route('admin.crm.leads.notes.store', $lead) }}" class="mb-4">
                @csrf
                <textarea class="form-control mb-3" name="body" rows="3" placeholder="{{ __('admin.add_comment') }}"></textarea>
                <button class="btn btn-primary">{{ __('admin.add_comment') }}</button>
            </form>
            <div class="d-grid gap-3">
                @forelse($lead->crmNotes as $note)
                    <div class="border rounded-4 p-3">
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <strong>{{ $note->user?->name ?: 'System' }}</strong>
                            <span class="text-muted small">{{ optional($note->created_at)->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>{{ $note->body }}</div>
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.crm_no_notes') }}</div>
                @endforelse
            </div>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.crm_tasks') }}</h2>
            <form method="post" action="{{ route('admin.crm.leads.tasks.store', $lead) }}" class="row g-3 mb-4">
                @csrf
                <div class="col-md-4"><input class="form-control" name="title" placeholder="{{ __('admin.title') }}"></div>
                <div class="col-md-3"><select class="form-select" name="assigned_user_id"><option value="">{{ __('admin.assigned_to') }}</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                <div class="col-md-3"><input type="datetime-local" class="form-control" name="due_at"></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">{{ __('admin.add_task') }}</button></div>
                <div class="col-12"><textarea class="form-control" name="description" rows="2" placeholder="{{ __('admin.description') }}"></textarea></div>
            </form>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.title') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.next_follow_up') }}</th><th></th></tr></thead>
                    <tbody>
                        @forelse($lead->crmTasks as $task)
                            <tr>
                                <td><div class="fw-semibold">{{ $task->title }}</div><div class="text-muted small">{{ $task->description }}</div></td>
                                <td>{{ $task->assignedUser?->name ?: '-' }}</td>
                                <td>
                                    <form method="post" action="{{ route('admin.crm.tasks.update', $task) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select class="form-select form-select-sm" name="status">
                                            @foreach(['open', 'in_progress', 'completed', 'cancelled'] as $status)
                                                <option value="{{ $status }}" @selected($task->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                </td>
                                <td>{{ optional($task->due_at)->format('Y-m-d H:i') ?: '-' }}</td>
                                <td class="text-end"><button class="btn btn-sm btn-outline-secondary">{{ __('admin.update') }}</button></form></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">{{ __('admin.crm_no_tasks') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <form method="post" action="{{ route('admin.crm.leads.update', $lead) }}" class="card admin-card p-4" id="crm-lead-form">
            @csrf
            @method('PUT')
            <h2 class="h5 mb-3">{{ __('admin.crm_lead_management') }}</h2>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">{{ __('admin.full_name') }}</label><input class="form-control" name="full_name" value="{{ old('full_name', $lead->full_name) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.phone') }}</label><input class="form-control" name="phone" value="{{ old('phone', $lead->phone) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.whatsapp_number') }}</label><input class="form-control" name="whatsapp_number" value="{{ old('whatsapp_number', $lead->whatsapp_number) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.email') }}</label><input class="form-control" name="email" value="{{ old('email', $lead->email) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.status') }}</label><select class="form-select" name="crm_status_id" data-crm-status-select>@foreach($statuses as $status)<option value="{{ $status->id }}" data-status-slug="{{ $status->slug }}" @selected($selectedStatusId === (int) $status->id)>{{ $status->localizedName() }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.source') }}</label><select class="form-select" name="crm_source_id"><option value="">-</option>@foreach($sources as $source)<option value="{{ $source->id }}" @selected((int) old('crm_source_id', $lead->crm_source_id) === (int) $source->id)>{{ $source->localizedName() }}</option>@endforeach</select></div>
                @if($canViewAllLeads)
                    <div class="col-md-6"><label class="form-label">{{ __('admin.assigned_to') }}</label><select class="form-select" name="assigned_user_id"><option value="">{{ __('admin.crm_unassigned') }}</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((int) old('assigned_user_id', $lead->assigned_user_id) === (int) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
                @else
                    <input type="hidden" name="assigned_user_id" value="{{ old('assigned_user_id', $lead->assigned_user_id) }}">
                    <div class="col-md-6"><label class="form-label">{{ __('admin.assigned_to') }}</label><input class="form-control" value="{{ $lead->assignedUser?->name ?: __('admin.crm_unassigned') }}" disabled></div>
                @endif
                <div class="col-md-6"><label class="form-label">{{ __('admin.priority') }}</label><select class="form-select" name="priority">@foreach(['low', 'normal', 'high', 'urgent'] as $priority)<option value="{{ $priority }}" @selected(old('priority', $lead->priority ?: 'normal') === $priority)>{{ ucfirst($priority) }}</option>@endforeach</select></div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.crm_service_type') }}</label>
                    <select class="form-select" name="crm_service_type_id" data-crm-service-type-select>
                        <option value="">-</option>
                        @foreach($serviceTypes as $serviceType)
                            <option value="{{ $serviceType->id }}"
                                    data-slug="{{ $serviceType->slug }}"
                                    data-requires-subtype="{{ $serviceType->requires_subtype ? '1' : '0' }}"
                                    data-destination-label="{{ $serviceType->localizedDestinationLabel() }}"
                                    @selected($selectedServiceTypeId === (int) $serviceType->id)>
                                {{ $serviceType->localizedName() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 d-none" data-crm-subtype-group>
                    <label class="form-label">{{ __('admin.crm_service_subtype') }}</label>
                    <select class="form-select" name="crm_service_subtype_id" data-crm-service-subtype-select>
                        <option value="">-</option>
                        @foreach($serviceTypes as $serviceType)
                            @foreach($serviceType->subtypes as $subtype)
                                <option value="{{ $subtype->id }}" data-parent-id="{{ $serviceType->id }}" @selected($selectedServiceSubtypeId === (int) $subtype->id)>{{ $subtype->localizedName() }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 d-none" data-crm-country-group>
                    <label class="form-label" data-crm-country-label>{{ __('admin.country') }}</label>
                    <input class="form-control" name="service_country_name" value="{{ old('service_country_name', $lead->service_country_name) }}">
                </div>
                <div class="col-md-6 d-none" data-crm-tourism-group>
                    <label class="form-label">{{ __('admin.crm_tourism_destination') }}</label>
                    <input class="form-control" name="tourism_destination" value="{{ old('tourism_destination', $lead->tourism_destination) }}">
                </div>
                <div class="col-md-6 d-none" data-crm-travel-group>
                    <label class="form-label">{{ __('admin.crm_travel_destination') }}</label>
                    <input class="form-control" name="travel_destination" value="{{ old('travel_destination', $lead->travel_destination) }}">
                </div>
                <div class="col-md-6 d-none" data-crm-hotel-group>
                    <label class="form-label">{{ __('admin.crm_hotel_destination') }}</label>
                    <input class="form-control" name="hotel_destination" value="{{ old('hotel_destination', $lead->hotel_destination) }}">
                </div>

                <div class="col-md-6"><label class="form-label">{{ __('admin.crm_number_of_persons') }}</label><input type="number" class="form-control" name="travelers_count" value="{{ old('travelers_count', $lead->travelers_count) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.country') }}</label><input class="form-control" name="country" value="{{ old('country', $lead->country) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.campaign_name') }}</label><input class="form-control" name="campaign_name" value="{{ old('campaign_name', $lead->campaign_name) }}"></div>
                <div class="col-md-6"><label class="form-label">UTM Source</label><input class="form-control" name="utm_source" value="{{ old('utm_source', $lead->utm_source) }}"></div>
                <div class="col-md-6"><label class="form-label">UTM Campaign</label><input class="form-control" name="utm_campaign" value="{{ old('utm_campaign', $lead->utm_campaign) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.crm_total_price') }}</label><input type="number" step="0.01" class="form-control" name="total_price" value="{{ old('total_price', $lead->total_price) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.crm_expenses') }}</label><input type="number" step="0.01" class="form-control" name="expenses" value="{{ old('expenses', $lead->expenses) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.crm_net_price') }}</label><input type="number" step="0.01" class="form-control" name="net_price" value="{{ old('net_price', $lead->net_price) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.travel_date') }}</label><input type="date" class="form-control" name="travel_date" value="{{ old('travel_date', optional($lead->travel_date)->format('Y-m-d')) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.last_follow_up') }}</label><input type="datetime-local" class="form-control" name="last_follow_up_at" value="{{ old('last_follow_up_at', optional($lead->last_follow_up_at)->format('Y-m-d\\TH:i')) }}"></div>
                <div class="col-md-6"><label class="form-label">{{ __('admin.next_follow_up') }}</label><input type="datetime-local" class="form-control" name="next_follow_up_at" value="{{ old('next_follow_up_at', optional($lead->next_follow_up_at)->format('Y-m-d\\TH:i')) }}"></div>
                <div class="col-12"><label class="form-label">{{ __('admin.crm_follow_up_result') }}</label><textarea class="form-control" name="follow_up_result" rows="2">{{ old('follow_up_result', $lead->follow_up_result) }}</textarea></div>
                <div class="col-12"><label class="form-label">{{ __('admin.message') }}</label><textarea class="form-control" name="message" rows="3">{{ old('message', $lead->message) }}</textarea></div>
                <div class="col-12"><label class="form-label">{{ __('admin.comment') }}</label><textarea class="form-control" name="admin_notes" rows="3">{{ old('admin_notes', $lead->admin_notes) }}</textarea></div>
                <div class="col-12"><label class="form-label">{{ __('admin.crm_additional_notes') }}</label><textarea class="form-control" name="additional_notes" rows="3">{{ old('additional_notes', $lead->additional_notes) }}</textarea></div>
                <div class="col-12"><label class="form-label">{{ __('admin.crm_status_change_note') }}</label><textarea class="form-control" name="status_change_note" rows="2" placeholder="{{ __('admin.crm_status_change_note_help') }}"></textarea></div>

                <div class="col-12 crm-call-later-box {{ $callLaterSelected ? '' : 'd-none' }}" data-call-later-box>
                    <div class="border rounded-4 p-3 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('admin.crm_follow_up_date') }}</label>
                                <input type="date" class="form-control" name="scheduled_follow_up_date" value="{{ old('scheduled_follow_up_date', optional(optional($pendingFollowUp)->scheduled_at)->format('Y-m-d')) }}" data-call-later-field>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('admin.crm_follow_up_time') }}</label>
                                <input type="time" class="form-control" name="scheduled_follow_up_time" value="{{ old('scheduled_follow_up_time', optional(optional($pendingFollowUp)->scheduled_at)->format('H:i')) }}" data-call-later-field>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('admin.crm_reminder_before') }}</label>
                                <select class="form-select" name="follow_up_reminder_offset" data-call-later-field>
                                    @foreach([15 => __('admin.crm_15_minutes'), 30 => __('admin.crm_30_minutes'), 60 => __('admin.crm_1_hour'), 1440 => __('admin.crm_1_day')] as $value => $label)
                                        <option value="{{ $value }}" @selected((int) old('follow_up_reminder_offset', optional($pendingFollowUp)->reminder_offset_minutes ?: 30) === (int) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('admin.crm_follow_up_note') }}</label>
                                <textarea class="form-control" name="follow_up_schedule_note" rows="2">{{ old('follow_up_schedule_note', optional($pendingFollowUp)->note) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary">{{ __('admin.update') }}</button>
                @if(auth()->user()?->hasPermission('leads.delete'))
                    <button type="submit" form="crm-delete-form" class="btn btn-outline-danger" onclick="return confirm('{{ __('admin.confirm_delete') }}')">{{ __('admin.delete') }}</button>
                @endif
            </div>
        </form>
        @if(auth()->user()?->hasPermission('leads.delete'))
            <form id="crm-delete-form" method="post" action="{{ route('admin.crm.leads.destroy', $lead) }}">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.querySelector('[data-crm-status-select]');
    const callLaterBox = document.querySelector('[data-call-later-box]');
    const callLaterFields = document.querySelectorAll('[data-call-later-field]');
    const typeSelect = document.querySelector('[data-crm-service-type-select]');
    const subtypeSelect = document.querySelector('[data-crm-service-subtype-select]');
    const subtypeGroup = document.querySelector('[data-crm-subtype-group]');
    const countryGroup = document.querySelector('[data-crm-country-group]');
    const countryLabel = document.querySelector('[data-crm-country-label]');
    const tourismGroup = document.querySelector('[data-crm-tourism-group]');
    const travelGroup = document.querySelector('[data-crm-travel-group]');
    const hotelGroup = document.querySelector('[data-crm-hotel-group]');

    const syncCallLater = () => {
        if (!statusSelect || !callLaterBox) {
            return;
        }

        const selected = statusSelect.options[statusSelect.selectedIndex]?.text?.trim();
        const isCallLater = selected === 'اتصل لاحقًا' || selected === 'Call Later';
        callLaterBox.classList.toggle('d-none', !isCallLater);
        const callLaterSlug = statusSelect.options[statusSelect.selectedIndex]?.dataset.statusSlug || '';
        const callLaterEnabled = callLaterSlug === 'call-later';
        callLaterBox.classList.toggle('d-none', !callLaterEnabled);
        callLaterFields.forEach((field) => {
            field.required = callLaterEnabled;
            field.disabled = !callLaterEnabled;
        });
    };

    const syncServiceFields = () => {
        if (!typeSelect) {
            return;
        }

        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const selectedTypeId = selectedOption?.value || '';
        const slug = selectedOption?.dataset.slug || '';
        const requiresSubtype = selectedOption?.dataset.requiresSubtype === '1';
        const destinationLabel = selectedOption?.dataset.destinationLabel || '{{ __('admin.country') }}';

        subtypeGroup?.classList.toggle('d-none', !requiresSubtype);
        countryGroup?.classList.toggle('d-none', slug !== 'external-visas');
        tourismGroup?.classList.toggle('d-none', slug !== 'domestic-tourism');
        travelGroup?.classList.toggle('d-none', slug !== 'flight-tickets');
        hotelGroup?.classList.toggle('d-none', slug !== 'hotel-booking');

        if (countryLabel) {
            countryLabel.textContent = destinationLabel;
        }

        if (subtypeSelect) {
            Array.from(subtypeSelect.options).forEach((option) => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden = option.dataset.parentId !== selectedTypeId;
            });

            const current = subtypeSelect.options[subtypeSelect.selectedIndex];
            if (current && current.hidden) {
                subtypeSelect.value = '';
            }
        }
    };

    statusSelect?.addEventListener('change', syncCallLater);
    typeSelect?.addEventListener('change', syncServiceFields);

    syncCallLater();
    syncServiceFields();
});
</script>
@endsection
