<?php

namespace App\Http\Middleware;

use App\Support\CrmFollowUpReminderService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DispatchCrmFollowUpReminders
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && Cache::add('crm_follow_up_reminders_dispatching', now()->timestamp, now()->addSeconds(15))) {
            app(CrmFollowUpReminderService::class)->dispatchDueReminders();
        }

        return $next($request);
    }
}
