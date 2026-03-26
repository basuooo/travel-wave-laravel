<?php

namespace App\Console\Commands;

use App\Support\AdminNotificationCenterService;
use App\Support\CrmFollowUpReminderService;
use Illuminate\Console\Command;

class DispatchOperationalNotifications extends Command
{
    protected $signature = 'notifications:dispatch-operational';

    protected $description = 'Dispatch CRM operational notifications and reminders.';

    public function handle(
        CrmFollowUpReminderService $followUpReminderService,
        AdminNotificationCenterService $notificationCenterService
    ): int {
        $followUpReminderService->dispatchDueReminders();
        $notificationCenterService->dispatchOperationalReminders();

        $this->info('Operational notifications dispatched.');

        return self::SUCCESS;
    }
}
