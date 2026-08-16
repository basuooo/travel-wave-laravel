<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZapierSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ZapierAdminController extends Controller
{
    /**
     * Show Zapier integration dashboard in Admin Panel.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        // Ensure personal_access_tokens and zapier_subscriptions tables exist
        ZapierSubscription::ensureTableExists();

        $tokens = collect();
        if (Schema::hasTable('personal_access_tokens')) {
            $tokens = $user->tokens()->where('name', 'Zapier Integration Token')->latest()->get();
        }

        $quickWebhook = ZapierSubscription::where('event', 'global.catch_hook')->first();

        try {
            $subscriptions = ZapierSubscription::with('user')
                ->where('event', '!=', 'global.catch_hook')
                ->latest()
                ->paginate(15);
        } catch (\Throwable $e) {
            $subscriptions = new LengthAwarePaginator([], 0, 15);
        }

        return view('admin.zapier.index', [
            'user' => $user,
            'tokens' => $tokens,
            'subscriptions' => $subscriptions,
            'quickWebhook' => $quickWebhook,
            'baseUrl' => $request->schemeAndHttpHost(),
        ]);
    }

    /**
     * Save 1-Click Quick Zapier Catch Hook URL.
     */
    public function saveQuickWebhook(Request $request): RedirectResponse
    {
        ZapierSubscription::ensureTableExists();

        $request->validate([
            'quick_webhook_url' => 'nullable|url',
        ]);

        $url = trim((string) $request->input('quick_webhook_url'));

        if (! empty($url)) {
            ZapierSubscription::updateOrCreate(
                ['event' => 'global.catch_hook'],
                [
                    'target_url' => $url,
                    'is_active' => true,
                    'user_id' => $request->user()?->id,
                ]
            );
            return redirect()->route('admin.zapier.index')
                ->with('success', 'تم حفظ وتفعيل رابط Zapier السريع بنجاح! 🚀 أي بيانات جديدة بالسيستم ستُرسل فورياً إلى Zapier.');
        } else {
            ZapierSubscription::where('event', 'global.catch_hook')->delete();
            return redirect()->route('admin.zapier.index')
                ->with('success', 'تم إلغاء رابط الربط السريع بنجاح.');
        }
    }

    /**
     * Generate a new API token for Zapier.
     */
    public function generateToken(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Create Sanctum Token
        $tokenResult = $user->createToken('Zapier Integration Token');

        return redirect()->route('admin.zapier.index')
            ->with('success', 'تم إنشاء مفتاح API جديد لـ Zapier بنجاح!')
            ->with('plain_text_token', $tokenResult->plainTextToken);
    }

    /**
     * Revoke an API token.
     */
    public function revokeToken(Request $request, $tokenId): RedirectResponse
    {
        $user = $request->user();
        $user->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('admin.zapier.index')
            ->with('success', 'تم إلغاء مفتاح الـ API بنجاح.');
    }

    /**
     * Delete/Deactivate a Zapier subscription.
     */
    public function deleteSubscription(ZapierSubscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('admin.zapier.index')
            ->with('success', 'تم حذف اشتراك الـ Webhook بنجاح.');
    }
}
