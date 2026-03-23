<?php

namespace App\Http\Controllers;

use App\Models\ChatbotKnowledgeItem;
use App\Models\Setting;
use App\Support\SiteChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        protected SiteChatbotService $chatbotService
    ) {
    }

    public function ask(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        abort_unless($setting->shouldRenderChatbot(), 404);
        abort_unless(ChatbotKnowledgeItem::query()->exists(), 503);

        $data = $request->validate([
            'question' => ['required', 'string', 'min:2', 'max:1000'],
            'locale' => ['nullable', 'in:ar,en'],
        ]);

        return response()->json(
            $this->chatbotService->answer(
                question: $data['question'],
                locale: $data['locale'] ?? null,
                request: $request,
            )
        );
    }
}
