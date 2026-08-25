<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppContact;
use App\Models\CrmLead;
use App\Models\CrmCustomer;
use App\Models\WhatsAppBlacklist;
use Illuminate\Support\Facades\Log;

class WhatsAppContactMatchingService
{
    /**
     * Normalize a phone number to standard international format (digits only).
     */
    public function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/\D/', '', $phone);
        
        // Strip leading double zeros if present
        if (str_starts_with($cleaned, '00')) {
            $cleaned = substr($cleaned, 2);
        }

        return $cleaned;
    }

    /**
     * Validate if a phone number appears to be valid.
     */
    public function validatePhone(string $phone): bool
    {
        $normalized = $this->normalizePhone($phone);
        // Valid international phone number usually between 7 and 15 digits
        return strlen($normalized) >= 7 && strlen($normalized) <= 15;
    }

    /**
     * Parse raw text, CSV, or array of numbers and build structured contacts list.
     */
    public function parseNumbersInput(string $rawInput): array
    {
        $lines = preg_split('/[\r\n,;]+/', $rawInput);
        $records = [];
        $seen = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            // Check if line contains comma/tab separated (Name, Phone, etc.)
            $parts = preg_split('/[\t|]+/', $trimmed);
            $name = null;
            $phone = $trimmed;

            if (count($parts) >= 2) {
                $name = trim($parts[0]);
                $phone = trim($parts[1]);
            }

            $normalized = $this->normalizePhone($phone);
            if (empty($normalized)) {
                continue;
            }

            $isValid = $this->validatePhone($phone);
            $isDuplicate = isset($seen[$normalized]);
            $seen[$normalized] = true;

            $records[] = [
                'name'             => $name ?: 'Contact ' . substr($normalized, -4),
                'phone'            => $phone,
                'normalized_phone' => $normalized,
                'is_valid'         => $isValid,
                'is_duplicate'     => $isDuplicate,
            ];
        }

        return $records;
    }

    /**
     * Core Retargeting Requirement:
     * Match uploaded numbers against previous conversations of SPECIFIC WhatsApp Account ($whatsappAccountId).
     */
    public function matchRetargetingNumbers(int $whatsappAccountId, array $parsedRecords): array
    {
        // Fetch all conversations for this specific WhatsApp account indexed by normalized wa_id/phone
        $conversations = WhatsAppConversation::where('whatsapp_account_id', $whatsappAccountId)
            ->get()
            ->keyBy(function ($item) {
                return $this->normalizePhone($item->wa_id ?? '');
            });

        // Also fetch blacklisted numbers
        $blacklisted = WhatsAppBlacklist::pluck('normalized_phone')->flip();

        $matchedList = [];
        $prevCount = 0;
        $notPrevCount = 0;

        foreach ($parsedRecords as $record) {
            $norm = $record['normalized_phone'];
            $isBlacklisted = isset($blacklisted[$norm]);

            $existingConv = $conversations->get($norm);
            $isPreviouslyContacted = $existingConv !== null;

            if ($isPreviouslyContacted) {
                $contactStatus = 'previously_contacted';
                $prevCount++;
            } else {
                $contactStatus = 'not_previously_contacted';
                $notPrevCount++;
            }

            // Lookup CRM lead / customer info if existing
            $crmLead = CrmLead::where('phone', 'LIKE', "%{$norm}%")->first();
            $crmCustomer = CrmCustomer::where('phone', 'LIKE', "%{$norm}%")->first();

            $matchedList[] = [
                'name'                  => $record['name'] ?: ($existingConv->contact_name ?? ($crmLead->name ?? ($crmCustomer->name ?? 'Contact'))),
                'phone'                 => $record['phone'],
                'normalized_phone'      => $norm,
                'contact_status'        => $contactStatus,
                'is_previously_contacted'=> $isPreviouslyContacted,
                'previous_whatsapp_account_id' => $existingConv ? $existingConv->whatsapp_account_id : null,
                'last_contact_at'       => $existingConv?->last_message_at ? $existingConv->last_message_at->format('Y-m-d H:i') : null,
                'is_blacklisted'        => $isBlacklisted,
                'is_selected'           => true, // Default selected, user can toggle in UI!
                'lead_id'               => $crmLead?->id,
                'customer_id'           => $crmCustomer?->id,
            ];
        }

        return [
            'records'                      => $matchedList,
            'total_count'                  => count($matchedList),
            'previously_contacted_count'   => $prevCount,
            'not_previously_contacted_count'=> $notPrevCount,
        ];
    }
}
