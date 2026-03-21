<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFormAssignment extends Model
{
    use HasFactory;

    public const PAGE_KEY = 'page_key';
    public const PAGE_GROUP = 'page_group';
    public const VISA_COUNTRY = 'visa_country';
    public const VISA_CATEGORY = 'visa_category';
    public const DESTINATION = 'destination';
    public const DESTINATION_TYPE = 'destination_type';

    protected $fillable = [
        'lead_form_id',
        'assignment_type',
        'target_id',
        'target_key',
        'display_position',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'target_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(LeadForm::class, 'lead_form_id');
    }
}
