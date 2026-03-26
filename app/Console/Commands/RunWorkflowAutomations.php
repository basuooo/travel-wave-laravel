<?php

namespace App\Console\Commands;

use App\Support\WorkflowAutomationService;
use Illuminate\Console\Command;

class RunWorkflowAutomations extends Command
{
    protected $signature = 'workflow:run-scheduled';

    protected $description = 'Run scheduled workflow automations for overdue and reminder-based rules.';

    public function handle(WorkflowAutomationService $workflowAutomationService): int
    {
        $workflowAutomationService->runScheduledChecks();
        $this->info('Scheduled workflow automations processed.');

        return self::SUCCESS;
    }
}
