<?php

namespace App\Models;

use App\Support\AuditLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action_type',
        'module',
        'auditable_type',
        'auditable_id',
        'title',
        'description',
        'old_values',
        'new_values',
        'changed_fields',
        'target_label',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'auditable_id' => 'integer',
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'created_at' => 'datetime',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public function localizedAction(): string
    {
        return AuditLogService::actionLabel($this->action_type);
    }

    public function localizedModule(): string
    {
        return AuditLogService::moduleLabel($this->module);
    }

    public function actionBadgeClass(): string
    {
        return AuditLogService::actionBadgeClass($this->action_type);
    }

    public function moduleBadgeClass(): string
    {
        return AuditLogService::moduleBadgeClass($this->module);
    }
}
