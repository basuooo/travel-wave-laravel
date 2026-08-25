<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppAccount;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use App\Models\WhatsAppTemplate;
use App\Services\WhatsApp\WhatsAppContactMatchingService;
use App\Services\WhatsApp\WhatsAppCampaignProcessorService;
use App\Support\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class WhatsAppRetargetingController extends Controller
{
    protected WhatsAppContactMatchingService $matchingService;

    public function __construct(WhatsAppContactMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function create()
    {
        if (!Schema::hasTable('whatsapp_accounts')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $accounts = Schema::hasTable('whatsapp_accounts') ? WhatsAppAccount::where('is_active', true)->get() : collect();
        $templates = Schema::hasTable('whatsapp_templates') ? WhatsAppTemplate::all() : collect();
        $employees = User::all();

        return view('admin.whatsapp.retargeting.create', compact('accounts', 'templates', 'employees'));
    }

    public function matchNumbers(Request $request)
    {
        if (!Schema::hasTable('whatsapp_accounts')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'raw_numbers'         => 'nullable|string',
        ]);

        $accountId = (int) $request->whatsapp_account_id;
        $rawNumbers = $request->raw_numbers ?? '';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $rawNumbers .= "\n" . file_get_contents($file->getRealPath());
        }

        $parsed = $this->matchingService->parseNumbersInput($rawNumbers);
        $matchedResult = $this->matchingService->matchRetargetingNumbers($accountId, $parsed);

        return response()->json($matchedResult);
    }

    public function store(Request $request, WhatsAppCampaignProcessorService $processor)
    {
        if (!Schema::hasTable('whatsapp_campaigns')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'whatsapp_account_id'  => 'required|exists:whatsapp_accounts,id',
            'message_content'      => 'required|string',
            'recipients'           => 'required|array|min:1',
            'recipients.*.phone'   => 'required|string',
            'recipients.*.name'    => 'nullable|string',
            'recipients.*.status'  => 'required|string',
            'recipients.*.is_selected' => 'required|boolean',
            'schedule_type'        => 'required|in:now,scheduled',
            'scheduled_at'         => 'nullable|date',
            'interval_type'        => 'required|in:fixed,random',
            'interval_min_sec'     => 'required|integer|min:5',
            'interval_max_sec'     => 'required|integer|min:5',
            'daily_limit'          => 'nullable|integer',
        ]);

        $account = WhatsAppAccount::findOrFail($validated['whatsapp_account_id']);

        $selectedRecipients = array_filter($validated['recipients'], fn($r) => $r['is_selected'] == true);
        $prevCount = count(array_filter($selectedRecipients, fn($r) => $r['status'] === 'previously_contacted'));
        $notPrevCount = count(array_filter($selectedRecipients, fn($r) => $r['status'] === 'not_previously_contacted'));

        $campaign = WhatsAppCampaign::create([
            'name'                 => $validated['name'],
            'type'                 => 'retargeting',
            'whatsapp_account_id'  => $account->id,
            'created_by_user_id'   => auth()->id(),
            'status'               => $validated['schedule_type'] === 'scheduled' ? 'scheduled' : 'running',
            'audience_source'      => 'upload_matching',
            'message_content'      => $validated['message_content'],
            'schedule_type'        => $validated['schedule_type'],
            'scheduled_at'         => $validated['scheduled_at'] ? Carbon::parse($validated['scheduled_at']) : null,
            'interval_type'        => $validated['interval_type'],
            'interval_min_sec'     => $validated['interval_min_sec'],
            'interval_max_sec'     => $validated['interval_max_sec'],
            'daily_limit'          => $validated['daily_limit'] ?? null,
            'total_contacts'       => count($selectedRecipients),
            'previously_contacted_count'    => $prevCount,
            'not_previously_contacted_count'=> $notPrevCount,
            'pending_count'        => count($selectedRecipients),
            'started_at'           => $validated['schedule_type'] === 'now' ? now() : null,
        ]);

        foreach ($validated['recipients'] as $rec) {
            if (!$rec['is_selected']) continue;

            $norm = $this->matchingService->normalizePhone($rec['phone']);

            WhatsAppCampaignRecipient::create([
                'campaign_id'         => $campaign->id,
                'whatsapp_account_id' => $account->id,
                'phone'               => $rec['phone'],
                'normalized_phone'    => $norm,
                'contact_name'        => $rec['name'] ?? 'Contact',
                'contact_status'      => $rec['status'],
                'is_selected'         => true,
                'status'              => 'pending',
            ]);
        }

        app(AuditLogService::class)->log(auth()->user(), 'whatsapp', 'created', $campaign, ['description' => "Created Retargeting Campaign #{$campaign->id} ({$campaign->name}) with {$campaign->total_contacts} contacts"]);

        if ($campaign->status === 'running') {
            dispatch(new \App\Jobs\ProcessWhatsAppCampaignJob($campaign->id));
        }

        return redirect()->route('admin.whatsapp.bulk.index')->with('success', 'تم إنشاء وحفظ حملة إعادة الاستهداف بنجاح وهي تعمل الآن في الخلفية!');
    }
}
