<?php

namespace App\Listeners;

use App\Events\LeadReceived;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifySalesTeam implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(LeadReceived $event)
    {
        $lead = $event->lead;
        $assignedUser = $lead->assignedUser;

        if ($assignedUser) {
            // Send internal notification or email
            Log::info("Notifying sales user #{$assignedUser->id} about new lead #{$lead->id}");
            // $assignedUser->notify(new NewLeadNotification($lead));
        }
    }
}
