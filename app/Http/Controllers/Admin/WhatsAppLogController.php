<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppCampaignRecipient;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class WhatsAppLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('whatsapp_campaign_recipients')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $query = WhatsAppCampaignRecipient::with(['campaign', 'account']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('phone', 'LIKE', "%{$s}%")
                  ->orWhere('contact_name', 'LIKE', "%{$s}%");
            });
        }

        $logs = Schema::hasTable('whatsapp_campaign_recipients') ? $query->latest()->paginate(30) : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 30);

        return view('admin.whatsapp.logs.index', compact('logs'));
    }
}
