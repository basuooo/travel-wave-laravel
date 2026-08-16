<?php

namespace App\Services;

use App\Jobs\DispatchZapierWebhookJob;
use App\Models\ZapierSubscription;
use Illuminate\Support\Facades\Log;

class ZapierWebhookService
{
    /**
     * Dispatch an event payload to all active Zapier subscriptions registered for that event.
     *
     * @param string $event E.g. 'customer.created', 'customer.stage_updated', 'inquiry.created', 'task.created'
     * @param array $payload The data payload to send to Zapier.
     * @param int|null $userId Optional filter for subscriptions owned by a specific user.
     */
    public static function dispatchEvent(string $event, array $payload, ?int $userId = null): void
    {
        $query = ZapierSubscription::where('event', $event)
            ->where('is_active', true);

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->whereNull('user_id')->orWhere('user_id', $userId);
            });
        }

        $subscriptions = $query->get();

        foreach ($subscriptions as $subscription) {
            Log::info("Dispatching Zapier event [{$event}] to target URL: {$subscription->target_url}");
            DispatchZapierWebhookJob::dispatch($subscription->target_url, $payload);
        }
    }
}
