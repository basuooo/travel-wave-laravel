<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppImportHistory extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_import_history';

    protected $fillable = [
        'file_name',
        'campaign_type',
        'total_numbers',
        'valid_count',
        'invalid_count',
        'duplicate_count',
        'imported_count',
        'rejected_count',
        'uploaded_by_user_id',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
