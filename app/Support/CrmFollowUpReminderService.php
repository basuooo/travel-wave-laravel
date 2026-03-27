<?php

namespace App\Support;

use App\Models\CrmFollowUp;
use App\Models\User;
use App\Notifications\CrmFollowUpReminderNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CrmFollowUpReminderService
{
    public function dispatchDueReminders(): void
    {
        try {
            if (! Schema::hasTable('crm_follow_ups') || ! Schema::hasTable('notifications') || ! Schema::hasTable('users')) {
                return;
            }
        } catch (Throwable $exception) {
            report($exception);
            return;
        }

        $adminRecipients = $this->adminRecipients();

        CrmFollowUp::query()
            ->with(['inquiry', 'assignedUser'])
            ->where('status', CrmFollowUp::STATUS_PENDING)
            ->whereNull('reminder_sent_at')
            ->whereNotNull('remind_at')
            ->where('remind_at', '<=', now())
            ->chunkById(50, function ($followUps) use ($adminRecipients) {
                foreach ($followUps as $followUp) {
                    $recipients = collect();

                    if ($followUp->assignedUser) {
                        $recipients->push($followUp->assignedUser);
                    }

                    $adminRecipients->each(fn (User $user) => $recipients->push($user));

                    $recipients
                        ->unique('id')
                        ->each(fn (User $user) => $user->notify(new CrmFollowUpReminderNotification($followUp)));

                    $followUp->forceFill([
                        'reminder_sent_at' => now(),
                    ])->save();
                }
            });
    }

    protected function adminRecipients(): Collection
    {
        return User::query()
            ->with('roles')
            ->where('is_active', true)
            ->get()
            ->filter(function (User $user) {
                return $user->is_admin
                    || $user->roles->contains(fn ($role) => in_array($role->slug, ['super-admin', 'admin'], true));
            })
            ->values();
    }
}
