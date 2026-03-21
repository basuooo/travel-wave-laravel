<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadForm extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'name',
        'slug',
        'form_category',
        'title_en',
        'title_ar',
        'subtitle_en',
        'subtitle_ar',
        'submit_text_en',
        'submit_text_ar',
        'success_message_en',
        'success_message_ar',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function fields()
    {
        return $this->hasMany(LeadFormField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function assignments()
    {
        return $this->hasMany(LeadFormAssignment::class)->orderBy('sort_order')->orderBy('id');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }
}
