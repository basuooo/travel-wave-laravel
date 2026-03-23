<?php

namespace App\Support;

use App\Models\CrmFollowUp;
use App\Models\User;
use App\Notifications\CrmFollowUpReminderNotification;
use Illuminate\Support\Facades\Schema;

class CrmFollowUpReminderService
{
    public function dispatchDueReminders(): void
    {
        if (! Schema::hasTable('crm_follow_ups') || ! Schema::hasTable('notifications')) {
            return;
        }

        CrmFollowUp::query()
            ->with(['inquiry', 'assignedUser'])
            ->where('status', CrmFollowUp::STATUS_PENDING)
            ->whereNull('reminder_sent_at')
            ->whereNotNull('remind_at')
            ->where('remind_at', '<=', now())
            ->chunkById(50, function ($followUps) {
                foreach ($followUps as $followUp) {
                    $recipients = collect();

                    if ($followUp->assignedUser) {
                        $recipients->push($followUp->assignedUser);
                    }

                    User::query()
                        ->where('is_active', true)
                        ->get()
                        ->filter(function (User $user) {
                            return $user->is_admin
                                || $user->roles->contains(fn ($role) => in_array($role->slug, ['super-admin', 'admin'], true));
                        })
                        ->each(fn (User $user) => $recipients->push($user));

                    $recipients
                        ->unique('id')
                        ->each(fn (User $user) => $user->notify(new CrmFollowUpReminderNotification($followUp)));

                    $followUp->forceFill([
                        'reminder_sent_at' => now(),
                    ])->save();
                }
            });
    }
}
