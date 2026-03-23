<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmStatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'status_level',
        'old_status_id',
        'new_status_id',
        'changed_by',
        'changed_at',
        'note',
    ];

    protected $casts = [
        'inquiry_id' => 'integer',
        'old_status_id' => 'integer',
        'new_status_id' => 'integer',
        'changed_by' => 'integer',
        'changed_at' => 'datetime',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function oldStatus()
    {
        return $this->belongsTo(CrmStatus::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(CrmStatus::class, 'new_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
