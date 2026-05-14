<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppMessage;
use App\Models\Setting;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * GET /api/webhooks/whatsapp
     * Meta sends a GET request to verify the webhook URL.
     */
    public function verify(Request $request): Response
    {
        $settings    = Setting::query()->firstOrCreate([]);
        $whatsApp    = new WhatsAppService($settings);
        $challenge   = $whatsApp->verifyWebhook($request->query());

        if ($challenge !== null) {
            return response($challenge, 200);
        }

        Log::warning('WhatsApp webhook verification failed', $request->query());

        return response('Forbidden', 403);
    }

    /**
     * POST /api/webhooks/whatsapp
     * Meta sends incoming messages, status updates, etc.
     */
    public function handle(Request $request): Response
    {
        $payload = $request->json()->all();

        Log::debug('WhatsApp webhook received', ['payload' => $payload]);

        try {
            $this->dispatchMessages($payload);
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook dispatch error', ['error' => $e->getMessage()]);
        }

        // Always return 200 to Meta to acknowledge receipt
        return response('OK', 200);
    }

    protected function dispatchMessages(array $payload): void
    {
        $entries = $payload['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];

            foreach ($changes as $change) {
                if (($change['field'] ?? '') !== 'messages') {
                    continue;
                }

                $value    = $change['value'] ?? [];
                $messages = $value['messages'] ?? [];
                $contacts = $value['contacts'] ?? [];

                foreach ($messages as $message) {
                    $waId    = $message['from'] ?? null;
                    $contact = collect($contacts)->firstWhere('wa_id', $waId) ?? [];

                    ProcessWhatsAppMessage::dispatch($message, $contact);
                }
            }
        }
    }
}
