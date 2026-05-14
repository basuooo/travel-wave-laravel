<?php

namespace App\Services\Ai;

use App\Models\AiBotConfig;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Log;

class AiLeadExtractor
{
    protected AiGateway $gateway;

    public function __construct()
    {
        $this->gateway = new AiGateway(AiBotConfig::getDefault());
    }

    /**
     * Extract lead information from conversation context.
     */
    public function extract(WhatsAppConversation $conversation): array
    {
        if (! $this->gateway->isEnabled()) {
            return [];
        }

        // Get last few messages to understand the lead
        $history = $conversation->messages()
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->reverse()
            ->map(fn($m) => "{$m->direction}: {$m->body}")
            ->implode("\n");

        $systemPrompt = "You are a lead qualification assistant. Extract structured JSON data from the conversation. 
            Fields: 
            - name: full name if mentioned
            - interest: what service are they interested in (e.g. France Visa, Dubai Tour)
            - budget: if mentioned
            - job: profession if mentioned
            - travel_history: countries mentioned
            - qualification_score: 0-100 based on interest and provided details
            - intent: 'information_gathering', 'ready_to_buy', 'complaint'
            
            ONLY return valid JSON. Do not explain.";

        try {
            $result = $this->gateway->reply(
                "Extract lead data from this chat:\n\n{$history}",
                collect(),
                'en'
            );

            $data = json_decode($result['reply'], true);

            if (is_array($data)) {
                // Update conversation metadata
                $currentMetadata = (array) $conversation->metadata;
                $conversation->update([
                    'metadata' => array_merge($currentMetadata, [
                        'lead_info' => $data,
                        'last_extraction_at' => now()->toDateTimeString(),
                    ])
                ]);

                // Sync to CRM Leads if we have enough info
                if (isset($data['name']) || $conversation->wa_id) {
                    \Illuminate\Support\Facades\DB::table('crm_leads')->updateOrInsert(
                        ['phone' => $conversation->wa_id],
                        [
                            'full_name' => $data['name'] ?? $conversation->contact_name ?? 'WhatsApp Lead',
                            'platform' => 'whatsapp',
                            'source' => 'AI Bot Extraction',
                            'status' => 'new',
                            'metadata' => json_encode($data),
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
                
                return $data;
            }
        } catch (\Throwable $e) {
            Log::error('Lead extraction failed', ['error' => $e->getMessage()]);
        }

        return [];
    }
}
