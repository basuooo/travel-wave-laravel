<?php

namespace App\Services\WhatsApp;

use App\Models\AiBotConfig;
use App\Models\WhatsappConfig;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Support\ChatbotKnowledgeManager;
use App\Support\SiteChatbotService;
use Illuminate\Support\Facades\Log;

class WhatsAppBotProcessor
{
    protected WhatsappConfig $whatsappConfig;
    protected AiBotConfig $aiConfig;
    protected WhatsAppService $whatsApp;
    protected SiteChatbotService $chatbotService;

    public function __construct(
        WhatsappConfig $whatsappConfig,
        AiBotConfig $aiConfig,
        WhatsAppService $whatsApp,
        SiteChatbotService $chatbotService
    ) {
        $this->whatsappConfig = $whatsappConfig;
        $this->aiConfig       = $aiConfig;
        $this->whatsApp       = $whatsApp;
        $this->chatbotService = $chatbotService;
    }

    /**
     * Process a single incoming WhatsApp message payload.
     */
    public function process(array $messageData, array $contactData): void
    {
        $waId        = $messageData['from'] ?? null;
        $messageId   = $messageData['id'] ?? null;
        $messageType = $messageData['type'] ?? 'unknown';

        if (! $waId) {
            Log::warning('WhatsApp: missing sender wa_id', $messageData);

            return;
        }

        // Mark as read
        if ($messageId) {
            $this->whatsApp->markAsRead($messageId);
        }

        // Get or create conversation
        $conversation = WhatsAppConversation::firstOrCreate(
            ['wa_id' => $waId],
            [
                'contact_name'    => $contactData['profile']['name'] ?? null,
                'locale'          => 'ar',
                'status'          => 'active',
                'ai_active'       => true,
                'last_message_at' => now(),
            ]
        );

        $conversation->update(['last_message_at' => now()]);

        // Extract text from message
        $inboundText = $this->extractText($messageData, $messageType);

        // Store inbound message
        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'wa_message_id'   => $messageId,
            'direction'       => 'inbound',
            'message_type'    => $messageType,
            'body'            => $inboundText,
            'payload'         => $messageData,
            'status'          => 'received',
        ]);

        // If not text, we can't answer
        if ($inboundText === null) {
            $this->sendNotSupported($conversation);

            return;
        }

        // Check for human handover keyword
        if ($this->isHandoverRequest($inboundText)) {
            $conversation->disableAi();
            $this->sendHandoverMessage($conversation);

            return;
        }

        // Check for resume-AI keyword
        if ($this->isResumeRequest($inboundText)) {
            $conversation->enableAi();
            $this->sendResumedMessage($conversation);

            return;
        }

        // If human agent has taken over, don't auto-reply
        if (! $conversation->isAiActive()) {
            return;
        }

        // Generate AI/chatbot reply
        $this->generateAndSendReply($conversation, $inboundText);

        // Every 3 inbound messages, try to extract/update lead info
        $inboundCount = $conversation->messages()->where('direction', 'inbound')->count();
        if ($inboundCount % 3 === 0) {
            app(\App\Services\Ai\AiLeadExtractor::class)->extract($conversation);
        }
    }

    /**
     * Generate a reply using the Chatbot service and send it.
     */
    protected function generateAndSendReply(WhatsAppConversation $conversation, string $question): void
    {
        try {
            // Get last 8 messages for context
            $history = $conversation->messages()
                ->orderByDesc('created_at')
                ->take(8)
                ->get()
                ->reverse()
                ->map(fn ($m) => [
                    'role'    => $m->direction === 'inbound' ? 'user' : 'assistant',
                    'content' => $m->body,
                ])
                ->all();

            $result = $this->chatbotService->answer(
                question: $question,
                locale: $conversation->locale ?? 'ar',
                history: $history
            );

            $reply = $result['answer'] ?? null;

            if (! filled($reply)) {
                return;
            }

            // Human-like delay (simulating typing)
            // Approx 50ms per character, min 1s, max 5s
            $delaySeconds = min(5, max(1, mb_strlen($reply) * 0.05));
            usleep($delaySeconds * 1000000);

            $this->whatsApp->sendText($conversation->wa_id, $reply);

            // Store outbound message
            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'wa_message_id'   => null,
                'direction'       => 'outbound',
                'message_type'    => 'text',
                'body'            => $reply,
                'payload'         => null,
                'ai_provider'     => $result['provider'] ?? 'chatbot',
                'status'          => 'sent',
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp Bot reply error', [
                'wa_id' => $conversation->wa_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function extractText(array $messageData, string $type): ?string
    {
        return match ($type) {
            'text'        => $messageData['text']['body'] ?? null,
            'interactive' => $messageData['interactive']['button_reply']['title']
                             ?? $messageData['interactive']['list_reply']['title']
                             ?? null,
            default       => null,
        };
    }

    protected function isHandoverRequest(string $text): bool
    {
        $keyword = mb_strtolower((string) ($this->whatsappConfig->handover_keyword ?? 'وكيل'));
        $keyword = trim($keyword);

        return str_contains(mb_strtolower($text), $keyword)
            || str_contains(mb_strtolower($text), 'human')
            || str_contains(mb_strtolower($text), 'agent')
            || str_contains(mb_strtolower($text), 'موظف')
            || str_contains(mb_strtolower($text), 'مسؤول');
    }

    protected function isResumeRequest(string $text): bool
    {
        $normalized = mb_strtolower($text);

        return str_contains($normalized, 'bot')
            || str_contains($normalized, 'روبوت')
            || str_contains($normalized, 'بوت')
            || str_contains($normalized, 'ذكاء اصطناعي')
            || str_contains($normalized, 'ai');
    }

    protected function sendHandoverMessage(WhatsAppConversation $conversation): void
    {
        $message = $conversation->locale === 'ar'
            ? "تم تحويلك إلى أحد ممثلي خدمة العملاء. سيتواصل معك أحد فريقنا قريبًا. 🙏"
            : "You have been transferred to a customer service agent. A team member will contact you shortly. 🙏";

        $this->whatsApp->sendText($conversation->wa_id, $message);

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction'       => 'outbound',
            'message_type'    => 'text',
            'body'            => $message,
            'status'          => 'sent',
        ]);
    }

    protected function sendResumedMessage(WhatsAppConversation $conversation): void
    {
        $message = $conversation->locale === 'ar'
            ? "مرحبًا! المساعد الذكي موجود ومستعد للمساعدة. 🤖 كيف يمكنني مساعدتك؟"
            : "Hello! The AI assistant is back and ready to help. 🤖 How can I assist you?";

        $this->whatsApp->sendText($conversation->wa_id, $message);

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction'       => 'outbound',
            'message_type'    => 'text',
            'body'            => $message,
            'status'          => 'sent',
        ]);
    }

    protected function sendNotSupported(WhatsAppConversation $conversation): void
    {
        $message = $conversation->locale === 'ar'
            ? "عذرًا، أستطيع فقط الرد على الرسائل النصية حاليًا. يرجى إرسال استفسارك كنص. ✍️"
            : "Sorry, I can only respond to text messages currently. Please send your inquiry as text. ✍️";

        $this->whatsApp->sendText($conversation->wa_id, $message);
    }
}
