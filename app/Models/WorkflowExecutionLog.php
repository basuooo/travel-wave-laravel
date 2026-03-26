<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowExecutionLog extends Model
{
    use HasFactory;

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'workflow_automation_id',
        'entity_type',
        'entity_id',
        'trigger_type',
        'execution_status',
        'target_label',
        'result_summary',
        'error_message',
        'context',
        'executed_at',
    ];

    protected $casts = [
        'workflow_automation_id' => 'integer',
        'entity_id' => 'integer',
        'context' => 'array',
        'executed_at' => 'datetime',
    ];

    public function automation()
    {
        return $this->belongsTo(WorkflowAutomation::class, 'workflow_automation_id');
    }
}
