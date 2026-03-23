<?php

namespace App\Http\Middleware;

use App\Support\CrmFollowUpReminderService;
use Closure;
use Illuminate\Http\Request;

class DispatchCrmFollowUpReminders
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            app(CrmFollowUpReminderService::class)->dispatchDueReminders();
        }

        return $next($request);
    }
}
