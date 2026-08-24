<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'visa_record_id',
        'visa_country_id',
        'user_id',
        'user_name',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'description',
    ];

    public function visaRecord(): BelongsTo
    {
        return $this->belongsTo(VisaRecord::class, 'visa_record_id');
    }

    public function visaCountry(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
