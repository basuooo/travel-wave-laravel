<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFormField extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'lead_form_id',
        'field_key',
        'type',
        'label_en',
        'label_ar',
        'placeholder_en',
        'placeholder_ar',
        'help_text_en',
        'help_text_ar',
        'validation_rule',
        'default_value',
        'options',
        'depends_on_field',
        'depends_on_value',
        'is_required',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function form()
    {
        return $this->belongsTo(LeadForm::class, 'lead_form_id');
    }
}
