<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppCampaignRecipient;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppBlacklist;
use App\Models\WhatsAppContact;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppCampaignProcessorService
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Process pending messages for active campaigns.
     * Designed to run via artisan schedule or background queue worker.
     */
    public function processActiveCampaigns(): int
    {
        $activeCampaigns = WhatsAppCampaign::where('status', 'running')->get();
        $processedTotal = 0;

        foreach ($activeCampaigns as $campaign) {
            $processedTotal += $this->processCampaignBatch($campaign);
        }

        return $processedTotal;
    }

    /**
     * Process a single batch for a specific campaign.
     */
    public function processCampaignBatch(WhatsAppCampaign $campaign, int $maxBatchSize = 10): int
    {
        // 1. Check if campaign is paused or stopped
        if ($campaign->status !== 'running') {
            return 0;
        }

        // 2. Check schedule time
        if ($campaign->schedule_type === 'scheduled' && $campaign->scheduled_at && Carbon::now()->isBefore($campaign->scheduled_at)) {
            return 0; // Not time yet
        }

        // 3. Check allowed days
        $todayDayNum = Carbon::now()->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
        if (!empty($campaign->allowed_days) && !in_array($todayDayNum, $campaign->allowed_days)) {
            return 0; // Today is not an allowed sending day
        }

        // 4. Check sending window hours
        $currentTimeStr = Carbon::now()->format('H:i:s');
        if ($campaign->sending_window_start && $campaign->sending_window_end) {
            if ($currentTimeStr < $campaign->sending_window_start || $currentTimeStr > $campaign->sending_window_end) {
                return 0; // Outside of allowed working hours
            }
        }

        // 5. Check daily limit
        if ($campaign->daily_limit > 0) {
            $sentToday = WhatsAppCampaignRecipient::where('campaign_id', $campaign->id)
                ->where('status', 'sent')
                ->whereDate('sent_at', Carbon::today())
                ->count();

            if ($sentToday >= $campaign->daily_limit) {
                Log::info("Campaign #{$campaign->id} reached daily limit of {$campaign->daily_limit}. Auto-pausing for today.");
                return 0;
            }
        }

        // 6. Fetch pending selected recipients
        $recipients = WhatsAppCampaignRecipient::where('campaign_id', $campaign->id)
            ->where('status', 'pending')
            ->where('is_selected', true)
            ->limit($maxBatchSize)
            ->get();

        if ($recipients->isEmpty()) {
            // Check if all selected recipients are processed
            $remainingPending = WhatsAppCampaignRecipient::where('campaign_id', $campaign->id)
                ->where('status', 'pending')
                ->where('is_selected', true)
                ->count();

            if ($remainingPending === 0) {
                $campaign->update([
                    'status'       => 'completed',
                    'completed_at' => Carbon::now(),
                ]);
                Log::info("Campaign #{$campaign->id} ({$campaign->name}) completed successfully!");
            }
            return 0;
        }

        $processedCount = 0;

        foreach ($recipients as $recipient) {
            // Check Blacklist
            $isBlacklisted = WhatsAppBlacklist::where('normalized_phone', $recipient->normalized_phone)->exists();
            if ($isBlacklisted) {
                $recipient->update([
                    'status'        => 'skipped_blacklist',
                    'error_message' => 'Number is blacklisted',
                ]);
                continue;
            }

            // Check Opt-out status
            $contact = WhatsAppContact::where('normalized_phone', $recipient->normalized_phone)->first();
            if ($contact && in_array($contact->opt_out_status, ['opted_out', 'do_not_contact', 'blocked', 'suppressed'])) {
                $recipient->update([
                    'status'        => 'skipped_optout',
                    'error_message' => 'Contact opted out / Do Not Contact',
                ]);
                continue;
            }

            // Format message content with dynamic variables
            $messageBody = $this->renderDynamicMessage($campaign->message_content, $recipient, $campaign);

            // Mark as processing
            $recipient->update(['status' => 'processing']);

            // Send via WhatsApp Service
            $sentResult = $this->whatsappService->sendText($recipient->phone, $messageBody);

            if ($sentResult && isset($sentResult['messages'][0]['id'])) {
                $metaId = $sentResult['messages'][0]['id'];

                $recipient->update([
                    'status'     => 'sent',
                    'sent_at'    => Carbon::now(),
                    'error_message' => null,
                ]);

                // Create or update conversation record
                $conversation = WhatsAppConversation::firstOrCreate([
                    'whatsapp_account_id' => $campaign->whatsapp_account_id,
                    'wa_id'               => $recipient->normalized_phone,
                ], [
                    'contact_name'     => $recipient->contact_name,
                    'status'           => 'active',
                    'first_contact_at' => Carbon::now(),
                ]);

                $conversation->update([
                    'last_message_at' => Carbon::now(),
                    'message_count'   => $conversation->message_count + 1,
                ]);

                // Record outbound message in log
                WhatsAppMessage::create([
                    'conversation_id'     => $conversation->id,
                    'whatsapp_account_id' => $campaign->whatsapp_account_id,
                    'direction'           => 'outbound',
                    'message_type'        => 'text',
                    'content'             => $messageBody,
                    'status'              => 'sent',
                    'meta_message_id'     => $metaId,
                    'sent_at'             => Carbon::now(),
                ]);

                $campaign->increment('sent_count');
                $campaign->decrement('pending_count');
                $processedCount++;
            } else {
                $recipient->update([
                    'status'        => 'failed',
                    'error_message' => 'WhatsApp API sending failure',
                    'retry_count'   => $recipient->retry_count + 1,
                ]);

                $campaign->increment('failed_count');
                $campaign->decrement('pending_count');
            }

            // Delay between messages (Fixed or Random Delay)
            $delaySec = $campaign->interval_type === 'random'
                ? rand($campaign->interval_min_sec ?: 30, $campaign->interval_max_sec ?: 90)
                : ($campaign->interval_min_sec ?: 60);

            if ($delaySec > 0 && $processedCount < count($recipients)) {
                sleep(min($delaySec, 5)); // Cap sleep in single process execution for queue friendliness
            }
        }

        return $processedCount;
    }

    /**
     * Render message variables: {{name}}, {{phone}}, {{country}}, {{service}}, {{employee}}, {{branch}}
     */
    public function renderDynamicMessage(string $template, WhatsAppCampaignRecipient $recipient, WhatsAppCampaign $campaign): string
    {
        $contactName = $recipient->contact_name ?: 'عميلنا العزيز';
        $phone = $recipient->phone;
        $country = $recipient->lead?->country ?? ($recipient->customer?->country ?? 'الرئيسي');
        $service = $recipient->lead?->service_type ?? ($recipient->customer?->service_type ?? 'خدماتنا');
        $employee = $campaign->account?->assignedUser?->name ?? 'خدمة العملاء';
        $branch = $campaign->account?->department_branch ?? 'الفرع الرئيسي';

        $replacements = [
            '{{name}}'     => $contactName,
            '{{phone}}'    => $phone,
            '{{country}}'  => $country,
            '{{service}}'  => $service,
            '{{employee}}' => $employee,
            '{{branch}}'   => $branch,
        ];

        return strtr($template, $replacements);
    }
}
