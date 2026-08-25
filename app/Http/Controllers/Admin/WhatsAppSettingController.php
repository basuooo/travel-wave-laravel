<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppBlacklist;
use App\Services\WhatsApp\WhatsAppContactMatchingService;
use Illuminate\Http\Request;

class WhatsAppSettingController extends Controller
{
    protected WhatsAppContactMatchingService $matchingService;

    public function __construct(WhatsAppContactMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index()
    {
        $blacklist = WhatsAppBlacklist::with('addedBy')->latest()->get();
        return view('admin.whatsapp.settings.index', compact('blacklist'));
    }

    public function storeBlacklist(Request $request)
    {
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
