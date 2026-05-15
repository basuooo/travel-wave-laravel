<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\Setting;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppConversationController extends Controller
{
    public function index(Request $request)
    {
        $query = WhatsAppConversation::query()
            ->with(['assignedUser', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('wa_id', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%");
            });
        }

        return view('admin.whatsapp.index', [
            'conversations' => $query->paginate(25)->withQueryString(),
            'stats' => [
                'total'          => WhatsAppConversation::count(),
                'active'         => WhatsAppConversation::where('status', 'active')->count(),
                'human_handover' => WhatsAppConversation::where('status', 'human_handover')->count(),
            ],
        ]);
    }

    public function show(WhatsAppConversation $conversation)
    {
        $messages = $conversation->messages()->orderBy('created_at')->get();

        return view('admin.whatsapp.show', [
            'conversation' => $conversation,
            'messages'     => $messages,
        ]);
    }

    public function toggleAi(WhatsAppConversation $conversation)
    {
        if ($conversation->ai_active) {
            $conversation->disableAi();
            $message = 'تم إيقاف الرد التلقائي بالذكاء الاصطناعي';
        } else {
            $conversation->enableAi();
            $message = 'تم تفعيل الرد التلقائي بالذكاء الاصطناعي';
        }

        return back()->with('success', $message);
    }

    public function sendMessage(Request $request, WhatsAppConversation $conversation)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:4096'],
        ]);

        $settings = Setting::query()->firstOrCreate([]);
        $service  = new WhatsAppService($settings);
        $result   = $service->sendText($conversation->wa_id, $data['message']);

        if ($result) {
            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'direction'       => 'outbound',
                'message_type'    => 'text',
                'body'            => $data['message'],
                'ai_provider'     => null,
                'status'          => 'sent',
            ]);

            $conversation->update(['last_message_at' => now()]);

            return back()->with('success', 'تم إرسال الرسالة بنجاح');
        }

        return back()->with('error', 'فشل في إرسال الرسالة — تأكد من إعدادات WhatsApp');
    }

    public function assign(Request $request, WhatsAppConversation $conversation)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $conversation->update(['assigned_user_id' => $data['user_id']]);

        return back()->with('success', 'تم تعيين المحادثة');
    }

    public function clearHistory(WhatsAppConversation $conversation)
    {
        $conversation->messages()->delete();
        $conversation->update(['metadata' => null]); // Clear lead info too

        return back()->with('success', 'تم مسح ذاكرة المحادثة بنجاح');
    }
}
