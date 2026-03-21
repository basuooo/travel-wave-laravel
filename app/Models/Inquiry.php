<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_form_id',
        'lead_form_assignment_id',
        'marketing_landing_page_id',
        'type',
        'form_name',
        'form_category',
        'full_name',
        'phone',
        'email',
        'nationality',
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
        'admin_notes',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'submitted_data' => 'array',
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
}
