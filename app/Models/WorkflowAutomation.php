<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowAutomation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'entity_type',
        'conditions',
        'actions',
        'is_active',
        'priority',
        'run_once',
        'cooldown_minutes',
        'created_by',
        'updated_by',
        'last_executed_at',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'run_once' => 'boolean',
        'priority' => 'integer',
        'cooldown_minutes' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'last_executed_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function executionLogs()
    {
        return $this->hasMany(WorkflowExecutionLog::class)->latest('executed_at');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
