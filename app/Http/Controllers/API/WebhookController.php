<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Integrations\PlatformManager;
use App\Jobs\ProcessWebhookJob;
use App\Models\CrmIntegration;
use App\Models\CrmWebhookLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhooks for various platforms.
     */
    public function handle(Request $request, string $platform)
    {
        // 1. Identify the integration
        // For Meta, we might need to find by verify_token or app_id
        // For simplicity, we'll try to find an active integration for this platform
        $integration = CrmIntegration::where('platform', $platform)
            ->where('is_active', true)
            ->first();

        if (!$integration) {
            Log::warning("Webhook received for unsupported or inactive platform: {$platform}");
            return response()->json(['message' => 'Integration not found'], 404);
        }

        // 2. Handle Meta's verification challenge
        if ($platform === 'meta' && $request->has('hub_mode')) {
            $manager = PlatformManager::make($integration);
            if ($manager->verifyWebhook($request, $integration)) {
                return response($request->get('hub_challenge'));
            }
            return response('Unauthorized', 403);
        }

        // 3. Log the raw webhook
        $webhookLog = CrmWebhookLog::create([
            'integration_id' => $integration->id,
            'platform' => $platform,
            'payload' => $request->all(),
            'status' => 'pending',
            'request_ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);

        // 4. Dispatch processing job
        ProcessWebhookJob::dispatch($webhookLog);

        return response()->json(['message' => 'Webhook received and queued'], 202);
    }
}
