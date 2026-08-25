<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use App\Models\WhatsAppTemplate;
use App\Services\WhatsApp\WhatsAppContactMatchingService;
use App\Support\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class WhatsAppBulkCampaignController extends Controller
{
    protected WhatsAppContactMatchingService $matchingService;

    public function __construct(WhatsAppContactMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function index(Request $request)
    {
        if (!Schema::hasTable('whatsapp_campaigns')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $query = WhatsAppCampaign::with(['account', 'creator']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $campaigns = Schema::hasTable('whatsapp_campaigns') ? $query->latest()->paginate(15) : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        return view('admin.whatsapp.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        if (!Schema::hasTable('whatsapp_campaigns')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $accounts = Schema::hasTable('whatsapp_accounts') ? WhatsAppAccount::where('is_active', true)->get() : collect();
        $templates = Schema::hasTable('whatsapp_templates') ? WhatsAppTemplate::all() : collect();
        $employees = User::all();

        return view('admin.whatsapp.bulk.create', compact('accounts', 'templates', 'employees'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('whatsapp_campaigns')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'whatsapp_account_id'  => 'required|exists:whatsapp_accounts,id',
            'raw_numbers'          => 'required|string',
            'message_content'      => 'required|string',
            'schedule_type'        => 'required|in:now,scheduled',
            'scheduled_at'         => 'nullable|date',
            'interval_type'        => 'required|in:fixed,random',
            'interval_min_sec'     => 'required|integer|min:5',
            'interval_max_sec'     => 'required|integer|min:5',
            'daily_limit'          => 'nullable|integer',
        ]);

        $account = WhatsAppAccount::findOrFail($validated['whatsapp_account_id']);
        $parsed = $this->matchingService->parseNumbersInput($validated['raw_numbers']);

        // Filter valid numbers
        $validRecords = array_filter($parsed, fn($p) => $p['is_valid'] && !$p['is_duplicate']);

        $campaign = WhatsAppCampaign::create([
            'name'                 => $validated['name'],
            'type'                 => 'bulk',
            'whatsapp_account_id'  => $account->id,
            'created_by_user_id'   => auth()->id(),
            'status'               => $validated['schedule_type'] === 'scheduled' ? 'scheduled' : 'running',
            'audience_source'      => 'bulk_upload',
            'message_content'      => $validated['message_content'],
            'schedule_type'        => $validated['schedule_type'],
            'scheduled_at'         => $validated['scheduled_at'] ? Carbon::parse($validated['scheduled_at']) : null,
            'interval_type'        => $validated['interval_type'],
            'interval_min_sec'     => $validated['interval_min_sec'],
            'interval_max_sec'     => $validated['interval_max_sec'],
            'daily_limit'          => $validated['daily_limit'] ?? null,
            'total_contacts'       => count($validRecords),
            'pending_count'        => count($validRecords),
            'started_at'           => $validated['schedule_type'] === 'now' ? now() : null,
        ]);

        foreach ($validRecords as $rec) {
            WhatsAppCampaignRecipient::create([
                'campaign_id'         => $campaign->id,
                'whatsapp_account_id' => $account->id,
                'phone'               => $rec['phone'],
                'normalized_phone'    => $rec['normalized_phone'],
                'contact_name'        => $rec['name'],
                'contact_status'      => 'bulk_imported',
                'is_selected'         => true,
                'status'              => 'pending',
            ]);
        }

        AuditLogService::log('whatsapp_campaign_created', "Created Bulk Campaign #{$campaign->id} ({$campaign->name})");

        if ($campaign->status === 'running') {
            dispatch(new \App\Jobs\ProcessWhatsAppCampaignJob($campaign->id));
        }

        return redirect()->route('admin.whatsapp.bulk.index')->with('success', 'تم إطلاق الحملة الجماعية بنجاح وتسجيلها في الخلفية!');
    }

    public function show(WhatsAppCampaign $campaign)
    {
        $campaign->load(['account', 'creator', 'recipients' => function ($q) {
            $q->latest()->paginate(50);
        }]);

        return view('admin.whatsapp.campaigns.show', compact('campaign'));
    }

    public function pause(WhatsAppCampaign $campaign)
    {
        $campaign->update([
            'status'    => 'paused',
            'paused_at' => now(),
        ]);

        AuditLogService::log('whatsapp_campaign_paused', "Paused Campaign #{$campaign->id}");

        return redirect()->back()->with('success', 'تم إيقاف الحملة مؤقتاً.');
    }

    public function resume(WhatsAppCampaign $campaign)
    {
        $campaign->update([
            'status'    => 'running',
            'paused_at' => null,
        ]);

        AuditLogService::log('whatsapp_campaign_resumed', "Resumed Campaign #{$campaign->id}");
        dispatch(new \App\Jobs\ProcessWhatsAppCampaignJob($campaign->id));

        return redirect()->back()->with('success', 'تم استئناف الحملة وسوف تستكمل الإرسال من المكان الذي توقفت عنده.');
    }

    public function stop(WhatsAppCampaign $campaign)
    {
        $campaign->update([
            'status' => 'cancelled',
        ]);

        AuditLogService::log('whatsapp_campaign_cancelled', "Cancelled Campaign #{$campaign->id}");

        return redirect()->back()->with('success', 'تم إلغاء الحملة بنجاح.');
    }

    public function duplicate(WhatsAppCampaign $campaign)
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->name = "نسخة من " . $campaign->name;
        $newCampaign->status = 'draft';
        $newCampaign->sent_count = 0;
        $newCampaign->failed_count = 0;
        $newCampaign->pending_count = $campaign->total_contacts;
        $newCampaign->started_at = null;
        $newCampaign->completed_at = null;
        $newCampaign->paused_at = null;
        $newCampaign->save();

        foreach ($campaign->recipients as $rec) {
            $newRec = $rec->replicate();
            $newRec->campaign_id = $newCampaign->id;
            $newRec->status = 'pending';
            $newRec->sent_at = null;
            $newRec->error_message = null;
            $newRec->save();
        }

        AuditLogService::log('whatsapp_campaign_duplicated', "Duplicated Campaign #{$campaign->id} into #{$newCampaign->id}");

        return redirect()->route('admin.whatsapp.bulk.index')->with('success', 'تم نسخ الحملة بنجاح كمسودة جديدة!');
    }

    public function destroy(WhatsAppCampaign $campaign)
    {
        AuditLogService::log('whatsapp_campaign_deleted', "Deleted Campaign #{$campaign->id}");
        $campaign->delete();

        return redirect()->route('admin.whatsapp.bulk.index')->with('success', 'تم حذف الحملة بنجاح!');
    }
}
