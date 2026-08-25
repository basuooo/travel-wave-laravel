<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppBlacklist;
use App\Services\WhatsApp\WhatsAppContactMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class WhatsAppSettingController extends Controller
{
    protected WhatsAppContactMatchingService $matchingService;

    public function __construct(WhatsAppContactMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index()
    {
        if (!Schema::hasTable('whatsapp_blacklist')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $blacklist = Schema::hasTable('whatsapp_blacklist') ? WhatsAppBlacklist::with('addedBy')->latest()->get() : collect();
        return view('admin.whatsapp.settings.index', compact('blacklist'));
    }

    public function storeBlacklist(Request $request)
    {
        if (!Schema::hasTable('whatsapp_blacklist')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $validated = $request->validate([
            'phone'  => 'required|string',
            'reason' => 'nullable|string',
        ]);

        $norm = $this->matchingService->normalizePhone($validated['phone']);

        WhatsAppBlacklist::firstOrCreate([
            'normalized_phone' => $norm
        ], [
            'phone'            => $validated['phone'],
            'reason'           => $validated['reason'] ?? 'Manual Blacklist',
            'added_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.whatsapp.settings.index')->with('success', 'تم إضافة الرقم إلى القائمة السوداء بنجاح!');
    }

    public function destroyBlacklist(WhatsAppBlacklist $blacklist)
    {
        $blacklist->delete();
        return redirect()->route('admin.whatsapp.settings.index')->with('success', 'تم إزالة الرقم من القائمة السوداء بنجاح!');
    }
}
