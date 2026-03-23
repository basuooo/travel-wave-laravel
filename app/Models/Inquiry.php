<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_form_id',
        'lead_form_assignment_id',
        'marketing_landing_page_id',
        'type',
        'form_name',
        'form_category',
        'full_name',
        'phone',
        'whatsapp_number',
        'email',
        'nationality',
        'country',
        'destination',
        'service_type',
        'travel_date',
        'return_date',
        'travelers_count',
        'nights_count',
        'accommodation_type',
        'estimated_budget',
        'preferred_language',
        'source_page',
        'display_position',
        'message',
        'submitted_data',
        'status',
        'crm_status_id',
        'crm_status2_id',
        'crm_source_id',
        'crm_service_type_id',
        'crm_service_subtype_id',
        'status_1_updated_at',
        'status_1_updated_by',
        'status_2_updated_at',
        'status_2_updated_by',
        'crm_status_updated_at',
        'crm_status_updated_by',
        'assigned_user_id',
        'lead_source',
        'campaign_name',
        'service_country_name',
        'tourism_destination',
        'travel_destination',
        'hotel_destination',
        'utm_source',
        'utm_campaign',
        'priority',
        'last_follow_up_at',
        'next_follow_up_at',
        'follow_up_result',
        'admin_notes',
        'additional_notes',
        'total_price',
        'expenses',
        'net_price',
        'deleted_by',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'submitted_data' => 'array',
        'crm_status_id' => 'integer',
        'crm_status2_id' => 'integer',
        'crm_source_id' => 'integer',
        'crm_service_type_id' => 'integer',
        'crm_service_subtype_id' => 'integer',
        'status_1_updated_by' => 'integer',
        'status_2_updated_by' => 'integer',
        'crm_status_updated_by' => 'integer',
        'assigned_user_id' => 'integer',
        'deleted_by' => 'integer',
        'travelers_count' => 'integer',
        'nights_count' => 'integer',
        'status_1_updated_at' => 'datetime',
        'status_2_updated_at' => 'datetime',
        'crm_status_updated_at' => 'datetime',
        'last_follow_up_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'total_price' => 'decimal:2',
        'expenses' => 'decimal:2',
        'net_price' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function form()
    {
        return $this->belongsTo(LeadForm::class, 'lead_form_id');
    }

    public function formAssignment()
    {
        return $this->belongsTo(LeadFormAssignment::class, 'lead_form_assignment_id');
    }

    public function marketingLandingPage()
    {
        return $this->belongsTo(MarketingLandingPage::class);
    }

    public function crmStatus()
    {
        return $this->belongsTo(CrmStatus::class, 'crm_status_id');
    }

    public function crmStatus2()
    {
        return $this->belongsTo(CrmStatus::class, 'crm_status2_id');
    }

    public function crmSource()
    {
        return $this->belongsTo(CrmLeadSource::class, 'crm_source_id');
    }

    public function crmServiceType()
    {
        return $this->belongsTo(CrmServiceType::class, 'crm_service_type_id');
    }

    public function crmServiceSubtype()
    {
        return $this->belongsTo(CrmServiceSubtype::class, 'crm_service_subtype_id');
    }

    public function status1UpdatedBy()
    {
        return $this->belongsTo(User::class, 'status_1_updated_by');
    }

    public function status2UpdatedBy()
    {
        return $this->belongsTo(User::class, 'status_2_updated_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function crmStatusUpdatedBy()
    {
        return $this->belongsTo(User::class, 'crm_status_updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function crmNotes()
    {
        return $this->hasMany(CrmLeadNote::class)->latest();
    }

    public function crmTasks()
    {
        return $this->hasMany(CrmTask::class)->latest();
    }

    public function crmFollowUps()
    {
        return $this->hasMany(CrmFollowUp::class)->latest('scheduled_at');
    }

    public function crmStatusUpdates()
    {
        return $this->hasMany(CrmStatusUpdate::class)->latest('changed_at');
    }

    public function crmAssignments()
    {
        return $this->hasMany(CrmLeadAssignment::class)->latest('changed_at');
    }

    public function localizedPrimaryStatus(): string
    {
        return $this->crmStatus?->localizedName()
            ?: ucfirst(str_replace('-', ' ', $this->status ?: 'new'));
    }

    public function localizedStatus(): string
    {
        return $this->localizedPrimaryStatus();
    }

    public function localizedSecondaryStatus(): ?string
    {
        return $this->crmStatus2?->localizedName();
    }

    public function effectiveStatus(): ?CrmStatus
    {
        return $this->crmStatus;
    }

    public function localizedEffectiveStatus(): string
    {
        return $this->localizedStatus();
    }

    public function lastStatusChangeAt()
    {
        return $this->crm_status_updated_at
            ?? $this->status_1_updated_at
            ?? $this->status_2_updated_at
            ?? $this->updated_at
            ?? $this->created_at;
    }

    public function statusChangedAt()
    {
        return $this->crm_status_updated_at
            ?? $this->status_1_updated_at
            ?? $this->status_2_updated_at
            ?? $this->updated_at
            ?? $this->created_at;
    }

    public function scopeWhereEffectiveStatus($query, int $statusId)
    {
        return $query->where('crm_status_id', $statusId);
    }

    public function localizedServiceType(): ?string
    {
        return $this->crmServiceType?->localizedName() ?: $this->service_type;
    }

    public function localizedServiceSubtype(): ?string
    {
        return $this->crmServiceSubtype?->localizedName();
    }

    public function localizedServiceDestinationLabel(): ?string
    {
        return $this->crmServiceType?->localizedDestinationLabel();
    }

    public function serviceDestinationValue(): ?string
    {
        return $this->service_country_name
            ?: $this->tourism_destination
            ?: $this->travel_destination
            ?: $this->hotel_destination
            ?: $this->destination
            ?: $this->country;
    }

    public function normalizedWhatsappNumber(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->whatsapp_number);

        return $number !== '' ? $number : null;
    }

    public function whatsappGreetingMessage(?string $sellerName = null): string
    {
        $sellerName = trim((string) $sellerName);

        return 'اهلا وسهلا بحضرتك معاك '
            . ($sellerName !== '' ? $sellerName : 'فريق Travel Wave')
            . ' من Travel Wave';
    }

    public function whatsappChatUrl(?string $sellerName = null): ?string
    {
        $number = $this->normalizedWhatsappNumber();

        if (! $number) {
            return null;
        }

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($this->whatsappGreetingMessage($sellerName));
    }
}
