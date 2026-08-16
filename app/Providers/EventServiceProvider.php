<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\LeadReceived::class => [
            \App\Listeners\NotifySalesTeam::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\CrmCustomer::observe(\App\Observers\ZapierObserver::class);
        \App\Models\Inquiry::observe(\App\Observers\ZapierObserver::class);
        \App\Models\CrmTask::observe(\App\Observers\ZapierObserver::class);
    }

}
