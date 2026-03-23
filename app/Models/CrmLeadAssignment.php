<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmLeadAssignment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'inquiry_id',
        'old_assigned_user_id',
        'new_assigned_user_id',
        'changed_by',
        'changed_at',
        'note',
    ];

    protected $casts = [
        'inquiry_id' => 'integer',
        'old_assigned_user_id' => 'integer',
        'new_assigned_user_id' => 'integer',
        'changed_by' => 'integer',
        'changed_at' => 'datetime',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function oldAssignedUser()
    {
        return $this->belongsTo(User::class, 'old_assigned_user_id');
    }

    public function newAssignedUser()
    {
        return $this->belongsTo(User::class, 'new_assigned_user_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
