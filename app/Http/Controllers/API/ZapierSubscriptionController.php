<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ZapierSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ZapierSubscriptionController extends Controller
{
    /**
     * Supported Zapier event names.
     */
    public const ALLOWED_EVENTS = [
        'customer.created',
        'customer.stage_updated',
        'inquiry.created',
        'task.created',
    ];

    /**
     * Handle Zapier REST Hook subscription creation.
     */
    public function subscribe(Request $request): JsonResponse
    {
        ZapierSubscription::ensureTableExists();

        $validated = $request->validate([
            'event' => ['required', 'string', Rule::in(self::ALLOWED_EVENTS)],
            'target_url' => ['required', 'url'],
        ]);

        $subscription = ZapierSubscription::create([
            'user_id' => $request->user()?->id,
            'event' => $validated['event'],
            'target_url' => $validated['target_url'],
            'is_active' => true,
        ]);

        return response()->json([
            'id' => (string) $subscription->id,
            'event' => $subscription->event,
            'target_url' => $subscription->target_url,
            'status' => 'subscribed',
            'created_at' => $subscription->created_at?->toISOString(),
        ], 201);
    }

    /**
     * Handle Zapier REST Hook unsubscription.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        ZapierSubscription::ensureTableExists();

        $subscriptionId = $request->input('id') ?? $request->route('id');
        $targetUrl = $request->input('target_url');

        $query = ZapierSubscription::query();

        if ($subscriptionId) {
            $query->where('id', $subscriptionId);
        } elseif ($targetUrl) {
            $query->where('target_url', $targetUrl);
        } else {
            return response()->json(['error' => 'Please provide subscription ID or target_url.'], 422);
        }

        if ($request->user()) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('user_id')->orWhere('user_id', $request->user()->id);
            });
        }

        $deletedCount = $query->delete();

        return response()->json([
            'status' => 'unsubscribed',
            'deleted_count' => $deletedCount,
        ], 200);
    }

    /**
     * List user subscriptions.
     */
    public function index(Request $request): JsonResponse
    {
        ZapierSubscription::ensureTableExists();

        $subscriptions = ZapierSubscription::where('user_id', $request->user()?->id)
            ->get(['id', 'event', 'target_url', 'is_active', 'created_at']);

        return response()->json(['subscriptions' => $subscriptions]);
    }
}
